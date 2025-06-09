<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavingSegment;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException; // <-- Pastikan ini ada
use Illuminate\Support\Facades\DB; // <-

class StudentController extends Controller
{
    public function dashboard()
    {
        $student = Auth::user();

        // Mengambil segments yang diikuti dan menghitung saldo untuk setiap segment
        // Ambil hanya 5 segmen terbaru untuk ditampilkan di dashboard
        $joinedSegments = $student->joinedSavingSegments()
                                   ->latest()
                                   ->take(5) // HANYA AMBIL 5 TERATAS UNTUK DASHBOARD
                                   ->get()
                                   ->map(function ($segment) use ($student) {
                                       $totalDeposits = $student->transactions()
                                           ->where('saving_segment_id', $segment->id)
                                           ->where('type', 'deposit')
                                           ->where('status', 'approved')
                                           ->sum('amount');

                                       $totalWithdrawals = $student->transactions()
                                           ->where('saving_segment_id', $segment->id)
                                           ->where('type', 'withdrawal')
                                           ->where('status', 'approved')
                                           ->sum('amount');

                                       $segment->balance = $totalDeposits - $totalWithdrawals;
                                       return $segment;
                                   });

        // Ambil juga total semua segmen (tanpa batasan 5) untuk keperluan checking 'show more'
        $totalJoinedSegmentsCount = $student->joinedSavingSegments()->count();


        $pendingTransactions = $student->transactions()->where('status', 'pending')->latest()->get();
        $approvedTransactions = $student->transactions()->where('status', 'approved')->latest()->get();
        $rejectedTransactions = $student->transactions()->where('status', 'rejected')->latest()->get();

        // Total saldo global (akumulasi dari semua segment yang disetujui)
        $totalBalance = $joinedSegments->sum('balance'); // Menggunakan saldo per segmen yang sudah dihitung

        $now = Carbon::now();
        $startThisMonth = $now->copy()->startOfMonth();
        $startLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Hitung pemasukan (deposit) bulan ini dan bulan lalu
        $incomeThisMonth = $student->transactions()
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->whereBetween('updated_at', [$startThisMonth, $now])
            ->sum('amount');

        $incomeLastMonth = $student->transactions()
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->whereBetween('updated_at', [$startLastMonth, $endLastMonth])
            ->sum('amount');

        $saldoGrowth = 0;
        if ($incomeLastMonth > 0) {
            $saldoGrowth = (($incomeThisMonth - $incomeLastMonth) / $incomeLastMonth) * 100;
        }

        // --- Data Chart Batang per 7 hari ---
        $weeklyLabels = [];
        $weeklyIncome = [];
        $weeklyExpense = [];

        // Data untuk chart "Pendapatan Mingguan: Bulan Ini vs Bulan Lalu"
        $weeklyIncomeThisMonth = [];
        $weeklyIncomeLastMonth = [];
        $monthlyWeeklyLabels = [];

        // Periode untuk chart Pemasukan vs Pengeluaran (Mingguan)
        // Ambil data untuk 4 periode 7 hari terakhir
        for ($i = 0; $i < 4; $i++) {
            $currentPeriodStart = $now->copy()->subDays((3 - $i) * 7 + 6)->startOfDay();
            $currentPeriodEnd = $now->copy()->subDays((3 - $i) * 7)->endOfDay();

            // Label untuk chart "Pemasukan vs Pengeluaran"
            $weeklyLabels[] = $currentPeriodStart->format('d/m') . ' - ' . $currentPeriodEnd->format('d/m');

            $weeklyIncome[] = $student->transactions()
                ->where('type', 'deposit')
                ->where('status', 'approved')
                ->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])
                ->sum('amount');

