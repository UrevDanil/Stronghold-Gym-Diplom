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
        // Нельзя отменить, если уже отменено
        if ($this->status === self::STATUS_CANCELLED) {
            return false;
        }
        
        // Нельзя отменить, если уже посещено
        if ($this->status === self::STATUS_ATTENDED) {
            return false;
        }
        
        // Нельзя отменить, если уже пропущено
        if ($this->status === self::STATUS_MISSED) {
            return false;
        }
        
        // Нельзя отменить, если занятие прошло
        if ($this->schedule->isPast()) {
            return false;
        }
        
        return true;
    }

    public function cancel($reason = null)
    {
        // Проверяем, не было ли посещения
        $attendanceExists = Attendance::where('booking_id', $this->id)->exists();
        if ($attendanceExists) {
            throw new \Exception('Нельзя отменить тренировку, которая уже была посещена');
        }
        
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