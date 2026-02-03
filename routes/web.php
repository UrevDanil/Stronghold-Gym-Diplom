<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Публичные маршруты
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/services', 'service')->name('services');

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

// Аутентификация
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Защищенные маршруты (требуют авторизации)
Route::middleware(['auth'])->group(function () {
    
    // Для клиентов
    Route::middleware(['role:client'])->prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('dashboard');
        Route::get('/schedule', [ClientController::class, 'schedule'])->name('schedule');
        Route::post('/book/{schedule}', [ClientController::class, 'book'])->name('book');
        Route::get('/my-bookings', [ClientController::class, 'myBookings'])->name('bookings');
        Route::get('/my-subscriptions', [ClientController::class, 'mySubscriptions'])->name('subscriptions');
    });
    
    // Для тренеров
    Route::middleware(['role:trainer'])->prefix('trainer')->name('trainer.')->group(function () {
        Route::get('/dashboard', [TrainerController::class, 'dashboard'])->name('dashboard');
        Route::get('/my-schedule', [TrainerController::class, 'mySchedule'])->name('schedule');
        Route::get('/attendance/{schedule}', [TrainerController::class, 'attendance'])->name('attendance');
        Route::post('/mark-attendance', [TrainerController::class, 'markAttendance'])->name('mark.attendance');
    });

    // Для администраторов
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::resource('/users', UserController::class);
        Route::resource('/workouts', WorkoutController::class);
        Route::resource('/schedules', ScheduleController::class);
        Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    });
});