            $weeklyExpense[] = $student->transactions()
                ->where('type', 'withdrawal')
                ->where('status', 'approved')
                ->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])
                ->sum('amount');
        }

        // Data untuk chart "Pendapatan Mingguan: Bulan Ini vs Bulan Lalu"
        for ($i = 0; $i < 4; $i++) {
            $currentMonthWeekStart = $startThisMonth->copy()->addWeeks($i)->startOfWeek(Carbon::SUNDAY);
            $currentMonthWeekEnd = $currentMonthWeekStart->copy()->endOfWeek(Carbon::SATURDAY);

            $lastMonthWeekStart = $startLastMonth->copy()->addWeeks($i)->startOfWeek(Carbon::SUNDAY);
            $lastMonthWeekEnd = $lastMonthWeekStart->copy()->endOfWeek(Carbon::SATURDAY);

            // Pastikan tidak melewati tanggal saat ini atau akhir bulan lalu
            $currentMonthWeekEnd = $currentMonthWeekEnd->min($now);
            $lastMonthWeekEnd = $lastMonthWeekEnd->min($endLastMonth);

            $monthlyWeeklyLabels[] = 'Minggu ' . ($i + 1);

            $weeklyIncomeThisMonth[] = $student->transactions()
                ->where('type', 'deposit')
                ->where('status', 'approved')
                ->whereBetween('created_at', [$currentMonthWeekStart, $currentMonthWeekEnd])
                ->sum('amount');

            $weeklyIncomeLastMonth[] = $student->transactions()
                ->where('type', 'deposit')
                ->where('status', 'approved')
                ->whereBetween('created_at', [$lastMonthWeekStart, $lastMonthWeekEnd])
                ->sum('amount');
        }

        return view('student.dashboard', compact(
            'joinedSegments',
            'pendingTransactions',
            'approvedTransactions',
            'rejectedTransactions',
            'totalBalance',
            'saldoGrowth',
            'weeklyLabels',
            'weeklyIncome',
            'weeklyExpense',
            'weeklyIncomeThisMonth',
            'weeklyIncomeLastMonth',
            'monthlyWeeklyLabels',
            'totalJoinedSegmentsCount' // Pastikan ini di-pass
        ));
    }

    public function joinSegmentForm()
    {
        return view('student.join_segment_form');
    }

    // Metode baru untuk menampilkan semua segmen
    public function indexSegments()
    {
        $student = Auth::user();
        $allJoinedSegments = $student->joinedSavingSegments()->latest()->get()->map(function ($segment) use ($student) {
            $totalDeposits = $student->transactions()
                ->where('saving_segment_id', $segment->id)
                ->where('type', 'deposit')
                ->where('status', 'approved')
                ->sum('amount');

            $totalWithdrawals = $student->transactions()
                ->where('saving_segment_id', $segment->id)
                ->where('type', 'withdrawal')
                ->where('status', 'approved')
                ->sum('amount');

            $segment->balance = $totalDeposits - $totalWithdrawals;
            return $segment;
        });

        return view('student.segments.index', compact('allJoinedSegments'));
    }

    public function segmentDetail(SavingSegment $segment)
    {
        $student = Auth::user();

        // Hitung saldo untuk segment spesifik ini
        $totalDeposits = $student->transactions()
            ->where('saving_segment_id', $segment->id)
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->sum('amount');

        $totalWithdrawals = $student->transactions()
            ->where('saving_segment_id', $segment->id)
            ->where('type', 'withdrawal')
            ->where('status', 'approved')
            ->sum('amount');

        $balance = $totalDeposits - $totalWithdrawals;


        // Get transactions
        $transactions = $student->transactions()
            ->where('saving_segment_id', $segment->id)
            ->latest()->paginate(10);

        // Statistik 7 hari terakhir
        $labels = [];
        $data = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('d M');
            $data[] = $student->transactions()
                ->whereDate('created_at', $date)
                ->where('saving_segment_id', $segment->id)
                ->where('status', 'approved') // Pastikan ini juga disaring
                ->sum('amount');
        }

        $weeklyStats = [
            'labels' => $labels,
            'data' => $data,
        ];


        return view('student.segments.detail', compact('segment', 'balance', 'transactions', 'weeklyStats'));

    }

    public function joinSegment(Request $request)
    {
        $request->validate([
            'unique_code' => 'required|string|size:8|exists:saving_segments,unique_code',
        ], [
            'unique_code.required' => 'Kode unik tidak boleh kosong.',
            'unique_code.size' => 'Kode unik harus 8 karakter.',
            'unique_code.exists' => 'Kode unik tidak ditemukan.',
        ]);

        $segment = SavingSegment::where('unique_code', $request->unique_code)->first();
        $student = Auth::user();

        // Check if the student is already a member of this segment
        if ($student->joinedSavingSegments()->where('saving_segments.id', $segment->id)->exists()) {
            return back()->with('error', 'Anda sudah bergabung dengan segment tabungan ini.');
        }

        // Attach the student to the saving segment
        $student->joinedSavingSegments()->attach($segment->id, ['joined_at' => now()]);

        return redirect()->route('student.dashboard')->with('success', 'Berhasil bergabung dengan segment tabungan!');
    }

    public function depositForm()
    {
        $student = Auth::user();
        $joinedSegments = $student->joinedSavingSegments()->get()->map(function ($segment) use ($student) {
            $totalDeposits = $student->transactions()
                ->where('saving_segment_id', $segment->id)
                ->where('type', 'deposit')
                ->where('status', 'approved')
                ->sum('amount');

            $totalWithdrawals = $student->transactions()
                ->where('saving_segment_id', $segment->id)
                ->where('type', 'withdrawal')
                ->where('status', 'approved')
                ->sum('amount');

            $segment->balance = $totalDeposits - $totalWithdrawals;
            return $segment;
        });

        return view('student.transactions.deposit_form', ['segments' => $joinedSegments]);
    }

    public function withdrawForm()
    {
        return redirect()->route('student.dashboard');
    }


    public function submitWithdraw(Request $request)
    {
        $request->validate([
            'saving_segment_id' => 'required|exists:saving_segments,id',
            'amount' => 'required|numeric|min:1000', // Minimal jumlah penarikan
        ]);

        $user = Auth::user();
        $segmentId = $request->input('saving_segment_id');
        $withdrawalAmount = $request->input('amount');

        // Pastikan siswa terdaftar di segmen ini
        // Ganti $user->segments() menjadi $user->joinedSavingSegments()
        if (!$user->joinedSavingSegments()->where('saving_segments.id', $segmentId)->exists()) { // <-- PERUBAHAN DI SINI
            return redirect()->back()->with('error', 'Anda tidak terdaftar di segmen tabungan ini.');
        }

        // Ambil segmen tabungan yang dipilih
        $segment = SavingSegment::findOrFail($segmentId);

        // Hitung total saldo yang tersedia di segmen tersebut
        // Ini adalah saldo yang sudah disetujui (deposit - withdrawal)
        $approvedDeposits = $user->transactions() // Gunakan $user->transactions() bukan $segment->transactions() untuk memastikan scope user
                                 ->where('saving_segment_id', $segment->id)
                                 ->where('type', 'deposit')
                                 ->where('status', 'approved')
                                 ->sum('amount');

        $approvedWithdrawals = $user->transactions() // Gunakan $user->transactions()
                                    ->where('saving_segment_id', $segment->id)
                                    ->where('type', 'withdrawal')
                                    ->where('status', 'approved')
                                    ->sum('amount');

        $currentBalance = $approvedDeposits - $approvedWithdrawals;

        // Hitung total penarikan yang masih pending untuk user dan segmen ini
        $pendingWithdrawals = $user->transactions() // Gunakan $user->transactions()
                                   ->where('saving_segment_id', $segment->id)
                                   ->where('type', 'withdrawal')
                                   ->where('status', 'pending')
                                   ->sum('amount');

        // Saldo efektif adalah saldo saat ini dikurangi penarikan yang masih pending
        $effectiveBalance = $currentBalance - $pendingWithdrawals;

        // Validasi: Apakah jumlah penarikan + semua penarikan pending melebihi saldo efektif?
        if ($withdrawalAmount > $effectiveBalance) {
            // Jika ada pengajuan pending, berikan pesan yang lebih spesifik
            if ($pendingWithdrawals > 0) {
                $errorMessage = 'Jumlah penarikan yang diminta (Rp ' . number_format($withdrawalAmount, 0, ',', '.') . ') melebihi saldo tersedia (Rp ' . number_format($currentBalance, 0, ',', '.') . ') setelah dikurangi pengajuan sebelumnya yang masih menunggu (Rp ' . number_format($pendingWithdrawals, 0, ',', '.') . '). Saldo efektif saat ini hanya Rp ' . number_format($effectiveBalance, 0, ',', '.') . '.';
            } else {
                $errorMessage = 'Jumlah penarikan (Rp ' . number_format($withdrawalAmount, 0, ',', '.') . ') melebihi saldo yang tersedia (Rp ' . number_format($currentBalance, 0, ',', '.') . ') di segmen ini.';
            }

            // Gunakan ValidationException untuk mengembalikan error ke formulir
            throw ValidationException::withMessages([
                'amount' => [$errorMessage],
            ]);
        }

        // Jika validasi lolos, buat transaksi penarikan
        DB::transaction(function () use ($user, $segment, $withdrawalAmount) {
            Transaction::create([
                'user_id' => $user->id,
                'saving_segment_id' => $segment->id,
                'type' => 'withdrawal',
                'amount' => $withdrawalAmount,
                'status' => 'pending', // Status awal adalah pending
                // 'approved_by_id' => null, // Ini akan diisi saat disetujui oleh guru
            ]);
        });

        return redirect()->back()->with('success', 'Pengajuan penarikan dana Anda berhasil dikirim dan menunggu validasi.');
    }


    public function submitDeposit(Request $request)
    {
        $request->validate([
            'saving_segment_id' => [
                'required',
                'exists:saving_segments,id',
                function ($attribute, $value, $fail) {
                    $user = Auth::user();
                    if (!$user->joinedSavingSegments()->where('saving_segments.id', $value)->exists()) {
                        $fail('Segment tabungan yang dipilih tidak valid atau Anda belum bergabung.');
                    }
                },
            ],
            'amount' => 'required|numeric|min:1000|max:1000000000',
        ], [
            'saving_segment_id.required' => 'Segment tabungan harus dipilih.',
            'saving_segment_id.exists' => 'Segment tabungan tidak valid.',
            'amount.required' => 'Jumlah deposit harus diisi.',
            'amount.numeric' => 'Jumlah deposit harus berupa angka.',
            'amount.min' => 'Jumlah deposit minimal Rp :min.',
            'amount.max' => 'Jumlah deposit maksimal Rp :max.',
        ]);

        $student = Auth::user();

        Transaction::create([
            'user_id' => $student->id,
            'saving_segment_id' => $request->saving_segment_id,
            'amount' => $request->amount,
            'type' => 'deposit',
            'status' => 'pending',
        ]);

        return redirect()->route('student.dashboard')
            ->with('success', 'Pengajuan deposit tabungan berhasil dikirim. Menunggu validasi guru.');
    }
}