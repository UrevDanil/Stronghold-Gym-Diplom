<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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

// Позже добавим динамические маршруты
// Route::get('/schedule', [ScheduleController::class, 'index'])->name('schedule');
Route::get('/check-files', function() {
    $files = [
        'bootstrap.css' => public_path('assets/css/bootstrap.css'),
        'style.css' => public_path('assets/css/style.css'),
        'jquery.js' => public_path('assets/js/jquery-3.4.1.min.js'),
        'hero-bg.jpg' => public_path('assets/images/hero-bg.jpg')
    ];
    
    echo '<h1>Проверка файлов</h1>';
    foreach($files as $name => $path) {
        echo "<p>$name: " . (file_exists($path) ? 
             '✅ СУЩЕСТВУЕТ (' . filesize($path) . ' байт)' : 
             '❌ НЕ НАЙДЕН') . "</p>";
    }
    
    echo '<h2>Прямые ссылки:</h2>';
    echo '<ul>';
    echo '<li><a href="/assets/css/bootstrap.css" target="_blank">/assets/css/bootstrap.css</a></li>';
    echo '<li><a href="/assets/css/style.css" target="_blank">/assets/css/style.css</a></li>';
    echo '<li><a href="/assets/images/hero-bg.jpg" target="_blank">/assets/images/hero-bg.jpg</a></li>';
    echo '</ul>';
});