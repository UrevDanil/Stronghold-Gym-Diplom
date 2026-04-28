<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\Booking;
use App\Services\NotificationService;
use App\Models\Schedule;
use App\Models\Workout;
use App\Models\Notification;
use App\Models\Attendance;
use App\Events\ScheduleDeleted;
use App\Events\ScheduleCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        // Статистика для дашборда
        $stats = [
            'total_clients' => User::whereHas('role', function($q) {
                $q->where('name', 'client');
            })->count(),
            
            'total_trainers' => User::whereHas('role', function($q) {
                $q->where('name', 'trainer');
            })->count(),
            
            'active_subscriptions' => UserSubscription::where('status', 'active')->count(),
            
            'today_bookings' => Booking::whereHas('schedule', function($q) {
                $q->whereDate('date', Carbon::today());
            })->count(),
            
            // ИСПРАВЛЕНО: получаем сумму через связанную модель Subscription
            'revenue_month' => UserSubscription::where('status', 'active')
                ->whereMonth('created_at', Carbon::now()->month)
                ->with('subscription')
                ->get()
                ->sum(function($userSub) {
                    return $userSub->subscription->price ?? 0;
                }),
                
            'total_workouts' => Workout::count(),
            
            'completed_trainings' => Schedule::where('status', 'completed')->count(),
        ];
        
        // Последние бронирования
        $recentBookings = Booking::with(['user', 'schedule.workout'])
            ->latest()
            ->limit(10)
            ->get();
            
        // Новые клиенты
        $recentClients = User::whereHas('role', function($q) {
                $q->where('name', 'client');
            })
            ->latest()
            ->limit(5)
            ->get();
        
        // Популярные тренировки
        $popularWorkouts = Workout::withCount('schedules')
            ->orderBy('schedules_count', 'desc')
            ->limit(5)
            ->get();
        
        // Активные абонементы по типам
        $subscriptionsByType = UserSubscription::where('status', 'active')
            ->with('subscription')
            ->get()
            ->groupBy('subscription.name')
            ->map(function($group) {
                return $group->count();
            });
        
        return view('admin.dashboard', [
            'stats' => $stats,
            'recentBookings' => $recentBookings,
            'recentClients' => $recentClients,
            'popularWorkouts' => $popularWorkouts,
            'subscriptionsByType' => $subscriptionsByType
        ]);
    }

/**
 * Удаление (блокировка) пользователя
 */
public function deleteUser($id)
    {
        if ($id == auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Вы не можете заблокировать самого себя');
        }
        
        $user = User::findOrFail($id);
        
        if ($user->isOwner()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Вы не можете заблокировать владельца системы');
        }
        
        // Уведомление пользователю
        $this->notify->send(
            $user->id,
            "⚠️ Ваш аккаунт был заблокирован администратором. Для получения информации обратитесь в поддержку.",
            'warning',
            ['user_id' => $user->id]
        );
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', "Пользователь {$user->name} успешно заблокирован");
    }

/**
 * Полное удаление пользователя из БД (осторожно!)
 */
public function forceDeleteUser($id)
{
    // Только для владельца
    if (!auth()->user()->isOwner()) {
        abort(403, 'Только владелец может полностью удалять пользователей');
    }
    
    $user = User::withTrashed()->findOrFail($id);
    
    // Не даем удалить владельца (даже владельцу нельзя удалить самого себя)
    if ($user->isOwner()) {
        return redirect()->route('admin.users.index')
            ->with('error', 'Нельзя удалить владельца системы');
    }
    
    // Не даем удалить самого себя (дополнительная защита)
    if ($id == auth()->id()) {
        return redirect()->route('admin.users.index')
            ->with('error', 'Вы не можете удалить самого себя');
    }
    
    $userName = $user->name;
    $user->forceDelete();
    
    return redirect()->route('admin.users.index')
        ->with('success', "Пользователь {$userName} полностью удален из системы");
}

/**
 * Восстановление пользователя
 */
