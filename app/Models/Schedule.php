<?php

// app/Models/Schedule.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'workout_id',
        'trainer_id',
        'date',
        'start_time',
        'end_time',
        'capacity',
        'booked_count',
        'is_active',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'booked_count' => 'integer'
    ];

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

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Проверка доступности мест
    public function hasAvailableSlots()
    {
        return $this->booked_count < $this->capacity;
    }

    // Количество свободных мест
    public function availableSlots()
    {
        return $this->capacity - $this->booked_count;
    }

    // Проверка, прошло ли занятие
    public function isPast()
    {
        return $this->date < now()->toDateString() || 
               ($this->date == now()->toDateString() && $this->end_time < now()->format('H:i:s'));
    }

    // Проверка, можно ли бронировать
    public function canBook()
    {
        return !$this->isPast() && 
               $this->is_active && 
               $this->hasAvailableSlots();
    }
}