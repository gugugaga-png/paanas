<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use App\Models\User; // Sudah tidak perlu eksplisit jika pakai namespace
// use App\Models\Transaction; // Sudah tidak perlu eksplisit jika pakai namespace

class SavingSegment extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'description',
    'target_amount',
    'banner',
    'user_id',
    'unique_code', // <-- Tambahkan baris ini!
];

    /**
     * Relasi ke pengguna (guru) yang membuat segmen ini.
     */
    public function teacher() // Use 'teacher' as the relationship name
    {
        return $this->belongsTo(User::class, 'user_id'); // 'user_id' is the foreign key in 'saving_segments' table
    }

    /**
     * Relasi ke semua transaksi yang terjadi dalam segmen ini.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'saving_segment_id'); // Pastikan FK-nya benar
    }

    /**
     * Relasi ke semua siswa yang telah bergabung dengan segmen ini
     * melalui tabel pivot 'user_saving_segment'.
     */
    public function joinedUsers()
    {
        return $this->belongsToMany(User::class, 'user_saving_segment', 'saving_segment_id', 'user_id')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    /**
     * Relasi ke entri saldo spesifik setiap siswa di segmen ini
     * dari tabel 'student_segment_balances'.
     */
    public function studentBalances()
    {
        return $this->hasMany(StudentSegmentBalance::class, 'saving_segment_id');
    }

    // Anda bisa menambahkan accessor jika ingin mendapatkan total saldo seluruh siswa di segmen ini
    public function getTotalSegmentBalanceAttribute()
    {
        return $this->studentBalances->sum('balance');
    }
}