public function restoreUser($id)
{
    $user = User::withTrashed()->findOrFail($id);
    
    // Восстанавливать владельца может только владелец
    if ($user->isOwner() && !auth()->user()->isOwner()) {
        return redirect()->route('admin.users.index')
            ->with('error', 'Только владелец может восстанавливать владельца');
    }
    
    $user->restore();
    
    return redirect()->route('admin.users.index')
        ->with('success', "Пользователь {$user->name} успешно восстановлен");
}

    /**
     * Список пользователей
     */
    public function users(Request $request)
    {
        $query = User::with('role');
        
        // Поиск
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Фильтр по роли
        if ($request->filled('role')) {
            $query->whereHas('role', function($q) use ($request) {
                $q->where('name', $request->role);
            });
        }
        
        // Фильтр по статусу
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at')->whereNull('deleted_at');
            } elseif ($request->status === 'inactive') {
                $query->whereNull('email_verified_at')->orWhereNotNull('deleted_at');
            }
        }
        
        // Фильтр по дате
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(15);
        $roles = Role::all(); // Теперь класс Role найден
        
        return view('admin.users', compact('users', 'roles'));
    }

    /**
     * Форма создания пользователя
     */
    public function createUser()
    {
        $roles = Role::all();
        return view('admin.users-create', compact('roles'));
    }

/**
 * Сохранение нового пользователя
 */
public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
            'birth_date' => 'nullable|date',
            'qualification' => 'nullable|string|max:255',
            'specialization' => 'nullable|string|max:255',
            'health_info' => 'nullable|string',
        ]);
        
        $validated['password'] = Hash::make($validated['password']);
        
        if ($request->input('email_verified') == '1') {
            $validated['email_verified_at'] = now();
        }
        
        $healthInfo = $validated['health_info'] ?? null;
        unset($validated['health_info']);
        
        $qualification = $validated['qualification'] ?? null;
        $specialization = $validated['specialization'] ?? null;
        unset($validated['qualification']);
        unset($validated['specialization']);
        
        $user = User::create($validated);
        
        if ($request->role_id == 3) {
            $user->qualification = $qualification;
            $user->specialization = $specialization;
            $user->save();
        }
        
        if ($request->role_id == 4 && $healthInfo) {
            $user->notes = $healthInfo;
            $user->save();
        }
        
        // Уведомление новому пользователю
        $this->notify->send(
            $user->id,
            "🎉 Добро пожаловать в Stronghold Gym! Ваш аккаунт успешно создан.",
            'system',
            ['user_id' => $user->id]
        );
        
        return redirect()->route('admin.users.index')
            ->with('success', 'Пользователь успешно создан');
    }

/**
 * Просмотр профиля пользователя
 */
public function showUser($id)
{
    $user = User::with(['role', 'subscriptions'])
        ->withCount(['bookings as total_bookings', 
                     'bookings as attended_bookings' => function($q) {
                         $q->where('status', 'attended');
                     }])
        ->findOrFail($id);
    
    // Для тренера - дополнительные данные
    if ($user->role->name == 'trainer') {
        // Количество проведенных тренировок
        $user->trainings_count = Schedule::where('trainer_id', $user->id)
            ->where('status', 'completed')
            ->count();
        
        // Количество уникальных клиентов
        $user->clients_count = Booking::whereHas('schedule', function($q) use ($user) {
                $q->where('trainer_id', $user->id);
            })
            ->distinct('user_id')
            ->count('user_id');
        
        // Тренировки на сегодня
        $todayTrainings = Schedule::with(['workout', 'bookings'])
            ->withCount('bookings')
            ->where('trainer_id', $user->id)
            ->whereDate('date', Carbon::today())
            ->where('status', 'scheduled')
            ->orderBy('start_time')
            ->get();
        
        // Ближайшие тренировки тренера с количеством бронирований
        $upcomingTrainings = Schedule::with('workout')
            ->withCount('bookings')
            ->where('trainer_id', $user->id)
            ->where('date', '>', Carbon::today())
            ->where('status', 'scheduled')
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(5)
            ->get();
    }
    
    // Для клиента - получаем последние бронирования и абонементы
    if ($user->role->name == 'client') {
        $recentBookings = Booking::with(['schedule.workout', 'schedule.trainer'])
            ->where('user_id', $user->id)
            ->latest()
            ->limit(10)
            ->get();
        
        $subscriptions = UserSubscription::with('subscription')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        $attendanceStats = [
            'total' => $user->bookings()->count(),
            'attended' => $user->bookings()->where('status', 'attended')->count(),
            'missed' => $user->bookings()->where('status', 'missed')->count(),
            'cancelled' => $user->bookings()->where('status', 'cancelled')->count(),
        ];
        
        $attendanceRate = $attendanceStats['total'] > 0 
            ? round(($attendanceStats['attended'] / $attendanceStats['total']) * 100, 1) 
            : 0;
    }
    
    return view('admin.users-show', [
        'profile' => $user,
        'recentBookings' => $recentBookings ?? collect(),
        'subscriptions' => $subscriptions ?? collect(),
        'attendanceStats' => $attendanceStats ?? ['total' => 0, 'attended' => 0, 'missed' => 0, 'cancelled' => 0],
        'attendanceRate' => $attendanceRate ?? 0,
        'todayTrainings' => $todayTrainings ?? collect(),      // <-- НОВОЕ
        'upcomingTrainings' => $upcomingTrainings ?? collect()
    ]);
}
 
