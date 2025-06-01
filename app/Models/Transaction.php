<?php

namespace App\Models; // Pastikan namespace ini benar!

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model // Pastikan nama kelas ini benar!
{
    use HasFactory;

    // Tambahkan ini jika Anda menggunakan mass assignment
    protected $fillable = [
        'user_id',
        'saving_segment_id',
        'amount',
        'type',
        'description',
        'status', // Pastikan 'status' ada jika Anda mengaturnya
        'processed_by_user_id',
        'teacher_id', // Pastikan ini juga ada jika Anda ingin bisa di-mass assign
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    // Relasi ke User (siswa)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke SavingSegment
    public function savingSegment()
    {
        return $this->belongsTo(SavingSegment::class);
    }

    // Relasi ke User (guru/admin) yang memproses
    public function processedByUser()
    {
        return $this->belongsTo(User::class, 'processed_by_user_id');
    }

    // Relasi ke User (guru) yang menyetujui/menolak
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}