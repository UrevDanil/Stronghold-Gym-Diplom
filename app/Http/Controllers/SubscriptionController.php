<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'duration_days',
        'session_count', // храним общее количество тренировок в абонементе
        'is_active',
        'features'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_days' => 'integer',
        'session_count' => 'integer',
        'is_active' => 'boolean',
        'features' => 'array'
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
            'remaining_workouts' => $this->session_count,
            'status' => 'active',
            'activated_by' => $activatedBy,
            'activated_at' => now(),
        ]);
    }
}