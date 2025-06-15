<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavingSegment;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\StudentSegmentBalance; 
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException; // Pastikan ini ada
use Carbon\Carbon; // Pastikan Carbon diimpor jika digunakan di dashboard

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacher = auth()->user();

        // Mengambil segmen yang dibuat oleh guru dan menghitung saldo saat ini
        $segments = $teacher->savingSegments()->latest()->get();

        $segments = $segments->map(function ($segment) {
            // Hitung total setoran yang disetujui untuk segmen ini
            $totalDeposits = $segment->transactions()
                                    ->where('type', 'deposit')
                                    ->where('status', 'approved')
                                    ->sum('amount');

            // Hitung total penarikan yang disetujui untuk segmen ini
            $totalWithdrawals = $segment->transactions()
                                     ->where('type', 'withdrawal')
                                     ->where('status', 'approved')
                                     ->sum('amount');

            // Saldo saat ini adalah total setoran dikurangi total penarikan
            $segment->currentBalance = $totalDeposits - $totalWithdrawals;

            // Pastikan menggunakan kolom target_amount dari DB
            $segment->totalTarget = $segment->target_amount ?? 1; // Pastikan tidak nol untuk mencegah pembagian dengan nol

            return $segment;
        });

        $pendingTransactions = Transaction::whereIn('saving_segment_id', $segments->pluck('id'))
                                           ->where('status', 'pending')
                                           ->latest()
                                           ->get();

        $approvedTransactions = Transaction::whereIn('saving_segment_id', $segments->pluck('id'))
                                            ->where('status', 'approved')
                                            ->latest()
                                            ->get();

        $rejectedTransactions = Transaction::whereIn('saving_segment_id', $segments->pluck('id'))
                                            ->where('status', 'rejected')
                                            ->latest()
                                            ->get();

        $totalTarget = $segments->sum('totalTarget');

        return view('teacher.dashboard', compact(
            'segments',
            'pendingTransactions',
            'approvedTransactions',
            'rejectedTransactions',
            'totalTarget'
        ));
    }

    public function showStudents(SavingSegment $segment)
    {
        $teacher = Auth::user();

        $students = User::where('role_id', 3)
            ->whereHas('savingSegments', function ($query) use ($segment) {
                $query->where('saving_segments.id', $segment->id);
            })
            ->get();

        // Hitung saldo tiap siswa berdasarkan transaksi yang disetujui (deposit - withdrawal)
        $studentsWithBalances = $students->map(function ($student) use ($segment) {
            $totalDeposits = Transaction::where('user_id', $student->id)
                ->where('saving_segment_id', $segment->id)
                ->where('type', 'deposit')
                ->where('status', 'approved')
                ->sum('amount');

            $totalWithdrawals = Transaction::where('user_id', $student->id)
                ->where('saving_segment_id', $segment->id)
                ->where('type', 'withdrawal')
                ->where('status', 'approved')
                ->sum('amount');

            $student->balance = $totalDeposits - $totalWithdrawals;
            return $student;
        });

        return view('teacher.segments.show_students', compact('studentsWithBalances', 'segment'));
    }

    public function showSegment(SavingSegment $segment)
    {
        // Pastikan guru yang login adalah pemilik segmen ini
        if (auth()->id() !== $segment->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk melihat detail segmen ini.');
        }

        // Ambil semua transaksi yang disetujui untuk perhitungan saldo dan chart
        $approvedTransactions = $segment->transactions()
                                       ->where('status', 'approved')
                                       ->get();

        // =======================================================================
        // Bagian KRITIS: Perhitungan Saldo Saat Ini (currentBalance)
        // Setoran DIJUMLAHKAN, Penarikan DIKURANGKAN
        // =======================================================================
        $totalDeposits = $approvedTransactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $approvedTransactions->where('type', 'withdrawal')->sum('amount');
        $currentBalance = $totalDeposits - $totalWithdrawals;

        // =======================================================================
        // Bagian KRITIS: Menghitung Kontribusi per Siswa (contributions)
        // Ini juga harus menghitung saldo bersih (deposit - withdrawal) per siswa
        // =======================================================================
        $contributions = [];
        $segmentStudents = $segment->joinedStudents ?? User::whereHas('savingSegments', function($q) use ($segment){
            $q->where('saving_segments.id', $segment->id);
        })->where('role_id', 3)->get();

        foreach ($segmentStudents as $student) {
            $studentDeposits = $student->transactions()
                                        ->where('saving_segment_id', $segment->id)
                                        ->where('type', 'deposit')
                                        ->where('status', 'approved')
                                        ->sum('amount');

            $studentWithdrawals = $student->transactions()
                                          ->where('saving_segment_id', $segment->id)
                                          ->where('type', 'withdrawal')
                                          ->where('status', 'approved')
                                          ->sum('amount');

            $netContribution = $studentDeposits - $studentWithdrawals; 

            // Hanya sertakan siswa dengan kontribusi bersih positif untuk chart
            if ($netContribution > 0) {
                $contributions[] = [
                    'name' => $student->name,
                    'amount' => $netContribution,
                ];
            }
        }

        // Urutkan kontribusi dari yang terbesar ke terkecil
        usort($contributions, function($a, $b) {
            return $b['amount'] <=> $a['amount'];
        });

        // Ambil SEMUA transaksi untuk segmen ini (untuk riwayat transaksi)
        $transactions = $segment->transactions()
                                ->with('user')
                                ->latest()
                                ->paginate(10);

        // Pastikan Anda mengembalikan view setelah debugging selesai!
        return view('teacher.segments.show', compact('segment', 'currentBalance', 'contributions', 'transactions'));
    }

    public function createSegment()
    {
        $uniqueCode = Str::random(8); // Example: Generate a random 8-character code
        return view('teacher.segments.create', compact('uniqueCode'));
    }

    public function storeSegment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $teacher = auth()->user();
        $defaultBannerPath = 'images/default.png';
        $bannerPath = null;

        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
        }

        $generatedUniqueCode = Str::random(8);

        while (SavingSegment::where('unique_code', $generatedUniqueCode)->exists()) {
            $generatedUniqueCode = Str::random(8);
        }

        $segment = $teacher->savingSegments()->create([
            'name' => $request->name,
            'description' => $request->description,
            'target_amount' => $request->target_amount,
            'unique_code' => $generatedUniqueCode,
            'banner' => $bannerPath,
        ]);

        return redirect()->route('teacher.dashboard')->with('success', 'Segment tabungan berhasil dibuat!');
    }

    /**
     * Display transactions for a specific saving segment.
     *
     * @param  \App\Models\SavingSegment  $segment
     * @return \Illuminate\View\View
     */
    public function showSegmentTransactions(SavingSegment $segment)
    {
        if (auth()->user()->id !== $segment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $transactions = $segment->transactions()->with('user')->latest()->get();

        return view('teacher.segments.transactions', compact('segment', 'transactions'));
    }

    public function approveTransaction(Transaction $transaction)
    {
        // Pastikan pengguna yang login memiliki hak untuk menyetujui transaksi ini
        if (auth()->user()->id !== $transaction->savingSegment->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk menyetujui transaksi ini.');
        }

        // Jika transaksi sudah diapprove atau direject, jangan proses lagi
        if ($transaction->status === 'approved' || $transaction->status === 'rejected') {
            return back()->with('error', 'Transaksi ini sudah tidak pending.');
        }

        $transaction->status = 'approved';
        $transaction->save();

        // Temukan atau buat entri saldo siswa untuk segmen ini
        $studentBalance = StudentSegmentBalance::firstOrCreate(
            [
                'user_id' => $transaction->user_id,
                'saving_segment_id' => $transaction->saving_segment_id,
            ],
            ['balance' => 0] // Jika baru dibuat, saldo awal 0
        );

        // Update saldo berdasarkan tipe transaksi yang disetujui
        if ($transaction->type === 'deposit') {
            $studentBalance->balance += $transaction->amount;
        } elseif ($transaction->type === 'withdrawal') {
            // Penting: Kurangi saldo jika ini adalah penarikan yang disetujui
            $studentBalance->balance -= $transaction->amount;
        }
        $studentBalance->save();

        return back()->with('success', 'Transaksi berhasil disetujui!');
    }

    public function rejectTransaction(Transaction $transaction)
    {
        // Pastikan pengguna yang login memiliki hak untuk menolak transaksi ini
        if (auth()->user()->id !== $transaction->savingSegment->user_id) {
            abort(403, 'Anda tidak memiliki akses untuk menolak transaksi ini.');
        }

        // Jika transaksi sudah diapprove atau direject, jangan proses lagi
        if ($transaction->status === 'approved' || $transaction->status === 'rejected') {
            return back()->with('error', 'Transaksi ini sudah tidak pending.');
        }

        // Jika transaksi yang ditolak adalah penarikan dan sebelumnya sudah disetujui,
        // perlu dikembalikan saldonya. Namun, logika Anda saat ini hanya menolak 'pending'.
        // Jadi, ini hanya perlu untuk mencegah penarikan yang sudah 'approved' ditolak dan saldo dikembalikan dua kali.
        if ($transaction->status === 'approved' && $transaction->type === 'withdrawal') {
            // Logika ini mungkin tidak akan terpicu jika hanya pending yang bisa ditolak.
            // Namun, jika ada kemungkinan mengubah status dari approved ke rejected,
            // maka saldo perlu dikembalikan.
            $studentBalance = StudentSegmentBalance::where('user_id', $transaction->user_id)
                                                    ->where('saving_segment_id', $transaction->saving_segment_id)
                                                    ->first();
            if ($studentBalance) {
                $studentBalance->balance += $transaction->amount; 
                $studentBalance->save();
            }
        }
        
        // Pastikan untuk selalu mengubah status transaksi menjadi rejected
        $transaction->status = 'rejected';
        $transaction->save();

        return back()->with('error', 'Transaksi ditolak.');
    }
}
