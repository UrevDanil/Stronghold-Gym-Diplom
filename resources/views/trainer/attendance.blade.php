<!-- Отметка посещений -->
@extends('layouts.app')

@section('title', 'Отметка посещаемости')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/trainer/attendance.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4 attendance-page">
    <!-- Заголовок -->
    <div class="attendance-header d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-clipboard-check me-3"></i>Отметка посещаемости
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('trainer.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert attendance-alert success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert attendance-alert error alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Фильтры -->
    <div class="filters-card">
        <div class="filters-header">
            <i class="fas fa-filter"></i> Фильтры
        </div>
        <div class="filters-body">
            <form method="GET" action="{{ route('trainer.attendance') }}" class="row g-3">
                <div class="col-md-5">
                    <label for="date" class="form-label">Дата</label>
                    <input type="date" name="date" id="date" class="form-control" 
                           value="{{ request('date', now()->format('Y-m-d')) }}">
                </div>
                <div class="col-md-5">
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
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn-filter flex-grow-1">
                        <i class="fas fa-search me-2"></i>Применить
                    </button>
                    <a href="{{ route('trainer.attendance') }}" class="btn-reset">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Список клиентов для отметки -->
    @if($bookings->count() > 0)
        <div class="attendance-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-clipboard-list me-2"></i>
                    Клиенты на сегодня
                </div>
                <span class="date-badge">
                    <i class="fas fa-calendar me-2"></i>
                    {{ \Carbon\Carbon::parse(request('date', now()))->isoFormat('D MMMM YYYY') }}
                </span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table attendance-table">
                        <thead>
                            <tr>
                                <th>Время</th>
                                <th>Тренировка</th>
                                <th>Клиент</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                                @php
                                    $rowClass = '';
                                    if ($booking->status === 'attended') $rowClass = 'attended-row';
                                    elseif ($booking->status === 'missed') $rowClass = 'missed-row';
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td data-label="Время">
                                        <strong>{{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}</strong>
                                    </td>
                                    <td data-label="Тренировка">
                                        {{ $booking->schedule->workout->name }}
                                        <br>
                                        <small class="text-muted">{{ $booking->schedule->room ?? 'Зал' }}</small>
                                    </td>
                                    <td data-label="Клиент">
                                        <div class="client-info">
                                            <div class="client-avatar">
                                                {{ strtoupper(substr($booking->user->name, 0, 1)) }}
                                            </div>
                                            <div class="client-details">
                                                <div class="client-name">{{ $booking->user->name }}</div>
                                                @if($booking->user->birth_date)
                                                    <div class="client-age">
                                                        {{ \Carbon\Carbon::parse($booking->user->birth_date)->age }} лет
                                                    </div>
                                                @endif
                                                @if($booking->user->phone)
                                                    <a href="tel:{{ $booking->user->phone }}" class="client-phone">
                                                        <i class="fas fa-phone"></i> {{ $booking->user->phone }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Статус">
                                        @if($booking->status === 'booked')
                                            <span class="status-badge waiting">Ожидает</span>
                                        @elseif($booking->status === 'attended')
                                            <span class="status-badge attended">Посетил</span>
                                        @elseif($booking->status === 'missed')
                                            <span class="status-badge missed">Пропустил</span>
                                        @elseif($booking->status === 'cancelled')
                                            <span class="status-badge cancelled">Отменено</span>
                                        @endif
                                    </td>
                                    <td data-label="Действия">
                                        @if($booking->status === 'booked' && !$booking->schedule->isPast())
                                            <div class="action-btn-group">
                                                <form action="{{ route('trainer.attendance.mark', $booking->schedule) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                    <button type="submit" name="status" value="attended" 
                                                            class="btn-attend"
                                                            onclick="return confirm('Отметить клиента как посетившего?')">
                                                        <i class="fas fa-check"></i> Пришел
                                                    </button>
                                                </form>
                                                <form action="{{ route('trainer.attendance.mark', $booking->schedule) }}" 
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                    <button type="submit" name="status" value="missed" 
                                                            class="btn-miss"
                                                            onclick="return confirm('Отметить как пропустившего?')">
                                                        <i class="fas fa-times"></i> Не пришел
                                                    </button>
                                                </form>
                                            </div>
                                        @elseif($booking->status === 'attended')
                                            <span class="status-text success">
                                                <i class="fas fa-check-circle"></i> Отмечено
                                            </span>
                                        @elseif($booking->status === 'missed')
                                            <span class="status-text danger">
                                                <i class="fas fa-times-circle"></i> Пропуск
                                            </span>
                                        @elseif($booking->schedule->isPast())
                                            <span class="status-text text-muted">
                                                <i class="fas fa-clock"></i> Тренировка прошла
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
        <div class="stats-row">
            <div class="row">
                <div class="col-md-4">
                    <div class="stat-card bg-success">
                        <div class="card-body">
                            <div class="stat-icon">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-label">Посетили</div>
                            <div class="stat-number">{{ $bookings->where('status', 'attended')->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-warning">
                        <div class="card-body">
                            <div class="stat-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-label">Ожидают</div>
                            <div class="stat-number">{{ $bookings->where('status', 'booked')->count() }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-card bg-danger">
                        <div class="card-body">
                            <div class="stat-icon">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="stat-label">Пропустили</div>
                            <div class="stat-number">{{ $bookings->where('status', 'missed')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <h4>Нет записей на выбранную дату</h4>
            <p class="text-muted">
                На {{ \Carbon\Carbon::parse(request('date', now()))->isoFormat('D MMMM YYYY') }} нет клиентов
            </p>
            <div class="action-buttons">
                <a href="{{ route('trainer.schedule') }}" class="btn-primary">
                    <i class="fas fa-calendar-alt me-2"></i>Посмотреть расписание
                </a>
                <a href="{{ route('trainer.attendance') }}?date={{ now()->addDay()->format('Y-m-d') }}" 
                class="btn-outline-secondary">
                    <i class="fas fa-arrow-right me-2"></i>Следующий день
                </a>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Подтверждение действий
        const forms = document.querySelectorAll('form[onsubmit]');
        forms.forEach(form => {
            const originalSubmit = form.onsubmit;
            form.onsubmit = function(e) {
                if (typeof originalSubmit === 'function') {
                    return originalSubmit.call(form, e);
                }
                return true;
            };
        });
    });
</script>
@endpush
@endsection