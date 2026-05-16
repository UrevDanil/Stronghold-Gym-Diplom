<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Subscription;
use App\Models\Attendance; 
use App\Services\NotificationService;
use App\Models\UserSubscription;
use App\Models\Notification;
use App\Models\User;
use App\Events\SubscriptionPurchased;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected $notify;
    
    public function __construct(NotificationService $notificationService)
    {
        $this->notify = $notificationService;
    }
    
public function dashboard()
{
    $user = Auth::user();

    $user->refreshSubscriptionStatuses();
    
    if (!$user->is_active) {
        Auth::logout();
        return redirect()->route('login')
            ->withErrors(['email' => 'Ваш аккаунт деактивирован.']);
    }

    $userSubscriptions = $user->userSubscriptions()
        ->with('subscription')
        ->orderBy('created_at', 'desc')
        ->get();

    $hasTrainerSubscription = $user->hasActiveTrainerSubscription();
    
    // Проверяем замороженный абонемент
    $hasFrozen = false;
    $frozenSub = null;
    foreach($userSubscriptions as $sub) {
        if($sub->status === 'frozen' && $sub->isPaused()) {
            $hasFrozen = true;
            $frozenSub = $sub;
            break;
        }
    }
    
    $upcomingBookings = $hasTrainerSubscription 
        ? $user->upcomingBookings()->limit(5)->get() 
        : collect();

    $data = [
        'user' => $user,
        'userSubscriptions' => $userSubscriptions,
        'upcomingBookings' => $upcomingBookings,
        'hasTrainerSubscription' => $hasTrainerSubscription,
        'pastBookings' => Booking::where('user_id', $user->id)
            ->whereIn('status', ['attended', 'missed'])
            ->with('schedule.workout', 'schedule.trainer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(),
        'activeSubscription' => $user->activeSubscription(),
        'availableSubscriptions' => Subscription::where('is_active', true)->get(),
        'hasFrozen' => $hasFrozen,
        'frozenSub' => $frozenSub,
    ];
    
    return view('client.dashboard', $data);
}

    public function schedule(Request $request)
    {
        $user = Auth::user();
        
        if (!$user->hasActiveTrainerSubscription()) {
            return redirect()->route('client.dashboard')
                ->with('error', 'Для записи на тренировки с тренером необходим абонемент "С тренером"');
        }
        
        $workoutId = $request->get('workout_id');
        $date = $request->get('date');
        
        $query = Schedule::with(['workout', 'trainer'])
            ->where('date', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->whereHas('workout', function($q) {
                $q->where('is_active', true);
            });

        if ($workoutId && $workoutId != '') {
            $query->where('workout_id', $workoutId);
        }

        if ($date && $date != '') {
            $query->where('date', $date);
        } else {
            $query->where('date', '<=', now()->addDays(7)->toDateString());
        }

        $schedules = $query->orderBy('date')->orderBy('start_time')->get();
        $workouts = \App\Models\Workout::where('is_active', true)->get();
        
        $userBookings = Auth::user()->bookings()
            ->whereIn('schedule_id', $schedules->pluck('id'))
            ->where('status', '!=', 'cancelled')
            ->pluck('schedule_id')
            ->toArray();

        return view('client.schedule', [
            'schedules' => $schedules,
            'workouts' => $workouts,
            'userBookings' => $userBookings,
            'selectedWorkout' => $workoutId,
            'selectedDate' => $date,
        ]);
    }

    public function book(Schedule $schedule, Request $request)
    {
        $user = Auth::user();

        if (!$user->hasActiveTrainerSubscription()) {
            return back()->with('error', 'Для записи на тренировки с тренером необходим абонемент "С тренером"');
        }

        if (!$schedule->canBook()) {
            return back()->with('error', 'Это занятие нельзя забронировать');
        }

        $existingBooking = Booking::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->whereIn('status', ['booked', 'attended'])
            ->first();

        if ($existingBooking) {
            return back()->with('error', 'Вы уже забронировали это занятие (статус: ' . $existingBooking->status . ')');
        }

        if (!$schedule->hasAvailableSlots()) {
            return back()->with('error', 'Нет свободных мест');
        }

        $cancelledBooking = Booking::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->where('status', 'cancelled')
            ->first();
            
        if ($cancelledBooking) {
            $cancelledBooking->status = Booking::STATUS_BOOKED;
            $cancelledBooking->cancelled_at = null;
            $cancelledBooking->save();
            
            $booking = $cancelledBooking;
            $schedule->increment('current_participants');
            
            $activeSubscription = $user->activeSubscription();
            if ($activeSubscription && $activeSubscription->remaining_workouts > 0) {
                $activeSubscription->decrement('remaining_workouts');
            }
            
            // Уведомление о восстановлении
            $this->notify->send(
                $user->id,
                "Вы восстановили бронирование на занятие '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
                'booking',
                ['schedule_id' => $schedule->id, 'workout_name' => $schedule->workout->name]
            );
            
            $this->notify->send(
                $schedule->trainer_id,
                "Клиент {$user->name} восстановил бронирование на '{$schedule->workout->name}' в {$schedule->start_time}",
                'booking',
                ['schedule_id' => $schedule->id, 'client_id' => $user->id]
            );
            
            return back()->with('success', 'Бронирование восстановлено!');
        }

        $booking = Booking::create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'status' => Booking::STATUS_BOOKED,
        ]);

        $schedule->increment('current_participants');

        $activeSubscription = $user->activeSubscription();
        if ($activeSubscription && $activeSubscription->remaining_workouts > 0) {
            $activeSubscription->decrement('remaining_workouts');
        }
        
        // Уведомления
        $this->notify->send(
            $user->id,
            "Вы забронировали занятие '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
            'booking',
            ['schedule_id' => $schedule->id, 'workout_name' => $schedule->workout->name]
        );
        
        $this->notify->send(
            $schedule->trainer_id,
            "Новый клиент! {$user->name} записался на тренировку '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
            'booking',
            ['schedule_id' => $schedule->id, 'client_id' => $user->id]
        );
        
        if ($schedule->current_participants >= $schedule->capacity) {
            $this->notify->notifyAdmins(
                "Тренировка '{$schedule->workout->name}' полностью заполнена! ({$schedule->current_participants}/{$schedule->capacity})",
                'booking',
                ['schedule_id' => $schedule->id, 'trainer_id' => $schedule->trainer_id]
            );
        }

        return back()->with('success', 'Занятие успешно забронировано!');
    }

    public function cancelBooking(Booking $booking, Request $request)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$booking->canCancel()) {
            return back()->with('error', 'Это бронирование нельзя отменить');
        }

        $attendanceExists = Attendance::where('booking_id', $booking->id)->exists();
        if ($attendanceExists) {
            return back()->with('error', 'Нельзя отменить тренировку, которая уже была посещена');
        }

        $scheduleDate = $booking->schedule->date;
        $user = Auth::user();
        $schedule = $booking->schedule;

        $booking->cancel();

        if ($scheduleDate > now()->toDateString()) {
            $activeSubscription = $user->activeSubscription();
            if ($activeSubscription) {
                $activeSubscription->increment('remaining_workouts');
            }
        }
        
        $this->notify->send(
            $user->id,
            "Вы отменили бронирование на '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
            'booking',
            ['schedule_id' => $schedule->id]
        );
        
        $this->notify->send(
            $schedule->trainer_id,
            "Клиент {$user->name} отменил бронирование на '{$schedule->workout->name}' в {$schedule->start_time}",
            'booking',
            ['schedule_id' => $schedule->id, 'client_id' => $user->id]
        );

        return back()->with('success', 'Бронирование отменено, тренировка возвращена в абонемент');
    }

    public function subscriptions()
    {
        $subscriptions = Subscription::where('is_active', true)->get();
        
        $userSubscriptions = Auth::user()->userSubscriptions()
            ->with('subscription')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('client.subscriptions', [
            'subscriptions' => $subscriptions,
            'userSubscriptions' => $userSubscriptions,
            'user' => Auth::user(),
        ]);
    }

