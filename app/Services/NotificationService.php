<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function send($userId, $message, $type, $data = null)
    {
        try {
            return Notification::create([
                'user_id' => $userId,
                'message' => $message,
                'type' => $type,
                'data' => $data,
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::error('Ошибка отправки уведомления: ' . $e->getMessage());
            return null;
        }
    }

    public function sendBulk($userIds, $message, $type, $data = null)
    {
        $notifications = [];
        foreach ($userIds as $userId) {
            $notifications[] = [
                'user_id' => $userId,
                'message' => $message,
                'type' => $type,
                'data' => json_encode($data),
                'is_read' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        
        return Notification::insert($notifications);
    }

    public function notifyAdmins($message, $type, $data = null)
    {
        $adminIds = User::whereHas('role', function($q) {
            $q->whereIn('name', ['admin', 'owner']);
        })->pluck('id')->toArray();
        
        return $this->sendBulk($adminIds, $message, $type, $data);
    }

    public function notifyTrainer($trainerId, $message, $type, $data = null)
    {
        return $this->send($trainerId, $message, $type, $data);
    }

    public function notifyClient($clientId, $message, $type, $data = null)
    {
        return $this->send($clientId, $message, $type, $data);
    }
}