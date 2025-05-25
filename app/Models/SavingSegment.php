<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User; // Add this
use App\Models\Transaction; // Add this

class SavingSegment extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'unique_code',
        'target_amount',
        'user_id', // This is the teacher's ID
    ];

    // Inverse relationship to the User who created it (the teacher)
    public function user() // Renamed from 'teacher' to 'user' for consistency with foreign key 'user_id'
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationship to transactions belonging to this segment
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    // Relationship to users who have joined this segment (many-to-many)
    public function joinedUsers()
    {
        return $this->belongsToMany(User::class, 'user_saving_segment', 'saving_segment_id', 'user_id')
                    ->withPivot('joined_at')
                    ->withTimestamps(); // Important for created_at/updated_at on pivot
    }
}