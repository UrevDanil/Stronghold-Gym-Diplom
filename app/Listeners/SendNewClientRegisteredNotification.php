<?php

namespace App\Listeners;

use App\Events\NewClientRegistered;
use App\Services\NotificationService;

class SendNewClientRegisteredNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(NewClientRegistered $event)
    {
        // Уведомление админам о новом клиенте
        $this->notificationService->notifyAdmins(
            "Новый клиент зарегистрировался: {$event->user->name} ({$event->user->email})",
            'system',
            ['user_id' => $event->user->id]
        );
        
        // Приветственное уведомление клиенту
        $this->notificationService->send(
            $event->user->id,
            "Добро пожаловать в Stronghold Gym! Рады видеть вас среди наших клиентов.",
            'system',
            ['user_id' => $event->user->id]
        );
    }
}