/**
 * Форма редактирования пользователя
 */
public function editUser($id)
{
    $user = User::findOrFail($id);
    $roles = Role::all();
    
    // Не даем редактировать владельца обычным админам
    if ($user->role->name == 'owner' && !auth()->user()->isOwner()) {
        abort(403, 'Только владелец может редактировать владельца');
    }
    
    return view('admin.users-edit', [
        'user' => $user,
        'roles' => $roles
    ]);
}

/**
 * Обновление пользователя
 */
public function updateUser(Request $request, $id)
{
    $user = User::findOrFail($id);
    
    // Не даем редактировать владельца обычным админам
    if ($user->role->name == 'owner' && !auth()->user()->isOwner()) {
        abort(403, 'Только владелец может редактировать владельца');
    }
    
    $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email,' . $id,
        'phone' => 'nullable|string|max:20',
        'role_id' => 'required|exists:roles,id',
        'birth_date' => 'nullable|date',
        'qualification' => 'nullable|string|max:255',
        'specialization' => 'nullable|string|max:255',
        'notes' => 'nullable|string',
        'is_active' => 'boolean', // ДОБАВЛЕНО
    ];
    
    if ($request->filled('password')) {
        $rules['password'] = 'required|string|min:8|confirmed';
    }
    
    $validated = $request->validate($rules);
    
    if ($request->filled('password')) {
        $validated['password'] = Hash::make($validated['password']);
    }
    
    // Явно устанавливаем is_active из чекбокса
    $validated['is_active'] = $request->has('is_active');
    
    $user->update($validated);
    
    return redirect()->route('admin.users.index')
        ->with('success', 'Пользователь успешно обновлен');
}

/**
 * Отметка посещаемости клиента (для обычных абонементов)
 */
public function markClientAttendance(Request $request, $clientId){
    {
        $admin = auth()->user();
        $client = User::findOrFail($clientId);
        
        if (!$client->isClient()) {
            return back()->with('error', 'Пользователь не является клиентом');
        }
        
        $validated = $request->validate([
            'date' => 'required|date',
            'workout_name' => 'nullable|string|max:255',
            'comment' => 'nullable|string',
        ]);
        
        $activeSubscription = $client->activeSubscription();
        
        if (!$activeSubscription) {
            return back()->with('error', 'У клиента нет активного абонемента');
        }
        
        if ($activeSubscription->remaining_workouts <= 0 && $activeSubscription->subscription->workouts_count > 0) {
            return back()->with('error', 'У клиента закончились тренировки в абонементе');
        }
        
        Attendance::create([
            'booking_id' => null,
            'user_id' => $client->id,
            'marked_by' => $admin->id,
            'attended_at' => $validated['date'],
            'comment' => $validated['comment'] ?? null,
            'attendance_type' => 'attended'
        ]);
        
        if ($activeSubscription->subscription->workouts_count > 0) {
            $activeSubscription->decrement('remaining_workouts');
            if ($activeSubscription->remaining_workouts <= 0) {
                $activeSubscription->status = UserSubscription::STATUS_EXPIRED;
                $activeSubscription->save();
            }
        }
        
        $this->notify->send(
            $client->id,
            "✅ Администратор отметил ваше посещение {$validated['date']}. Осталось тренировок: " . ($activeSubscription->remaining_workouts ?? 0),
            'attendance',
            ['date' => $validated['date'], 'workout' => $validated['workout_name'] ?? 'Тренировка']
        );
        
        $remainingText = $activeSubscription->subscription->workouts_count > 0 
            ? "Осталось тренировок: " . ($activeSubscription->remaining_workouts ?? 0)
            : "Безлимитный абонемент";
        
        return back()->with('success', "Посещение клиента {$client->name} отмечено. " . $remainingText);
    }
}

