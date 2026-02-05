<!-- Личный кабинет -->
@extends('layouts.app')

@section('title', 'Личный кабинет')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Добро пожаловать, {{ $user->name }}!</h1>
        </div>
    </div>

    <div class="row">
        <!-- Статистика -->
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Активный абонемент</h6>
                            @if($activeSubscription)
                                <p class="mb-0">{{ $activeSubscription->name }}</p>
                            @else
                                <p class="mb-0">Нет</p>
                            @endif
                        </div>
                        <i class="fas fa-id-card fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Осталось тренировок</h6>
                            <p class="mb-0">
                                @if($activeSubscription)
                                    {{ $activeSubscription->pivot->remaining_workouts }}
                                @else
                                    0
                                @endif
                            </p>
                        </div>
                        <i class="fas fa-dumbbell fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Забронировано</h6>
                            <p class="mb-0">{{ $upcomingBookings->count() }}</p>
                        </div>
                        <i class="fas fa-calendar-check fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title mb-0">Абонемент до</h6>
                            <p class="mb-0">
                                @if($activeSubscription)
                                    {{ \Carbon\Carbon::parse($activeSubscription->pivot->end_date)->format('d.m.Y') }}
                                @else
                                    Нет
                                @endif
                            </p>
                        </div>
                        <i class="fas fa-calendar-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Предстоящие тренировки -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Предстоящие тренировки</h5>
                </div>
                <div class="card-body">
                    @if($upcomingBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Дата</th>
                                        <th>Время</th>
                                        <th>Тип тренировки</th>
                                        <th>Тренер</th>
                                        <th>Действия</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingBookings as $booking)
                                    <tr>
                                        <td>{{ $booking->schedule->date->format('d.m.Y') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}</td>
                                        <td>{{ $booking->schedule->workout->name }}</td>
                                        <td>{{ $booking->schedule->trainer->name }}</td>
                                        <td>
                                            <form action="{{ route('client.bookings.cancel', $booking) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Вы уверены, что хотите отменить бронирование?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Отменить
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <a href="{{ route('client.schedule') }}" class="btn btn-primary">
                            Забронировать еще
                        </a>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted">У вас нет предстоящих тренировок</p>
                            <a href="{{ route('client.schedule') }}" class="btn btn-primary">
                                Посмотреть расписание
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- История тренировок -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">История посещений</h5>
                </div>
                <div class="card-body">
                    @if($pastBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Дата</th>
                                        <th>Тренировка</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pastBookings as $booking)
                                    <tr>
                                        <td>{{ $booking->schedule->date->format('d.m.Y') }}</td>
                                        <td>{{ $booking->schedule->workout->name }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match($booking->status) {
                                                    'completed' => 'success',
                                                    'cancelled' => 'danger',
                                                    'absent' => 'warning',
                                                    default => 'secondary'
                                                };
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">
                                                @if($booking->status === 'booked') забронировано
                                                @elseif($booking->status === 'completed') посещено
                                                @elseif($booking->status === 'cancelled') отменено
                                                @elseif($booking->status === 'absent') неявка
                                                @else {{ $booking->status }}
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">У вас еще нет истории посещений</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Боковая панель -->
        <div class="col-md-4">
            <!-- Активный абонемент -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Ваш абонемент</h5>
                </div>
                <div class="card-body">
                    @if($activeSubscription)
                        <div class="text-center mb-3">
                            <i class="fas fa-id-card fa-3x text-primary mb-3"></i>
                            <h4>{{ $activeSubscription->name }}</h4>
                            <p class="text-muted">{{ $activeSubscription->description }}</p>
                        </div>
                        
                        @php
                            // Получаем общее количество тренировок из абонемента
                            $totalWorkouts = $activeSubscription->session_count ?? 0;
                            $remaining = $activeSubscription->pivot->remaining_workouts ?? 0;
                            $used = max(0, $totalWorkouts - $remaining);
                            $percentage = $totalWorkouts > 0 ? min(100, ($used / $totalWorkouts) * 100) : 0;
                        @endphp
                        
                        <div class="progress mb-3" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $percentage }}%"
                                 aria-valuenow="{{ $percentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $used }}/{{ $totalWorkouts }}
                            </div>
                        </div>
                        
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Осталось тренировок:</span>
                                <strong>{{ $remaining }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Дата начала:</span>
                                <strong>{{ \Carbon\Carbon::parse($activeSubscription->pivot->start_date)->format('d.m.Y') }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Действует до:</span>
                                <strong>{{ \Carbon\Carbon::parse($activeSubscription->pivot->end_date)->format('d.m.Y') }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Статус:</span>
                                <strong>
                                    @if($activeSubscription->pivot->status === 'active')
                                        <span class="badge bg-success">Активен</span>
                                    @elseif($activeSubscription->pivot->status === 'expired')
                                        <span class="badge bg-danger">Истек</span>
                                    @elseif($activeSubscription->pivot->status === 'frozen')
                                        <span class="badge bg-warning">Заморожен</span>
                                    @elseif($activeSubscription->pivot->status === 'canceled')
                                        <span class="badge bg-secondary">Отменен</span>
                                    @else
                                        {{ $activeSubscription->pivot->status }}
                                    @endif
                                </strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Цена:</span>
                                <strong>{{ number_format($activeSubscription->price, 0, ',', ' ') }} ₽</strong>
                            </li>
                        </ul>
                        
                        <div class="mt-3">
                            <a href="{{ route('client.subscriptions') }}" class="btn btn-outline-primary btn-block">
                                Продлить или купить новый
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                            <p class="text-muted mb-3">У вас нет активного абонемента</p>
                            <a href="{{ route('client.subscriptions') }}" class="btn btn-primary">
                                Купить абонемент
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Быстрые действия -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Быстрые действия</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('client.schedule') }}" class="btn btn-outline-primary">
                            <i class="fas fa-calendar-alt me-2"></i> Расписание
                        </a>
                        <a href="{{ route('client.subscriptions') }}" class="btn btn-outline-success">
                            <i class="fas fa-credit-card me-2"></i> Абонементы
                        </a>
                        <!--<a href="{{ route('client.profile') }}" class="btn btn-outline-info">
                            <i class="fas fa-user-cog me-2"></i> Профиль
                        </a>-->
                        <a href="#" class="btn btn-outline-info disabled">
                        <i class="fas fa-user-cog me-2"></i> Профиль (скоро)
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection