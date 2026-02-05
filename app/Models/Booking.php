<?php

// app/Models/Booking.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'schedule_id',
        'status',
        'notes',
        'cancelled_at',
        'cancellation_reason'
    ];

    protected $casts = [
        'cancelled_at' => 'datetime'
    ];

    const STATUS_BOOKED = 'booked';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ABSENT = 'absent';

    protected $attributes = [
        'status' => self::STATUS_BOOKED
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    // Проверка, можно ли отменить бронь
    public function canCancel()
    {
        return $this->status === self::STATUS_BOOKED && 
               !$this->schedule->isPast();
    }

    // Отмена бронирования
    public function cancel($reason = null)
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason
        ]);

        // Уменьшаем счетчик бронирований
        $this->schedule->decrement('booked_count');
    }
}