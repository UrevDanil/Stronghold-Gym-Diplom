<?php

namespace App\Listeners;

use App\Events\SubscriptionPurchased;
use App\Services\NotificationService;

class SendSubscriptionPurchasedNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(SubscriptionPurchased $event)
    {
        $userSubscription = $event->userSubscription;
        $subscription = $userSubscription->subscription;
        $user = $userSubscription->user;
        
        // Уведомление клиенту
        $this->notificationService->send(
            $user->id,
            "🎉 Вы приобрели абонемент '{$subscription->name}'! Действителен до {$userSubscription->end_date->format('d.m.Y')}",
            'subscription',
            [
                'subscription_id' => $subscription->id,
                'end_date' => $userSubscription->end_date->format('d.m.Y'),
                'remaining_workouts' => $userSubscription->remaining_workouts
            ]
        );
        
        // Уведомление админам
        $this->notificationService->notifyAdmins(
            "💰 Клиент {$user->name} приобрел абонемент '{$subscription->name}' на сумму {$subscription->price} руб.",
            'subscription',
            ['user_id' => $user->id, 'subscription_id' => $subscription->id]
        );
    }
}