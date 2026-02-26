<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource (для админа).
     */
    public function index(Request $request)
    {
        // Только для админов и тренеров
        $this->authorize('viewAny', Booking::class);
        
        $bookings = Booking::with(['user', 'schedule.workout', 'schedule.trainer'])
            ->when($request->date, function($q, $date) {
                $q->whereHas('schedule', function($sq) use ($date) {
                    $sq->whereDate('date', $date);
                });
            })
            ->when($request->user_id, function($q, $userId) {
                $q->where('user_id', $userId);
            })
            ->when($request->status, function($q, $status) {
                $q->where('status', $status);
            })
            ->when($request->workout_id, function($q, $workoutId) {
                $q->whereHas('schedule', function($sq) use ($workoutId) {
                    $sq->where('workout_id', $workoutId);
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        // Для фильтров
        $users = User::clients()->get();
        $statuses = [
            Booking::STATUS_BOOKED => 'Забронировано',
            Booking::STATUS_CANCELLED => 'Отменено',
            Booking::STATUS_ATTENDED => 'Посещено',
            Booking::STATUS_MISSED => 'Пропущено'
        ];
            
        return view('admin.bookings.index', compact('bookings', 'users', 'statuses'));
    }

    /**
     * Show the form for creating a new resource (для админа).
     */
    public function create()
    {
        $this->authorize('create', Booking::class);
        
        $users = User::clients()->get();
        $schedules = Schedule::with(['workout', 'trainer'])
            ->where('date', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
            
        return view('admin.bookings.create', compact('users', 'schedules'));
    }

    /**
     * Store a newly created resource in storage (для админа).
     */
    public function store(Request $request)
    {
        $this->authorize('create', Booking::class);
        
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'schedule_id' => 'required|exists:schedules,id',
            'paid_separately' => 'boolean',
        ]);

        // Проверяем, не забронировано ли уже
        $existingBooking = Booking::where('user_id', $validated['user_id'])
            ->where('schedule_id', $validated['schedule_id'])
            ->where('status', '!=', Booking::STATUS_CANCELLED)
            ->first();

        if ($existingBooking) {
            return back()->with('error', 'Этот пользователь уже забронировал данное занятие')->withInput();
        }

        // Проверяем свободные места
        $schedule = Schedule::find($validated['schedule_id']);
        if ($schedule->current_participants >= $schedule->capacity()) {
            return back()->with('error', 'Нет свободных мест')->withInput();
        }

        // Создаем бронирование
        $booking = Booking::create([
            'user_id' => $validated['user_id'],
            'schedule_id' => $validated['schedule_id'],
            'status' => Booking::STATUS_BOOKED,
            'paid_separately' => $validated['paid_separately'] ?? false,
        ]);

        // Увеличиваем счетчик
        $schedule->increment('current_participants');

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Бронирование успешно создано');
    }

    /**
     * Display the specified resource.
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        
        $booking->load(['user', 'schedule.workout', 'schedule.trainer']);
        
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);
        
        return view('admin.bookings.edit', compact('booking'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);
        
        $validated = $request->validate([
            'status' => 'sometimes|in:booked,cancelled,attended,missed',
        ]);

        $booking->update($validated);

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Бронирование обновлено');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Booking $booking)
    {
        $this->authorize('delete', $booking);
        
        // Возвращаем место в расписании
        if ($booking->status !== Booking::STATUS_CANCELLED) {
            $booking->schedule->decrement('current_participants');
        }
        
        $booking->delete();

        return redirect()->route('admin.bookings.index')
            ->with('success', 'Бронирование удалено');
    }

    // =========== МЕТОДЫ ДЛЯ ТРЕНЕРА ===========

    /**
     * Отметка посещаемости (для тренера)
     */
    public function markAttendance(Booking $booking, Request $request)
    {
        // Проверяем, что тренер ведет это занятие
        if (Auth::user()->isTrainer() && $booking->schedule->trainer_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:attended,missed',
        ]);

        if ($validated['status'] === 'attended') {
            $booking->markAttended();
            
            // Создаем запись о посещении
            Attendance::create([
                'booking_id' => $booking->id,
                'marked_by' => Auth::id(),
                'attended_at' => now(),
                'attendance_type' => 'attended'
            ]);
        } else {
            $booking->markMissed();
        }

        return back()->with('success', 'Посещаемость отмечена');
    }

    /**
     * Статистика бронирований (для админа)
     */
    public function statistics(Request $request)
    {
        $this->authorize('viewStatistics', Booking::class);
        
        $startDate = $request->get('start_date', now()->startOfMonth()->toDateString());
        $endDate = $request->get('end_date', now()->endOfMonth()->toDateString());

        $stats = [
            'total' => Booking::whereBetween('created_at', [$startDate, $endDate])->count(),
            'by_status' => Booking::whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('status, count(*) as count')
                ->groupBy('status')
                ->pluck('count', 'status'),
            'by_workout' => Booking::whereBetween('created_at', [$startDate, $endDate])
                ->join('schedules', 'bookings.schedule_id', '=', 'schedules.id')
                ->join('workouts', 'schedules.workout_id', '=', 'workouts.id')
                ->selectRaw('workouts.name, count(*) as count')
                ->groupBy('workouts.name')
                ->pluck('count', 'name'),
        ];

        return view('admin.bookings.statistics', compact('stats', 'startDate', 'endDate'));
    }
}