<!-- Панель администратора -->
@extends('layouts.app')

@section('title', 'Панель администратора')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/admin/dashboard.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 admin-dashboard-page">
    <!-- Заголовок -->
    <div class="dashboard-header">
        <h1 class="mb-0">
            <i class="fas fa-chalkboard-user me-3"></i>Панель администратора
        </h1>
        <div class="admin-badge">
            <i class="fas fa-user-shield me-2"></i>
            {{ auth()->user()->name }}
            <span class="ms-2">(Администратор)</span>
        </div>
    </div>

    @if(!auth()->user()->is_active)
        <div class="alert dashboard-alert warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Внимание!</strong> Ваш аккаунт деактивирован. Обратитесь к администратору.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Первая строка статистики -->
    <div class="stats-row">
        <div class="row g-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-left-primary">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-label">Клиенты</div>
                        <div class="stat-value">{{ $stats['total_clients'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-left-success">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="stat-label">Тренеры</div>
                        <div class="stat-value">{{ $stats['total_trainers'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-left-info">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="stat-label">Активные абонементы</div>
                        <div class="stat-value">{{ $stats['active_subscriptions'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-left-warning">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-ruble-sign"></i>
                        </div>
                        <div class="stat-label">Выручка за месяц</div>
                        <div class="stat-value">{{ number_format($stats['revenue_month'], 0, ',', ' ') }} ₽</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Вторая строка статистики -->
    <div class="stats-row">
        <div class="row g-4">
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-left-primary">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stat-label">Тренировок сегодня</div>
                        <div class="stat-value">{{ $stats['today_bookings'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-left-success">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <div class="stat-label">Всего тренировок</div>
                        <div class="stat-value">{{ $stats['total_workouts'] }}</div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="stat-card border-left-info">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-label">Завершенных тренировок</div>
                        <div class="stat-value">{{ $stats['completed_trainings'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Последние бронирования -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="card-header bg-primary">
                    <i class="fas fa-calendar-alt"></i> Последние бронирования
                </div>
                <div class="card-body">
                    @if($recentBookings->count() > 0)
                        <ul class="list-group-custom">
                            @foreach($recentBookings as $booking)
                                <li class="list-group-item-custom">
                                    <div>
                                        <div class="item-title">{{ $booking->user->name }}</div>
                                        <div class="item-subtitle">
                                            <i class="fas fa-dumbbell me-1"></i>{{ $booking->schedule->workout->name }} |
                                            <i class="fas fa-calendar-alt me-1 ms-1"></i>{{ \Carbon\Carbon::parse($booking->schedule->date)->format('d.m.Y') }}
                                        </div>
                                    </div>
                                    <div>
                                        <span class="item-badge {{ $booking->status }}">
                                            @if($booking->status == 'booked')
                                                <i class="fas fa-check-circle me-1"></i>Забронировано
                                            @elseif($booking->status == 'attended')
                                                <i class="fas fa-user-check me-1"></i>Посещено
                                            @elseif($booking->status == 'cancelled')
                                                <i class="fas fa-ban me-1"></i>Отменено
                                            @elseif($booking->status == 'missed')
                                                <i class="fas fa-times-circle me-1"></i>Пропущено
                                            @endif
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state-mini">
                            <i class="fas fa-calendar-times"></i>
                            <p>Нет бронирований</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Новые клиенты -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="card-header bg-success">
                    <i class="fas fa-user-plus"></i> Новые клиенты
                </div>
                <div class="card-body">
                    @if($recentClients->count() > 0)
                        <ul class="list-group-custom">
                            @foreach($recentClients as $client)
                                <li class="list-group-item-custom">
                                    <div>
                                        <div class="item-title">{{ $client->name }}</div>
                                        <div class="item-subtitle">
                                            <i class="fas fa-envelope me-1"></i>{{ $client->email }}
                                        </div>
                                    </div>
                                    <div class="item-date">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        {{ $client->created_at->format('d.m.Y') }}
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state-mini">
                            <i class="fas fa-user-friends"></i>
                            <p>Нет новых клиентов</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Популярные тренировки -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="card-header bg-info">
                    <i class="fas fa-chart-line"></i> Популярные тренировки
                </div>
                <div class="card-body">
                    @if($popularWorkouts->count() > 0)
                        <ul class="list-group-custom">
                            @foreach($popularWorkouts as $workout)
                                <li class="list-group-item-custom">
                                    <div>
                                        <div class="item-title">{{ $workout->name }}</div>
                                        @if($workout->description)
                                            <div class="item-subtitle">{{ Str::limit($workout->description, 50) }}</div>
                                        @endif
                                    </div>
                                    <div>
                                        <span class="item-badge booked">
                                            <i class="fas fa-calendar-alt me-1"></i>{{ $workout->schedules_count }} тренировок
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state-mini">
                            <i class="fas fa-dumbbell"></i>
                            <p>Нет данных</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Активные абонементы по типам -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="card-header bg-warning">
                    <i class="fas fa-chart-pie"></i> Активные абонементы
                </div>
                <div class="card-body">
                    @if($subscriptionsByType->count() > 0)
                        <ul class="list-group-custom">
                            @foreach($subscriptionsByType as $name => $count)
                                <li class="list-group-item-custom">
                                    <div class="item-title">{{ $name }}</div>
                                    <div>
                                        <span class="item-badge booked">
                                            <i class="fas fa-users me-1"></i>{{ $count }}
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="empty-state-mini">
                            <i class="fas fa-id-card"></i>
                            <p>Нет активных абонементов</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="quick-actions-card">
        <div class="card-header">
            <i class="fas fa-bolt me-2"></i>Быстрые действия
        </div>
        <div class="quick-actions-grid">
            <a href="{{ route('admin.users.index') }}" class="quick-action-btn primary">
                <i class="fas fa-users"></i> Управление пользователей
            </a>
            <a href="{{ route('admin.schedule.index') }}" class="quick-action-btn success">
                <i class="fas fa-calendar-alt"></i> Расписание
            </a>
            <a href="{{ route('admin.subscriptions.index') }}" class="quick-action-btn info">
                <i class="fas fa-id-card"></i> Абонементы
            </a>
            <a href="{{ route('admin.reports') }}" class="quick-action-btn warning">
                <i class="fas fa-chart-bar"></i> Отчеты
            </a>
            <a href="{{ route('admin.attendance') }}" class="quick-action-btn secondary">
                <i class="fas fa-clipboard-list"></i> Отметить посещаемость
            </a>
        </div>
    </div>
</div>
@endsection