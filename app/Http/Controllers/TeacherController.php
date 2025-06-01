<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavingSegment; // Make sure this is imported
use App\Models\Transaction;   // Make sure this is imported
use Illuminate\Support\Str;   // For Str::random if used for unique codes
use App\Models\Mail;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\StudentSegmentBalance;


class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacher = auth()->user();
        $segments = $teacher->savingSegments()->latest()->get();

        // Fetch transactions for the dashboard view, if needed (based on previous code)
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

    // Hitung saldo tiap siswa berdasarkan transaksi yang disetujui
    $studentsWithBalances = $students->map(function ($student) use ($segment) {
        $balance = Transaction::where('user_id', $student->id)
            ->where('saving_segment_id', $segment->id)
            ->where('status', 'approved')
            ->sum('amount');

        $student->balance = $balance;
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

        // Ambil transaksi yang pending (status 'pending') HANYA untuk segmen ini.
        $pendingTransactions = $segment->transactions()
                                     ->where('status', 'pending')
                                     ->with('user') // Eager load user
                                     ->latest()
                                     ->get();

        // --- TAMBAHKAN BAGIAN INI ---
        // Ambil SEMUA transaksi untuk segmen ini (untuk riwayat transaksi)
        $transactions = $segment->transactions()
                                ->with('user') // Eager load user
                                ->latest() // Urutkan dari yang terbaru
                                ->get();
        // -----------------------------

        // Teruskan kedua variabel ke view
        return view('teacher.segments.show', compact('segment', 'pendingTransactions', 'transactions'));
    }
    public function createSegment()
    {       
        $uniqueCode = Str::random(8); // Example: Generate a random 8-character code
        return view('teacher.segments.create', compact('uniqueCode'));
    }

   public function storeSegment(Request $request)
    {
        // 1. Validasi data input dari form (TANPA unique_code karena digenerate)
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            // Pastikan UNIQUE_CODE TIDAK ADA DI SINI dalam validasi request
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $teacher = auth()->user();
        $defaultBannerPath = 'images/default.png';
        // 2. Inisialisasi $bannerPath ke null
        $bannerPath = null;

        // 3. Tangani upload banner jika ada
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
        }

        // 4. GENERATE unique_code di sini
        $generatedUniqueCode = Str::random(8); // Buat string acak 10 karakter

        // Pastikan kode yang digenerate belum ada di database
        // Loop sampai menemukan kode yang unik
        while (SavingSegment::where('unique_code', $generatedUniqueCode)->exists()) {
            $generatedUniqueCode = Str::random(8    ); // Regenerasi jika sudah ada
        }

        // 5. Buat record SavingSegment di database
        // PASTIKAN SEMUA FIELD YANG DIPERLUKAN ADA DI SINI
        $segment = $teacher->savingSegments()->create([
            'name' => $request->name,
            'description' => $request->description,
            'target_amount' => $request->target_amount,
            'unique_code' => $generatedUniqueCode, // <-- BARIS INI PENTING DAN HARUS ADA!
            'banner' => $bannerPath,
        ]);

        // 6. Redirect dengan pesan sukses
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
        // IMPORTANT: Ensure the authenticated user (teacher) owns this segment
        if (auth()->user()->id !== $segment->user_id) {
            abort(403, 'Unauthorized action.'); // Or redirect with an error message
        }

        // Load transactions related to this segment, ordered by latest first, with the user who made them
        $transactions = $segment->transactions()->with('user')->latest()->get();

        return view('teacher.segments.transactions', compact('segment', 'transactions'));
    }
    


    // You'll likely need these for approving/rejecting transactions:
    public function approveTransaction(Transaction $transaction)
    {
        // Ensure the transaction belongs to one of the teacher's segments
        if (auth()->user()->id !== $transaction->savingSegment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $transaction->status = 'approved';
        $transaction->save();

        return back()->with('success', 'Transaksi berhasil disetujui!');
    }

    public function rejectTransaction(Transaction $transaction)
    {
        // Ensure the transaction belongs to one of the teacher's segments
        if (auth()->user()->id !== $transaction->savingSegment->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $transaction->status = 'rejected';
        $transaction->save();

        return back()->with('error', 'Transaksi ditolak.');
    }

    // ... (any other methods)
}