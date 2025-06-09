<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavingSegment;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class StudentController extends Controller
{
    public function dashboard()
    {
        $student = Auth::user();

        // Mengambil segments yang diikuti dan menghitung saldo untuk setiap segment
        $joinedSegments = $student->joinedSavingSegments()->latest()->get()->map(function ($segment) use ($student) {
            $totalDeposits = $student->transactions()
                ->where('saving_segment_id', $segment->id)
                ->where('type', 'deposit') // Hanya deposit
                ->where('status', 'approved')
                ->sum('amount');

            $totalWithdrawals = $student->transactions()
                ->where('saving_segment_id', $segment->id)
                ->where('type', 'withdrawal') // Hanya withdrawal
                ->where('status', 'approved')
                ->sum('amount');

            $segment->balance = $totalDeposits - $totalWithdrawals; // Saldo adalah deposit dikurangi withdrawal
            return $segment;
        });

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
            $currentPeriodStart = $now->copy()->subDays((3 - $i) * 7 + 6)->startOfDay(); // Menyesuaikan untuk 4 minggu terakhir
            $currentPeriodEnd = $now->copy()->subDays((3 - $i) * 7)->endOfDay();

            // Label untuk chart "Pemasukan vs Pengeluaran"
            $weeklyLabels[] = $currentPeriodStart->format('d/m') . ' - ' . $currentPeriodEnd->format('d/m');

            $weeklyIncome[] = $student->transactions()
                ->where('type', 'deposit')
                ->where('status', 'approved')
                ->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])
                ->sum('amount');

            $weeklyExpense[] = $student->transactions()
                ->where('type', 'withdrawal') // Gunakan 'withdrawal' sesuai dengan yang Anda simpan di DB
                ->where('status', 'approved')
                ->whereBetween('created_at', [$currentPeriodStart, $currentPeriodEnd])
                ->sum('amount');
        }

        // Data untuk chart "Pendapatan Mingguan: Bulan Ini vs Bulan Lalu"
        // Ini akan menampilkan 4 minggu dari bulan ini dan 4 minggu dari bulan lalu.
        for ($i = 0; $i < 4; $i++) {
            $currentMonthWeekStart = $startThisMonth->copy()->addWeeks($i)->startOfWeek(Carbon::SUNDAY); // Asumsi minggu dimulai hari Minggu
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
            'monthlyWeeklyLabels' // Pastikan ini di-pass
        ));
    }

    public function joinSegmentForm()
    {
        return view('student.join_segment_form');
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
            // Untuk chart di detail segment, mungkin lebih baik menampilkan total transaksi (deposit + withdrawal)
            // Atau Anda bisa memisahkan menjadi deposit dan withdrawal seperti di dashboard
            $data[] = $student->transactions()
                ->whereDate('created_at', $date)
                ->where('saving_segment_id', $segment->id)
                ->sum('amount'); // Ini akan menjumlahkan deposit dan withdrawal. Sesuaikan jika ingin dipisah.
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
        // Get the segments the student has joined to populate a dropdown
        // Sekarang, kita perlu menghitung saldo untuk setiap segment di sini juga agar bisa ditampilkan di form deposit (jika ada)
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

        return view('student.transactions.deposit_form', ['segments' => $joinedSegments]); // Menggunakan 'segments'
    }

    // Metode kosong karena modal langsung memicu submitWithdraw
    public function withdrawForm()
    {
        // Karena kita menggunakan modal di dashboard, metode ini mungkin tidak perlu mengembalikan view.
        // Namun, jika Anda punya halaman withdraw terpisah, ini akan dibutuhkan.
        // Untuk saat ini, kita bisa membiarkannya kosong atau redirect ke dashboard.
        return redirect()->route('student.dashboard');
    }

    public function submitWithdraw(Request $request)
    {
        $request->validate([
            'saving_segment_id' => [
                'required',
                'exists:saving_segments,id',
                function ($attribute, $value, $fail) {
                    $user = auth()->user();
                    if (!$user->joinedSavingSegments()->where('saving_segments.id', $value)->exists()) {
                        $fail('Segment tabungan yang dipilih tidak valid atau Anda belum bergabung.');
                    }
                },
            ],
            'amount' => 'required|numeric|min:1000|max:1000000000',
        ], [
            'saving_segment_id.required' => 'Segment tabungan harus dipilih.',
            'saving_segment_id.exists' => 'Segment tabungan tidak valid.',
            'amount.required' => 'Jumlah penarikan harus diisi.',
            'amount.numeric' => 'Jumlah penarikan harus berupa angka.',
            'amount.min' => 'Jumlah penarikan minimal Rp :min.',
            'amount.max' => 'Jumlah penarikan maksimal Rp :max.',
        ]);

        $user = auth()->user();

        // *PERBAIKAN PENTING DI SINI*
        // Hitung saldo aktual untuk segmen yang dipilih
        $segmentId = $request->saving_segment_id;

        $totalDeposits = $user->transactions()
            ->where('saving_segment_id', $segmentId)
            ->where('type', 'deposit')
            ->where('status', 'approved')
            ->sum('amount');

        $totalWithdrawals = $user->transactions()
            ->where('saving_segment_id', $segmentId)
            ->where('type', 'withdrawal')
            ->where('status', 'approved')
            ->sum('amount');

        $currentBalance = $totalDeposits - $totalWithdrawals;


        if ($request->amount > $currentBalance) {
            return back()->withErrors(['amount' => 'Saldo tidak cukup untuk penarikan. Saldo Anda saat ini: Rp ' . number_format($currentBalance, 0, ',', '.') . '.'])->withInput();
        }

        // Buat transaksi withdrawal dengan status pending
        Transaction::create([
            'user_id' => $user->id,
            'saving_segment_id' => $segmentId,
            'amount' => $request->amount,
            'type' => 'withdrawal', // tipe withdraw
            'status' => 'pending', // status pending dulu, menunggu validasi guru
        ]);

        return redirect()->route('student.dashboard') // Redirect ke dashboard setelah submit withdraw
            ->with('success', 'Pengajuan penarikan tabungan berhasil dikirim. Menunggu validasi guru.');
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

    // Redirect ke dashboard setelah submit deposit
    return redirect()->route('student.dashboard')
        ->with('success', 'Pengajuan deposit tabungan berhasil dikirim. Menunggu validasi guru.');
}
}