/**
* Покупка абонемента
*/
    public function purchaseSubscription(Subscription $subscription)
    {
        $user = Auth::user();
        
        // Создаем запись о покупке абонемента
        $userSubscription = $user->subscriptions()->attach($subscription->id, [
            'start_date' => now(),
            'end_date' => now()->addDays($subscription->duration_days),
            'remaining_workouts' => $subscription->workouts_count,
            'status' => 'active',
            'activated_by' => $user->id,
            'activated_at' => now(),
        ]);
        
        // Получаем созданную запись user_subscription
        $userSubscription = UserSubscription::where('user_id', $user->id)
            ->where('subscription_id', $subscription->id)
            ->where('status', 'active')
            ->latest()
            ->first();
        
        // 1. Уведомление клиенту
        $this->notify->send(
            $user->id,
            "🎉 Вы приобрели абонемент '{$subscription->name}'!\n📅 Действителен до: " . now()->addDays($subscription->duration_days)->format('d.m.Y') . "\n🏋️ Тренировок: " . ($subscription->workouts_count == 0 ? 'Безлимит' : $subscription->workouts_count),
            'subscription',
            [
                'subscription_id' => $subscription->id,
                'subscription_name' => $subscription->name,
                'end_date' => now()->addDays($subscription->duration_days)->format('d.m.Y'),
                'remaining_workouts' => $subscription->workouts_count,
                'price' => $subscription->price
            ]
        );
        
        // 2. Уведомление админам
        $this->notify->notifyAdmins(
            "💰 Клиент {$user->name} приобрел абонемент '{$subscription->name}'!\n📅 Сумма: {$subscription->price} руб.\n📅 Действует до: " . now()->addDays($subscription->duration_days)->format('d.m.Y'),
            'subscription',
            [
                'user_id' => $user->id,
                'user_name' => $user->name,
                'subscription_id' => $subscription->id,
                'subscription_name' => $subscription->name,
                'price' => $subscription->price,
                'end_date' => now()->addDays($subscription->duration_days)->format('d.m.Y')
            ]
        );

        return back()->with('success', "Абонемент '{$subscription->name}' успешно приобретен!");
    }

    public function profile()
    {
        $user = Auth::user();
        return view('client.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
            
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date|before:today',
            'notes' => 'nullable|string|max:1000',
        ]);
            
        $user->update($validated);
            
        return redirect()->route('client.profile')
        ->with('success', 'Профиль успешно обновлен');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);
            
        $user = Auth::user();
        $user->update([
            'password' => Hash::make($request->password)
        ]);
            
        return redirect()->route('client.profile')
        ->with('success', 'Пароль успешно изменен');
        }

