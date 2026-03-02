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
        'health_info' => 'nullable|string', // Будет сохранено в notes
    ]);
    
    // Хэшируем пароль
    $validated['password'] = Hash::make($validated['password']);
    
    if ($request->has('email_verified')) {
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
    
}