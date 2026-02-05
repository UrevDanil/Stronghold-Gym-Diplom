<?php

// app/Http/Controllers/Client/DashboardController.php
namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            ->where('is_active', true)
            ->whereHas('workout', function($q) {
                $q->where('is_active', true);
            });

        if ($workoutId) {
            $query->where('workout_id', $workoutId);
        }

        if ($date) {
            $query->where('date', $date);
        } else {
            // Показываем на неделю вперед по умолчанию
            $query->where('date', '<=', now()->addDays(7)->toDateString());
        }

        $schedules = $query->orderBy('date')->orderBy('start_time')->get();
        $workouts = \App\Models\Workout::where('is_active', true)->get();
        
        // Проверяем, какие занятия уже забронированы пользователем
        $userBookings = Auth::user()->bookings()
            ->whereIn('schedule_id', $schedules->pluck('id'))
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

    // Проверки
    if (!$user->hasActiveSubscription()) {
        return back()->with('error', 'У вас нет активного абонемента');
    }

    if (!$schedule->canBook()) {
        return back()->with('error', 'Это занятие нельзя забронировать');
    }

    // Проверяем, не забронировал ли уже пользователь это занятие
    $existingBooking = Booking::where('user_id', $user->id)
        ->where('schedule_id', $schedule->id)
        ->where('status', '!=', Booking::STATUS_CANCELLED)
        ->first();

    if ($existingBooking) {
        return back()->with('error', 'Вы уже забронировали это занятие');
    }

    // Создаем бронирование
    $booking = Booking::create([
        'user_id' => $user->id,
        'schedule_id' => $schedule->id,
        'status' => Booking::STATUS_BOOKED,
    ]);

    // Увеличиваем счетчик бронирований
    $schedule->increment('booked_count');

    // Используем тренировку из абонемента
    $subscription = $user->activeSubscription();
    if ($subscription && $subscription->pivot->remaining_workouts > 0) {
        $subscription->pivot->decrement('remaining_workouts');
    }
    
    // Создаем уведомление
    \App\Models\Notification::create([
        'user_id' => $user->id,
        'title' => 'Бронирование создано',
        'message' => "Вы забронировали занятие '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
        'type' => 'booking',
        'is_read' => false,
    ]);

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

    $booking->cancel($request->cancellation_reason);

    // Возвращаем тренировку в абонемент (если отмена не в день занятия)
    if ($booking->schedule->date > now()->toDateString()) {
        $subscription = Auth::user()->activeSubscription();
        if ($subscription) {
            $subscription->pivot->increment('remaining_workouts');
        }
    }

    return back()->with('success', 'Бронирование отменено');
    }

    public function subscriptions()
    {
        $subscriptions = Subscription::where('is_active', true)->get();
        $userSubscriptions = Auth::user()->subscriptions()
            ->orderBy('pivot_purchase_date', 'desc')
            ->get();

        return view('client.subscriptions', [
            'subscriptions' => $subscriptions,
            'userSubscriptions' => $userSubscriptions,
        ]);
    }

    public function purchaseSubscription(Subscription $subscription)
    {
        // Здесь должна быть интеграция с платежной системой
        // Пока просто создаем запись
        
        $user = Auth::user();
        
        $user->subscriptions()->attach($subscription->id, [
            'purchase_date' => now(),
            'expiry_date' => now()->addDays($subscription->duration_days),
            'remaining_sessions' => $subscription->session_count,
            'status' => 'active',
        ]);

        return back()->with('success', "Абонемент '{$subscription->name}' успешно приобретен!");
    }

    // Показ профиля пользователя
public function profile()
{
    $user = Auth::user();
    return view('client.profile', compact('user'));
}

// Обновление профиля пользователя
public function updateProfile(Request $request)
{
    $user = Auth::user();
    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        'phone' => 'nullable|string|max:20',
        'birth_date' => 'nullable|date|before:today',
        'address' => 'nullable|string|max:500',
        'health_info' => 'nullable|string|max:1000',
    ]);
    
    // Обновляем пароль, если он указан
    if ($request->filled('password')) {
        $request->validate([
            'password' => 'string|min:8|confirmed',
        ]);
        $validated['password'] = Hash::make($request->password);
    }
    
    $user->update($validated);
    
    return redirect()->route('client.profile')
        ->with('success', 'Профиль успешно обновлен');
}
}