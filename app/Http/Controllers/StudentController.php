<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavingSegment; // Make sure this is imported
use Illuminate\Support\Facades\Auth; // Make sure this is imported
use App\Models\Transaction;   // <--- ADD THIS LINE
use Illuminate\Support\Facades\Storage; // To handle file storage
class StudentController extends Controller
{
    public function dashboard()
    {
        $student = Auth::user();

        // Get segments the student has joined
        $joinedSegments = $student->joinedSavingSegments()->latest()->get();

        // Get ALL transactions for the student (for general display)
        $allTransactions = $student->transactions()->latest()->get();

        // Filter transactions by status for specific sections
        $pendingTransactions = $student->transactions()->where('status', 'pending')->latest()->get();
        $approvedTransactions = $student->transactions()->where('status', 'approved')->latest()->get();
        $rejectedTransactions = $student->transactions()->where('status', 'rejected')->latest()->get();


        return view('student.dashboard', compact(
            'joinedSegments',
            'allTransactions', // You might use this for a combined list
            'pendingTransactions',
            'approvedTransactions',
            'rejectedTransactions'
        ));
    }
    public function joinSegmentForm()
    {
        return view('student.join_segment_form');
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
        $segments = $student->joinedSavingSegments()->get();

        return view('student.transactions.deposit_form', compact('segments'));
    }

    // ... (other methods like depositForm, submitDeposit)
     public function submitDeposit(Request $request)
    {
        // 1. Validate the request data
        $request->validate([
            'saving_segment_id' => [
                'required',
                'exists:saving_segments,id',
                // Optional: Custom rule to ensure the student actually joined this segment
                function ($attribute, $value, $fail) {
                    $user = Auth::user();
                    if (!$user->joinedSavingSegments()->where('saving_segments.id', $value)->exists()) {
                        $fail('Segment tabungan yang dipilih tidak valid atau Anda belum bergabung.');
                    }
                },
            ],
            'amount' => 'required|numeric|min:1000|max:1000000000', // Example min/max, adjust as needed
           
        ], [
            'saving_segment_id.required' => 'Segment tabungan harus dipilih.',
            'saving_segment_id.exists' => 'Segment tabungan tidak valid.',
            'amount.required' => 'Jumlah deposit harus diisi.',
            'amount.numeric' => 'Jumlah deposit harus berupa angka.',
            'amount.min' => 'Jumlah deposit minimal Rp :min.',
            'amount.max' => 'Jumlah deposit maksimal Rp :max.',
        ]);

        $student = Auth::user();


        // 2. Handle file upload


        // 3. Create the transaction record
        Transaction::create([
            'user_id' => $student->id,
            'saving_segment_id' => $request->saving_segment_id,
            'amount' => $request->amount,
            'type' => 'deposit', // This is a deposit transaction
            'status' => 'pending', // All new deposits are pending by default
        ]);

        // 4. Redirect with a success message
        return redirect()->route('student.dashboard')->with('success', 'Pengajuan deposit tabungan berhasil dikirim. Menunggu validasi guru.');
    }
}