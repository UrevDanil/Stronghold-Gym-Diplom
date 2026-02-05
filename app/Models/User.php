<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'role_id', 
        'birth_date', 'notes', 'avatar', 'qualification', 'specialization'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
    ];

    // Связи
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }
    
    public function activeSubscription()
    {
        return $this->hasOne(UserSubscription::class)
            ->where('status', 'active')
            ->where(function($query) {
                $query->where('end_date', '>=', now())
                      ->orWhereNull('end_date');
            })
            ->latest();
    }

    public function subscriptions() 
    {
    return $this->belongsToMany(Subscription::class, 'user_subscriptions')
                ->withPivot('purchase_date', 'expiry_date', 'remaining_sessions');
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    public function upcomingBookings()
    {
        return $this->hasMany(Booking::class)
            ->whereHas('schedule', function($query) {
                $query->where('date', '>=', now()->toDateString());
            })
            ->with('schedule.workout')
            ->orderBy('created_at', 'desc');
    }
    
    public function attendances()
    {
        return $this->hasManyThrough(Attendance::class, Booking::class);
    }
    
    // Для тренера
    public function scheduledTrainings()
    {
        return $this->hasMany(Schedule::class, 'trainer_id');
    }
    
    // Scope'ы для фильтрации
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
    
    public function scopeAdmins($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('name', 'admin');
        });
    }
    
    // Проверка ролей
    public function isAdmin()
    {
        return $this->role->name === 'admin';
    }
    
    public function isClient()
    {
        return $this->role->name === 'client';
    }
    
    public function isTrainer()
    {
        return $this->role->name === 'trainer';
    }
    
    public function isOwner()
    {
        return $this->role->name === 'owner';
    }
}