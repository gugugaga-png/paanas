<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\SavingSegment; // Add this

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

    // Define the 'role' relationship (keep this, it's used for Auth::user()->role->name)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Define the 'transactions' relationship
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Define the 'joinedSavingSegments' relationship
   public function joinedSavingSegments()
    {
        return $this->belongsToMany(SavingSegment::class, 'user_saving_segment', 'user_id', 'saving_segment_id')
                    ->withPivot('joined_at')
                    ->withTimestamps(); // Important for created_at/updated_at on pivot
    }
    // Add this relationship for segments created by the teacher
    public function savingSegments()
    {
        return $this->hasMany(SavingSegment::class, 'user_id');
    }

   
}