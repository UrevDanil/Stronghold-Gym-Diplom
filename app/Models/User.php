<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'birth_date',
        'role_id',
        'avatar',
        'address',
        'health_info',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
    ];

    // =========== ОТНОШЕНИЯ ===========

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function subscriptions()
    {
    return $this->belongsToMany(Subscription::class, 'user_subscriptions')
                ->using(UserSubscription::class)
                ->withPivot([
                    'start_date',
                    'end_date', 
                    'remaining_workouts',
                    'status',
                    'activated_by',
                    'activated_at'
                ]);
    }

    // Тренер проводит занятия
    public function trainings()
    {
        return $this->hasMany(Schedule::class, 'trainer_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // =========== ПРОВЕРКИ РОЛЕЙ ===========

    public function isAdmin()
    {
        return $this->role->name === 'admin' || $this->role->name === 'owner';
    }

    public function isOwner()
    {
        return $this->role->name === 'owner';
    }

    public function isClient()
    {
        return $this->role->name === 'client';
    }

    public function isTrainer()
    {
        return $this->role->name === 'trainer';
    }

    // =========== SCOPE ===========

    public function scopeClients($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('name', 'client');
        });
    }

    public function scopeTrainers($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('name', 'trainer');
        });
    }

    // =========== HELPERS ===========

    public function hasActiveSubscription()
    {
    return $this->subscriptions()
        ->wherePivot('end_date', '>=', now()->toDateString())
        ->wherePivot('remaining_workouts', '>', 0)
        ->wherePivot('status', 'active')
        ->exists();
    }

    public function activeSubscription()
    {
    return $this->subscriptions()
        ->wherePivot('end_date', '>=', now()->toDateString())
        ->wherePivot('remaining_workouts', '>', 0)
        ->wherePivot('status', 'active')
        ->first();
    }

    public function upcomingBookings()
    {
        return $this->bookings()
            ->whereHas('schedule', function($q) {
                $q->where('date', '>=', now()->toDateString())
                  ->orWhere(function($query) {
                      $query->where('date', now()->toDateString())
                            ->where('start_time', '>', now()->format('H:i:s'));
                  });
            })
            ->with('schedule.workout', 'schedule.trainer')
            ->orderBy('created_at', 'desc');
    }

    public function useWorkoutSession()
    {  
    $activeSubscription = $this->activeSubscription();
    if ($activeSubscription) {
        $activeSubscription->pivot->decrement('remaining_workouts');
        return true;
    }
    return false;
    }
}