/**
 * Страница для отметки посещаемости клиентов
 */
public function attendance()
{
    // Получаем всех клиентов через scope
    $clients = User::clients()->orderBy('name')->get();
    
    // Вручную загружаем активный абонемент для каждого клиента
    foreach ($clients as $client) {
        $client->activeSubscription = $client->activeSubscription();
    }
    
    return view('admin.attendance', [
        'clients' => $clients
    ]);
}

/**
 * Получить информацию о клиенте для быстрой отметки (AJAX)
 */
public function getClientInfo($clientId)
{
    $client = User::findOrFail($clientId);
    
    if (!$client->isClient()) {
        return response()->json(['error' => 'Не клиент'], 404);
    }
    
    $activeSubscription = $client->activeSubscription();
    
    return response()->json([
        'id' => $client->id,
        'name' => $client->name,
        'has_subscription' => $activeSubscription !== null,
        'subscription_name' => $activeSubscription?->subscription->name ?? null,
        'remaining_workouts' => $activeSubscription?->remaining_workouts ?? 0,
        'is_unlimited' => $activeSubscription && $activeSubscription->subscription->workouts_count == 0,
        'end_date' => $activeSubscription?->end_date?->format('d.m.Y'),
        'phone' => $client->phone
    ]);
}

/**
 * Управление расписанием
 */
public function schedule(Request $request)
{
    $date = $request->get('date', now()->toDateString());
    $workoutId = $request->get('workout_id');
    $trainerId = $request->get('trainer_id');
    
    $query = Schedule::with(['workout', 'trainer', 'bookings.user']);
    
    // Фильтр по дате
    if ($date) {
        $query->whereDate('date', $date);
    }
    
    // Фильтр по тренировке
    if ($workoutId) {
        $query->where('workout_id', $workoutId);
    }
    
    // Фильтр по тренеру
    if ($trainerId) {
        $query->where('trainer_id', $trainerId);
    }
    
    $schedules = $query->orderBy('start_time')->get();
    
    // Для фильтров
    $workouts = Workout::where('is_active', true)->get();
    $trainers = User::whereHas('role', function($q) {
        $q->where('name', 'trainer');
    })->get();
    
    return view('admin.schedule', [
        'schedules' => $schedules,
        'workouts' => $workouts,
        'trainers' => $trainers,
        'selectedDate' => $date,
        'selectedWorkout' => $workoutId,
        'selectedTrainer' => $trainerId
    ]);
}

/**
 * Создание нового занятия в расписании
 */
public function createSchedule()
{
    $workouts = Workout::where('is_active', true)->get();
    $trainers = User::whereHas('role', function($q) {
        $q->where('name', 'trainer');
    })->get();
    
    return view('admin.schedule-create', [
        'workouts' => $workouts,
        'trainers' => $trainers
    ]);
}

