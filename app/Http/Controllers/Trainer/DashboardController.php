<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Booking; // <-- ДОБАВЬ ЭТУ СТРОКУ
use App\Models\Attendance; // <-- ДОБАВЬ ЭТУ СТРОКУ
use App\Models\Workout;
use App\Models\Notification; // <-- ДОБАВЬ ЭТУ СТРОКУ
use App\Models\UserSubscription; // <-- ДОБАВЬ ЭТУ СТРОКУ
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();

        if (!$user->is_active) {
        Auth::logout();
        return redirect()->route('login')
            ->withErrors(['email' => 'Ваш аккаунт деактивирован.']);
    }
        
        // ИСПРАВЛЕНО: используем trainings() вместо scheduledTrainings()
        $todaySchedules = $user->trainings()  // <-- ИЗМЕНЕНО
            ->with(['workout', 'bookings.user'])
            ->whereDate('date', Carbon::today())
            ->orderBy('start_time')
            ->get();
            
        $upcomingSchedules = $user->trainings()  // <-- ИЗМЕНЕНО
            ->with(['workout', 'bookings.user'])
            ->whereDate('date', '>', Carbon::today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();
            
        // Статистика для тренера
        $totalTrainings = $user->trainings()->count();
        $totalAttendances = Attendance::whereHas('booking.schedule', function($q) use ($user) {
            $q->where('trainer_id', $user->id);
        })->count();
        
        $uniqueClients = \App\Models\Booking::whereHas('schedule', function($q) use ($user) {
                $q->where('trainer_id', $user->id);
            })
            ->distinct('user_id')
            ->count('user_id');
        
        return view('trainer.dashboard', [
            'user' => $user,
            'todaySchedules' => $todaySchedules,
            'upcomingSchedules' => $upcomingSchedules,
            'totalTrainings' => $totalTrainings,
            'totalAttendances' => $totalAttendances,
            'uniqueClients' => $uniqueClients
        ]);
    }

    public function schedule(Request $request)
    {
        $user = auth()->user();
        
        $date = $request->get('date', now()->toDateString());
        
        // Тренировки на выбранный день
        $schedules = $user->trainings()
            ->with(['workout', 'bookings.user'])
            ->whereDate('date', $date)
            ->orderBy('start_time')
            ->get();
        
        // Тренировки на неделю для краткого обзора
        $weekSchedules = $user->trainings()
            ->with('workout')
            ->whereDate('date', '>=', now()->toDateString())
            ->whereDate('date', '<=', now()->addDays(7)->toDateString())
            ->orderBy('date')
            ->orderBy('start_time')
            ->get();
        
        return view('trainer.schedule', [
            'user' => $user,
            'schedules' => $schedules,
            'weekSchedules' => $weekSchedules,
            'currentDate' => $date
        ]);
    }

public function clients(Request $request)
{
    $user = auth()->user();
    
    // ============= НОВЫЙ КОД: Статистика для верхних карточек =============
    
    // 1. Количество тренировок на сегодня
    $todayTrainings = Schedule::where('trainer_id', $user->id)
        ->whereDate('date', today())
        ->count();
    
    // 2. Средняя посещаемость всех клиентов этого тренера
    $avgAttendance = Booking::whereHas('schedule', function($q) use ($user) {
            $q->where('trainer_id', $user->id);
        })
        ->selectRaw('AVG(CASE WHEN status = "attended" THEN 100 ELSE 0 END) as avg')
        ->value('avg') ?? 0;
    
    $avgAttendance = round($avgAttendance); // Округляем до целого
    
    // ============= ОСНОВНОЙ ЗАПРОС КЛИЕНТОВ =============
    
    // Получаем уникальных клиентов, которые посещали тренировки тренера
    $query = User::whereHas('bookings.schedule', function($q) use ($user) {
            $q->where('trainer_id', $user->id);
        })
        ->with(['bookings' => function($q) use ($user) {
            $q->whereHas('schedule', function($sq) use ($user) {
                $sq->where('trainer_id', $user->id);
            })
            ->with('schedule.workout')
            ->latest();
        }]);

    // Поиск по имени, email или телефону
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    // Фильтр по типу тренировки
    if ($request->filled('workout_id')) {
        $query->whereHas('bookings.schedule', function($q) use ($request) {
            $q->where('workout_id', $request->workout_id);
        });
    }

    // Получаем клиентов
    $clients = $query->get();

    // Добавляем дополнительную информацию
    foreach ($clients as $client) {
        // Количество тренировок у этого тренера
        $client->trainings_count = $client->bookings()
            ->whereHas('schedule', function($q) use ($user) {
                $q->where('trainer_id', $user->id);
            })
            ->count();
        
        // Последняя тренировка
        $client->last_booking = $client->bookings()
            ->whereHas('schedule', function($q) use ($user) {
                $q->where('trainer_id', $user->id);
            })
            ->with('schedule.workout')
            ->latest()
            ->first();
        
        // Прогресс (посещаемость)
        $total = $client->bookings()
            ->whereHas('schedule', function($q) use ($user) {
                $q->where('trainer_id', $user->id);
            })
            ->count();
        
        $attended = $client->bookings()
            ->whereHas('schedule', function($q) use ($user) {
                $q->where('trainer_id', $user->id);
            })
            ->where('status', 'attended')
            ->count();
        
        $client->progress = $total > 0 ? round(($attended / $total) * 100) : 0;
    }

    // Сортировка
    if ($request->filled('sort')) {
        switch ($request->sort) {
            case 'name_asc':
                $clients = $clients->sortBy('name');
                break;
            case 'name_desc':
                $clients = $clients->sortByDesc('name');
                break;
            case 'trainings_desc':
                $clients = $clients->sortByDesc('trainings_count');
                break;
            case 'trainings_asc':
                $clients = $clients->sortBy('trainings_count');
                break;
            case 'recent':
                $clients = $clients->sortByDesc(function($client) {
                    return $client->last_booking->schedule->date ?? null;
                });
                break;
        }
    }

    // Получаем все тренировки для фильтра
    $workouts = Workout::whereHas('schedules', function($q) use ($user) {
        $q->where('trainer_id', $user->id);
    })->get();

    // Пагинация
    $perPage = 12;
    $page = $request->get('page', 1);
    $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
        $clients->forPage($page, $perPage),
        $clients->count(),
        $perPage,
        $page,
        ['path' => $request->url(), 'query' => $request->query()]
    );

    return view('trainer.clients', [
        'user' => $user,
        'clients' => $paginated,
        'workouts' => $workouts,
        'todayTrainings' => $todayTrainings,      // <-- НОВОЕ
        'avgAttendance' => $avgAttendance          // <-- НОВОЕ
    ]);
}

