<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    Admin\DashboardController as AdminDashboardController,
    Client\DashboardController as ClientDashboardController,
    Trainer\DashboardController as TrainerDashboardController,
    ScheduleController,
    BookingController
};

Route::get('/', function () {
    return view('welcome');
});

// Публичные маршруты
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/services', 'service')->name('services');

// Главная страница
Route::get('/', function () {return view('home');})->name('home');

// Аутентификация (Breeze)
// require __DIR__.'/auth.php';

// После входа - общий dashboard
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


Route::middleware(['auth', 'role:client'])->prefix('client')->name('client.')->group(function () {
    Route::get('/dashboard', [ClientDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/schedule', [ClientDashboardController::class, 'schedule'])->name('schedule');
    Route::post('/schedule/{schedule}/book', [ClientDashboardController::class, 'book'])->name('schedule.book');
    Route::post('/bookings/{booking}/cancel', [ClientDashboardController::class, 'cancelBooking'])->name('bookings.cancel');
    Route::get('/subscriptions', [ClientDashboardController::class, 'subscriptions'])->name('subscriptions');
    Route::post('/subscriptions/{subscription}/purchase', [ClientDashboardController::class, 'purchaseSubscription'])->name('subscriptions.purchase');
});

// Тренер
Route::middleware(['auth', 'role:trainer'])->prefix('trainer')->name('trainer.')->group(function () {
    Route::get('/dashboard', [TrainerDashboardController::class, 'dashboard'])->name('dashboard');
    Route::get('/schedule', [TrainerDashboardController::class, 'schedule'])->name('schedule');
    Route::post('/schedule/{schedule}/attendance', [TrainerDashboardController::class, 'markAttendance'])->name('attendance.mark');
    Route::get('/clients', [TrainerDashboardController::class, 'clients'])->name('clients');
});

// Публичное расписание (для незарегистрированных)
Route::get('/public-schedule', [ScheduleController::class, 'public'])->name('schedule.public');

// Статические страницы
Route::view('/about', 'pages.about')->name('about');
Route::view('/services', 'pages.services')->name('services');
Route::view('/contact', 'pages.contact')->name('contact');