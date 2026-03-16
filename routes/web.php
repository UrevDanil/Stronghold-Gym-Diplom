<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Admin\DashboardController as AdminDashboardController,
    Client\DashboardController as ClientDashboardController,
    Trainer\DashboardController as TrainerDashboardController,
    AdminController,
    AuthController,
    BookingController,
    ClientController,
    Controller,
    ScheduleController,
    SubscriptionController,
    TrainerController,
    WorkoutController
};

// ====================
// ПУБЛИЧНЫЕ МАРШРУТЫ (без авторизации)
// ====================

// Главная страница
Route::get('/', function () {return view('home');})->name('home');

// Статические страницы
Route::get('/about', function () {return view('pages.about');})->name('about');
Route::get('/service', function () {return view('pages.service');})->name('service');
Route::get('/bodybuilding', function () {return view('pages.bodybuilding');})->name('bodybuilding');
Route::get('/crossfit', function () {return view('pages.crossfit');})->name('crossfit');
Route::get('/powerlifting', function () {return view('pages.powerlifting');})->name('powerlifting');
Route::get('/nutrition', function () {return view('pages.nutrition');})->name('nutrition');
Route::get('/contact', function () {return view('pages.contact');})->name('contact');

// Публичное расписание
Route::get('/public-schedule', [ScheduleController::class, 'public'])->name('schedule.public');

// ====================
// АУТЕНТИФИКАЦИЯ
// ====================

// AuthController
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ====================
// ОБЩИЙ DASHBOARD ПОСЛЕ ВХОДА
// ====================

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->isAdmin() || $user->isOwner()) {
        return redirect()->route('admin.dashboard');
    } elseif ($user->isClient()) {
        return redirect()->route('client.dashboard');
    } elseif ($user->isTrainer()) {
        return redirect()->route('trainer.dashboard');
    }
    
    return redirect('/');
})->middleware(['auth'])->name('dashboard');

// ====================
// АДМИНИСТРАТОР
// ====================

