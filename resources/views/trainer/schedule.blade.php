<!-- Мое расписание -->

@extends('layouts.app')

@section('title', 'Моё расписание')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Моё расписание</h1>
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

    <!-- Навигация по датам -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <a href="{{ route('trainer.schedule') }}?date={{ \Carbon\Carbon::parse(request('date', now()))->subDay()->format('Y-m-d') }}" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-chevron-left"></i>
                </a>
                
                <div class="text-center">
                    <h5 class="mb-1">
                        {{ \Carbon\Carbon::parse(request('date', now()))->isoFormat('dddd, D MMMM YYYY') }}
                    </h5>
                    <span class="badge bg-info">
                        {{ $schedules->count() ?? 0 }} тренировок
                    </span>
                </div>
                
                <a href="{{ route('trainer.schedule') }}?date={{ \Carbon\Carbon::parse(request('date', now()))->addDay()->format('Y-m-d') }}" 
                   class="btn btn-outline-primary">
                    <i class="fas fa-chevron-right"></i>
                </a>
            </div>
            
            <div class="text-center mt-3">
                <a href="{{ route('trainer.schedule') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-calendar-day me-2"></i>Сегодня
                </a>
            </div>
        </div>
    </div>

    <!-- Расписание на день -->
    @if(isset($schedules) && $schedules->count() > 0)
        <div class="row">
            @foreach($schedules as $schedule)
                <div class="col-md-6 mb-4">
                    <div class="card h-100 {{ $schedule->isPast() ? 'border-secondary' : 'border-primary' }}">
                        <div class="card-header {{ $schedule->isPast() ? 'bg-secondary' : 'bg-primary' }} text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock me-2"></i>
                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }} - 
                                    {{ \Carbon\Carbon::parse($schedule->end_time)->format('H:i') }}
                                </h5>
                                <span class="badge bg-light text-dark">
                                    {{ $schedule->room ?? 'Зал' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h4>{{ $schedule->workout->name }}</h4>
                                    @if($schedule->workout->description)
                                        <p class="text-muted small">{{ $schedule->workout->description }}</p>
                                    @endif
                                </div>
                                <div class="text-end">
                                    <span class="badge bg-{{ $schedule->current_participants >= $schedule->capacity() ? 'danger' : 'success' }} fs-6">
                                        {{ $schedule->current_participants }}/{{ $schedule->capacity() }}
                                    </span>
                                </div>
                            </div>

                            <!-- Список клиентов -->
                            @if($schedule->bookings->where('status', '!=', 'cancelled')->count() > 0)
                                <h6 class="mb-2">
                                    <i class="fas fa-users me-2"></i>Записавшиеся клиенты:
                                </h6>
                                <div class="list-group mb-3">
                                    @foreach($schedule->bookings->where('status', '!=', 'cancelled') as $booking)
                                        <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                            <div>
                                                <i class="fas fa-user-circle text-primary me-2"></i>
                                                <strong>{{ $booking->user->name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    @if($booking->user->phone)
                                                        <i class="fas fa-phone me-1"></i>{{ $booking->user->phone }}
                                                    @endif
                                                </small>
                                            </div>
                                            <div>
                                                @if($booking->status === 'attended')
                                                    <span class="badge bg-success">Посетил</span>
                                                @elseif($booking->status === 'booked' && !$schedule->isPast())
                                                    <form action="{{ route('trainer.attendance.mark', $schedule) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                        <button type="submit" name="status" value="attended" 
                                                                class="btn btn-sm btn-success"
                                                                onclick="return confirm('Отметить клиента как посетившего?')">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('trainer.attendance.mark', $schedule) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                                                        <button type="submit" name="status" value="missed" 
                                                                class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Отметить как пропустившего?')">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                @elseif($booking->status === 'missed')
                                                    <span class="badge bg-danger">Пропустил</span>
                                                @elseif($schedule->isPast())
                                                    <span class="badge bg-secondary">Тренировка прошла</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-light text-center py-3">
                                    <i class="fas fa-user-slash fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Нет записавшихся клиентов</p>
                                </div>
                            @endif

                            <!-- Дополнительная информация -->
                            <div class="mt-3 pt-3 border-top">
                                <div class="row text-center">
                                    <div class="col-4">
                                        <small class="text-muted d-block">Всего мест</small>
                                        <strong>{{ $schedule->capacity() }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Записано</small>
                                        <strong>{{ $schedule->bookings->where('status', '!=', 'cancelled')->count() }}</strong>
                                    </div>
                                    <div class="col-4">
                                        <small class="text-muted d-block">Свободно</small>
                                        <strong class="text-success">{{ $schedule->availableSlots() }}</strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Кнопки действий -->
                            @if(!$schedule->isPast())
                                <div class="mt-3 d-flex gap-2">
                                    <a href="{{ route('trainer.attendance') }}?date={{ $schedule->date }}&schedule_id={{ $schedule->id }}" 
                                       class="btn btn-primary w-100">
                                        <i class="fas fa-clipboard-check me-2"></i>Отметить посещаемость
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h4>Нет тренировок на этот день</h4>
                <p class="text-muted mb-4">
                    На {{ \Carbon\Carbon::parse(request('date', now()))->isoFormat('D MMMM YYYY') }} 
                    у вас нет запланированных тренировок
                </p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('trainer.schedule') }}?date={{ \Carbon\Carbon::tomorrow()->format('Y-m-d') }}" 
                       class="btn btn-primary">
                        <i class="fas fa-arrow-right me-2"></i>Смотреть завтра
                    </a>
                    <a href="{{ route('trainer.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-home me-2"></i>На главную
                    </a>
                </div>
            </div>
        </div>
    @endif

    <!-- Краткое расписание на неделю -->
    @if(isset($weekSchedules) && $weekSchedules->count() > 0)
        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-week me-2"></i>Расписание на неделю
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
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
                                    <td>{{ \Carbon\Carbon::parse($schedule->date)->isoFormat('D MMM') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</td>
                                    <td>{{ $schedule->workout->name }}</td>
                                    <td>{{ $schedule->room ?? '—' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $schedule->current_participants >= $schedule->capacity() ? 'danger' : 'success' }}">
                                            {{ $schedule->current_participants }}/{{ $schedule->capacity() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($schedule->date->isToday())
                                            <span class="badge bg-primary">Сегодня</span>
                                        @elseif($schedule->date->isFuture())
                                            <span class="badge bg-success">Предстоит</span>
                                        @else
                                            <span class="badge bg-secondary">Прошло</span>
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

<style>
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
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
</style>
@endsection