<?php

// app/Models/Schedule.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Schedule extends Model
{
    protected $table = 'schedules';
    
    protected $fillable = [
        'workout_id',
        'trainer_id',
        'date',
        'start_time',
        'end_time',
        'status',
        'room',
        'current_participants',
        'notes'
    ];

    protected $casts = [
        'date' => 'date',
        'current_participants' => 'integer'
    ];

    // Статусы занятия
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_CANCELLED = 'cancelled';
    const STATUS_COMPLETED = 'completed';

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

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Вместимость (можно добавить в модель Workout)
    public function capacity()
    {
        return $this->workout->capacity ?? 10; // Значение по умолчанию
    }

    // Проверка доступности мест
    public function hasAvailableSlots()
    {
        return $this->current_participants < $this->capacity();
    }

    // Количество свободных мест
    public function availableSlots()
    {
        return $this->capacity() - $this->current_participants;
    }

    // Проверка, прошло ли занятие
    public function isPast()
    {
        $now = Carbon::now();
        $scheduleDateTime = Carbon::parse($this->date->format('Y-m-d') . ' ' . $this->end_time);
        return $now->greaterThan($scheduleDateTime);
    }

    // Проверка, можно ли бронировать
    public function canBook()
    {
        return !$this->isPast() && 
               $this->status === self::STATUS_SCHEDULED && 
               $this->hasAvailableSlots();
    }

    // Проверка, активно ли занятие
    public function isActive()
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    // Получить статус для отображения
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            self::STATUS_SCHEDULED => '<span class="badge bg-success">Запланировано</span>',
            self::STATUS_CANCELLED => '<span class="badge bg-danger">Отменено</span>',
            self::STATUS_COMPLETED => '<span class="badge bg-secondary">Завершено</span>',
            default => '<span class="badge bg-secondary">Неизвестно</span>'
        };
    }

    // Скоупы для фильтрации
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', Carbon::today())
                     ->where('status', self::STATUS_SCHEDULED)
                     ->orderBy('date')
                     ->orderBy('start_time');
    }

    public function scopeForTrainer($query, $trainerId)
    {
        return $query->where('trainer_id', $trainerId);
    }

    public function scopeForWorkout($query, $workoutId)
    {
        return $query->where('workout_id', $workoutId);
    }

    public function scopeOnDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }
}