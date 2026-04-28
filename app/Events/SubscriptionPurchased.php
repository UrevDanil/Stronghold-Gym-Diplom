<?php

namespace App\Events;

use App\Models\UserSubscription;
use Illuminate\Foundation\Events\Dispatchable;

class SubscriptionPurchased
{
    use Dispatchable;

    public $userSubscription;

    public function __construct(UserSubscription $userSubscription)
    {
        $this->userSubscription = $userSubscription;
    }
}