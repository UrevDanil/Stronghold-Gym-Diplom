<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'workout_id', 'trainer_id', 'date', 'start_time', 'end_time',
        'status', 'room', 'current_participants'
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    // Связи
    public function workout()
    {
        return $this->belongsTo(Workout::class);
    }
    
    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    public function attendees()
    {
        return $this->hasManyThrough(
            User::class,
            Booking::class,
            'schedule_id',
            'id',
            'id',
            'user_id'
        );
    }
}