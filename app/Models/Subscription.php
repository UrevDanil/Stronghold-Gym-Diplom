<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'workouts_count',
        'is_active',
        'type'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'workouts_count' => 'integer',
        'is_active' => 'boolean'
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

    // =========== ДОБАВЛЕННЫЕ МЕТОДЫ ===========

    // Проверка типа абонемента
    public function isTimeBased()
    {
        return $this->type === 'time';
    }

    public function isCountBased()
    {
        return $this->type === 'count';
    }

    // Активация абонемента для пользователя
    public function activateForUser($user, $activatedBy = null)
    {
        return $this->users()->attach($user->id, [
            'start_date' => now(),
            'end_date' => now()->addDays($this->duration_days),
            'remaining_workouts' => $this->workouts_count,
            'status' => 'active',
            'activated_by' => $activatedBy,
            'activated_at' => now(),
        ]);
    }

    // Форматированная цена
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 0, ',', ' ') . ' ₽';
    }

    // Активные абонементы (scope)
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Сортировка по цене (scope)
    public function scopeCheapest($query)
    {
        return $query->orderBy('price', 'asc');
    }

    public function scopeMostExpensive($query)
    {
        return $query->orderBy('price', 'desc');
    }

    // Проверка, есть ли свободные тренировки
    public function hasAvailableWorkouts($user)
    {
        $userSubscription = $user->subscriptions()
            ->where('subscriptions.id', $this->id)
            ->wherePivot('status', 'active')
            ->wherePivot('remaining_workouts', '>', 0)
            ->wherePivot('end_date', '>=', now()->toDateString())
            ->first();

        return $userSubscription !== null;
    }

    // Получить остаток тренировок для пользователя
    public function getRemainingWorkoutsForUser($user)
    {
        $userSubscription = $user->subscriptions()
            ->where('subscriptions.id', $this->id)
            ->wherePivot('status', 'active')
            ->first();

        return $userSubscription ? $userSubscription->pivot->remaining_workouts : 0;
    }
}