/**
     * Сохранение нового занятия
     */
    public function storeSchedule(Request $request)
    {
        $validated = $request->validate([
            'workout_id' => 'required|exists:workouts,id',
            'trainer_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'room' => 'nullable|string|max:255',
            'capacity' => 'required|integer|min:1|max:100',
        ]);
        
        $schedule = Schedule::create([
            'workout_id' => $validated['workout_id'],
            'trainer_id' => $validated['trainer_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'room' => $validated['room'] ?? null,
            'capacity' => $validated['capacity'],
            'status' => 'scheduled',
            'current_participants' => 0,
        ]);
        
        // Уведомление тренеру через сервис
        $this->notify->send(
            $validated['trainer_id'],
            "Администратор добавил вам тренировку '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
            'schedule',
            ['schedule_id' => $schedule->id]
        );
        
        // ВЫЗЫВАЕМ СОБЫТИЕ СОЗДАНИЯ ТРЕНИРОВКИ (для уведомления админов)
        event(new ScheduleCreated($schedule));
        
        return redirect()->route('admin.schedule.index')
            ->with('success', 'Занятие успешно добавлено в расписание');
}

/**
 * Редактирование занятия
 */
public function editSchedule($id)
{
    $schedule = Schedule::findOrFail($id);
    $workouts = Workout::where('is_active', true)->get();
    $trainers = User::whereHas('role', function($q) {
        $q->where('name', 'trainer');
    })->get();
    
    return view('admin.schedule-edit', [
        'schedule' => $schedule,
        'workouts' => $workouts,
        'trainers' => $trainers
    ]);
}

/**
 * Обновление занятия
 */
public function updateSchedule(Request $request, $id)
{
    $schedule = Schedule::findOrFail($id);
    
    $validated = $request->validate([
        'workout_id' => 'required|exists:workouts,id',
        'trainer_id' => 'required|exists:users,id',
        'date' => 'required|date',
        'start_time' => 'required',
        'end_time' => 'required|after:start_time',
        'room' => 'nullable|string|max:255',
        'capacity' => 'required|integer|min:1|max:100',
        'status' => 'required|in:scheduled,cancelled,completed',
    ]);
    
    $schedule->update($validated);
    
    return redirect()->route('admin.schedule.index')
        ->with('success', 'Расписание обновлено');
}

/**
 * Удаление занятия
 */
public function deleteSchedule($id)
{
    $schedule = Schedule::findOrFail($id);
    
    // Проверяем, есть ли бронирования
    $bookingsCount = $schedule->bookings()->count();
    
    if ($bookingsCount > 0) {
        return back()->with('error', 
            "Нельзя удалить занятие, на которое есть записи (всего: $bookingsCount). Сначала отмените все бронирования.");
    }
    
    $schedule->delete();
    
    return redirect()->route('admin.schedule.index')
        ->with('success', 'Занятие удалено из расписания');
}

/**
 * Отмена занятия (мягкое удаление)
 */
public function cancelSchedule($id)
{
    $schedule = Schedule::findOrFail($id);
    
    $schedule->status = 'cancelled';
    $schedule->save();
    
    $notifiedCount = 0;
    
    foreach ($schedule->bookings()->where('status', 'booked')->get() as $booking) {
        $booking->status = 'cancelled';
        $booking->cancelled_at = now();
        $booking->save();
        
        $activeSubscription = UserSubscription::where('user_id', $booking->user_id)
            ->where('status', 'active')
            ->first();
        if ($activeSubscription) {
            $activeSubscription->increment('remaining_workouts');
        }
        
        $schedule->decrement('current_participants');
        
        $this->notify->send(
            $booking->user_id,
            "❌ Занятие '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time} отменено администратором. Тренировка возвращена в абонемент.",
            'system',
            ['schedule_id' => $schedule->id]
        );
        
        $notifiedCount++;
    }
    
    $this->notify->send(
        $schedule->trainer_id,
        "⚠️ Администратор отменил вашу тренировку '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time}",
        'schedule',
        ['schedule_id' => $schedule->id]
    );
    
    // ВЫЗЫВАЕМ СОБЫТИЕ ОТМЕНЫ ТРЕНИРОВКИ
    event(new \App\Events\ScheduleDeleted($schedule, auth()->user()));
    
    return redirect()->route('admin.schedule.index')
        ->with('success', "Занятие отменено, оповещено {$notifiedCount} клиентов");
}

/**
 * Восстановление отмененного занятия
 */
public function restoreSchedule($id)
{
    $schedule = Schedule::findOrFail($id);
    
    // Сбрасываем счетчик участников на 0 (все места свободны)
    $schedule->current_participants = 0;
    $schedule->status = 'scheduled';
    $schedule->save();
    
    // Получаем все отмененные бронирования этого занятия
    $cancelledBookings = $schedule->bookings()
        ->where('status', 'cancelled')
        ->get();
    
    $restoredCount = 0;
    
    foreach ($cancelledBookings as $booking) {
        // Возвращаем тренировку в абонемент (если она была списана)
        $activeSubscription = UserSubscription::where('user_id', $booking->user_id)
            ->where('status', 'active')
            ->first();
        if ($activeSubscription) {
            $activeSubscription->increment('remaining_workouts');
        }
        
        // Удаляем бронирование (оно больше не актуально)
        $booking->delete();
        $restoredCount++;
        
        // Уведомляем клиента, что занятие восстановлено и нужно записаться заново
        \App\Models\Notification::create([
            'user_id' => $booking->user_id,
            'type' => 'system',
            'message' => "Занятие '{$schedule->workout->name}' на {$schedule->date->format('d.m.Y')} в {$schedule->start_time} было восстановлено. Вы можете записаться заново через расписание.",
            'is_read' => false,
            'data' => json_encode([
                'schedule_id' => $schedule->id,
                'workout_name' => $schedule->workout->name,
                'date' => $schedule->date->format('d.m.Y'),
                'time' => $schedule->start_time,
                'restored_by' => 'admin',
                'requires_new_booking' => true
            ])
        ]);
    }
    
    $message = "Занятие восстановлено. Свободных мест: {$schedule->capacity()}. ";
    if ($restoredCount > 0) {
        $message .= "Отмененные бронирования ({$restoredCount}) удалены. Клиенты уведомлены о необходимости повторной записи.";
    } else {
        $message .= "Не было отмененных бронирований.";
    }
    
    return redirect()->route('admin.schedule.index')
        ->with('success', $message);
}

/**
 * Управление абонементами
 */
public function subscriptions(Request $request)
{
    $query = Subscription::query();
    
    // Поиск
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }
    
    // Фильтр по статусу
    if ($request->filled('status')) {
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }
    }
    
    // Сортировка по умолчанию - по ID (новые сверху)
    $subscriptions = $query->orderBy('id', 'desc')->paginate(10);
    
    return view('admin.subscriptions', compact('subscriptions'));
}

