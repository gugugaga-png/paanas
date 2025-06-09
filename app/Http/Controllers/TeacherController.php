<?php

namespace App\Http\Controllers; // <--- INI MASALAHNYA!

use Illuminate\Http\Request;
use App\Models\SavingSegment;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\StudentSegmentBalance; 
use Illuminate\Support\Facades\DB;
class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacher = auth()->user();
        $segments = $teacher->savingSegments()->latest()->get();

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

        return view('teacher.dashboard', compact('segments', 'pendingTransactions', 'approvedTransactions', 'rejectedTransactions'));
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
        // Pastikan Anda hanya mengambil transaksi dengan status 'approved'
        $approvedTransactions = $segment->transactions()
                                       ->where('status', 'approved')
                                       ->get();

        // =======================================================================
        // Bagian KRITIS: Perhitungan Saldo Saat Ini (currentBalance)
        // Setoran DIJUMLAHKAN, Penarikan DIKURANGKAN
        // =======================================================================
        $totalDeposits = $approvedTransactions->where('type', 'deposit')->sum('amount');
        $totalWithdrawals = $approvedTransactions->where('type', 'withdrawal')->sum('amount');
        $currentBalance = $totalDeposits - $totalWithdrawals; // <-- INI PENTING!
        
        // =======================================================================
        // Bagian KRITIS: Menghitung Kontribusi per Siswa (contributions)
        // Ini juga harus menghitung saldo bersih (deposit - withdrawal) per siswa
        // =======================================================================
        $contributions = [];
        // Asumsi relasi 'joinedStudents' sudah ada di model SavingSegment
        // Jika tidak, Anda perlu cara lain untuk mendapatkan daftar siswa yang bergabung.
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

            $netContribution = $studentDeposits - $studentWithdrawals; // <-- INI BENAR, DI DALAM LOOP!

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

        // Debugging: Hentikan eksekusi dan tampilkan variabel
        dd([
            'segment' => $segment->toArray(),
            'totalDeposits' => $totalDeposits,
            'totalWithdrawals' => $totalWithdrawals,
            'currentBalanceCalculated' => $currentBalance,
            'transactions' => $transactions->toArray(), // Untuk melihat data transaksi yang dipaginasi
            'contributions' => $contributions,
        ]);
        // Pastikan Anda mengembalikan view setelah debugging selesai!
        // return view('teacher.segments.show', compact('segment', 'currentBalance', 'contributions', 'transactions'));
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

    // Simpan status lama sebelum diubah, jika perlu untuk logika rollback/reversal
    // $oldStatus = $transaction->status; 

    $transaction->status = 'approved';
    $transaction->save();

    // --- INI ADALAH BAGIAN PENTING YANG HARUS DITAMBAHKAN ---
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
    // --- AKHIR BAGIAN PENTING ---

    return back()->with('success', 'Transaksi berhasil disetujui!');
}

public function rejectTransaction(Transaction $transaction)
{
    // Pastikan pengguna yang login memiliki hak untuk menolak transaksi ini
    if (auth()->user()->id !== $transaction->savingSegment->user_id) {
        abort(403, 'Anda tidak memiliki akses untuk menolak transaksi ini.');
    }

    // Penting: Jika transaksi sebelumnya sudah disetujui (misalnya withdrawal)
    // dan sekarang ditolak, Anda mungkin perlu mengembalikan saldo yang sudah terkurang.
    // Ini adalah logika yang lebih kompleks. Untuk kasus sederhana, jika hanya 'pending' yang bisa ditolak:
    if ($transaction->status === 'approved' && $transaction->type === 'withdrawal') {
        // Jika penarikan yang sudah disetujui sekarang ditolak, kembalikan saldonya
        $studentBalance = StudentSegmentBalance::where('user_id', $transaction->user_id)
                                                ->where('saving_segment_id', $transaction->saving_segment_id)
                                                ->first();
        if ($studentBalance) {
            $studentBalance->balance += $transaction->amount; // Tambahkan kembali
            $studentBalance->save();
        }
    }
    // Pastikan untuk selalu mengubah status transaksi
    $transaction->status = 'rejected';
    $transaction->save();

    return back()->with('error', 'Transaksi ditolak.');
}
}