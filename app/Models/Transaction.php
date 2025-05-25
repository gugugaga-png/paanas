<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'saving_segment_id',
        'amount',
        'type', // 'deposit' or 'withdrawal'
        'status', // 'pending', 'approved', 'rejected'
        'proof_image_path',
        'teacher_id', // If teachers are involved in approving/rejecting
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function savingSegment()
    {
        return $this->belongsTo(SavingSegment::class);
    }

    // If you store which teacher approved/rejected a transaction
    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}