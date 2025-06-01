<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// Removed redundant use statements, as they are in the same namespace
// use App\Models\SavingSegment;
// use App\Models\Transaction;
// use App\Models\StudentSegmentBalance; // You'll need this import if StudentSegmentBalance is in a different namespace, otherwise it's fine

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the role associated with the user.
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get the transactions for the user.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id'); // Explicitly define foreign key for clarity
    }

    /**
     * The saving segments that the user has joined (as a student).
     */
    public function joinedSavingSegments()
    {
        return $this->belongsToMany(SavingSegment::class, 'user_saving_segment', 'user_id', 'saving_segment_id')
                    ->withPivot('joined_at')
                    ->withTimestamps();
    }

    /**
     * The saving segments created by this user (as a teacher).
     */
    public function savingSegments() // Consider renaming to createdSavingSegments() for clarity
    {
        return $this->hasMany(SavingSegment::class, 'user_id');
    }
    // app/Models/User.php
public function segments()
{
    return $this->belongsToMany(\App\Models\SavingSegment::class, 'student_segments');
}


    /**
     * Get all the individual segment balances for the user.
     * This is the relationship that provides the "balance per segment".
     */
    public function segmentBalances()
    {
        return $this->hasMany(StudentSegmentBalance::class, 'user_id');
    }

    /**
     * Accessor to get the total balance across all segments for the user.
     */
    public function getTotalBalanceAttribute()
    {
        // This will now correctly sum up balances from the 'student_segment_balances' table
        return $this->segmentBalances->sum('balance');
    }
}