public function freezeSubscription(Request $request)
{
    $request->validate([
        'reason' => 'required|string|max:255',
        'days' => 'required|integer|min:1|max:14',
    ]);
    
    $user = Auth::user();
    
    // Ищем ТОЛЬКО активный (не замороженный) абонемент
    $userSubscription = $user->userSubscriptions()
        ->where('status', UserSubscription::STATUS_ACTIVE)
        ->whereDate('start_date', '<=', Carbon::today())
        ->whereDate('end_date', '>=', Carbon::today())
        ->where('remaining_workouts', '>', 0)
        ->where(function($q) {
            $q->whereNull('paused_at')
              ->orWhereDate('paused_until', '<', Carbon::today());
        })
        ->first();
    
    if (!$userSubscription) {
        return back()->with('error', 'У вас нет активного абонемента для заморозки');
    }
    
    if ($userSubscription->isPaused()) {
        return back()->with('error', 'Абонемент уже заморожен');
    }
    
    try {
        $userSubscription->pause($request->days, $request->reason);
        
        $this->notify->send(
            $user->id,
            "❄️ Ваш абонемент заморожен на {$request->days} дней. Причина: {$request->reason}",
            'subscription',
            ['subscription_id' => $userSubscription->subscription_id]
        );
        
        return back()->with('success', "Абонемент успешно заморожен на {$request->days} дней");
        
    } catch (\Exception $e) {
        return back()->with('error', 'Ошибка при заморозке: ' . $e->getMessage());
    }
}

    public function resumeSubscription(Request $request)
    {
        $user = Auth::user();
            
        $userSubscription = $user->userSubscriptions()
            ->where('status', UserSubscription::STATUS_FROZEN)
            ->whereNotNull('paused_at')
            ->whereNotNull('paused_until')
            ->first();
            
        if (!$userSubscription) {
            return back()->with('error', 'У вас нет замороженного абонемента');
        }
            
        try {
            if (!$userSubscription->isPaused()) {
                $userSubscription->status = UserSubscription::STATUS_ACTIVE;
                $userSubscription->paused_at = null;
                $userSubscription->paused_until = null;
                $userSubscription->pause_days = null;
                $userSubscription->save();
            } else {
                $userSubscription->resume();
            }
                
            $this->notify->send(
                $user->id,
                "Ваш абонемент успешно разморожен. Срок действия обновлен.",
                'subscription',
                ['subscription_id' => $userSubscription->subscription_id]
            );
                
            return back()->with('success', 'Абонемент успешно разморожен');
                
        } catch (\Exception $e) {
                return back()->with('error', 'Ошибка при разморозке: ' . $e->getMessage());
        }
    }

/**
* Получить информацию о тренере для модального окна
*/
    public function getTrainerInfo($trainerId)
    {
        $trainer = User::where('role_id', 3) 
            ->withCount(['trainings as total_trainings' => function($q) {
                $q->where('status', 'completed');
            }])
            ->find($trainerId);
        
        if (!$trainer) {
            return response()->json([
                'success' => false,
                'message' => 'Тренер не найден'
            ], 404);
        }
        
        // Количество уникальных клиентов
        $totalClients = Booking::whereHas('schedule', function($q) use ($trainerId) {
                $q->where('trainer_id', $trainerId);
            })
            ->distinct('user_id')
            ->count('user_id');
        
        // Процент посещаемости клиентов этого тренера
        $totalBookings = Booking::whereHas('schedule', function($q) use ($trainerId) {
            $q->where('trainer_id', $trainerId);
        })->count();
        
        $attendedBookings = Booking::whereHas('schedule', function($q) use ($trainerId) {
            $q->where('trainer_id', $trainerId);
        })->where('status', 'attended')->count();
        
        $attendanceRate = $totalBookings > 0 ? round(($attendedBookings / $totalBookings) * 100) : 0;
        
        return response()->json([
            'success' => true,
            'trainer' => [
                'id' => $trainer->id,
                'name' => $trainer->name,
                'email' => $trainer->email,
                'phone' => $trainer->phone,
                'qualification' => $trainer->qualification,
                'specialization' => $trainer->specialization,
                'bio' => $trainer->bio ?? null,
                'avatar' => $trainer->avatar ? asset('storage/' . $trainer->avatar) : null,
            ],
            'stats' => [
                'total_trainings' => $trainer->total_trainings ?? 0,
                'total_clients' => $totalClients,
                'attendance_rate' => $attendanceRate,
            ]
        ]);
    }
}