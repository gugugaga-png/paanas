<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// Penting: Impor model User dan SavingSegment jika mereka berada di namespace yang berbeda
// (Dalam kasus Anda, mereka ada di namespace yang sama, App\Models, jadi ini mungkin tidak diperlukan,
// tetapi selalu baik untuk memastikan atau menambahkannya untuk kejelasan.)
use App\Models\User;
use App\Models\SavingSegment;

class StudentSegmentBalance extends Model
{
    use HasFactory;

    // Nama tabel di database (jika berbeda dari konvensi Laravel)
    // Laravel secara otomatis akan mencari 'student_segment_balances' (plural dari nama model)
    // protected $table = 'student_segment_balances'; // Bisa dihilangkan jika mengikuti konvensi

    // Kolom-kolom yang boleh diisi secara massal (mass assignable)
    protected $fillable = [
        'user_id',           // Foreign key ke tabel 'users' (siswa)
        'saving_segment_id', // Foreign key ke tabel 'saving_segments'
        'balance',           // Saldo siswa untuk segmen spesifik ini
    ];

    // Kolom-kolom yang harus di-cast ke tipe data tertentu (opsional tapi bagus untuk 'balance')
    protected $casts = [
        'balance' => 'decimal:2', // Pastikan saldo selalu dalam format desimal dengan 2 angka di belakang koma
    ];

    /**
     * Relasi: Saldo ini dimiliki oleh satu pengguna (siswa).
     * Inverse relationship dari User::segmentBalances()
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // 'user_id' adalah foreign key di tabel ini
    }

    /**
     * Relasi: Saldo ini terkait dengan satu segmen tabungan.
     * Inverse relationship dari SavingSegment::studentBalances()
     */
    public function segment() // Bisa juga dinamakan 'savingSegment' untuk konsistensi
    {
        return $this->belongsTo(SavingSegment::class, 'saving_segment_id'); // 'saving_segment_id' adalah foreign key di tabel ini
    }
}