<!-- Личный кабинет -->
 @extends('layouts.app')

@section('title', 'Личный кабинет клиента')

@section('content')
<div class="container">
    <h1>Личный кабинет клиента</h1>
    
    <div class="alert alert-info">
        <h4>Добро пожаловать, {{ $user->name }}!</h4>
        <p>Ваш email: {{ $user->email }}</p>
        <p>Телефон: {{ $user->phone ?? 'не указан' }}</p>
    </div>
    
    @if($activeSubscription)
    <div class="alert alert-success">
        <h5>📅 Активный абонемент</h5>
        <p><strong>{{ $activeSubscription->subscription->name }}</strong></p>
        <p>Действует до: {{ $activeSubscription->end_date ? $activeSubscription->end_date->format('d.m.Y') : 'бессрочно' }}</p>
        @if($activeSubscription->remaining_workouts)
            <p>Осталось занятий: {{ $activeSubscription->remaining_workouts }}</p>
        @endif
    </div>
    @else
    <div class="alert alert-warning">
        <h5>⚠️ Нет активного абонемента</h5>
        <p>У вас нет активного абонемента. <a href="{{ route('client.subscriptions') }}">Приобрести абонемент</a></p>
    </div>
    @endif
    
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Ближайшие тренировки</h5>
                </div>
                <div class="card-body">
                    @if($upcomingBookings->count() > 0)
                        <ul class="list-group">
                            @foreach($upcomingBookings as $booking)
                            <li class="list-group-item">
                                <strong>{{ $booking->schedule->workout->name }}</strong><br>
                                {{ $booking->schedule->date->format('d.m.Y') }} в {{ $booking->schedule->start_time->format('H:i') }}<br>
                                Тренер: {{ $booking->schedule->trainer->name }}
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <p>У вас нет предстоящих тренировок</p>
                        <a href="{{ route('client.schedule') }}" class="btn btn-primary">Записаться на тренировку</a>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Доступные тренировки</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($workouts as $workout)
                        <li class="list-group-item">
                            <strong>{{ $workout->name }}</strong><br>
                            {{ $workout->duration_minutes }} минут • {{ $workout->level }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('client.schedule') }}" class="btn btn-primary mt-3">Посмотреть расписание</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="mt-4">
        <div class="card">
            <div class="card-header">
                <h5>Быстрые действия</h5>
            </div>
            <div class="card-body">
                <a href="{{ route('client.schedule') }}" class="btn btn-primary me-2">📅 Расписание</a>
                <a href="{{ route('client.subscriptions') }}" class="btn btn-success me-2">💳 Абонементы</a>
                <a href="{{ route('client.bookings') }}" class="btn btn-info me-2">🎫 Мои записи</a>
            </div>
        </div>
    </div>
</div>
@endsectionы