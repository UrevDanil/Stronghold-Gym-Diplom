<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'schedule_id', 'user_subscription_id', 
        'status', 'paid_separately', 'cancelled_at'
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
    ];

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
    
    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }
}