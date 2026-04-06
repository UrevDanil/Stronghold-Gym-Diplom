<?php

namespace App\Listeners;

use App\Events\ClientBookedWorkout;
use App\Services\NotificationService;

class SendBookingNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(ClientBookedWorkout $event)
    {
        $booking = $event->booking;
        $schedule = $booking->schedule;
        $client = $booking->user;
        $trainer = $schedule->trainer;
        
        // 1. Уведомление тренеру
        $this->notificationService->send(
            $trainer->id,
            "Новый клиент! {$client->name} записался на тренировку '{$schedule->workout->name}' {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
            'booking',
            [
                'booking_id' => $booking->id,
                'schedule_id' => $schedule->id,
                'client_id' => $client->id,
                'link' => route('trainer.schedule')
            ]
        );
        
        // 2. Уведомление клиенту (подтверждение)
        $this->notificationService->send(
            $client->id,
            "Вы успешно записались на тренировку '{$schedule->workout->name}' {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
            'booking',
            [
                'schedule_id' => $schedule->id,
                'link' => route('client.schedule')
            ]
        );
        
        // 3. Уведомление админам (если тренировка заполнена)
        if ($schedule->current_participants >= $schedule->capacity) {
            $this->notificationService->notifyAdmins(
                "Тренировка '{$schedule->workout->name}' полностью заполнена! Записано {$schedule->current_participants}/{$schedule->capacity}",
                'booking',
                ['schedule_id' => $schedule->id]
            );
        }
    }
}