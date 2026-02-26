<!-- Расписание для записи -->

@extends('layouts.app')

@section('title', 'Расписание тренировок')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Расписание тренировок</h1>
        <div>
            <a href="{{ route('client.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Назад
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
            <form method="GET" action="{{ route('client.schedule') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="workout_id" class="form-label">Тип тренировки</label>
                    <select name="workout_id" id="workout_id" class="form-select">
                        <option value="">Все тренировки</option>
                        @foreach($workouts as $workout)
                            <option value="{{ $workout->id }}" {{ $selectedWorkout == $workout->id ? 'selected' : '' }}>
                                {{ $workout->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="date" class="form-label">Дата</label>
                    <input type="date" name="date" id="date" class="form-control" 
                           value="{{ $selectedDate }}" min="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-2"></i>Применить
                    </button>
                    <a href="{{ route('client.schedule') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Сбросить
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Расписание -->
    @if($schedules->count() > 0)
        <div class="row">
            @foreach($schedules->groupBy('date') as $date => $daySchedules)
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Время</th>
                                            <th>Тренировка</th>
                                            <th>Тренер</th>
                                            <th>Зал</th>
                                            <th>Места</th>
                                            <th>Статус</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($daySchedules as $schedule)
                                            @php
                                                $isBooked = in_array($schedule->id, $userBookings);
                                                $availableSlots = $schedule->availableSlots();
                                                $canBook = $schedule->canBook() && !$isBooked;
                                            @endphp
                                            <tr class="{{ $isBooked ? 'table-success' : '' }}">
                                                <td>
                                                    <strong>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</strong>
                                                    - {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                                </td>
                                                <td>
                                                    <strong>{{ $schedule->workout->name }}</strong>
                                                    @if($schedule->workout->description)
                                                        <br>
                                                        <small class="text-muted">{{ $schedule->workout->description }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $schedule->trainer->name }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ $schedule->room ?? 'Основной зал' }}</span>
                                                </td>
                                                <td>
                                                    @if($availableSlots > 0)
                                                        <span class="badge bg-success">{{ $availableSlots }}/{{ $schedule->capacity() }}</span>
                                                    @else
                                                        <span class="badge bg-danger">0/{{ $schedule->capacity() }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($schedule->isPast())
                                                        <span class="badge bg-secondary">Завершено</span>
                                                    @elseif($schedule->status === 'cancelled')
                                                        <span class="badge bg-danger">Отменено</span>
                                                    @elseif($availableSlots == 0)
                                                        <span class="badge bg-warning">Мест нет</span>
                                                    @else
                                                        <span class="badge bg-success">Доступно</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($canBook)
                                                        <form action="{{ route('client.schedule.book', $schedule) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-primary">
                                                                <i class="fas fa-calendar-check me-1"></i>Забронировать
                                                            </button>
                                                        </form>
                                                    @elseif($isBooked)
                                                        <form action="{{ route('client.bookings.cancel', ['booking' => $schedule->bookings->where('user_id', Auth::id())->first()->id]) }}" 
                                                              method="POST" class="d-inline"
                                                              onsubmit="return confirm('Отменить бронирование?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                                <i class="fas fa-times me-1"></i>Отменить
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-sm btn-secondary" disabled>
                                                            Недоступно
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h4>Нет доступных тренировок</h4>
                <p class="text-muted">На выбранные даты нет запланированных тренировок</p>
                <a href="{{ route('client.schedule') }}" class="btn btn-primary">
                    <i class="fas fa-sync-alt me-2"></i>Сбросить фильтры
                </a>
            </div>
        </div>
    @endif
</div>

<style>
    .table td, .table th {
        vertical-align: middle;
    }
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
</style>
@endsection