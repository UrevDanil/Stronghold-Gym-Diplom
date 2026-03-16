<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'data' => 'array'
    ];

    // Типы уведомлений
    const TYPE_BOOKING = 'booking';
    const TYPE_SUBSCRIPTION = 'subscription';
    const TYPE_SYSTEM = 'system';
    const TYPE_REMINDER = 'reminder';

    // Связи
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Скоупы
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Отметить как прочитанное
    public function markAsRead()
    {
        $this->update([
            'is_read' => true
        ]);
    }

    // Отметить как непрочитанное
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false
        ]);
    }

    // Добавь в конец класса, перед последней закрывающей скобкой

/**
 * Получить иконку для типа уведомления
 */
public function getIconAttribute()
{
    return match($this->type) {
        self::TYPE_BOOKING => 'calendar-check',
        self::TYPE_SUBSCRIPTION => 'id-card',
        self::TYPE_REMINDER => 'clock',
        default => 'bell'
    };
}

/**
 * Получить цвет для типа уведомления
 */
public function getColorAttribute()
{
    return match($this->type) {
        self::TYPE_BOOKING => 'primary',
        self::TYPE_SUBSCRIPTION => 'success',
        self::TYPE_REMINDER => 'warning',
        default => 'info'
    };
}

/**
 * Получить текст статуса
 */
public function getStatusTextAttribute()
{
    return $this->is_read ? 'Прочитано' : 'Новое';
}
}