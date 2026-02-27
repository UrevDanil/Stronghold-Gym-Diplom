<!-- Кабинет тренера -->

@extends('layouts.app')

@section('title', 'Панель тренера')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Панель тренера</h1>
        <div>
            <span class="text-muted me-3">
                <i class="fas fa-user me-2"></i>{{ $user->name }}
            </span>
            <span class="badge bg-primary">{{ $user->qualification ?? 'Тренер' }}</span>
        </div>
    </div>

    <!-- Статистика -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Всего тренировок</h6>
                            <h2 class="mt-2 mb-0">{{ $totalTrainings }}</h2>
                        </div>
                        <i class="fas fa-dumbbell fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Посещений</h6>
                            <h2 class="mt-2 mb-0">{{ $totalAttendances }}</h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Клиентов</h6>
                            <h2 class="mt-2 mb-0">{{ $uniqueClients }}</h2>
                        </div>
                        <i class="fas fa-user-friends fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Сегодня</h6>
                            <h2 class="mt-2 mb-0">{{ $todaySchedules->count() }}</h2>
                        </div>
                        <i class="fas fa-calendar-day fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Сегодняшние тренировки -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-day me-2"></i>Тренировки на сегодня
                    </h5>
                </div>
                <div class="card-body">
                    @if($todaySchedules->count() > 0)
                        @foreach($todaySchedules as $schedule)
                            <div class="card mb-3 border-{{ $schedule->isPast() ? 'secondary' : 'primary' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                                {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                            </h6>
                                            <p class="mb-1">
                                                <strong>{{ $schedule->workout->name }}</strong>
                                            </p>
                                            <p class="mb-0 text-muted small">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $schedule->room ?? 'Зал не указан' }}
                                            </p>
                                        </div>
                                        <div>
                                            <span class="badge bg-{{ $schedule->isPast() ? 'secondary' : 'success' }}">
                                                {{ $schedule->bookings->count() }}/{{ $schedule->capacity() }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    @if(!$schedule->isPast() && $schedule->bookings->count() > 0)
                                        <hr>
                                        <div class="mt-2">
                                            <p class="mb-2 small fw-bold">Записавшиеся клиенты:</p>
                                            @foreach($schedule->bookings->take(3) as $booking)
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <span>
                                                        <i class="fas fa-user-circle me-1"></i>
                                                        {{ $booking->user->name }}
                                                    </span>
                                                    @if($booking->status === 'booked')
                                                        <form action="{{ route('trainer.attendance.mark', $schedule) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                            <button type="submit" name="status" value="attended" 
                                                                    class="btn btn-sm btn-success">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <span class="badge bg-success">Отмечен</span>
                                                    @endif
                                                </div>
                                            @endforeach
                                            @if($schedule->bookings->count() > 3)
                                                <div class="text-center mt-2">
                                                    <small class="text-muted">
                                                        И еще {{ $schedule->bookings->count() - 3 }} клиентов
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">На сегодня тренировок нет</p>
                        </div>
                    @endif
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('trainer.schedule') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-calendar-alt me-2"></i>Полное расписание
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Предстоящие тренировки -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-week me-2"></i>Ближайшие тренировки
                    </h5>
                </div>
                <div class="card-body">
                    @if($upcomingSchedules->count() > 0)
                        <div class="list-group">
                            @foreach($upcomingSchedules as $schedule)
                                <div class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="mb-1">
                                                <strong>{{ \Carbon\Carbon::parse($schedule->date)->isoFormat('D MMMM') }}</strong>
                                                <span class="mx-2">•</span>
                                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                            </p>
                                            <p class="mb-0">
                                                {{ $schedule->workout->name }}
                                                <span class="badge bg-info ms-2">{{ $schedule->room ?? 'Зал' }}</span>
                                            </p>
                                            <small class="text-muted">
                                                Записано: {{ $schedule->bookings->count() }}/{{ $schedule->capacity() }}
                                            </small>
                                        </div>
                                        <div>
                                            <a href="{{ route('trainer.schedule') }}?date={{ $schedule->date }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Нет предстоящих тренировок</p>
                        </div>
                    @endif
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('trainer.schedule') }}" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-calendar-plus me-2"></i>Все тренировки
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Быстрые действия -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Быстрые действия</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('trainer.schedule') }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-calendar-alt me-2"></i>Расписание
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('trainer.clients') }}" class="btn btn-outline-success w-100">
                                <i class="fas fa-users me-2"></i>Мои клиенты
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('trainer.schedule') }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-clipboard-list me-2"></i>Отметить посещаемость
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('trainer.profile') }}" class="btn btn-outline-secondary w-100">
                                <i class="fas fa-user-cog me-2"></i>Профиль
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .opacity-50 {
        opacity: 0.5;
    }
    .card {
        transition: transform 0.2s;
    }
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
    .list-group-item {
        transition: background-color 0.2s;
    }
    .list-group-item:hover {
        background-color: #f8f9fa;
    }
</style>
@endsection