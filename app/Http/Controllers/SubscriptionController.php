<?php

namespace App\Models; // Изменил namespace на Models

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'workouts_count', // ИЗМЕНИЛ: session_count → workouts_count
        'is_active',
        'type', // Добавил поле type из вашей БД
        // 'features' убрал, так как нет в вашей БД
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'workouts_count' => 'integer', // ИЗМЕНИЛ: session_count → workouts_count
        'is_active' => 'boolean',
        // 'features' => 'array' убрал
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_subscriptions')
                    ->withPivot([
                        'start_date',
                        'end_date',
                        'remaining_workouts',
                        'status',
                        'activated_by',
                        'activated_at'
                    ])
                    ->withTimestamps();
    }

    // Метод для активации абонемента пользователю
    public function activateForUser(User $user, $activatedBy = null)
    {
        return $this->users()->attach($user->id, [
            'start_date' => now(),
            'end_date' => now()->addDays($this->duration_days),
            'remaining_workouts' => $this->workouts_count, // ИЗМЕНИЛ
            'status' => 'active',
            'activated_by' => $activatedBy,
            'activated_at' => now(),
        ]);
    }
    
    // Дополнительные методы для работы с типом абонемента
    public function isTimeBased()
    {
        return $this->type === 'time';
    }
    
    public function isCountBased()
    {
        return $this->type === 'count';
    }
    
    // Метод для получения цены с форматированием
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', ' ') . ' ₽';
    }
    
    // Метод для получения продолжительности с правильным склонением
    public function getFormattedDurationAttribute()
    {
        $days = $this->duration_days;
        
        if ($days == 30) {
            return '1 месяц';
        } elseif ($days == 60) {
            return '2 месяца';
        } elseif ($days == 90) {
            return '3 месяца';
        } elseif ($days == 180) {
            return '6 месяцев';
        } elseif ($days == 365) {
            return '1 год';
        } else {
            return $days . ' ' . $this->getDaysWord($days);
        }
    }
    
    private function getDaysWord($number)
    {
        $lastDigit = $number % 10;
        $lastTwoDigits = $number % 100;
        
        if ($lastTwoDigits >= 11 && $lastTwoDigits <= 19) {
            return 'дней';
        }
        
        switch ($lastDigit) {
            case 1: return 'день';
            case 2:
            case 3:
            case 4: return 'дня';
            default: return 'дней';
        }
    }
}