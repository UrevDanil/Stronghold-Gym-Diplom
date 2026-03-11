<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Subscription;
use App\Models\Attendance; 
use App\Models\UserSubscription; // <-- ДОБАВЬ ЭТУ СТРОКУ
use App\Models\Notification; // <-- И ЭТУ, ЕСЛИ ЕЩЕ НЕТ
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        if (!$user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Ваш аккаунт деактивирован.']);
        }

        $data = [
            'user' => $user,
            'upcomingBookings' => $user->upcomingBookings()->limit(5)->get(),
            // ИСПРАВЛЕНО: используем статусы attended и missed для истории
            'pastBookings' => Booking::where('user_id', $user->id)
                ->whereIn('status', ['attended', 'missed']) // Только посещенные и пропущенные
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

    // ПРОВЕРЯЕМ СУЩЕСТВУЮЩИЕ БРОНИРОВАНИЯ
    $existingBooking = Booking::where('user_id', $user->id)
        ->where('schedule_id', $schedule->id)
        ->whereIn('status', ['booked', 'attended']) // Только активные бронирования
        ->first();

    if ($existingBooking) {
        return back()->with('error', 'Вы уже забронировали это занятие (статус: ' . $existingBooking->status . ')');
    }

    // Проверяем, есть ли свободные места
    if (!$schedule->hasAvailableSlots()) {
        return back()->with('error', 'Нет свободных мест');
    }

    // Проверяем, есть ли отмененное бронирование, которое можно восстановить
    $cancelledBooking = Booking::where('user_id', $user->id)
        ->where('schedule_id', $schedule->id)
        ->where('status', 'cancelled')
        ->first();
        
    if ($cancelledBooking) {
        // Восстанавливаем отмененное бронирование
        $cancelledBooking->status = Booking::STATUS_BOOKED;
        $cancelledBooking->cancelled_at = null;
        $cancelledBooking->save();
        
        $booking = $cancelledBooking;
        
        // Увеличиваем счетчик занятых мест
        $schedule->increment('current_participants');
        
        // Уменьшаем количество тренировок в абонементе
        $activeSubscription = $user->activeSubscription();
        if ($activeSubscription && $activeSubscription->remaining_workouts > 0) {
            $activeSubscription->decrement('remaining_workouts');
        }
        
        // Создаем уведомление о восстановлении
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => 'booking',
            'message' => "Вы восстановили бронирование на занятие '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
            'is_read' => false,
            'data' => json_encode([
                'schedule_id' => $schedule->id,
                'workout_name' => $schedule->workout->name,
                'date' => $schedule->date->format('d.m.Y'),
                'time' => $schedule->start_time
            ])
        ]);
        
        return back()->with('success', 'Бронирование восстановлено!');
    }

    // Создаем новое бронирование
    $booking = Booking::create([
        'user_id' => $user->id,
        'schedule_id' => $schedule->id,
        'status' => Booking::STATUS_BOOKED,
    ]);

    // Увеличиваем счетчик занятых мест
    $schedule->increment('current_participants');

    // Уменьшаем количество тренировок в абонементе
    $activeSubscription = $user->activeSubscription();
    if ($activeSubscription && $activeSubscription->remaining_workouts > 0) {
        $activeSubscription->decrement('remaining_workouts');
    }
    
    // Создаем уведомление
    \App\Models\Notification::create([
        'user_id' => $user->id,
        'type' => 'booking',
        'message' => "Вы забронировали занятие '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
        'is_read' => false,
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

    // Проверяем, не было ли уже посещение
    $attendanceExists = Attendance::where('booking_id', $booking->id)->exists();
    if ($attendanceExists) {
        return back()->with('error', 'Нельзя отменить тренировку, которая уже была посещена');
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
        }
    }

    return back()->with('success', 'Бронирование отменено, тренировка возвращена в абонемент');
}

/**
 * ИСПРАВЛЕННЫЙ МЕТОД subscriptions
 */
public function subscriptions()
{
    $subscriptions = Subscription::where('is_active', true)->get();
    
    // ИСПРАВЛЕНО: используем userSubscriptions() вместо subscriptions()
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
        'notes' => 'nullable|string|max:1000', // ДОБАВЛЕНО
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

/**
 * Разморозка абонемента
 */
public function resumeSubscription(Request $request)
{
    $user = Auth::user();
    
    // Получаем замороженный абонемент
    $userSubscription = $user->userSubscriptions()
        ->where('status', UserSubscription::STATUS_FROZEN)
        ->whereNotNull('paused_at')
        ->whereNotNull('paused_until')
        ->whereDate('paused_until', '>=', Carbon::today())
        ->first();
    
    if (!$userSubscription) {
        return back()->with('error', 'У вас нет замороженного абонемента');
    }
    
    try {
        // Используем метод resume из модели UserSubscription
        $userSubscription->resume();
        
        // Создаем уведомление
        \App\Models\Notification::create([
            'user_id' => $user->id,
            'type' => 'subscription',
            'message' => 'Ваш абонемент успешно разморожен. Срок действия обновлен.',
            'is_read' => false,
            'data' => json_encode([
                'subscription_id' => $userSubscription->subscription_id,
                'subscription_name' => $userSubscription->subscription->name ?? 'Абонемент',
                'new_end_date' => $userSubscription->end_date->format('d.m.Y')
            ])
        ]);
        
        return back()->with('success', 'Абонемент успешно разморожен');
        
    } catch (\Exception $e) {
        return back()->with('error', 'Ошибка при разморозке: ' . $e->getMessage());
    }
}

}