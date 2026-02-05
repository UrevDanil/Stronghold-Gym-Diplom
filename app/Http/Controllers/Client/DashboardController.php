<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Booking;
use App\Models\UserSubscription;
use App\Models\Workout;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        $data = [
            'activeSubscription' => $user->activeSubscription,
            'upcomingBookings' => $user->upcomingBookings()->get(),
            'bookingHistory' => $user->bookings()
                ->whereHas('schedule', function($q) {
                    $q->where('date', '<', today());
                })
                ->with('schedule.workout')
                ->latest()
                ->limit(10)
                ->get(),
            'workouts' => Workout::where('is_active', true)->get(),
        ];
        
        return view('client.dashboard', $data);
    }
    
    public function schedule()
    {
        $schedules = Schedule::with(['workout', 'trainer', 'bookings' => function($q) {
            $q->where('user_id', auth()->id());
        }])
            ->where('date', '>=', today())
            ->where('status', 'scheduled')
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(12);
            
        return view('client.schedule.index', compact('schedules'));
    }
    
    public function book(Schedule $schedule)
    {
        $user = auth()->user();
        
        // Проверка доступности
        if ($schedule->current_participants >= $schedule->workout->max_participants) {
            return back()->with('error', 'Нет свободных мест');
        }
        
        // Проверка активного абонемента
        if (!$user->activeSubscription) {
            return back()->with('error', 'У вас нет активного абонемента');
        }
        
        // Создание записи
        Booking::create([
            'user_id' => $user->id,
            'schedule_id' => $schedule->id,
            'user_subscription_id' => $user->activeSubscription->id,
            'status' => 'booked',
        ]);
        
        // Обновление счетчика
        $schedule->increment('current_participants');
        
        return back()->with('success', 'Вы успешно записались на тренировку');
    }
    
    public function cancelBooking(Booking $booking)
    {
        if ($booking->user_id !== auth()->id()) {
            abort(403);
        }
        
        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);
        
        // Обновление счетчика
        $booking->schedule->decrement('current_participants');
        
        return back()->with('success', 'Запись отменена');
    }
    
    public function subscriptions()
    {
        $user = auth()->user();
        $subscriptions = $user->userSubscriptions()
            ->with('subscription')
            ->latest()
            ->get();
            
        $availableSubscriptions = \App\Models\Subscription::where('is_active', true)->get();
        
        return view('client.subscriptions.index', compact('subscriptions', 'availableSubscriptions'));
    }
    
    public function purchaseSubscription($subscriptionId)
    {
        $subscription = \App\Models\Subscription::findOrFail($subscriptionId);
        $user = auth()->user();
        
        UserSubscription::create([
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
            'start_date' => now(),
            'end_date' => $subscription->duration_days ? now()->addDays($subscription->duration_days) : null,
            'remaining_workouts' => $subscription->workouts_count,
            'status' => 'active',
            'activated_by' => $user->id,
            'activated_at' => now(),
        ]);
        
        return back()->with('success', 'Абонемент успешно активирован');
    }
}