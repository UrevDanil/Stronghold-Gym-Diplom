<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Booking;
use App\Models\UserSubscription;
use App\Models\Schedule;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_clients' => User::clients()->count(),
            'total_trainers' => User::trainers()->count(),
            'active_subscriptions' => UserSubscription::where('status', 'active')->count(),
            'today_bookings' => Booking::whereHas('schedule', function($q) {
                $q->where('date', today());
            })->count(),
            'revenue_month' => UserSubscription::where('status', 'active')
                ->whereMonth('created_at', now()->month)
                ->sum('price'),
        ];
        
        $recentBookings = Booking::with(['user', 'schedule.workout'])
            ->latest()
            ->limit(10)
            ->get();
            
        $recentClients = User::clients()
            ->latest()
            ->limit(5)
            ->get();
        
        return view('admin.dashboard', compact('stats', 'recentBookings', 'recentClients'));
    }
    
    public function clients()
    {
        $clients = User::clients()
            ->with(['activeSubscription', 'bookings'])
            ->paginate(15);
            
        return view('admin.clients.index', compact('clients'));
    }
    
    public function showClient(User $client)
    {
        $client->load(['userSubscriptions.subscription', 'bookings.schedule.workout']);
        return view('admin.clients.show', compact('client'));
    }
    
    public function trainers()
    {
        $trainers = User::trainers()->paginate(15);
        return view('admin.trainers.index', compact('trainers'));
    }
    
    public function schedules()
    {
        $schedules = Schedule::with(['workout', 'trainer', 'bookings.user'])
            ->where('date', '>=', today())
            ->orderBy('date')
            ->orderBy('start_time')
            ->paginate(20);
            
        return view('admin.schedules.index', compact('schedules'));
    }
    
    public function bookings()
    {
        $bookings = Booking::with(['user', 'schedule.workout', 'schedule.trainer'])
            ->latest()
            ->paginate(20);
            
        return view('admin.bookings.index', compact('bookings'));
    }
    
    public function subscriptions()
    {
        $subscriptions = UserSubscription::with(['user', 'subscription'])
            ->latest()
            ->paginate(20);
            
        return view('admin.subscriptions.index', compact('subscriptions'));
    }
}