/**
 * Форма создания абонемента
 */
public function createSubscription()
{
    return view('admin.subscriptions-create');
}

/**
 * Сохранение нового абонемента
 */
public function storeSubscription(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'duration_days' => 'required|integer|min:1',
        'workouts_count' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
        'type' => 'required|in:time,count',
        'is_active' => 'boolean',
    ]);
    
    $validated['is_active'] = $request->has('is_active');
    
    Subscription::create($validated);
    
    return redirect()->route('admin.subscriptions.index')
        ->with('success', 'Абонемент успешно создан');
}

/**
 * Форма редактирования абонемента
 */
public function editSubscription($id)
{
    $subscription = Subscription::findOrFail($id);
    return view('admin.subscriptions-edit', compact('subscription'));
}

/**
 * Обновление абонемента
 */
public function updateSubscription(Request $request, $id)
{
    $subscription = Subscription::findOrFail($id);
    
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'duration_days' => 'required|integer|min:1',
        'workouts_count' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
        'type' => 'required|in:time,count',
        'is_active' => 'boolean',
    ]);
    
    $validated['is_active'] = $request->has('is_active');
    
    $subscription->update($validated);
    
    return redirect()->route('admin.subscriptions.index')
        ->with('success', 'Абонемент обновлен');
}

/**
 * Удаление абонемента
 */
public function deleteSubscription($id)
{
    $subscription = Subscription::findOrFail($id);
    
    // Проверяем, есть ли активные подписки на этот абонемент
    $activeSubscriptions = UserSubscription::where('subscription_id', $id)
        ->where('status', 'active')
        ->count();
    
    if ($activeSubscriptions > 0) {
        return back()->with('error', 'Нельзя удалить абонемент, на который есть активные подписки');
    }
    
    $subscription->delete();
    
    return redirect()->route('admin.subscriptions.index')
        ->with('success', 'Абонемент удален');
}

/**
 * Страница отчетов
 */
public function reports(Request $request)
{
    // Фильтры
    $period = $request->get('period', 'month');
    $dateFrom = $request->get('date_from');
    $dateTo = $request->get('date_to');
    
    // Устанавливаем даты в зависимости от периода
    if ($period === 'month') {
        $dateFrom = Carbon::now()->startOfMonth();
        $dateTo = Carbon::now()->endOfMonth();
    } elseif ($period === 'quarter') {
        $dateFrom = Carbon::now()->startOfQuarter();
        $dateTo = Carbon::now()->endOfQuarter();
    } elseif ($period === 'year') {
        $dateFrom = Carbon::now()->startOfYear();
        $dateTo = Carbon::now()->endOfYear();
    } elseif ($period === 'custom' && $dateFrom && $dateTo) {
        $dateFrom = Carbon::parse($dateFrom);
        $dateTo = Carbon::parse($dateTo);
    } else {
        $dateFrom = Carbon::now()->startOfMonth();
        $dateTo = Carbon::now()->endOfMonth();
    }
    
    // 1. Общая статистика
    $totalClients = User::clients()->count();
    $totalTrainers = User::trainers()->count();
    $totalWorkouts = Workout::count();
    
    // 2. Финансовая статистика
    $totalRevenue = UserSubscription::where('status', 'active')
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->with('subscription')
        ->get()
        ->sum(function($sub) {
            return $sub->subscription->price ?? 0;
        });
    
    $revenueBySubscription = UserSubscription::where('status', 'active')
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->with('subscription')
        ->get()
        ->groupBy('subscription.name')
        ->map(function($group) {
            return [
                'count' => $group->count(),
                'revenue' => $group->sum(function($item) {
                    return $item->subscription->price ?? 0;
                })
            ];
        });
    
    // 3. Статистика тренировок
    $totalTrainings = Schedule::whereBetween('date', [$dateFrom, $dateTo])->count();
    $completedTrainings = Schedule::where('status', 'completed')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->count();
    $cancelledTrainings = Schedule::where('status', 'cancelled')
        ->whereBetween('date', [$dateFrom, $dateTo])
        ->count();
    
    // 4. Статистика посещаемости
    $totalBookings = Booking::whereBetween('created_at', [$dateFrom, $dateTo])->count();
    $attendedCount = Booking::where('status', 'attended')
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->count();
    $missedCount = Booking::where('status', 'missed')
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->count();
    $cancelledCount = Booking::where('status', 'cancelled')
        ->whereBetween('created_at', [$dateFrom, $dateTo])
        ->count();
    
    $attendanceRate = $totalBookings > 0 
        ? round(($attendedCount / $totalBookings) * 100, 1) 
        : 0;
    
    // 5. Популярные тренировки
    $popularWorkouts = Workout::withCount(['schedules' => function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('date', [$dateFrom, $dateTo]);
        }])
        ->orderBy('schedules_count', 'desc')
        ->limit(5)
        ->get();
    
    // 6. Топ тренеров
    $topTrainers = User::trainers()
        ->withCount(['trainings as trainings_count' => function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('date', [$dateFrom, $dateTo]);
        }])
        ->orderBy('trainings_count', 'desc')
        ->limit(5)
        ->get();
    
    // 7. Топ клиентов
    $topClients = User::clients()
        ->withCount(['bookings as bookings_count' => function($q) use ($dateFrom, $dateTo) {
            $q->whereBetween('created_at', [$dateFrom, $dateTo]);
        }])
        ->orderBy('bookings_count', 'desc')
        ->limit(5)
        ->get();
    
    return view('admin.reports', [
        'period' => $period,
        'dateFrom' => $dateFrom->format('Y-m-d'),
        'dateTo' => $dateTo->format('Y-m-d'),
        'totalClients' => $totalClients,
        'totalTrainers' => $totalTrainers,
        'totalWorkouts' => $totalWorkouts,
        'totalRevenue' => $totalRevenue,
        'revenueBySubscription' => $revenueBySubscription,
        'totalTrainings' => $totalTrainings,
        'completedTrainings' => $completedTrainings,
        'cancelledTrainings' => $cancelledTrainings,
        'totalBookings' => $totalBookings,
        'attendedCount' => $attendedCount,
        'missedCount' => $missedCount,
        'cancelledCount' => $cancelledCount,
        'attendanceRate' => $attendanceRate,
        'popularWorkouts' => $popularWorkouts,
        'topTrainers' => $topTrainers,
        'topClients' => $topClients,
    ]);
}

