<?php

// app/Models/User.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role_id',
        'birth_date',
        'notes',          // Для клиентов
        'avatar',
        'qualification',  // Для тренеров
        'specialization', // Для тренеров
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth_date' => 'date',
    ];

    // =========== ОТНОШЕНИЯ ===========

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // ИЗМЕНЕНО: используем UserSubscription модель напрямую
    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class, 'user_subscriptions')
                    ->using(UserSubscription::class)
                    ->withPivot([
                        'id', // Добавили id для доступа к конкретной записи
                        'start_date',
                        'end_date', 
                        'remaining_workouts',
                        'status',
                        'activated_by',
                        'activated_at',
                        // Добавляем новые поля для заморозки
                        'paused_at',
                        'paused_until',
                        'pause_reason',
                        'pause_days',
                        'original_end_date'
                    ])
                    ->withTimestamps();
    }

    // НОВОЕ: отношение для прямого доступа к UserSubscription записям
    public function userSubscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

/**
 * Проверка, активен ли пользователь (для отображения статуса онлайн)
 */
public function isActive(): bool
{
    // Если есть поле last_activity (которого пока нет), используем его
    if (property_exists($this, 'last_activity') && $this->last_activity) {
        return Carbon::parse($this->last_activity)->diffInMinutes(now()) < 5;
    }
    
    // Если нет поля last_activity, просто возвращаем true
    // чтобы не ломать отображение
    return true;
    
    // ИЛИ можно проверять по полю is_active
    // return (bool) $this->is_active;
}

// Тренер проводит занятия
public function trainings()
{
    return $this->hasMany(Schedule::class, 'trainer_id');
}

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // =========== ПРОВЕРКИ РОЛЕЙ ===========

    public function isAdmin()
    {
        return $this->role && ($this->role->name === 'admin' || $this->role->name === 'owner');
    }

    public function isOwner()
    {
        return $this->role && $this->role->name === 'owner';
    }

    public function isClient()
    {
        return $this->role && $this->role->name === 'client';
    }

    public function isTrainer()
    {
        return $this->role && $this->role->name === 'trainer';
    }

    // =========== SCOPE ===========

    public function scopeClients($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('name', 'client');
        });
    }

    public function scopeTrainers($query)
    {
        return $query->whereHas('role', function($q) {
            $q->where('name', 'trainer');
        });
    }

    // =========== HELPERS ===========

    // ИСПРАВЛЕНО: теперь используем UserSubscription
    public function hasActiveSubscription(): bool
    {
        return $this->userSubscriptions()
            ->where('status', UserSubscription::STATUS_ACTIVE)
            ->whereDate('start_date', '<=', Carbon::today())
            ->whereDate('end_date', '>=', Carbon::today())
            ->where('remaining_workouts', '>', 0)
            ->where(function($q) {
                $q->whereNull('paused_at')
                  ->orWhereDate('paused_until', '<', Carbon::today());
            })
            ->exists();
    }

/**
 * Получить активный абонемент
 */
public function activeSubscription()
{
    return $this->userSubscriptions()
        ->where('status', UserSubscription::STATUS_ACTIVE)
        ->whereDate('start_date', '<=', Carbon::today())
        ->whereDate('end_date', '>=', Carbon::today())
        ->where('remaining_workouts', '>', 0)
        ->where(function($q) {
            $q->whereNull('paused_at')
              ->orWhereDate('paused_until', '<', Carbon::today());
        })
        ->latest()
        ->first();
}

    // НОВОЕ: получить замороженный абонемент
    public function frozenSubscription(): ?UserSubscription
    {
        return $this->userSubscriptions()
            ->where('status', UserSubscription::STATUS_FROZEN)
            ->whereNotNull('paused_at')
            ->whereNotNull('paused_until')
            ->whereDate('paused_until', '>=', Carbon::today())
            ->latest()
            ->first();
    }

    // НОВОЕ: получить все активные и замороженные абонементы
    public function getActiveSubscriptions()
    {
        return $this->userSubscriptions()
            ->whereIn('status', [UserSubscription::STATUS_ACTIVE, UserSubscription::STATUS_FROZEN])
            ->whereDate('end_date', '>=', Carbon::today())
            ->orderBy('created_at', 'desc')
            ->get();
    }

