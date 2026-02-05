<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public function users()
{
    return $this->belongsToMany(User::class, 'user_subscriptions')
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
}
