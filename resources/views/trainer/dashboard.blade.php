<!-- Кабинет тренера -->
@extends('layouts.app')

@section('title', 'Панель тренера')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/trainer/trainer-dashboard.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4 trainer-dashboard">
    <!-- Заголовок -->
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-chalkboard-user me-3"></i>Панель тренера
        </h1>
        <div class="trainer-badge">
            <i class="fas fa-user-check"></i>
            {{ $user->name }}
            @if($user->qualification)
                <span class="ms-1">({{ $user->qualification }})</span>
            @endif
        </div>
    </div>

    @if(!auth()->user()->is_active)
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Внимание!</strong> Ваш аккаунт деактивирован. Обратитесь к администратору.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Статистика -->
    <div class="stats-row">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="stat-card bg-primary">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-dumbbell"></i>
                        </div>
                        <div class="stat-label">Всего тренировок</div>
                        <div class="stat-value">{{ $totalTrainings }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card bg-info">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-label">Клиентов</div>
                        <div class="stat-value">{{ $uniqueClients }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card bg-warning">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-calendar-day"></i>
                        </div>
                        <div class="stat-label">Сегодня</div>
                        <div class="stat-value">{{ $todaySchedules->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="stat-card bg-success">
                    <div class="card-body">
                        <div class="stat-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-label">Посещений</div>
                        <div class="stat-value">{{ $totalAttendances }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Сегодняшние тренировки -->
        <div class="col-md-6">
            <div class="section-card">
                <div class="card-header">
                    <i class="fas fa-calendar-day me-2"></i>Тренировки на сегодня
                </div>
                <div class="card-body">
                    @if($todaySchedules->count() > 0)
                        @foreach($todaySchedules as $schedule)
                            <div class="workout-item-card">
                                <div class="card-body">
                                    <div class="workout-time {{ $schedule->isPast() ? 'past' : '' }}">
                                        <i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                    </div>
                                    <div class="workout-name">{{ $schedule->workout->name }}</div>
                                    <div class="workout-room">
                                        <i class="fas fa-door-open"></i> {{ $schedule->room ?? 'Основной зал' }}
                                    </div>
                                    <div class="workout-capacity {{ $schedule->current_participants >= $schedule->capacity() ? 'full' : 'available' }}">
                                        <i class="fas fa-users"></i> Записано: {{ $schedule->bookings->count() }}/{{ $schedule->capacity() }}
                                    </div>
                                    
                                    @if($schedule->bookings->count() > 0)
                                        <div class="clients-preview">
                                            <div class="clients-preview-title">
                                                <i class="fas fa-user-friends"></i> Записавшиеся клиенты
                                            </div>
                                            @foreach($schedule->bookings->take(3) as $booking)
                                                <div class="client-preview-item">
                                                    <div class="client-info-preview">
                                                        <div class="client-avatar-mini">
                                                            {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                                                        </div>
                                                        <span class="client-name-preview">{{ $booking->user->name }}</span>
                                                    </div>
                                                    @if($booking->status === 'attended')
                                                        <span class="client-status">
                                                            <i class="fas fa-check-circle"></i> Посетил
                                                        </span>
                                                    @elseif($booking->status === 'missed')
                                                        <span class="client-status" style="background: rgba(220,53,69,0.1); color:#dc3545;">
                                                            <i class="fas fa-times-circle"></i> Пропустил
                                                        </span>
                                                    @else
                                                        <span class="client-status" style="background: rgba(255,193,7,0.1); color:#ffc107;">
                                                            <i class="fas fa-clock"></i> Ожидает
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if($schedule->bookings->count() > 3)
                                                <div class="more-clients">
                                                    <i class="fas fa-ellipsis-h"></i> и еще {{ $schedule->bookings->count() - 3 }} клиентов
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="empty-state-mini">
                                            <i class="fas fa-user-slash"></i>
                                            <p>Нет записавшихся клиентов</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state-mini">
                            <i class="fas fa-calendar-times"></i>
                            <p>На сегодня тренировок нет</p>
                        </div>
                    @endif
                    
                    <div class="text-center mt-4">
                        <!--<a href="{{ route('trainer.schedule') }}" class="quick-action-btn primary">
                            <i class="fas fa-calendar-alt"></i> Полное расписание
                        </a>-->
                    </div>
                </div>
            </div>
        </div>

        <!-- Ближайшие тренировки -->
        <div class="col-md-6">
            <div class="section-card">
                <div class="card-header success">
                    <i class="fas fa-calendar-week me-2"></i>Ближайшие тренировки
                </div>
                <div class="card-body">
                    @if($upcomingSchedules->count() > 0)
                        <div class="upcoming-list">
                            @foreach($upcomingSchedules as $schedule)
                                <div class="upcoming-item">
                                    <div class="upcoming-info">
                                        <div class="upcoming-date">
                                            <i class="fas fa-calendar-alt"></i>
                                            {{ \Carbon\Carbon::parse($schedule->date)->isoFormat('D MMMM, dddd') }}
                                            <span class="ms-2">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</span>
                                        </div>
                                        <div class="upcoming-workout">{{ $schedule->workout->name }}</div>
                                        <div class="upcoming-meta">
                                            <i class="fas fa-door-open"></i> {{ $schedule->room ?? 'Основной зал' }}
                                            <span class="mx-2">•</span>
                                            <i class="fas fa-users"></i> {{ $schedule->bookings->count() }}/{{ $schedule->capacity() }}
                                        </div>
                                    </div>
                                   <!-- <div class="upcoming-capacity">
                                        <a href="{{ route('trainer.schedule') }}?date={{ $schedule->date }}" 
                                           class="view-btn">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>-->
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state-mini">
                            <i class="fas fa-calendar-plus"></i>
                            <p>Нет предстоящих тренировок</p>
                        </div>
                    @endif
                    
                    <div class="text-center mt-4">
                       <!-- <a href="{{ route('trainer.schedule') }}" class="quick-action-btn primary">
                            <i class="fas fa-calendar-plus"></i> Все тренировки
                        </a>-->
                    </div>
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
            <a href="{{ route('trainer.schedule') }}" class="quick-action-btn primary">
                <i class="fas fa-calendar-alt"></i> Мое расписание
            </a>
            <a href="{{ route('trainer.clients') }}" class="quick-action-btn success">
                <i class="fas fa-users"></i> Мои клиенты
            </a>
            <a href="{{ route('trainer.profile') }}" class="quick-action-btn secondary">
                <i class="fas fa-user-cog"></i> Профиль
            </a>
        </div>
    </div>
</div>
@endsection