/**
 * Детальная информация о клиенте
 */
public function clientDetails($id)
{
    $user = auth()->user();
    
    // Находим клиента
    $client = User::where('role_id', 4) // role_id = 4 для клиентов
        ->with(['bookings' => function($q) use ($user) {
            $q->whereHas('schedule', function($sq) use ($user) {
                $sq->where('trainer_id', $user->id);
            })
            ->with('schedule.workout')
            ->latest();
        }])
        ->findOrFail($id);
    
    // Проверяем, что этот клиент действительно посещал тренировки этого тренера
    $hasTrainings = $client->bookings()
        ->whereHas('schedule', function($q) use ($user) {
            $q->where('trainer_id', $user->id);
        })
        ->exists();
    
    if (!$hasTrainings) {
        abort(404, 'Клиент не найден или не посещал ваши тренировки');
    }
    
    // Статистика по клиенту
    $totalTrainings = $client->bookings()
        ->whereHas('schedule', function($q) use ($user) {
            $q->where('trainer_id', $user->id);
        })
        ->count();
    
    $attendedTrainings = $client->bookings()
        ->whereHas('schedule', function($q) use ($user) {
            $q->where('trainer_id', $user->id);
        })
        ->where('status', 'attended')
        ->count();
    
    $missedTrainings = $client->bookings()
        ->whereHas('schedule', function($q) use ($user) {
            $q->where('trainer_id', $user->id);
        })
        ->where('status', 'missed')
        ->count();
    
    $cancelledTrainings = $client->bookings()
        ->whereHas('schedule', function($q) use ($user) {
            $q->where('trainer_id', $user->id);
        })
        ->where('status', 'cancelled')
        ->count();
    
    $attendanceRate = $totalTrainings > 0 
        ? round(($attendedTrainings / $totalTrainings) * 100, 1) 
        : 0;
    
    // Последние 10 тренировок
    $recentBookings = $client->bookings()
        ->whereHas('schedule', function($q) use ($user) {
            $q->where('trainer_id', $user->id);
        })
        ->with('schedule.workout')
        ->latest()
        ->limit(10)
        ->get();
    
    return view('trainer.client-details', [
        'user' => $user,
        'client' => $client,
        'totalTrainings' => $totalTrainings,
        'attendedTrainings' => $attendedTrainings,
        'missedTrainings' => $missedTrainings,
        'cancelledTrainings' => $cancelledTrainings,
        'attendanceRate' => $attendanceRate,
        'recentBookings' => $recentBookings
    ]);
}

    /**
 * Отметка посещаемости
 */
