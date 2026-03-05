<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserActive
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Проверка на активность
            if (property_exists($user, 'is_active') && !$user->is_active) {
                Auth::logout(); // РАЗЛОГИНИВАЕМ
                $request->session()->invalidate(); // Очищаем сессию
                $request->session()->regenerateToken(); // Новый CSRF токен
                
                return redirect()->route('login')
                    ->withErrors(['email' => 'Ваш аккаунт деактивирован. Обратитесь к администратору.']);
            }
            
            // Проверка на удаленного (заблокированного)
            if (method_exists($user, 'trashed') && $user->trashed()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')
                    ->withErrors(['email' => 'Ваш аккаунт заблокирован. Обратитесь к администратору.']);
            }
        }
        
        return $next($request);
    }
}