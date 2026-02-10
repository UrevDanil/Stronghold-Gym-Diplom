<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of the subscriptions.
     */
    public function index()
    {
        $subscriptions = Subscription::where('is_active', true)
            ->orderBy('price')
            ->get();
            
        return view('subscriptions.index', compact('subscriptions'));
    }

    /**
     * Purchase a subscription.
     */
    public function purchase(Subscription $subscription, Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'Для покупки абонемента необходимо войти в систему');
        }
        
        if (!$user->isClient()) {
            return back()->with('error', 'Только клиенты могут приобретать абонементы');
        }
        
        // Проверяем активный абонемент
        if ($user->hasActiveSubscription()) {
            return back()->with('warning', 'У вас уже есть активный абонемент');
        }
        
        // Прикрепляем абонемент к пользователю
        $user->subscriptions()->attach($subscription->id, [
            'start_date' => now(),
            'end_date' => now()->addDays($subscription->duration_days),
            'remaining_workouts' => $subscription->workouts_count,
            'status' => 'active',
            'activated_by' => $user->id,
            'activated_at' => now(),
        ]);
        
        return redirect()->route('client.dashboard')
            ->with('success', "Абонемент '{$subscription->name}' успешно приобретен!");
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Только для администраторов
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        return view('admin.subscriptions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'workouts_count' => 'required|integer|min:1',
            'type' => 'required|in:time,count',
            'is_active' => 'boolean',
        ]);
        
        Subscription::create($validated);
        
        return redirect()->route('admin.subscriptions')
            ->with('success', 'Абонемент успешно создан');
    }

    /**
     * Display the specified resource.
     */
    public function show(Subscription $subscription)
    {
        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subscription $subscription)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        return view('admin.subscriptions.edit', compact('subscription'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subscription $subscription)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'required|integer|min:1',
            'workouts_count' => 'required|integer|min:1',
            'type' => 'required|in:time,count',
            'is_active' => 'boolean',
        ]);
        
        $subscription->update($validated);
        
        return redirect()->route('admin.subscriptions')
            ->with('success', 'Абонемент успешно обновлен');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subscription $subscription)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }
        
        $subscription->delete();
        
        return redirect()->route('admin.subscriptions')
            ->with('success', 'Абонемент успешно удален');
    }

    /**
     * Renew subscription.
     */
    public function renew(Subscription $subscription)
    {
        $user = Auth::user();
        
        if (!$user || !$user->isClient()) {
            abort(403);
        }
        
        // Деактивируем старый активный абонемент
        $activeSubscription = $user->activeSubscription();
        if ($activeSubscription) {
            $user->subscriptions()
                ->updateExistingPivot($activeSubscription->id, [
                    'status' => 'expired'
                ]);
        }
        
        // Создаем новый
        $user->subscriptions()->attach($subscription->id, [
            'start_date' => now(),
            'end_date' => now()->addDays($subscription->duration_days),
            'remaining_workouts' => $subscription->workouts_count,
            'status' => 'active',
            'activated_by' => $user->id,
            'activated_at' => now(),
        ]);
        
        return redirect()->route('client.dashboard')
            ->with('success', "Абонемент '{$subscription->name}' успешно продлен!");
    }
}