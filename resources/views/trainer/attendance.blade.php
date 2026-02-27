<!-- Отметка посещений -->

@extends('layouts.app')

@section('title', 'Отметка посещаемости')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Отметка посещаемости</h1>
        <div>
            <a href="{{ route('trainer.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
            <span class="text-muted">
                <i class="fas fa-user me-2"></i>{{ auth()->user()->name }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('trainer.attendance') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="date" class="form-label">Дата</label>
                    <input type="date" name="date" id="date" class="form-control" 
                           value="{{ request('date', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-4">
                    <label for="schedule_id" class="form-label">Тренировка</label>
                    <select name="schedule_id" id="schedule_id" class="form-select">
                        <option value="">Все тренировки</option>
                        @foreach($schedules as $schedule)
                            <option value="{{ $schedule->id }}" 
                                {{ request('schedule_id') == $schedule->id ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                {{ $schedule->workout->name }} ({{ $schedule->bookings_count ?? 0 }}/{{ $schedule->capacity() }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-2"></i>Применить
                    </button>
                    <a href="{{ route('trainer.attendance') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Сбросить
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Список клиентов для отметки -->
    @if($bookings->count() > 0)
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-clipboard-list me-2"></i>
                    Клиенты на 
                    {{ \Carbon\Carbon::parse(request('date', now()))->isoFormat('D MMMM YYYY') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Время</th>
                                <th>Тренировка</th>
                                <th>Клиент</th>
                                <th>Телефон</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                <tr class="{{ $booking->status === 'attended' ? 'table-success' : ($booking->status === 'missed' ? 'table-danger' : '') }}">
                                    <td>
                                        <strong>{{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}</strong>
                                    </td>
                                    <td>
                                        {{ $booking->schedule->workout->name }}
                                        <br>
                                        <small class="text-muted">{{ $booking->schedule->room ?? 'Зал' }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-user-circle fa-2x text-primary me-2"></i>
                                            <div>
                                                <strong>{{ $booking->user->name }}</strong>
                                                @if($booking->user->birth_date)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($booking->user->birth_date)->age }} лет
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <a href="tel:{{ $booking->user->phone }}" class="text-decoration-none">
                                            <i class="fas fa-phone me-1"></i>{{ $booking->user->phone ?? '—' }}
                                        </a>
                                    </td>
                                    <td>
                                        @if($booking->status === 'booked')
                                            <span class="badge bg-warning">Ожидает</span>
                                        @elseif($booking->status === 'attended')
                                            <span class="badge bg-success">Посетил</span>
                                        @elseif($booking->status === 'missed')
                                            <span class="badge bg-danger">Пропустил</span>
                                        @elseif($booking->status === 'cancelled')
                                            <span class="badge bg-secondary">Отменено</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($booking->status === 'booked' && !$booking->schedule->isPast())
                                            <div class="btn-group" role="group">
                                                <form action="{{ route('trainer.attendance.mark', $booking->schedule) }}" 
                                                      method="POST" class="d-inline me-1">
                                                    @csrf
                                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                    <button type="submit" name="status" value="attended" 
                                                            class="btn btn-sm btn-success" 
                                                            onclick="return confirm('Отметить клиента как посетившего?')">
                                                        <i class="fas fa-check me-1"></i>Пришел
                                                    </button>
                                                </form>
                                                <form action="{{ route('trainer.attendance.mark', $booking->schedule) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                    <button type="submit" name="status" value="missed" 
                                                            class="btn btn-sm btn-danger"
                                                            onclick="return confirm('Отметить как пропустившего?')">
                                                        <i class="fas fa-times me-1"></i>Не пришел
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif($booking->status === 'attended')
                                            <span class="text-success">
                                                <i class="fas fa-check-circle me-1"></i>Отмечено
                                            </span>
                                        @elseif($booking->status === 'missed')
                                            <span class="text-danger">
                                                <i class="fas fa-times-circle me-1"></i>Пропуск
                                            </span>
                                        @elseif($booking->schedule->isPast())
                                            <span class="text-muted">
                                                <i class="fas fa-clock me-1"></i>Тренировка прошла
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Статистика за день -->
        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h6 class="card-title">Посетили</h6>
                        <h3>{{ $bookings->where('status', 'attended')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h6 class="card-title">Ожидают</h6>
                        <h3>{{ $bookings->where('status', 'booked')->count() }}</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card text-white bg-danger">
                    <div class="card-body">
                        <h6 class="card-title">Пропустили</h6>
                        <h3>{{ $bookings->where('status', 'missed')->count() }}</h3>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h4>Нет записей на выбранную дату</h4>
                <p class="text-muted mb-4">
                    На {{ \Carbon\Carbon::parse(request('date', now()))->isoFormat('D MMMM YYYY') }} нет клиентов
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('trainer.schedule') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-alt me-2"></i>Посмотреть расписание
                    </a>
                    <a href="{{ route('trainer.attendance') }}?date={{ now()->addDay()->format('Y-m-d') }}" 
                       class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-right me-2"></i>Следующий день
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .table td, .table th {
        vertical-align: middle;
    }
    .btn-group .btn {
        min-width: 70px;
    }
    .opacity-50 {
        opacity: 0.5;
    }
    .table-success {
        background-color: rgba(40, 167, 69, 0.1);
    }
    .table-danger {
        background-color: rgba(220, 53, 69, 0.1);
    }
    .table-success:hover,
    .table-danger:hover {
        filter: brightness(0.95);
    }
</style>

@push('scripts')
<script>
    // Автоматическое обновление статистики (можно добавить позже)
    document.addEventListener('DOMContentLoaded', function() {
        // Здесь можно добавить JavaScript для обновления данных
    });
</script>
@endpush
@endsection