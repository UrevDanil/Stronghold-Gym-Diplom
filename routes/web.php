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

Route::middleware(['auth', 'role:admin,owner'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'dashboard'])->name('dashboard');
    
    // Для теста
    Route::get('/test', function () {return 'Панель администратора работает!';});
});

// ====================
// ТРЕНЕР
// ====================

Route::middleware(['auth', 'role:trainer'])->prefix('trainer')->name('trainer.')->group(function () {
    Route::get('/dashboard', [TrainerDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/schedule', [TrainerDashboardController::class, 'schedule'])->name('schedule');
    Route::post('/schedule/{schedule}/attendance', [TrainerDashboardController::class, 'markAttendance'])->name('attendance.mark');
    Route::get('/clients', [TrainerDashboardController::class, 'clients'])->name('clients');
});

// ====================
// КЛИЕНТ
// ====================

Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
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
    
    // Абонементы
    Route::get('/subscriptions', [ClientDashboardController::class, 'subscriptions'])->name('subscriptions');
    Route::post('/subscriptions/{subscription}/purchase', [ClientDashboardController::class, 'purchaseSubscription'])->name('subscriptions.purchase');
});

// Публичные маршруты
Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
Route::get('/subscriptions/{subscription}', [SubscriptionController::class, 'show'])->name('subscriptions.show');

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