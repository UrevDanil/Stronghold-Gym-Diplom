<?php

namespace App\Listeners;

use App\Events\AttendanceMarked;
use App\Services\NotificationService;

class SendAttendanceMarkedNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(AttendanceMarked $event)
    {
        $booking = $event->booking;
        $schedule = $booking->schedule;
        
        $message = $event->status === 'attended'
            ? "Вы посетили тренировку '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')}"
            : "Вы пропустили тренировку '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')}";
        
        $this->notificationService->send(
            $booking->user_id,
            $message,
            'attendance',
            ['schedule_id' => $schedule->id]
        );
    }
}