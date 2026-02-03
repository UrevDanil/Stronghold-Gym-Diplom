<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
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
            ->where('end_date', '>=', now());
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
    
    public function scheduledTrainings() // Для тренера
    {
        return $this->hasMany(Schedule::class, 'trainer_id');
    }
    
    // Scope для фильтрации по ролям
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
}
