<!-- Расписание для записи -->
@extends('layouts.app')

@section('title', 'Расписание тренировок')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/client/client-schedule.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4 client-schedule-page">
    <!-- Заголовок -->
    <div class="client-schedule-header d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-calendar-alt me-3"></i>Расписание тренировок
        </h1>
        <a href="{{ route('client.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
    </div>

    @if(session('success'))
        <div class="alert client-schedule-alert success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert client-schedule-alert error alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Фильтры -->
    <div class="client-filters-card">
        <div class="client-filters-header">
            <i class="fas fa-filter me-2"></i> Фильтры
        </div>
        <div class="client-filters-body">
            <form method="GET" action="{{ route('client.schedule') }}" class="row g-3">
                <div class="col-md-5">
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
                <div class="col-md-5">
                    <label for="date" class="form-label">Дата</label>
                    <input type="date" name="date" id="date" class="form-control" 
                           value="{{ $selectedDate }}" min="{{ now()->format('Y-m-d') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="client-btn-filter flex-grow-1">
                        <i class="fas fa-search me-2"></i>Применить
                    </button>
                    <a href="{{ route('client.schedule') }}" class="client-btn-reset">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Расписание -->
    @if($schedules->count() > 0)
        @foreach($schedules->groupBy('date') as $date => $daySchedules)
            <div class="client-day-card">
                <div class="client-day-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-day me-2"></i>
                        {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY') }}
                    </h5>
                </div>
                <div class="card-body p-0">
                    <!-- Десктопная версия таблицы -->
                    <div class="table-responsive client-schedule-table">
                        <table class="table mb-0">
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
                                    <tr class="{{ $isBooked ? 'booked-row' : '' }}">
                                        <td>
                                            <strong>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <span class="client-workout-name">{{ $schedule->workout->name }}</span>
                                            @if($schedule->workout->description)
                                                <br>
                                                <span class="client-workout-desc">{{ Str::limit($schedule->workout->description, 50) }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="client-trainer-name">
                                                <i class="fas fa-user-circle me-1"></i>{{ $schedule->trainer->name }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="client-room-badge">
                                                <i class="fas fa-door-open me-1"></i>{{ $schedule->room ?? 'Основной зал' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($availableSlots > 0)
                                                <span class="client-slots-badge available">
                                                    <i class="fas fa-users me-1"></i>{{ $availableSlots }}/{{ $schedule->capacity() }}
                                                </span>
                                            @else
                                                <span class="client-slots-badge full">
                                                    <i class="fas fa-ban me-1"></i>0/{{ $schedule->capacity() }}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($isBooked)
                                                <span class="client-status-badge booked">
                                                    <i class="fas fa-calendar-check me-1"></i>Забронировано
                                                </span>
                                            @elseif($schedule->isPast())
                                                <span class="client-status-badge completed">
                                                    <i class="fas fa-check-circle me-1"></i>Завершено
                                                </span>
                                            @elseif($schedule->status === 'cancelled')
                                                <span class="client-status-badge cancelled">
                                                    <i class="fas fa-times-circle me-1"></i>Отменено
                                                </span>
                                            @elseif($availableSlots == 0)
                                                <span class="client-status-badge full">
                                                    <i class="fas fa-exclamation-circle me-1"></i>Мест нет
                                                </span>
                                            @else
                                                <span class="client-status-badge available">
                                                    <i class="fas fa-check-circle me-1"></i>Доступно
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($canBook)
                                                <form action="{{ route('client.schedule.book', $schedule) }}" method="POST">
                                                    @csrf
                                                    <button type="submit" class="client-btn-book">
                                                        <i class="fas fa-calendar-check me-1"></i>Забронировать
                                                    </button>
                                                </form>
                                            @elseif($isBooked)
                                                @php
                                                    $userBooking = $schedule->bookings->where('user_id', Auth::id())->first();
                                                @endphp
                                                @if($userBooking)
                                                    <form action="{{ route('client.bookings.cancel', $userBooking->id) }}" 
                                                          method="POST" 
                                                          onsubmit="return confirm('Отменить бронирование?');">
                                                        @csrf
                                                        <button type="submit" class="client-btn-cancel">
                                                            <i class="fas fa-times me-1"></i>Отменить
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="client-btn-disabled">Забронировано</span>
                                                @endif
                                            @else
                                                <span class="client-btn-disabled">
                                                    <i class="fas fa-ban me-1"></i>Недоступно
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Мобильная версия карточек -->
                    <div class="client-mobile-cards">
                        @foreach($daySchedules as $schedule)
                            @php
                                $isBooked = in_array($schedule->id, $userBookings);
                                $availableSlots = $schedule->availableSlots();
                                $canBook = $schedule->canBook() && !$isBooked;
                                
                                $cardClass = 'available';
                                if($schedule->isPast()) $cardClass = 'completed';
                                elseif($schedule->status === 'cancelled') $cardClass = 'cancelled';
                                elseif($availableSlots == 0) $cardClass = 'full';
                                elseif($isBooked) $cardClass = 'booked';
                            @endphp
                            
                            <div class="client-mobile-workout-card {{ $cardClass }}">
                                <div class="client-mobile-card-header">
                                    <h4>{{ $schedule->workout->name }}</h4>
                                    <div class="client-mobile-time">
                                        <span class="start-time">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</span>
                                        <span class="separator">-</span>
                                        <span class="end-time">{{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}</span>
                                    </div>
                                </div>
                                
                                <div class="client-mobile-info-grid">
                                    <div class="client-mobile-info-item">
                                        <i class="fas fa-user-circle"></i>
                                        <div class="info-content">
                                            <span class="label">Тренер</span>
                                            <span class="value">{{ $schedule->trainer->name }}</span>
                                        </div>
                                    </div>
                                    <div class="client-mobile-info-item">
                                        <i class="fas fa-door-open"></i>
                                        <div class="info-content">
                                            <span class="label">Зал</span>
                                            <span class="value">{{ $schedule->room ?? 'Основной зал' }}</span>
                                        </div>
                                    </div>
                                    <div class="client-mobile-info-item">
                                        <i class="fas fa-users"></i>
                                        <div class="info-content">
                                            <span class="label">Места</span>
                                            <div class="value">
                                                @if($availableSlots > 0)
                                                    <span class="client-mobile-slots-badge available"> {{ $availableSlots }}/{{ $schedule->capacity() }}
                                                    </span>
                                                @else
                                                    <span class="client-mobile-slots-badge full">
                                                        <i class="fas fa-ban"></i> 0/{{ $schedule->capacity() }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="client-mobile-status">
                                    @if($schedule->isPast())
                                        <span class="client-mobile-status-badge completed"><i class="fas fa-check-circle me-1"></i>Завершено</span>
                                    @elseif($schedule->status === 'cancelled')
                                        <span class="client-mobile-status-badge cancelled"><i class="fas fa-times-circle me-1"></i>Отменено</span>
                                    @elseif($availableSlots == 0)
                                        <span class="client-mobile-status-badge full"><i class="fas fa-exclamation-circle me-1"></i>Мест нет</span>
                                    @elseif($isBooked)
                                        <span class="client-mobile-status-badge booked"><i class="fas fa-calendar-check me-1"></i>Забронировано</span>
                                    @else
                                        <span class="client-mobile-status-badge available"><i class="fas fa-check-circle me-1"></i>Доступно</span>
                                    @endif
                                </div>
                                
                                <div class="client-mobile-actions">
                                    @if($canBook)
                                        <form action="{{ route('client.schedule.book', $schedule) }}" method="POST" class="w-100">
                                            @csrf
                                            <button type="submit" class="client-btn-book w-100">
                                                <i class="fas fa-calendar-check me-2"></i>Забронировать
                                            </button>
                                        </form>
                                    @elseif($isBooked)
                                        @php
                                            $userBooking = $schedule->bookings->where('user_id', Auth::id())->first();
                                        @endphp
                                        @if($userBooking)
                                            <form action="{{ route('client.bookings.cancel', $userBooking->id) }}" 
                                                method="POST" class="w-100"
                                                onsubmit="return confirm('Отменить бронирование?');">
                                                @csrf
                                                <button type="submit" class="client-btn-cancel w-100">
                                                    <i class="fas fa-times me-2"></i>Отменить
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="client-empty-state">
            <i class="fas fa-calendar-times"></i>
            <h4>Нет доступных тренировок</h4>
            <p>На выбранные даты нет запланированных тренировок</p>
            <a href="{{ route('client.schedule') }}" class="client-btn-reset-large">
                <i class="fas fa-undo-alt me-2"></i>Сбросить фильтры
            </a>
        </div>
    @endif
</div>
@endsection