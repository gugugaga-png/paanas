<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\StudentSegmentBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\SavingSegment; 

class MailController extends Controller
{
   
   public function index(Request $request, SavingSegment $segment = null)
{
    $query = Transaction::with(['user', 'savingSegment'])
                        ->where('status', 'pending')
                        ->whereHas('savingSegment', function ($q) {
                            $q->where('user_id', auth()->id());
                        });

    if ($segment) {
        $query->where('saving_segment_id', $segment->id);
    }

    $pendingTransactions = $query->latest()->get();

    return view('teacher.mail.index', ['pendingTransactions' => $pendingTransactions, 'savingSegment' => $segment]);
}


 
    public function approve(Transaction $transaction)
    {
        // Pastikan guru memiliki hak untuk menyetujui transaksi ini (misalnya, pemilik segmen)
        if (auth()->id() !== $transaction->savingSegment->user_id) {
            return redirect()->back()->with('error', 'Anda tidak berhak menyetujui transaksi ini.');
        }

        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi ini sudah tidak dalam status menunggu.');
        }

        try {
            DB::transaction(function () use ($transaction) {
                $transaction->status = 'approved';
                $transaction->teacher_id = auth()->id();
                $transaction->save();

                $totalApprovedBalance = Transaction::where('user_id', $transaction->user_id)
                    ->where('saving_segment_id', $transaction->saving_segment_id)
                    ->where('status', 'approved')
                    ->sum('amount');

                StudentSegmentBalance::updateOrCreate(
                    [
                        'user_id' => $transaction->user_id,
                        'saving_segment_id' => $transaction->saving_segment_id
                    ],
                    ['balance' => $totalApprovedBalance]
                );
            });

            // Redirect kembali ke halaman index dengan parameter segmen jika ada
            $redirectParams = $transaction->saving_segment_id ? ['saving_segment' => $transaction->saving_segment_id] : [];
            return redirect()->route('teacher.mail.index', $redirectParams)->with('success', 'Transaksi berhasil disetujui dan saldo diperbarui!');

        } catch (\Exception $e) {
            Log::error("Error approving transaction: " . $e->getMessage(), [
                'transaction_id' => $transaction->id,
                'user_id' => $transaction->user_id,
                'saving_segment_id' => $transaction->saving_segment_id
            ]);
            $redirectParams = $transaction->saving_segment_id ? ['saving_segment' => $transaction->saving_segment_id] : [];
            return redirect()->route('teacher.mail.index', $redirectParams)->with('error', 'Terjadi kesalahan saat menyetujui transaksi. Silakan coba lagi.');
        }
    }

    /**
     * Menolak transaksi.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Transaction $transaction)
    {
        // Pastikan guru memiliki hak untuk menolak transaksi ini
        if (auth()->id() !== $transaction->savingSegment->user_id) {
            return redirect()->back()->with('error', 'Anda tidak berhak menolak transaksi ini.');
        }

        if ($transaction->status !== 'pending') {
            return redirect()->back()->with('error', 'Transaksi ini sudah tidak dalam status menunggu.');
        }

        try {
            DB::transaction(function () use ($transaction) {
                $transaction->status = 'rejected';
                $transaction->teacher_id = auth()->id();
                $transaction->save();
            });

            // Redirect kembali ke halaman index dengan parameter segmen jika ada
            $redirectParams = $transaction->saving_segment_id ? ['saving_segment' => $transaction->saving_segment_id] : [];
            return redirect()->route('teacher.mail.index', $redirectParams)->with('success', 'Transaksi berhasil ditolak!');

        } catch (\Exception $e) {
            \Log::error("Error rejecting transaction: " . $e->getMessage(), [
                'transaction_id' => $transaction->id,
            ]);
            $redirectParams = $transaction->saving_segment_id ? ['saving_segment' => $transaction->saving_segment_id] : [];
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menolak transaksi. Silakan coba lagi.');
        }
    }
}