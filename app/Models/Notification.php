<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Notification extends Model
{
    protected $table = 'notifications';
    
    protected $fillable = [
        'user_id',
        'type',
        'message',
        'is_read',
        'data'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',  // Laravel автоматически преобразует JSON в массив
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Типы уведомлений
    const TYPE_BOOKING = 'booking';
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_SYSTEM = 'system';
    const TYPE_REMINDER = 'reminder';
    const TYPE_ATTENDANCE = 'attendance';
    const TYPE_SCHEDULE = 'schedule';
    const TYPE_WARNING = 'warning';

    // Связи
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Скоупы
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->where('is_read', true);
    }

    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeBooking(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_BOOKING);
    }

    public function scopeSubscription(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_SUBSCRIPTION);
    }

    public function scopeAttendance(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_ATTENDANCE);
    }

    public function scopeSchedule(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_SCHEDULE);
    }

    public function scopeWarning(Builder $query): Builder
    {
        return $query->where('type', self::TYPE_WARNING);
    }

    // Методы работы с уведомлением
    public function markAsRead(): bool
    {
        return $this->update(['is_read' => true]);
    }

    public function markAsUnread(): bool
    {
        return $this->update(['is_read' => false]);
    }

    // Геттеры для UI
    public function getIconAttribute(): string
    {
        return match($this->type) {
            self::TYPE_BOOKING => 'calendar-check',
            self::TYPE_SUBSCRIPTION => 'id-card',
            self::TYPE_REMINDER => 'clock',
            self::TYPE_ATTENDANCE => 'user-check',
            self::TYPE_SCHEDULE => 'calendar-alt',
            self::TYPE_WARNING => 'exclamation-triangle',
            default => 'bell'
        };
    }

    public function getColorAttribute(): string
    {
        return match($this->type) {
            self::TYPE_BOOKING => 'primary',
            self::TYPE_SUBSCRIPTION => 'success',
            self::TYPE_REMINDER => 'warning',
            self::TYPE_ATTENDANCE => 'info',
            self::TYPE_SCHEDULE => 'secondary',
            self::TYPE_WARNING => 'danger',
            default => 'light'
        };
    }

    public function getStatusTextAttribute(): string
    {
        return $this->is_read ? 'Прочитано' : 'Новое';
    }

    public function getTimeAgoAttribute(): string
    {
        return $this->created_at ? $this->created_at->diffForHumans() : '';
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->created_at ? $this->created_at->format('d.m.Y H:i') : '';
    }

    public function getIsFreshAttribute(): bool
    {
        return !$this->is_read && $this->created_at && $this->created_at->diffInHours(now()) < 1;
    }

    // Получение данных из поля data
    public function getLinkAttribute(): ?string
    {
        return $this->data['link'] ?? null;
    }

    public function getBookingIdAttribute(): ?int
    {
        return $this->data['booking_id'] ?? null;
    }

    public function getScheduleIdAttribute(): ?int
    {
        return $this->data['schedule_id'] ?? null;
    }

    public function getClientIdAttribute(): ?int
    {
        return $this->data['client_id'] ?? null;
    }

    public function getTrainerIdAttribute(): ?int
    {
        return $this->data['trainer_id'] ?? null;
    }

    // Статические методы для создания уведомлений
    public static function createBookingNotification(int $userId, string $message, array $data = []): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_BOOKING,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    public static function createSubscriptionNotification(int $userId, string $message, array $data = []): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_SUBSCRIPTION,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    public static function createAttendanceNotification(int $userId, string $message, array $data = []): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_ATTENDANCE,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    public static function createScheduleNotification(int $userId, string $message, array $data = []): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_SCHEDULE,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    public static function createWarningNotification(int $userId, string $message, array $data = []): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_WARNING,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    public static function createSystemNotification(int $userId, string $message, array $data = []): self
    {
        return self::create([
            'user_id' => $userId,
            'type' => self::TYPE_SYSTEM,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    // Методы для получения уведомлений пользователя
    public static function getUnreadForUser(int $userId): \Illuminate\Database\Eloquent\Collection
    {
        return self::where('user_id', $userId)
            ->where('is_read', false)
            ->latest()
            ->get();
    }

    public static function getUnreadCountForUser(int $userId): int
    {
        return self::where('user_id', $userId)
            ->where('is_read', false)
            ->count();
    }

    public static function markAllAsReadForUser(int $userId): bool
    {
        return self::where('user_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]) > 0;
    }

    public static function deleteOldNotifications(int $days = 30): int
    {
        return self::where('created_at', '<', now()->subDays($days))
            ->delete();
    }
}