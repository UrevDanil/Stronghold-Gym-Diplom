<!-- Мое расписание -->
@extends('layouts.app')

@section('title', 'Моё расписание')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/trainer/trainer-schedule.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4 schedule-page">
<!-- Заголовок -->
<div class="schedule-header d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fas fa-calendar-alt me-3"></i>Моё расписание
    </h1>
    <a href="{{ route('trainer.dashboard') }}" class="back-btn">
        <i class="fas fa-arrow-left me-2"></i>Назад
    </a>
</div>

<!-- Навигация по датам -->
<div class="date-nav-card">
    <div class="card-body">
        <div class="date-nav-controls">
            <a href="{{ route('trainer.schedule') }}?date={{ \Carbon\Carbon::parse(request('date', now()))->subDay()->format('Y-m-d') }}" 
               class="nav-btn">
                <i class="fas fa-chevron-left"></i>
            </a>
            
            <div class="date-display">
                <h3>
                    {{ \Carbon\Carbon::parse(request('date', now()))->isoFormat('dddd, D MMMM YYYY') }}
                </h3>
                <span class="trainings-count">
                    <i class="fas fa-dumbbell me-1"></i>
                    {{ $schedules->count() ?? 0 }} тренировок
                </span>
            </div>
            
            <a href="{{ route('trainer.schedule') }}?date={{ \Carbon\Carbon::parse(request('date', now()))->addDay()->format('Y-m-d') }}" 
               class="nav-btn">
                <i class="fas fa-chevron-right"></i>
            </a>
        </div>
        
        <!-- Кнопки действий: Сегодня и Создать тренировку -->
        <div class="action-buttons-row">
            <a href="{{ route('trainer.schedule') }}" class="btn-today">
                <i class="fas fa-calendar-day me-2"></i>Сегодня
            </a>
            <a href="{{ route('trainer.schedule.create') }}" class="btn-create-workout">
                <i class="fas fa-plus me-2"></i>Создать тренировку
            </a>
        </div>
    </div>