/**
 * Простой возврат тренировки в абонемент
 */
public function refundTraining(Request $request, $clientId)
{
    $admin = auth()->user();
    $client = User::findOrFail($clientId);
    
    if (!$client->isClient()) {
        return back()->with('error', 'Пользователь не является клиентом');
    }
    
    $validated = $request->validate([
        'reason' => 'nullable|string|max:500',
    ]);
    
    // Получаем активный абонемент клиента
    $activeSubscription = $client->activeSubscription();
    
    if (!$activeSubscription) {
        return back()->with('error', 'У клиента нет активного абонемента');
    }
    
    // Просто добавляем одну тренировку обратно в абонемент
    $activeSubscription->increment('remaining_workouts');
    
    // Создаем запись о возврате (опционально)
    $refundNote = "Возврат тренировки администратором {$admin->name}. Причина: " . ($validated['reason'] ?? 'Не указана');
    
    // Можно создать уведомление или запись в лог
    \App\Models\Notification::create([
        'user_id' => $client->id,
        'type' => 'attendance',
        'message' => "🔄 Администратор вернул одну тренировку в ваш абонемент. Причина: " . ($validated['reason'] ?? 'Ошибка учета'),
        'is_read' => false,
        'data' => json_encode([
            'refunded_by' => $admin->id,
            'refunded_at' => now()->format('d.m.Y H:i'),
            'reason' => $validated['reason'] ?? null,
            'new_balance' => $activeSubscription->remaining_workouts
        ])
    ]);
    
    // Уведомление админам
    $this->notify->notifyAdmins(
        "🔄 Администратор {$admin->name} вернул тренировку клиенту {$client->name}. Теперь у клиента {$activeSubscription->remaining_workouts} тренировок в абонементе",
        'attendance',
        ['client_id' => $client->id, 'admin_id' => $admin->id]
    );
    
    $remainingText = $activeSubscription->subscription->workouts_count > 0 
        ? "Теперь у клиента {$activeSubscription->remaining_workouts} тренировок в абонементе"
        : "Безлимитный абонемент";
    
    return back()->with('success', "Тренировка успешно возвращена! " . $remainingText);
}

/**
 * Получить список списанных тренировок клиента (AJAX)
 */
public function getClientAttendanceHistory($clientId)
{
    $client = User::findOrFail($clientId);
    
    if (!$client->isClient()) {
        return response()->json(['error' => 'Не клиент'], 404);
    }
    
    $attendances = Attendance::where('user_id', $client->id)
        ->whereNotNull('booking_id')
        ->with(['booking.schedule.workout'])
        ->orderBy('attended_at', 'desc')
        ->get()
        ->map(function($attendance) {
            return [
                'id' => $attendance->id,
                'date' => $attendance->attended_at->format('Y-m-d'),
                'date_formatted' => $attendance->attended_at->format('d.m.Y'),
                'workout_name' => $attendance->booking->schedule->workout->name ?? 'Тренировка',
                'time' => substr($attendance->booking->schedule->start_time ?? '', 0, 5),
            ];
        });
    
    return response()->json([
        'success' => true,
        'attendances' => $attendances
    ]);
}

}