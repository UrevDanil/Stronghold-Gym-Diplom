<!-- Панель администратора -->
 @extends('layouts.app')

@section('title', 'Панель администратора')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Панель администратора</h1>
        <div>
            <span class="text-muted me-3">
                <i class="fas fa-user me-2"></i>{{ auth()->user()->name }}
            </span>
            <span class="badge bg-primary">Администратор</span>
        </div>
    </div>

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Клиенты</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_clients'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Тренеры</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_trainers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-tie fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Активные абонементы</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_subscriptions'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-id-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Выручка за месяц</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['revenue_month'], 0, ',', ' ') }} ₽</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ruble-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Вторая строка статистики -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Тренировок сегодня</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today_bookings'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Всего тренировок</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_workouts'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dumbbell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Завершенных тренировок</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['completed_trainings'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Последние бронирования -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Последние бронирования</h5>
                </div>
                <div class="card-body">
                    @if($recentBookings->count() > 0)
                        <div class="list-group">
                            @foreach($recentBookings as $booking)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $booking->user->name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $booking->schedule->workout->name }} | 
                                                {{ \Carbon\Carbon::parse($booking->schedule->date)->format('d.m.Y') }}
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $booking->status === 'booked' ? 'success' : 'secondary' }}">
                                            {{ $booking->status }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">Нет бронирований</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Новые клиенты -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Новые клиенты</h5>
                </div>
                <div class="card-body">
                    @if($recentClients->count() > 0)
                        <div class="list-group">
                            @foreach($recentClients as $client)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $client->name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-envelope me-1"></i>{{ $client->email }}
                                            </small>
                                        </div>
                                        <small class="text-muted">
                                            {{ $client->created_at->format('d.m.Y') }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">Нет новых клиентов</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Популярные тренировки -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Популярные тренировки</h5>
                </div>
                <div class="card-body">
                    @if($popularWorkouts->count() > 0)
                        <div class="list-group">
                            @foreach($popularWorkouts as $workout)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $workout->name }}</strong>
                                            @if($workout->description)
                                                <br>
                                                <small class="text-muted">{{ $workout->description }}</small>
                                            @endif
                                        </div>
                                        <span class="badge bg-primary">{{ $workout->schedules_count }} тренировок</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">Нет данных</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Абонементы по типам -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">Активные абонементы</h5>
                </div>
                <div class="card-body">
                    @if($subscriptionsByType->count() > 0)
                        <div class="list-group">
                            @foreach($subscriptionsByType as $name => $count)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>{{ $name }}</span>
                                        <span class="badge bg-success">{{ $count }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center py-3">Нет активных абонементов</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Быстрые действия</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-users me-2"></i>Управление пользователями
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.schedule.index') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-calendar-alt me-2"></i>Расписание
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-id-card me-2"></i>Абонементы
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.reports') }}" class="btn btn-outline-warning w-100">
                                <i class="fas fa-chart-bar me-2"></i>Отчеты
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .border-left-primary {
        border-left: 4px solid #4e73df;
    }
    .border-left-success {
        border-left: 4px solid #1cc88a;
    }
    .border-left-info {
        border-left: 4px solid #36b9cc;
    }
    .border-left-warning {
        border-left: 4px solid #f6c23e;
    }
    .text-gray-300 {
        color: #dddfeb;
    }
    .text-gray-800 {
        color: #5a5c69;
    }
</style>
@endsection