<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
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
            'schedule_id', // Foreign key on bookings table
            'id', // Foreign key on users table
            'id', // Local key on schedules table
            'user_id' // Local key on bookings table
        );
    }
}