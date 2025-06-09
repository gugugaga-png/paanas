<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SavingSegment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\StudentSegmentBalance; // Pastikan model ini benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SegmentController extends Controller
{
    public function create()
    {
        $uniqueCode = Str::random(8);
        return view('teacher.segments.create', compact('uniqueCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'banner' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['unique_code'] = strtoupper(Str::random(8));

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $validated['user_id'] = auth()->id();

        SavingSegment::create($validated);
        
        return redirect()->route('teacher.dashboard')->with('success', 'Segment berhasil dibuat.');
    }

    public function show(SavingSegment $segment)
    {
        $segment->load('teacher');

        $transactions = $segment->transactions()->with('user')->latest()->paginate(10);
        $studentBalances = $segment->studentBalances()->with('user')->get();

        // ✅ PERBAIKAN DI SINI: Gunakan 'balance' jika itu nama kolom saldo
        // Pastikan nama kolom di tabel 'student_segment_balances' adalah 'balance'
        $currentBalance = $studentBalances->sum('balance'); // <-- Kemungkinan ini adalah perbaikan yang Anda butuhkan

        $contributions = $studentBalances->map(function ($balance) {
            return [
                'name' => $balance->user->name ?? 'N/A',
                // ✅ Ini sudah benar jika kolom saldo bernama 'balance'
                'amount' => (float) $balance->balance, 
            ];
        });

        return view('teacher.segments.show', compact('segment', 'transactions', 'studentBalances', 'currentBalance', 'contributions'));
    }

    public function showStudents(SavingSegment $segment)
    {
        $studentsWithBalances = $segment->studentBalances()->with('user')->get();

        return view('teacher.segments.show_students', compact('segment', 'studentsWithBalances'));
    }
}