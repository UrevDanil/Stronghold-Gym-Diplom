<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Carbon\Carbon;

class UserSubscription extends Pivot
{
    protected $table = 'user_subscriptions';
    
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'remaining_workouts' => 'integer',
        'activated_at' => 'datetime',
        'paused_at' => 'datetime',
        'paused_until' => 'datetime',
        'original_end_date' => 'date',
    ];

    protected $fillable = [
        'user_id',
        'subscription_id',
        'start_date',
        'end_date',
        'remaining_workouts',
        'status',
        'activated_by',
        'activated_at',
        'paused_at',
        'paused_until',
        'pause_reason',
        'pause_days',
        'original_end_date'
    ];

    // Статусы (добавляем твои существующие + frozen)
    const STATUS_ACTIVE = 'active';
    const STATUS_FROZEN = 'frozen';
    const STATUS_EXPIRED = 'expired';
    const STATUS_CANCELLED = 'canceled';

    // ТВОЙ ИСХОДНЫЙ КОД (сохраняем)
    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE &&
               $this->end_date >= now()->toDateString() &&
               $this->remaining_workouts > 0;
    }

    public function useWorkout()
    {
        if ($this->remaining_workouts > 0) {
            $this->decrement('remaining_workouts');
            return true;
        }
        return false;
    }

    public function checkExpiration()
    {
        if ($this->end_date < now()->toDateString() && $this->status === self::STATUS_ACTIVE) {
            $this->update(['status' => self::STATUS_EXPIRED]);
            return true;
        }
        return false;
    }

    // НОВЫЕ МЕТОДЫ ДЛЯ ЗАМОРОЗКИ (добавляем)
    
    /**
 * Проверка, заморожен ли абонемент
 */
public function isPaused(): bool
{
    if (!$this->paused_at || !$this->paused_until) {
        return false;
    }
    
    $now = Carbon::now();
    $pausedUntil = Carbon::parse($this->paused_until);
    
    // Возвращаем true, если текущая дата меньше даты окончания заморозки
    return $now->lessThan($pausedUntil);
}

    /**
     * Заморозка абонемента
     */
    public function pause(int $days, ?string $reason = null)
    {
        if ($this->isPaused()) {
            throw new \Exception('Абонемент уже заморожен');
        }

        if ($this->status !== self::STATUS_ACTIVE) {
            throw new \Exception('Можно заморозить только активный абонемент');
        }

        $now = Carbon::now();
        $currentEndDate = Carbon::parse($this->end_date);

        $this->status = self::STATUS_FROZEN;
        $this->paused_at = $now;
        $this->paused_until = $now->copy()->addDays($days);
        $this->pause_days = $days;
        $this->pause_reason = $reason;
        
        // Сохраняем оригинальную дату и продлеваем
        if (!$this->original_end_date) {
            $this->original_end_date = $currentEndDate->format('Y-m-d');
        }
        
        // Продлеваем дату окончания
        $this->end_date = $currentEndDate->addDays($days)->format('Y-m-d');
        
        $this->save();
        
        return $this;
    }

    /**
     * Снятие заморозки
     */
    public function resume()
    {
        if (!$this->isPaused()) {
            throw new \Exception('Абонемент не заморожен');
        }

        $now = Carbon::now();
        $pausedUntil = Carbon::parse($this->paused_until);
        
        // Если снимаем заморозку досрочно
        if ($now->lt($pausedUntil)) {
            $daysLeft = $now->diffInDays($pausedUntil);
            $currentEndDate = Carbon::parse($this->end_date);
            
            // Возвращаем неиспользованные дни
            $this->end_date = $currentEndDate->subDays($daysLeft)->format('Y-m-d');
        }

        $this->status = self::STATUS_ACTIVE;
        $this->paused_at = null;
        $this->paused_until = null;
        $this->pause_days = null;
        
        $this->save();
        
        return $this;
    }

    /**
 * Проверка и автоматическое восстановление, если заморозка закончилась
 */
public function checkAndResumeIfExpired()
{
    if ($this->status === self::STATUS_FROZEN && !$this->isPaused()) {
        $this->resume();
        return true;
    }
    return false;
}

    /**
     * Отмена абонемента
     */
    public function cancel()
    {
        $this->status = self::STATUS_CANCELLED;
        $this->save();
        
        return $this;
    }

    /**
     * Получить оставшиеся дни
     */
    public function getDaysLeft(): int
    {
        if ($this->isPaused()) {
            return 0;
        }
        
        $now = Carbon::now();
        $endDate = Carbon::parse($this->end_date);
        
        if ($endDate->lt($now)) {
            return 0;
        }
        
        return $now->diffInDays($endDate);
    }

    /**
     * Получить процент использования
     */
    public function getUsagePercentage(): float
    {
        $total = $this->subscription->workouts_count ?? 0;
        $used = $total - $this->remaining_workouts;
        
        if ($total <= 0) {
            return 0;
        }
        
        return min(100, round(($used / $total) * 100, 1));
    }

    /**
     * Проверка, можно ли заморозить
     */
    public function canFreeze(): bool
    {
        return $this->status === self::STATUS_ACTIVE && 
               !$this->isPaused() &&
               Carbon::parse($this->start_date)->diffInDays(Carbon::now()) > 7; // Нельзя заморозить в первую неделю
    }

    // Связи
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function activator()
    {
        return $this->belongsTo(User::class, 'activated_by');
    }

    // Скоупы
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->whereDate('start_date', '<=', Carbon::today())
                     ->whereDate('end_date', '>=', Carbon::today());
    }

    public function scopeFrozen($query)
    {
        return $query->where('status', self::STATUS_FROZEN)
                     ->whereNotNull('paused_at')
                     ->whereNotNull('paused_until')
                     ->whereDate('paused_until', '>=', Carbon::today());
    }

    public function scopeExpiring($query, $days = 7)
    {
        return $query->where('status', self::STATUS_ACTIVE)
                     ->whereDate('end_date', '>=', Carbon::today())
                     ->whereDate('end_date', '<=', Carbon::today()->addDays($days));
    }
}