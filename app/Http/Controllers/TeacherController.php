<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SavingSegment; // Make sure this is imported
use App\Models\Transaction;   // Make sure this is imported
use Illuminate\Support\Str;   // For Str::random if used for unique codes

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
            'unique_code' => 'required|string|unique:saving_segments,unique_code|max:20',
        ]);

        $teacher = auth()->user();

        $segment = $teacher->savingSegments()->create([
            'name' => $request->name,
            'description' => $request->description,
            'target_amount' => $request->target_amount,
            'unique_code' => $request->unique_code,
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