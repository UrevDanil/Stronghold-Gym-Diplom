<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';
    
    protected $fillable = [
        'booking_id',
        'marked_by',
        'attended_at',
        'comment',
        'attendance_type'
    ];

    protected $casts = [
        'attended_at' => 'datetime'
    ];

    const TYPE_ATTENDED = 'attended';
    const TYPE_LATE = 'late';
    const TYPE_LEFT_EARLY = 'left_early';

    // Связь с бронированием (ДОБАВЛЯЕМ)
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Связь с пользователем, который отметил посещение (тренер/админ)
    public function marker()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }

    // Связь с расписанием через бронирование
    public function schedule()
    {
        return $this->hasOneThrough(
            Schedule::class,
            Booking::class,
            'id', // Foreign key on bookings table
            'id', // Foreign key on schedules table
            'booking_id', // Local key on attendances table
            'schedule_id' // Local key on bookings table
        );
    }

    // Скоупы
    public function scopeForTrainer($query, $trainerId)
    {
        return $query->whereHas('booking.schedule', function($q) use ($trainerId) {
            $q->where('trainer_id', $trainerId);
        });
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('attended_at', $date);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('attendance_type', $type);
    }
}