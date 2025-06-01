<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\SavingSegment;
use App\Models\Transaction;
use App\Models\User;
use App\Models\StudentSegmentBalance; // Pastikan ini diimpor
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SegmentController extends Controller
{
    public function show(SavingSegment $segment)
    {
        // === PERBAIKI BARIS INI ===
        // Ganti 'user' menjadi 'teacher' agar sesuai dengan definisi relasi di model SavingSegment
        $segment->load('teacher'); // Memuat pembuat segment (guru)

        $transactions = $segment->transactions()->with('user')->latest()->get(); // Memuat transaksi

        // Tambahkan ini juga jika Anda ingin menampilkan saldo siswa di segmen ini
        $studentBalances = $segment->studentBalances()->with('user')->get();


        return view('teacher.segments.show', compact('segment', 'transactions', 'studentBalances'));
    }

    public function showStudents(SavingSegment $segment)
    {
        // Pilihan terbaik untuk menampilkan siswa dengan saldo mereka di segmen ini:
        // Gunakan relasi studentBalances dari SavingSegment, lalu eager load user.
        $studentsWithBalances = $segment->studentBalances()->with('user')->get();

        // Jika Anda juga ingin menampilkan siswa yang bergabung tapi belum punya saldo/transaksi,
        // bisa gunakan $segment->joinedUsers()->get(); dan kirimkan juga ke view.
        // Tapi untuk tabel "Daftar Siswa & Saldo Tabungan", $studentsWithBalances lebih relevan.

        return view('teacher.segments.show_students', compact('segment', 'studentsWithBalances')); // <-- Kirim variabel ini
    }
}