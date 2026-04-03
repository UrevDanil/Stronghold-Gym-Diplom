<!-- Управление расписаниями -->
@extends('layouts.app')

@section('title', 'Управление расписанием')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/admin/schedule.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 admin-schedule-page">
    <!-- Заголовок -->
    <div class="schedule-header">
        <h1 class="mb-0">
            <i class="fas fa-calendar-alt me-3"></i>Управление расписанием
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
            <a href="{{ route('admin.schedule.create') }}" class="create-btn">
                <i class="fas fa-plus me-2"></i>Добавить занятие
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert schedule-alert success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert schedule-alert error alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Фильтры -->
    <div class="filters-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Фильтры
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.schedule.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="date" class="form-label">Дата</label>
                    <input type="date" class="form-control" id="date" name="date" 
                           value="{{ $selectedDate }}">
                </div>
                <div class="col-md-3">
                    <label for="workout_id" class="form-label">Тренировка</label>
                    <select class="form-select" id="workout_id" name="workout_id">
                        <option value="">Все тренировки</option>
                        @foreach($workouts as $workout)
                            <option value="{{ $workout->id }}" {{ $selectedWorkout == $workout->id ? 'selected' : '' }}>
                                {{ $workout->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="trainer_id" class="form-label">Тренер</label>
                    <select class="form-select" id="trainer_id" name="trainer_id">
                        <option value="">Все тренеры</option>
                        @foreach($trainers as $trainer)
                            <option value="{{ $trainer->id }}" {{ $selectedTrainer == $trainer->id ? 'selected' : '' }}>
                                {{ $trainer->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn-filter w-100">
                        <i class="fas fa-search me-2"></i>Применить
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Расписание -->
    <div class="schedule-card">
        <div class="card-body p-0">
            @if($schedules->count() > 0)
                <div class="table-responsive">
                    <table class="schedule-table">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Время</th>
                                <th>Тренировка</th>
                                <th>Тренер</th>
                                <th>Зал</th>
                                <th>Запись</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                @php
                                    // Используем метод capacity() который берет значение из schedules или workout
                                    $capacity = $schedule->capacity();
                                    $isFull = $schedule->current_participants >= $capacity;
                                @endphp
                                <tr>
                                    <td data-label="Дата">
                                        <strong>{{ \Carbon\Carbon::parse($schedule->date)->format('d.m.Y') }}</strong>
                                    </td>
                                    <td data-label="Время">
                                        <span class="badge-custom" style="background: #e9ecef; color: #495057;">
                                            <i class="fas fa-clock me-1"></i>
                                            {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
                                        </span>
                                    </td>
                                    <td data-label="Тренировка">
                                        <strong>{{ $schedule->workout->name }}</strong>
                                    </td>
                                    <td data-label="Тренер">
                                        <i class="fas fa-user-circle me-1 text-primary"></i>
                                        {{ $schedule->trainer->name }}
                                    </td>
                                    <td data-label="Зал">
                                        <i class="fas fa-door-open me-1 text-muted"></i>
                                        {{ $schedule->room ?? '—' }}
                                    </td>
                                    <td data-label="Запись">
                                        <span class="badge-capacity {{ $isFull ? 'full' : 'available' }}">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $schedule->current_participants }}/{{ $capacity }}
                                        </span>
                                    </td>
                                    <td data-label="Статус">
                                        @if($schedule->status == 'scheduled')
                                            <span class="badge-custom badge-scheduled">
                                                <i class="fas fa-check-circle me-1"></i>Запланировано
                                            </span>
                                        @elseif($schedule->status == 'cancelled')
                                            <span class="badge-custom badge-cancelled">
                                                <i class="fas fa-ban me-1"></i>Отменено
                                            </span>
                                        @elseif($schedule->status == 'completed')
                                            <span class="badge-custom badge-completed">
                                                <i class="fas fa-check-double me-1"></i>Завершено
                                            </span>
                                        @endif
                                    </td>
                                    <td data-label="Действия">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.schedule.edit', $schedule->id) }}" 
                                               class="action-btn edit" title="Редактировать">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            @if($schedule->status == 'scheduled')
                                                <form action="{{ route('admin.schedule.cancel', $schedule->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="action-btn cancel" 
                                                            title="Отменить занятие"
                                                            onclick="return confirm('Отменить занятие? Все бронирования будут отменены, тренировки вернутся в абонементы.')">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @elseif($schedule->status == 'cancelled')
                                                <span class="action-btn disabled" title="Занятие уже отменено">
                                                    <i class="fas fa-check"></i>
                                                </span>
                                            @elseif($schedule->status == 'completed')
                                                <span class="action-btn disabled" title="Занятие завершено">
                                                    <i class="fas fa-check-circle"></i>
                                                </span>
                                            @endif
                                        </div>
                                     </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <h4>Нет занятий в расписании</h4>
                    <p>На выбранные даты нет запланированных тренировок</p>
                    <a href="{{ route('admin.schedule.create') }}" class="btn-create">
                        <i class="fas fa-plus me-2"></i>Добавить занятие
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection