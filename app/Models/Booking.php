<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';
    
    protected $fillable = [
        'user_id',
        'schedule_id',
        'status',
        'user_subscription_id',
        'paid_separately',
        'cancelled_at'
    ];

    protected $casts = [
        'paid_separately' => 'boolean',
        'cancelled_at' => 'datetime'
    ];

    // Статусы бронирования
    const STATUS_BOOKED = 'booked';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_ATTENDED = 'attended';
    const STATUS_MISSED = 'missed';

    // Связи
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function userSubscription()
    {
        return $this->belongsTo(UserSubscription::class);
    }

    // Проверка, можно ли отменить бронирование
    public function canCancel()
    {
        // Нельзя отменить, если уже отменено или прошло
        if ($this->status === self::STATUS_CANCELLED) {
            return false;
        }
        
        if ($this->schedule->isPast()) {
            return false;
        }
        
        return true;
    }

public function cancel($reason = null)
{
    $this->update([
        'status' => self::STATUS_CANCELLED,
        'cancelled_at' => now(),
    ]);

    // Уменьшаем счетчик участников в расписании
    if ($this->schedule) {
        $this->schedule->decrement('current_participants');
    }

    return $this;
}

    // Отметить как посещенное
    public function markAttended()
    {
        $this->update([
            'status' => self::STATUS_ATTENDED
        ]);
    }

    // Отметить как пропущенное
    public function markMissed()
    {
        $this->update([
            'status' => self::STATUS_MISSED
        ]);
    }
}