Route::middleware(['auth', 'active', 'role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {
    // Дашборд
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Управление пользователями
    Route::get('/users', [AdminDashboardController::class, 'users'])->name('users.index');
    Route::get('/users/create', [AdminDashboardController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminDashboardController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{id}', [AdminDashboardController::class, 'showUser'])->name('users.show');
    Route::get('/users/{id}/edit', [AdminDashboardController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [AdminDashboardController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [AdminDashboardController::class, 'deleteUser'])->name('users.delete');

    // Удаление пользователя
    Route::delete('/users/{id}', [AdminDashboardController::class, 'deleteUser'])->name('users.destroy');
    Route::delete('/users/{id}/delete', [AdminDashboardController::class, 'deleteUser'])->name('users.delete'); // Для обратной совместимости
    Route::post('/users/{id}/restore', [AdminDashboardController::class, 'restoreUser'])->name('users.restore');
    Route::delete('/users/{id}/force', [AdminDashboardController::class, 'forceDeleteUser'])->name('users.force-delete');

    Route::get('/clients', [AdminDashboardController::class, 'clients'])->name('clients');
    Route::get('/trainers', [AdminDashboardController::class, 'trainers'])->name('trainers');
    
    // Управление расписанием
    Route::get('/schedule', [AdminDashboardController::class, 'schedule'])->name('schedule.index');
    Route::get('/schedule/create', [AdminDashboardController::class, 'createSchedule'])->name('schedule.create');
    Route::post('/schedule', [AdminDashboardController::class, 'storeSchedule'])->name('schedule.store');
    Route::get('/schedule/{id}/edit', [AdminDashboardController::class, 'editSchedule'])->name('schedule.edit');
    Route::put('/schedule/{id}', [AdminDashboardController::class, 'updateSchedule'])->name('schedule.update');
    Route::delete('/schedule/{id}', [AdminDashboardController::class, 'deleteSchedule'])->name('schedule.delete');
    Route::post('/schedules/{id}/cancel', [AdminDashboardController::class, 'cancelSchedule'])->name('schedule.cancel');
    Route::post('/schedules/{id}/restore', [AdminDashboardController::class, 'restoreSchedule'])->name('schedule.restore');

    // Управление абонементами
    Route::get('/subscriptions', [AdminDashboardController::class, 'subscriptions'])->name('subscriptions.index');
    Route::get('/subscriptions/create', [AdminDashboardController::class, 'createSubscription'])->name('subscriptions.create');
    Route::post('/subscriptions', [AdminDashboardController::class, 'storeSubscription'])->name('subscriptions.store');
    Route::get('/subscriptions/{id}/edit', [AdminDashboardController::class, 'editSubscription'])->name('subscriptions.edit');
    Route::put('/subscriptions/{id}', [AdminDashboardController::class, 'updateSubscription'])->name('subscriptions.update');
    Route::delete('/subscriptions/{id}', [AdminDashboardController::class, 'deleteSubscription'])->name('subscriptions.delete');
    
    // Управление бронированиями
    Route::get('/bookings', [AdminDashboardController::class, 'bookings'])->name('bookings.index');
    Route::get('/bookings/{id}', [AdminDashboardController::class, 'showBooking'])->name('bookings.show');
    Route::post('/bookings/{id}/cancel', [AdminDashboardController::class, 'cancelBooking'])->name('bookings.cancel');
    Route::post('/bookings/{id}/mark-attended', [AdminDashboardController::class, 'markAttended'])->name('bookings.mark-attended');
    
    // Отчеты
    Route::get('/reports', [AdminDashboardController::class, 'reports'])->name('reports');
    Route::get('/reports/attendance', [AdminDashboardController::class, 'attendanceReport'])->name('reports.attendance');
    Route::get('/reports/financial', [AdminDashboardController::class, 'financialReport'])->name('reports.financial');
    Route::get('/reports/export', [AdminDashboardController::class, 'exportReports'])->name('reports.export');
    
    // Статистика
    Route::get('/statistics', [AdminDashboardController::class, 'statistics'])->name('statistics');
    
    // Настройки
    Route::get('/settings', [AdminDashboardController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminDashboardController::class, 'updateSettings'])->name('settings.update');
    
    // Для теста
    Route::get('/test', function () {return 'Панель администратора работает!';})->name('test');
});

// ====================
// ТРЕНЕР
// ====================

Route::middleware(['auth', 'active', 'role:trainer'])->prefix('trainer')->name('trainer.')->group(function () {
    // Основные страницы
    Route::get('/dashboard', [TrainerDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/schedule', [TrainerDashboardController::class, 'schedule'])->name('schedule');
    Route::get('/clients', [TrainerDashboardController::class, 'clients'])->name('clients');
    Route::get('/attendance', [TrainerDashboardController::class, 'attendance'])->name('attendance');
    
    // Детальная информация о клиенте
    Route::get('/clients/{id}', [TrainerDashboardController::class, 'clientDetails'])->name('client-details');
    
    // История клиента
    Route::get('/clients/{id}/history', [TrainerDashboardController::class, 'clientHistory'])->name('client.history');
    
    // Отметка посещаемости
    Route::post('/schedule/{schedule}/attendance', [TrainerDashboardController::class, 'markAttendance'])->name('attendance.mark');
    
    // Массовая отметка посещаемости
    Route::post('/attendance/bulk', [TrainerDashboardController::class, 'bulkAttendance'])->name('attendance.bulk');
    
    // Статистика тренировок
    Route::get('/statistics', [TrainerDashboardController::class, 'statistics'])->name('statistics');
    Route::get('/statistics/export', [TrainerDashboardController::class, 'exportStatistics'])->name('statistics.export');
    
    // Работа с расписанием
    Route::get('/schedule/week', [TrainerDashboardController::class, 'weekSchedule'])->name('schedule.week');
    Route::get('/schedule/month', [TrainerDashboardController::class, 'monthSchedule'])->name('schedule.month');
    
    // Заметки о клиентах
    Route::post('/clients/{id}/notes', [TrainerDashboardController::class, 'addClientNote'])->name('client.notes.add');
    Route::delete('/notes/{note}', [TrainerDashboardController::class, 'deleteNote'])->name('notes.delete');
    
    // Прогресс клиента
    Route::get('/clients/{id}/progress', [TrainerDashboardController::class, 'clientProgress'])->name('client.progress');
    Route::post('/clients/{id}/progress', [TrainerDashboardController::class, 'updateProgress'])->name('client.progress.update');
    
    // Профиль тренера
    Route::get('/profile', [TrainerDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [TrainerDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/qualification', [TrainerDashboardController::class, 'updateQualification'])->name('profile.qualification');
    
    // Уведомления
    Route::get('/notifications', [TrainerDashboardController::class, 'notifications'])->name('notifications');
    Route::post('/notifications/{id}/read', [TrainerDashboardController::class, 'markNotificationRead'])->name('notifications.read');
    
    // API для AJAX запросов (если нужны)
    Route::prefix('api')->name('api.')->group(function () {
        Route::get('/clients/search', [TrainerDashboardController::class, 'apiSearchClients'])->name('search-clients');
        Route::get('/schedule/today', [TrainerDashboardController::class, 'apiTodaySchedule'])->name('today-schedule');
        Route::get('/attendance/today', [TrainerDashboardController::class, 'apiTodayAttendance'])->name('today-attendance');
    });

    // Маршруты для посещаемости (доступны тренеру)
Route::middleware(['auth', 'role:trainer'])->group(function () {
    Route::get('/attendance', [TrainerDashboardController::class, 'attendance'])->name('attendance');
    Route::post('/attendance/mark', [TrainerDashboardController::class, 'markAttendance'])->name('attendance.mark.simple');
});
});

// ====================
// КЛИЕНТ
// ====================

    Route::middleware(['auth', 'active', 'role:client'])->prefix('client')->name('client.')->group(function () {
    // Дашборд
    Route::get('/dashboard', [ClientDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Профиль - разные URL для разных действий
    Route::get('/profile', [ClientDashboardController::class, 'profile'])->name('profile');
    Route::post('/profile', [ClientDashboardController::class, 'updateProfile'])->name('profile.update');
    Route::post('/password', [ClientDashboardController::class, 'updatePassword'])->name('password.update');
    
    // Расписание
    Route::get('/schedule', [ClientDashboardController::class, 'schedule'])->name('schedule');
    Route::post('/schedule/{schedule}/book', [ClientDashboardController::class, 'book'])->name('schedule.book');
    
    // Бронирования
    Route::post('/bookings/{booking}/cancel', [ClientDashboardController::class, 'cancelBooking'])->name('bookings.cancel');
    Route::delete('/bookings/{booking}/cancel', [ClientDashboardController::class, 'cancelBooking'])->name('bookings.cancel.delete');
    
    // Абонементы
    Route::get('/subscriptions', [ClientDashboardController::class, 'subscriptions'])->name('subscriptions');
    Route::post('/subscriptions/{subscription}/purchase', [ClientDashboardController::class, 'purchaseSubscription'])->name('subscriptions.purchase');
    Route::post('/subscriptions/resume', [ClientDashboardController::class, 'resumeSubscription'])->name('subscriptions.resume');
});

// Публичные маршруты
Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');
Route::post('/freeze-subscription', [ClientDashboardController::class, 'freezeSubscription'])->name('freeze-subscription');

// Аутентифицированные маршруты
Route::middleware(['auth'])->group(function () {
    // Покупка абонемента
    Route::post('/subscriptions/{subscription}/purchase', 
               [SubscriptionController::class, 'purchase'])
           ->name('subscriptions.purchase');
    
    // Продление абонемента
    Route::post('/subscriptions/{subscription}/renew', 
               [SubscriptionController::class, 'renew'])
           ->name('subscriptions.renew');
});

// Админ маршруты
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('subscriptions', SubscriptionController::class)->except(['show']);
        Route::get('/subscriptions', [AdminDashboardController::class, 'subscriptions'])->name('subscriptions.index');
    Route::get('/subscriptions/create', [AdminDashboardController::class, 'createSubscription'])->name('subscriptions.create');
    Route::post('/subscriptions', [AdminDashboardController::class, 'storeSubscription'])->name('subscriptions.store');
});

// Уведомления
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', function () {
        $notifications = App\Models\Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);
            
        return view('notifications.index', compact('notifications'));
    })->name('notifications');
    
    Route::post('/notifications/{id}/read', function ($id) {
        $notification = App\Models\Notification::findOrFail($id);
        if ($notification->user_id == auth()->id()) {
            $notification->markAsRead();
        }
        return back();
    })->name('notifications.read');
    
    Route::post('/notifications/read-all', function () {
        App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        return back()->with('success', 'Все уведомления отмечены как прочитанные');
    })->name('notifications.read-all');
});

// ====================
// ТЕСТОВЫЕ МАРШРУТЫ
// ====================

// Тест middleware role
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/test-admin', function () {
        return '✅ Middleware role работает! Вы администратор.';
    })->name('test.admin');
});

Route::middleware(['auth', 'role:client'])->group(function () {
    Route::get('/test-client', function () {
        return '✅ Middleware role работает! Вы клиент.';
    })->name('test.client');
});

Route::middleware(['auth', 'role:trainer'])->group(function () {
    Route::get('/test-trainer', function () {
        return '✅ Middleware role работает! Вы тренер.';
    })->name('test.trainer');
});

// Проверка авторизации
Route::middleware(['auth'])->group(function () {
    Route::get('/check-auth', function () {
        $user = auth()->user();
        return response()->json([
            'authenticated' => true,
            'user' => $user->name,
            'role' => $user->role->name,
            'email' => $user->email,
        ]);
    })->name('check.auth');
});