<?php

namespace App\Providers;

use App\Events\AttendanceMarked;
use App\Events\ClientBookedWorkout;
use App\Events\NewClientRegistered;
use App\Events\ScheduleCreated;
use App\Events\ScheduleDeleted;
use App\Events\ScheduleUpdated;
use App\Events\SubscriptionExpiring;
use App\Events\SubscriptionPurchased;
use App\Listeners\SendAttendanceMarkedNotification;
use App\Listeners\SendBookingNotification;
use App\Listeners\SendNewClientRegisteredNotification;
use App\Listeners\SendScheduleCreatedNotification;
use App\Listeners\SendScheduleDeletedNotification;
use App\Listeners\SendScheduleUpdatedNotification;
use App\Listeners\SendSubscriptionExpiringNotification;
use App\Listeners\SendSubscriptionPurchasedNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ClientBookedWorkout::class => [
            SendBookingNotification::class,
        ],
        ScheduleCreated::class => [
            SendScheduleCreatedNotification::class,
        ],
        ScheduleDeleted::class => [
            SendScheduleDeletedNotification::class,
        ],
        AttendanceMarked::class => [
            SendAttendanceMarkedNotification::class,
        ],
        NewClientRegistered::class => [
            SendNewClientRegisteredNotification::class,
        ],
        ScheduleUpdated::class => [
            SendScheduleUpdatedNotification::class,
        ],
        SubscriptionExpiring::class => [
            SendSubscriptionExpiringNotification::class,
        ],
        SubscriptionPurchased::class => [
            SendSubscriptionPurchasedNotification::class,
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}