<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;  // <-- ДОБАВЬ ЭТУ СТРОКУ
use App\Models\Subscription;
use App\Models\UserSubscription;
use App\Models\Booking;
use App\Models\Schedule;
use App\Models\Workout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;  // <-- ДОБАВЬ ДЛЯ ХЭШИРОВАНИЯ ПАРОЛЕЙ
use Carbon\Carbon;

class DashboardController extends Controller
{
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
    // Не даем удалить самого себя
    if ($id == auth()->id()) {
        return redirect()->route('admin.users.index')
            ->with('error', 'Вы не можете заблокировать самого себя');
    }
    
    $user = User::findOrFail($id);
    
    // Мягкое удаление (поле deleted_at заполняется)
    $user->delete();
    
    return redirect()->route('admin.users.index')
        ->with('success', "Пользователь {$user->name} успешно заблокирован");
}

/**
 * Восстановление пользователя
 */
public function restoreUser($id)
{
    $user = User::withTrashed()->findOrFail($id);
    $user->restore();
    
    return redirect()->route('admin.users.index')
        ->with('success', "Пользователь {$user->name} успешно восстановлен");
}

/**
 * Полное удаление пользователя из БД (осторожно!)
 */
public function forceDeleteUser($id)
{
    // Только для админов с особыми правами
    if (!auth()->user()->isOwner()) {
        abort(403, 'Только владелец может полностью удалять пользователей');
    }
    
    $user = User::withTrashed()->findOrFail($id);
    $userName = $user->name;
    $user->forceDelete();
    
    return redirect()->route('admin.users.index')
        ->with('success', "Пользователь {$userName} полностью удален из системы");
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
    
    // Хэшируем пароль
    $validated['password'] = Hash::make($validated['password']);
    
    // ИСПРАВЛЕНО: проверяем чекбокс email_verified
if ($request->input('email_verified') == '1') {
    $validated['email_verified_at'] = now();
}
    
    // Сохраняем health_info в notes для клиентов
    $healthInfo = $validated['health_info'] ?? null;
    unset($validated['health_info']);
    
    // Сохраняем qualification и specialization для тренеров
    $qualification = $validated['qualification'] ?? null;
    $specialization = $validated['specialization'] ?? null;
    unset($validated['qualification']);
    unset($validated['specialization']);
    
    // Создаем пользователя
    $user = User::create($validated);
    
    // Для тренера - сохраняем квалификацию и специализацию
    if ($request->role_id == 3) { // ID роли тренера
        $user->qualification = $qualification;
        $user->specialization = $specialization;
        $user->save();
    }
    
    // Для клиента - сохраняем информацию о здоровье в notes
    if ($request->role_id == 4 && $healthInfo) { // ID роли клиента
        $user->notes = $healthInfo;
        $user->save();
    }
    
    return redirect()->route('admin.users.index')
        ->with('success', 'Пользователь успешно создан');
}

/**
 * Просмотр профиля пользователя
 */
public function showUser($id)
{
    $user = User::with(['role', 'subscriptions.subscription'])
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
        
        // Ближайшие тренировки тренера
        $upcomingTrainings = Schedule::with('workout')
            ->where('trainer_id', $user->id)
            ->where('date', '>=', now())
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
        // Убираем capacity и notes - их нет в таблице
    ]);
    
    Schedule::create([
        'workout_id' => $validated['workout_id'],
        'trainer_id' => $validated['trainer_id'],
        'date' => $validated['date'],
        'start_time' => $validated['start_time'],
        'end_time' => $validated['end_time'],
        'room' => $validated['room'] ?? null,
        'status' => 'scheduled',
        'current_participants' => 0,
    ]);
    
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
    if ($schedule->bookings()->where('status', '!=', 'cancelled')->count() > 0) {
        return back()->with('error', 'Нельзя удалить занятие, на которое есть записи');
    }
    
    $schedule->delete();
    
    return redirect()->route('admin.schedule.index')
        ->with('success', 'Занятие удалено из расписания');
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

}