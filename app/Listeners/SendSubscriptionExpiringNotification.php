<?php

namespace App\Listeners;

use App\Events\SubscriptionExpiring;
use App\Services\NotificationService;

class SendSubscriptionExpiringNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(SubscriptionExpiring $event)
    {
        $subscription = $event->userSubscription;
        $daysLeft = now()->diffInDays($subscription->end_date);
        
        $this->notificationService->send(
            $subscription->user_id,
            "Ваш абонемент '{$subscription->subscription->name}' истекает через {$daysLeft} дней! Продлите его, чтобы продолжить тренировки.",
            'subscription',
            ['subscription_id' => $subscription->id]
        );
        
        // Уведомление админам
        $this->notificationService->notifyAdmins(
            "У клиента {$subscription->user->name} истекает абонемент '{$subscription->subscription->name}' через {$daysLeft} дней",
            'subscription',
            ['user_id' => $subscription->user_id, 'subscription_id' => $subscription->id]
        );
    }
}