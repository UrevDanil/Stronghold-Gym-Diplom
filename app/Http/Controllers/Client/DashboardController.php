<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        $data = [
            'user' => $user,
            'upcomingBookings' => $user->upcomingBookings()->limit(5)->get(),
            'pastBookings' => Booking::where('user_id', $user->id)
                ->whereHas('schedule', function($q) {
                    $q->where('date', '<', now()->toDateString())
                      ->orWhere(function($query) {
                          $query->where('date', now()->toDateString())
                                ->where('end_time', '<', now()->format('H:i:s'));
                      });
                })
                ->with('schedule.workout', 'schedule.trainer')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get(),
            'activeSubscription' => $user->activeSubscription(),
            'availableSubscriptions' => Subscription::where('is_active', true)->get(),
        ];
        
        return view('client.dashboard', $data);
    }

public function schedule(Request $request)
{
    $workoutId = $request->get('workout_id');
    $date = $request->get('date');
    
    $query = Schedule::with(['workout', 'trainer'])
        ->where('date', '>=', now()->toDateString())
        ->where('status', 'scheduled') // Используем status = scheduled
        ->whereHas('workout', function($q) {
            $q->where('is_active', true);
        });

    // Фильтр по типу тренировки
    if ($workoutId && $workoutId != '') {
        $query->where('workout_id', $workoutId);
    }

    // Фильтр по дате
    if ($date && $date != '') {
        $query->where('date', $date);
    } else {
        // Если дата не выбрана, показываем на 7 дней вперед
        $query->where('date', '<=', now()->addDays(7)->toDateString());
    }

    $schedules = $query->orderBy('date')->orderBy('start_time')->get();
    
    // Получаем все активные тренировки для фильтра
    $workouts = \App\Models\Workout::where('is_active', true)->get();
    
    // Получаем ID забронированных тренировок текущего пользователя
    $userBookings = Auth::user()->bookings()
        ->whereIn('schedule_id', $schedules->pluck('id'))
        ->where('status', '!=', 'cancelled')
        ->pluck('schedule_id')
        ->toArray();

    // Для отладки - раскомментируй если нужно проверить данные
    // dd($schedules, $workouts, $userBookings);

    return view('client.schedule', [
        'schedules' => $schedules,
        'workouts' => $workouts,
        'userBookings' => $userBookings,
        'selectedWorkout' => $workoutId,
        'selectedDate' => $date,
    ]);
}

    /**
     * Бронирование тренировки
     */
    public function book(Schedule $schedule, Request $request)
    {
        $user = Auth::user();

        if (!$user->hasActiveSubscription()) {
            return back()->with('error', 'У вас нет активного абонемента');
        }

        if (!$schedule->canBook()) {
            return back()->with('error', 'Это занятие нельзя забронировать');
        }

        $existingBooking = Booking::where('user_id', $user->id)
            ->where('schedule_id', $schedule->id)
            ->where('status', '!=', Booking::STATUS_CANCELLED)
            ->first();

        if ($existingBooking) {
            return back()->with('error', 'Вы уже забронировали это занятие');
        }

        $booking = Booking::create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'status' => Booking::STATUS_BOOKED,
        ]);

        // Увеличиваем счетчик занятых мест
        $schedule->increment('current_participants');

        // ИСПРАВЛЕНО: используем прямые свойства UserSubscription
        $activeSubscription = $user->activeSubscription();
        if ($activeSubscription && $activeSubscription->remaining_workouts > 0) {
            $activeSubscription->decrement('remaining_workouts');
        }
        
        // Создаем уведомление
\App\Models\Notification::create([
    'user_id' => $user->id,
    'type' => 'booking', // Вместо title используем type
    'message' => "Вы забронировали занятие '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
    'is_read' => false,
    // data можно не заполнять или добавить информацию
    'data' => json_encode([
        'schedule_id' => $schedule->id,
        'workout_name' => $schedule->workout->name,
        'date' => $schedule->date->format('d.m.Y'),
        'time' => $schedule->start_time
    ])
]);

        return back()->with('success', 'Занятие успешно забронировано!');
    }

    /**
     * Отмена бронирования
     */
public function cancelBooking(Booking $booking, Request $request)
{
    if ($booking->user_id !== Auth::id()) {
        abort(403);
    }

    if (!$booking->canCancel()) {
        return back()->with('error', 'Это бронирование нельзя отменить');
    }

    // Сохраняем данные до отмены
    $scheduleDate = $booking->schedule->date;
    $user = Auth::user();

    // Отменяем бронирование
    $booking->cancel();

    // ВОЗВРАЩАЕМ ТРЕНИРОВКУ В АБОНЕМЕНТ (только для будущих занятий)
    if ($scheduleDate > now()->toDateString()) {
        $activeSubscription = $user->activeSubscription();
        if ($activeSubscription) {
            $activeSubscription->increment('remaining_workouts');
            
            // Для отладки - добавим сообщение в лог
            \Log::info('Тренировка возвращена', [
                'user_id' => $user->id,
                'subscription_id' => $activeSubscription->id,
                'new_remaining' => $activeSubscription->remaining_workouts
            ]);
        } else {
            \Log::warning('Активный абонемент не найден при отмене', [
                'user_id' => $user->id
            ]);
        }
    }

    return back()->with('success', 'Бронирование отменено, тренировка возвращена в абонемент');
}

    /**
     * ИСПРАВЛЕННЫЙ МЕТОД subscriptions - теперь с часами и минутами
     */
    public function subscriptions()
    {
        $subscriptions = Subscription::where('is_active', true)->get();
        $userSubscriptions = Auth::user()->subscriptions()
            ->orderBy('pivot_start_date', 'desc')
            ->get();

        return view('client.subscriptions', [
            'subscriptions' => $subscriptions,
            'userSubscriptions' => $userSubscriptions,
            'user' => Auth::user(),
        ]);
    }

    public function purchaseSubscription(Subscription $subscription)
    {
        $user = Auth::user();
        
        $user->subscriptions()->attach($subscription->id, [
            'start_date' => now(),
            'end_date' => now()->addDays($subscription->duration_days),
            'remaining_workouts' => $subscription->workouts_count,
            'status' => 'active',
            'activated_by' => $user->id,
            'activated_at' => now(),
        ]);

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

/**
 * Заморозка абонемента*/

public function freezeSubscription(Request $request)
{
    $request->validate([
        'reason' => 'required|string|max:255',
        'days' => 'required|integer|min:1|max:14',
    ]);
    
    $user = Auth::user();
    
    // Получаем активную запись user_subscriptions
    $userSubscription = $user->activeSubscription();
    
    if (!$userSubscription) {
        return back()->with('error', 'У вас нет активного абонемента');
    }
    
    // Проверяем, не заморожен ли уже
    if ($userSubscription->isPaused()) {
        return back()->with('error', 'Абонемент уже заморожен');
    }
    
    try {
        // Используем метод pause из модели UserSubscription
        $userSubscription->pause($request->days);
        
        // Сохраняем причину (можно добавить поле в таблицу)
        $userSubscription->pause_reason = $request->reason;
        $userSubscription->save();
        
        return back()->with('success', "Абонемент успешно заморожен на {$request->days} дней");
        
    } catch (\Exception $e) {
        return back()->with('error', 'Ошибка при заморозке: ' . $e->getMessage());
    }
}

}