/**
 * Предстоящие бронирования (только активные, не посещенные и не пропущенные)
 */
public function upcomingBookings()
{
    return $this->bookings()
        ->whereIn('status', ['booked']) // ТОЛЬКО забронированные (не attended, не missed, не cancelled)
        ->whereHas('schedule', function($q) {
            $q->where('date', '>=', now()->toDateString())
              ->orWhere(function($query) {
                  $query->where('date', now()->toDateString())
                        ->where('start_time', '>', now()->format('H:i:s'));
              });
        })
        ->with('schedule.workout', 'schedule.trainer')
        ->orderBy('created_at', 'desc');
}

/**
 * История посещений (прошедшие тренировки)
 */
public function pastBookings()
{
    return $this->bookings()
        ->whereIn('status', ['attended', 'missed', 'cancelled'])
        ->whereHas('schedule', function($q) {
            $q->where('date', '<', now()->toDateString())
              ->orWhere(function($query) {
                  $query->where('date', now()->toDateString())
                        ->where('end_time', '<', now()->format('H:i:s'));
              });
        })
        ->with('schedule.workout', 'schedule.trainer')
        ->orderBy('created_at', 'desc');
}

    // ИСПРАВЛЕНО: использование сессии тренировки
    public function useWorkoutSession(): bool
    {  
        $activeSubscription = $this->activeSubscription();
        if ($activeSubscription) {
            return $activeSubscription->useWorkout();
        }
        return false;
    }

    // НОВОЕ: проверить возможность заморозки
    public function canFreezeSubscription(): bool
    {
        $activeSubscription = $this->activeSubscription();
        return $activeSubscription && $activeSubscription->canFreeze();
    }

    // НОВОЕ: получить количество оставшихся тренировок
    public function getRemainingWorkoutsCount(): int
    {
        $activeSubscription = $this->activeSubscription();
        return $activeSubscription ? $activeSubscription->remaining_workouts : 0;
    }

    // НОВОЕ: получить полное имя с ролью (для админки)
    public function getDisplayNameAttribute(): string
    {
        $roleName = $this->role ? ucfirst($this->role->name) : 'Без роли';
        return "{$this->name} ({$roleName})";
    }

    // НОВОЕ: проверка на день рождения (для скидок и поздравлений)
    public function isBirthdayToday(): bool
    {
        if (!$this->birth_date) {
            return false;
        }
        
        $today = Carbon::today();
        $birthday = Carbon::parse($this->birth_date);
        
        return $today->month === $birthday->month && $today->day === $birthday->day;
    }

/**
 * Получить историю посещений через бронирования
 */
public function getAttendanceStats()
{
    // Получаем ID всех бронирований пользователя
    $bookingIds = $this->bookings()->pluck('id');
    
    // Считаем посещения по этим бронированиям
    $total = Attendance::whereIn('booking_id', $bookingIds)->count();
    
    // Посещения за текущий месяц
    $thisMonth = Attendance::whereIn('booking_id', $bookingIds)
        ->whereMonth('attended_at', Carbon::now()->month)
        ->whereYear('attended_at', Carbon::now()->year)
        ->count();
        
    return [
        'total' => $total,
        'this_month' => $thisMonth,
    ];
}

    // =========== АККЕССОРЫ ===========

    public function getAvatarUrlAttribute(): string
    {
        return $this->avatar 
            ? asset('storage/' . $this->avatar)
            : asset('images/default-avatar.png');
    }

    public function getFormattedPhoneAttribute(): ?string
    {
        if (!$this->phone) {
            return null;
        }
        
        // Форматируем телефон: +7 (999) 999-99-99
        $phone = preg_replace('/[^0-9]/', '', $this->phone);
        if (strlen($phone) === 11) {
            return '+' . substr($phone, 0, 1) . ' (' . substr($phone, 1, 3) . ') ' . 
                   substr($phone, 4, 3) . '-' . substr($phone, 7, 2) . '-' . substr($phone, 9, 2);
        }
        
        return $this->phone;
    }
}