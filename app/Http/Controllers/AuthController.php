<?php

// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use App\Models\User;
use App\Events\NewClientRegistered; // ДОБАВИТЬ
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Показ формы входа
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Обработка входа
     */
public function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->remember)) {
        $request->session()->regenerate();
        
        $user = Auth::user();
        
        // ИСПРАВЛЕНО: проверяем is_active напрямую
        if (!$user->is_active) {  // ← УБРАЛИ property_exists
            Auth::logout();
            return back()->withErrors([
                'email' => 'Ваш аккаунт деактивирован. Обратитесь к администратору.',
            ])->onlyInput('email');
        }
        
        // Проверка на мягкое удаление
        if ($user->trashed()) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Ваш аккаунт заблокирован. Обратитесь к администратору.',
            ])->onlyInput('email');
        }
        
        // ДОБАВЬТЕ ДЛЯ ОТЛАДКИ:
        \Log::info('Успешный вход пользователя', [
            'id' => $user->id,
            'email' => $user->email,
            'is_active' => $user->is_active,
            'role' => $user->role->name ?? 'unknown'
        ]);
        
        // Перенаправление в зависимости от роли
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isTrainer()) {
            return redirect()->route('trainer.dashboard');
        } elseif ($user->isClient()) {
            return redirect()->route('client.dashboard');
        }
        
        return redirect()->intended('dashboard');
    }

    // ДОБАВЬТЕ ОТЛАДКУ:
    \Log::warning('Неудачная попытка входа', [
        'email' => $request->email,
        'remember' => $request->remember
    ]);

    return back()->withErrors([
        'email' => 'Неверные учетные данные.',
    ])->onlyInput('email');
}

    // Показ формы регистрации
    public function showRegister()
    {
        return view('auth.register');
    }

    // Обработка регистрации
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'required|string|max:20',
        ]); 

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Находим роль "клиент"
        $clientRole = \App\Models\Role::where('name', 'client')->first();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'role_id' => $clientRole->id,
            'is_active' => true, // ДОБАВИТЬ: по умолчанию активен
        ]);

        // ВЫЗЫВАЕМ СОБЫТИЕ НОВОГО КЛИЕНТА
        event(new NewClientRegistered($user));

        Auth::login($user);

        return redirect()->route('client.dashboard')
            ->with('success', 'Регистрация прошла успешно! Добро пожаловать!');
    }

    // Выход
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/');
    }
}