<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        
        $todaySchedules = $user->scheduledTrainings()
            ->with(['workout', 'bookings.user'])
            ->where('date', today())
            ->orderBy('start_time')
            ->get();
            
        $upcomingSchedules = $user->scheduledTrainings()
            ->with('workout')
            ->where('date', '>', today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();
            
        return view('trainer.dashboard', compact('todaySchedules', 'upcomingSchedules'));
    }
    
    public function schedule()
    {
        $user = auth()->user();
        $schedules = $user->scheduledTrainings()
            ->with(['workout', 'bookings.user'])
            ->where('date', '>=', today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(15);
            
        return view('trainer.schedule.index', compact('schedules'));
    }
    
    public function markAttendance(Request $request, Schedule $schedule)
    {
        $booking = Booking::findOrFail($request->booking_id);
        
        // Проверка что тренировка ведет этот тренер
        if ($schedule->trainer_id !== auth()->id()) {
            abort(403);
        }
        
        // Создание записи о посещении
        \App\Models\Attendance::create([
            'booking_id' => $booking->id,
            'marked_by' => auth()->id(),
            'attendance_type' => $request->type,
            'comment' => $request->comment,
        ]);
        
        // Обновление статуса записи
        $booking->update(['status' => 'attended']);
        
        // Списание занятия с абонемента
        if ($booking->user_subscription_id) {
            $userSubscription = \App\Models\UserSubscription::find($booking->user_subscription_id);
            if ($userSubscription && $userSubscription->remaining_workouts > 0) {
                $userSubscription->decrement('remaining_workouts');
            }
        }
        
        return back()->with('success', 'Посещение отмечено');
    }
    
    public function clients()
    {
        $user = auth()->user();
        
        // Клиенты которые записаны на тренировки этого тренера
        $clients = User::whereHas('bookings.schedule', function($q) use ($user) {
            $q->where('trainer_id', $user->id);
        })
        ->with(['activeSubscription', 'bookings' => function($q) use ($user) {
            $q->whereHas('schedule', function($q2) use ($user) {
                $q2->where('trainer_id', $user->id);
            });
        }])
        ->distinct()
        ->paginate(15);
        
        return view('trainer.clients.index', compact('clients'));
    }
}