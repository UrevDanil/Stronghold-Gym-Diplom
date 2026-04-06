<?php

namespace App\Listeners;

use App\Events\ScheduleUpdated;
use App\Services\NotificationService;

class SendScheduleUpdatedNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(ScheduleUpdated $event)
    {
        $schedule = $event->schedule;
        $updatedBy = $event->updatedBy;
        
        // Если изменил админ - уведомляем тренера
        if ($updatedBy->isAdmin()) {
            $this->notificationService->send(
                $schedule->trainer_id,
                "Администратор изменил вашу тренировку '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
                'schedule',
                ['schedule_id' => $schedule->id]
            );
        }
        
        // Если изменил тренер - уведомляем админов
        if ($updatedBy->isTrainer()) {
            $this->notificationService->notifyAdmins(
                "Тренер {$updatedBy->name} изменил тренировку '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
                'schedule',
                ['schedule_id' => $schedule->id, 'trainer_id' => $updatedBy->id]
            );
        }
    }
}