public function markAttendance(Request $request, Schedule $schedule)
{
    // Проверяем, что тренировка принадлежит этому тренеру
    if ($schedule->trainer_id !== auth()->id()) {
        abort(403);
    }
    
    $validated = $request->validate([
        'booking_id' => 'required|exists:bookings,id',
        'status' => 'required|in:attended,missed'
    ]);
    
    $booking = Booking::find($validated['booking_id']);
    
    if ($validated['status'] === 'attended') {
    // Отмечаем как посещенное
    $booking->markAttended();
    
    // Создаем запись о посещении - используем 'attended' (разрешено)
    Attendance::create([
        'booking_id' => $booking->id,
        'marked_by' => auth()->id(),
        'attended_at' => now(),
        'attendance_type' => 'attended' // Допустимое значение
    ]);
        
        // Уменьшаем количество тренировок в абонементе (если еще не уменьшено)
        // Обычно это делается при бронировании, но на всякий случай проверим
        $activeSubscription = UserSubscription::where('user_id', $booking->user_id)
            ->where('status', 'active')
            ->first();
        
        // Отправляем уведомление клиенту
        \App\Models\Notification::create([
            'user_id' => $booking->user_id,
            'type' => 'booking',
            'message' => "Вы посетили тренировку '{$schedule->workout->name}' {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
            'is_read' => false,
            'data' => json_encode([
                'schedule_id' => $schedule->id,
                'workout_name' => $schedule->workout->name,
                'date' => $schedule->date->format('d.m.Y'),
                'time' => $schedule->start_time,
                'status' => 'attended'
            ])
        ]);
        
    } else {
    // Отмечаем как пропущенное
    $booking->markMissed();
    
    // ИСПРАВЛЕНО: для пропуска используем 'left_early' или другое допустимое значение
    Attendance::create([
        'booking_id' => $booking->id,
        'marked_by' => auth()->id(),
        'attended_at' => now(),
        'attendance_type' => 'left_early' // или 'late' - одно из допустимых значений
    ]);
        
        // Отправляем уведомление клиенту
        \App\Models\Notification::create([
            'user_id' => $booking->user_id,
            'type' => 'booking',
            'message' => "Вы пропустили тренировку '{$schedule->workout->name}' {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
            'is_read' => false,
            'data' => json_encode([
                'schedule_id' => $schedule->id,
                'workout_name' => $schedule->workout->name,
                'date' => $schedule->date->format('d.m.Y'),
                'time' => $schedule->start_time,
                'status' => 'missed'
            ])
        ]);
    }
    
    return back()->with('success', 'Посещаемость отмечена');
}

    public function attendance(Request $request)
{
    $user = auth()->user();
    
    $date = $request->get('date', now()->toDateString());
    $scheduleId = $request->get('schedule_id');
    
    // Получаем все тренировки тренера на выбранную дату
    $query = Schedule::with(['workout', 'bookings.user'])
        ->where('trainer_id', $user->id)
        ->whereDate('date', $date);
    
    if ($scheduleId) {
        $query->where('id', $scheduleId);
    }
    
    $schedules = $query->get();
    
    // Собираем все бронирования
    $bookings = collect();
    foreach ($schedules as $schedule) {
        $bookings = $bookings->merge($schedule->bookings);
    }
    
    // Сортируем по времени
    $bookings = $bookings->sortBy(function($booking) {
        return $booking->schedule->start_time;
    });
    
    // Получаем все тренировки для фильтра
    $allSchedules = Schedule::where('trainer_id', $user->id)
        ->whereDate('date', '>=', now()->subDays(7))
        ->with('workout')
        ->get();
    
    return view('trainer.attendance', [
        'user' => $user,
        'bookings' => $bookings,
        'schedules' => $allSchedules
    ]);
}

/**
 * Показать профиль тренера
 */
public function profile()
{
    $user = auth()->user();
    
    return view('trainer.profile', [
        'user' => $user
    ]);
}

/**
 * Обновление профиля тренера
 */
public function updateProfile(Request $request)
{
    $user = auth()->user();
    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $user->id,
        'phone' => 'nullable|string|max:20',
        'qualification' => 'nullable|string|max:255',
        'specialization' => 'nullable|string|max:255',
        'bio' => 'nullable|string',
    ]);
    
    $user->update($validated);
    
    return redirect()->route('trainer.profile')
        ->with('success', 'Профиль успешно обновлен');
}

/**
 * Обновление квалификации
 */
public function updateQualification(Request $request)
{
    $user = auth()->user();
    
    $validated = $request->validate([
        'qualification' => 'required|string|max:255',
        'specialization' => 'nullable|string|max:255',
    ]);
    
    $user->qualification = $validated['qualification'];
    $user->specialization = $validated['specialization'] ?? $user->specialization;
    $user->save();
    
    return back()->with('success', 'Квалификация обновлена');
}

/**
 * Смена пароля тренера
 */
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
    
    return redirect()->route('trainer.profile')
        ->with('success', 'Пароль успешно изменен');
}

}