<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserSubscription extends Pivot
{
    protected $table = 'user_subscriptions';
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'remaining_workouts' => 'integer',
        'activated_at' => 'datetime',
    ];

    // Метод для проверки активности абонемента
    public function isActive()
    {
        return $this->status === 'active' &&
               $this->end_date >= now()->toDateString() &&
               $this->remaining_workouts > 0;
    }

    // Метод для использования тренировки
    public function useWorkout()
    {
        if ($this->remaining_workouts > 0) {
            $this->decrement('remaining_workouts');
            return true;
        }
        return false;
    }

    // Метод для проверки истекшего абонемента
    public function checkExpiration()
    {
        if ($this->end_date < now()->toDateString() && $this->status === 'active') {
            $this->update(['status' => 'expired']);
            return true;
        }
        return false;
    }
}