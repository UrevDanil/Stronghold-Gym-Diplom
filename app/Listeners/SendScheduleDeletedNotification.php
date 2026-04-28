<?php

namespace App\Listeners;

use App\Events\ScheduleDeleted;
use App\Services\NotificationService;

class SendScheduleDeletedNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(ScheduleDeleted $event)
    {
        $schedule = $event->schedule;
        $deletedBy = $event->deletedBy;
        
        // 1. Уведомление тренеру (если удалил админ)
        if ($deletedBy->isAdmin() && $schedule->trainer_id != $deletedBy->id) {
            $this->notificationService->send(
                $schedule->trainer_id,
                "Администратор {$deletedBy->name} удалил вашу тренировку '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
                'schedule',
                ['link' => route('trainer.schedule')]
            );
        }
        
        // 2. Уведомление админам (если удалил тренер)
        if ($deletedBy->isTrainer()) {
            $this->notificationService->notifyAdmins(
                "Тренер {$deletedBy->name} удалил тренировку '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
                'schedule',
                ['trainer_id' => $deletedBy->id]
            );
        }
        
        // 3. Уведомление клиентам, которые были записаны
        foreach ($schedule->bookings as $booking) {
            if ($booking->status === 'booked') {
                $this->notificationService->send(
                    $booking->user_id,
                    "❌ Тренировка '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time} отменена. Посещение возвращено в ваш абонемент.",
                    'warning',  // ← ИЗМЕНИ С schedule НА warning
                    ['link' => route('client.schedule')]
                );
            }
        }
    }
}