@extends('layouts.app')

@section('title', 'Управление расписанием')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Управление расписанием</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
            <a href="{{ route('admin.schedule.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Добавить занятие
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Фильтры -->
    <div class="card mb-4">
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
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Применить
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Расписание -->
    <div class="card">
        <div class="card-body">
            @if($schedules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($schedules as $schedule)
                                @php
                                    // Получаем вместимость из связанной тренировки
                                    $capacity = $schedule->workout->capacity ?? 10;
                                    $isFull = $schedule->current_participants >= $capacity;
                                @endphp
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($schedule->date)->format('d.m.Y') }}</td>
                                    <td>{{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}</td>
                                    <td>{{ $schedule->workout->name }}</td>
                                    <td>{{ $schedule->trainer->name }}</td>
                                    <td>{{ $schedule->room ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $isFull ? 'danger' : 'success' }}">
                                            {{ $schedule->current_participants }}/{{ $capacity }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($schedule->status == 'scheduled')
                                            <span class="badge bg-success">Запланировано</span>
                                        @elseif($schedule->status == 'cancelled')
                                            <span class="badge bg-danger">Отменено</span>
                                        @elseif($schedule->status == 'completed')
                                            <span class="badge bg-secondary">Завершено</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.schedule.edit', $schedule->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Редактировать">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            @if($schedule->status != 'cancelled')
                                                <form action="{{ route('admin.schedule.cancel', $schedule->id) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" 
                                                            title="Отменить занятие"
                                                            onclick="return confirm('Отменить занятие? Все бронирования будут отменены, тренировки вернутся в абонементы.')">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="btn btn-sm btn-outline-secondary disabled" title="Занятие уже отменено">
                                                    <i class="fas fa-check"></i>
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
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                    <h4>Нет занятий в расписании</h4>
                    <p class="text-muted mb-4">На выбранные даты нет запланированных тренировок</p>
                    <a href="{{ route('admin.schedule.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Добавить занятие
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .table td, .table th {
        vertical-align: middle;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
</style>
@endsection