</div>

    <!-- Расписание на день -->
    @if(isset($schedules) && $schedules->count() > 0)
        <div class="row g-4">
            @foreach($schedules as $schedule)
                <div class="col-lg-6 col-md-12">
                    <div class="workout-card {{ $schedule->isPast() ? 'past' : '' }}" style="position: relative;">
                        <!-- Кнопки действий (редактировать и удалить) -->
                        <div class="card-actions">
                            @if(!$schedule->isPast())
                                <a href="{{ route('trainer.schedule.edit', $schedule->id) }}" 
                                   class="btn-edit" title="Редактировать тренировку">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <div class="card-delete-btn">
                                    <form action="{{ route('trainer.schedule.delete', $schedule->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Вы уверены, что хотите удалить эту тренировку? Все записанные клиенты получат уведомление, тренировки будут возвращены в их абонементы.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-delete" title="Удалить тренировку">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                        
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="time-badge">
                                    <i class="fas fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </div>
                                <div class="room-badge">
                                    <i class="fas fa-door-open"></i>
                                    {{ $schedule->room ?? 'Основной зал' }}
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h4 class="workout-title">{{ $schedule->workout->name }}</h4>
                                    @if($schedule->workout->description)
                                        <p class="workout-description">{{ Str::limit($schedule->workout->description, 80) }}</p>
                                    @endif
                                </div>
                                <div class="participants-badge {{ $schedule->current_participants >= $schedule->capacity() ? 'full' : 'available' }}">
                                    <i class="fas fa-users"></i>
                                    {{ $schedule->current_participants }}/{{ $schedule->capacity() }}
                                </div>
                            </div>

                            <!-- Список клиентов -->
                            <div class="clients-list">
                                <div class="clients-list-title">
                                    <i class="fas fa-user-friends"></i>
                                    Записавшиеся клиенты
                                </div>
                                @php
                                    $activeBookings = $schedule->bookings->where('status', '!=', 'cancelled');
                                @endphp
                                
                                @if($activeBookings->count() > 0)
                                    @foreach($activeBookings as $booking)
                                        <div class="client-item">
                                            <div class="client-info">
                                                <div class="client-avatar-small">
                                                    {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="client-name">{{ $booking->user->name }}</div>
                                                    @if($booking->user->phone)
                                                        <div class="client-phone">
                                                            <i class="fas fa-phone-alt"></i> {{ $booking->user->phone }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="client-actions">
                                                @if($booking->status === 'attended')
                                                    <span class="status-badge-small attended">
                                                        <i class="fas fa-check-circle"></i> Посетил
                                                    </span>
                                                @elseif($booking->status === 'missed')
                                                    <span class="status-badge-small missed">
                                                        <i class="fas fa-times-circle"></i> Пропустил
                                                    </span>
                                                @elseif($booking->status === 'booked')
                                                    <form action="{{ route('trainer.attendance.mark', $schedule) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                        <button type="submit" name="status" value="attended" 
                                                                class="btn-mark-attend"
                                                                title="Отметить как посетившего"
                                                                onclick="return confirm('Отметить {{ $booking->user->name }} как посетившего?')">
                                                            <i class="fas fa-check"></i> Пришел
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('trainer.attendance.mark', $schedule) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                        <button type="submit" name="status" value="missed" 
                                                                class="btn-mark-miss"
                                                                title="Отметить как пропустившего"
                                                                onclick="return confirm('Отметить {{ $booking->user->name }} как пропустившего?')">
                                                            <i class="fas fa-times"></i> Не пришел
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="empty-clients">
                                        <i class="fas fa-user-slash"></i>
                                        <p>Нет записавшихся клиентов</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Статистика -->
                            <div class="stats-grid">
                                <div class="stat-block">
                                    <span class="stat-label">Всего мест</span>
                                    <span class="stat-value">{{ $schedule->capacity() }}</span>
                                </div>
                                <div class="stat-block">
                                    <span class="stat-label">Записано</span>
                                    <span class="stat-value">{{ $activeBookings->count() }}</span>
                                </div>
                                <div class="stat-block">
                                    <span class="stat-label">Свободно</span>
                                    <span class="stat-value success">{{ $schedule->availableSlots() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-calendar-times"></i>
            </div>
            <h4>Нет тренировок на этот день</h4>
            <p>
                На {{ \Carbon\Carbon::parse(request('date', now()))->isoFormat('D MMMM YYYY') }} 
                у вас нет запланированных тренировок
            </p>
            <div class="action-buttons">
                <a href="{{ route('trainer.schedule') }}?date={{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" 
                   class="btn-tomorrow">
                    <i class="fas fa-arrow-right me-2"></i>Смотреть завтра
                </a>
                <a href="{{ route('trainer.dashboard') }}" class="btn-home">
                    <i class="fas fa-home me-2"></i>На главную
                </a>
            </div>
        </div>
    @endif

    <!-- Краткое расписание на неделю -->
    @if(isset($weekSchedules) && $weekSchedules->count() > 0)
        <div class="week-schedule-card">
            <div class="card-header">
                <i class="fas fa-calendar-week me-2"></i>Расписание на неделю
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="week-table">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Время</th>
                                <th>Тренировка</th>
                                <th>Зал</th>
                                <th>Клиентов</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($weekSchedules as $schedule)
                                <tr>
                                    <td data-label="Дата">
                                        <span class="date-cell">
                                            {{ \Carbon\Carbon::parse($schedule->date)->isoFormat('D MMM') }}
                                        </span>
                                    </td>
                                    <td data-label="Время">
                                        <span class="time-cell">
                                            {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}
                                        </span>
                                    </td>
                                    <td data-label="Тренировка">
                                        <span class="workout-cell">{{ $schedule->workout->name }}</span>
                                    </td>
                                    <td data-label="Зал">{{ $schedule->room ?? '—' }}</td>
                                    <td data-label="Клиентов">
                                        <span class="participants-badge {{ $schedule->current_participants >= $schedule->capacity() ? 'full' : 'available' }}">
                                            {{ $schedule->current_participants }}/{{ $schedule->capacity() }}
                                        </span>
                                    </td>
                                    <td data-label="Статус">
                                        @if($schedule->date->isToday())
                                            <span class="status-badge-week today">
                                                <i class="fas fa-star"></i> Сегодня
                                            </span>
                                        @elseif($schedule->date->isFuture())
                                            <span class="status-badge-week upcoming">
                                                <i class="fas fa-calendar-alt"></i> Предстоит
                                            </span>
                                        @else
                                            <span class="status-badge-week past">
                                                <i class="fas fa-history"></i> Прошло
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection