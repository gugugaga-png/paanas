<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SavingSegment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\StudentSegmentBalance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class SegmentController extends Controller
{
    public function create()
    {
        $uniqueCode = Str::random(8); // kalau mau generate kode unik dulu
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

    // Tambahkan teacher_id sebelum insert
    $validated['user_id'] = auth()->id(); // ✅ benar
// pastikan kolom di DB bernama teacher_id

    SavingSegment::create($validated);
    
    return redirect()->route('teacher.dashboard')->with('success', 'Segment berhasil dibuat.');
}


    public function show(SavingSegment $segment)
    {
        $segment->load('teacher'); // Pastikan relasi ini ada di model

        $transactions = $segment->transactions()->with('user')->latest()->get();

        $studentBalances = $segment->studentBalances()->with('user')->get();

        return view('teacher.segments.show', compact('segment', 'transactions', 'studentBalances'));
    }

    public function showStudents(SavingSegment $segment)
    {
        $studentsWithBalances = $segment->studentBalances()->with('user')->get();

        return view('teacher.segments.show_students', compact('segment', 'studentsWithBalances'));
    }
}
