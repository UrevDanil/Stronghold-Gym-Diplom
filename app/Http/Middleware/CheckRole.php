<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle($request, Closure $next, ...$roles) {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    if (!in_array(auth()->user()->role->name, $roles)) {
        abort(403, 'Доступ запрещен');
    }
    
    return $next($request);
}
}