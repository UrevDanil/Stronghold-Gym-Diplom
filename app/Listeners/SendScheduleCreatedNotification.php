<?php

namespace App\Listeners;

use App\Events\ScheduleCreated;
use App\Services\NotificationService;

class SendScheduleCreatedNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(ScheduleCreated $event)
    {
        $schedule = $event->schedule;
        
        // 1. Уведомление админам о новой тренировке
        $this->notificationService->notifyAdmins(
            "Тренер {$schedule->trainer->name} создал новую тренировку '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
            'schedule',
            [
                'schedule_id' => $schedule->id,
                'trainer_id' => $schedule->trainer_id,
                'link' => route('admin.schedule.index')
            ]
        );
        
        // 2. Уведомление тренеру (подтверждение)
        $this->notificationService->send(
            $schedule->trainer_id,
            "Вы успешно создали тренировку '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
            'schedule',
            ['link' => route('trainer.schedule')]
        );
    }
}