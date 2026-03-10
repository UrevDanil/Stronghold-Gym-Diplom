<!-- Личный кабинет -->
@extends('layouts.app')

@section('title', 'Личный кабинет')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/common.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/dashboard/client.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4">
    <!-- Приветствие (всегда сверху) -->
    <div class="row mb-4 fade-in">
        <div class="col-12">
            <h1 class="display-5 fw-bold" style="background: linear-gradient(135deg, #667eea, #764ba2); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">
                Добро пожаловать, {{ $user->name }}!
            </h1>
            <p class="text-muted">{{ now()->isoFormat('dddd, D MMMM YYYY') }}</p>
        </div>
    </div>

<!-- Активный абонемент (улучшенная версия) -->
<div class="main-card fade-in" style="animation-delay: 0.7s">
    <div class="card-header">
        <i class="fas fa-id-card"></i> Ваш абонемент
    </div>
    <div class="card-body">
        @if($activeSubscription)
            @php
                $subscription = $activeSubscription->subscription;
                $totalWorkouts = $subscription->workouts_count ?? 0;
                $remaining = $activeSubscription->remaining_workouts;
                $used = max(0, $totalWorkouts - $remaining);
                $percentage = $totalWorkouts > 0 ? round(($used / $totalWorkouts) * 100) : 0;
            @endphp
        
            <div class="text-center mb-4">
                <div class="subscription-icon mb-3">
                    <i class="fas fa-id-card"></i>
                </div>
                <h4 class="fw-bold mb-1">{{ $subscription->name ?? 'Абонемент' }}</h4>
                <p class="text-muted small">{{ $subscription->description ?? '' }}</p>
            </div>
            
            <!-- Прогресс бар -->
            <div class="mb-4">
                <div class="subscription-progress">
                    <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                </div>
                <div class="d-flex justify-content-between text-muted small mt-2">
                    <span><i class="fas fa-check-circle text-success me-1"></i>Использовано: <strong>{{ $used }}</strong></span>
                    <span><i class="fas fa-clock text-warning me-1"></i>Осталось: <strong>{{ $remaining }}</strong></span>
                    <span><i class="fas fa-list text-info me-1"></i>Всего: <strong>{{ $totalWorkouts }}</strong></span>
                </div>
            </div>
            
            <!-- Детали абонемента в карточках -->
            <div class="subscription-details-grid mb-4">
                <div class="detail-item">
                    <i class="fas fa-calendar-alt text-primary"></i>
                    <div>
                        <small class="text-muted d-block">Начало</small>
                        <strong>{{ \Carbon\Carbon::parse($activeSubscription->start_date)->format('d.m.Y') }}</strong>
                    </div>
                </div>
                
                <div class="detail-item">
                    <i class="fas fa-calendar-check text-success"></i>
                    <div>
                        <small class="text-muted d-block">Действует до</small>
                        <strong>{{ \Carbon\Carbon::parse($activeSubscription->end_date)->format('d.m.Y') }}</strong>
                    </div>
                </div>
                
                <div class="detail-item">
                    <i class="fas fa-tag text-info"></i>
                    <div>
                        <small class="text-muted d-block">Статус</small>
                        <strong>
                            @if($activeSubscription->status === 'active')
                                <span class="badge bg-success">Активен</span>
                            @elseif($activeSubscription->status === 'expired')
                                <span class="badge bg-danger">Истек</span>
                            @elseif($activeSubscription->status === 'frozen')
                                <span class="badge bg-warning">Заморожен</span>
                            @else
                                <span class="badge bg-secondary">{{ $activeSubscription->status }}</span>
                            @endif
                        </strong>
                    </div>
                </div>
                
                <div class="detail-item">
                    <i class="fas fa-ruble-sign text-warning"></i>
                    <div>
                        <small class="text-muted d-block">Цена</small>
                        <strong>{{ number_format($subscription->price ?? 0, 0, ',', ' ') }} ₽</strong>
                    </div>
                </div>
            </div>
            
            <!-- Кнопка с отступом -->
            <div class="subscription-action mt-3">
                <a href="{{ route('client.subscriptions') }}" class="btn btn-outline-primary w-100 py-3">
                    <i class="fas fa-sync-alt me-2"></i>Продлить или купить новый
                </a>
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-id-card"></i>
                <h4>Нет активного абонемента</h4>
                <p class="mb-4">Приобретите абонемент, чтобы начать тренировки</p>
                <a href="{{ route('client.subscriptions') }}" class="btn btn-primary w-100 py-3">
                    <i class="fas fa-shopping-cart me-2"></i>Купить абонемент
                </a>
            </div>
        @endif
    </div>
</div>

    <!-- 4 маленьких блока статистики -->
    <div class="row g-4 mb-5">
        <!-- Активный абонемент -->
        <div class="col-6 col-md-3 fade-in" style="animation-delay: 0.1s">
            <div class="stat-card bg-primary-gradient text-white">
                <div class="card-body">
                    <h6 class="card-title">Активный абонемент</h6>
                    @if($activeSubscription)
                        <div class="stat-value">{{ $activeSubscription->subscription->name ?? 'Абонемент' }}</div>
                        <small class="stat-label">{{ $activeSubscription->subscription->workouts_count ?? 0 }} тренировок</small>
                    @else
                        <div class="stat-value">Нет</div>
                        <small class="stat-label">Приобретите абонемент</small>
                    @endif
                    <i class="fas fa-id-card"></i>
                </div>
            </div>
        </div>

        <!-- Осталось тренировок -->
        <div class="col-6 col-md-3 fade-in" style="animation-delay: 0.2s">
            <div class="stat-card bg-success-gradient text-white">
                <div class="card-body">
                    <h6 class="card-title">Осталось тренировок</h6>
                    @if($activeSubscription)
                        <div class="stat-value">{{ $activeSubscription->remaining_workouts }}</div>
                        <small class="stat-label">из {{ $activeSubscription->subscription->workouts_count ?? 0 }}</small>
                    @else
                        <div class="stat-value">0</div>
                        <small class="stat-label">Нет абонемента</small>
                    @endif
                    <i class="fas fa-dumbbell"></i>
                </div>
            </div>
        </div>

        <!-- Забронировано -->
        <div class="col-6 col-md-3 fade-in" style="animation-delay: 0.3s">
            <div class="stat-card bg-info-gradient text-white">
                <div class="card-body">
                    <h6 class="card-title">Забронировано</h6>
                    <div class="stat-value">{{ $upcomingBookings->count() }}</div>
                    <small class="stat-label">предстоящих тренировок</small>
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
        </div>

        <!-- Абонемент до -->
        <div class="col-6 col-md-3 fade-in" style="animation-delay: 0.4s">
            <div class="stat-card bg-warning-gradient text-white">
                <div class="card-body">
                    <h6 class="card-title">Абонемент до</h6>
                    @if($activeSubscription)
                        <div class="stat-value">{{ \Carbon\Carbon::parse($activeSubscription->end_date)->format('d.m') }}</div>
                        <small class="stat-label">{{ \Carbon\Carbon::parse($activeSubscription->end_date)->format('Y') }}</small>
                    @else
                        <div class="stat-value">—</div>
                        <small class="stat-label">Нет абонемента</small>
                    @endif
                    <i class="fas fa-calendar-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Предупреждение о деактивации -->
    @if(!auth()->user()->is_active)
        <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                <div>
                    <strong>Внимание!</strong> Ваш аккаунт деактивирован. 
                    Некоторые функции могут быть недоступны. Обратитесь к администратору.
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Левая колонка (Предстоящие тренировки) -->
        <div class="col-lg-8">
            <!-- Предстоящие тренировки -->
            <div class="main-card fade-in" style="animation-delay: 0.5s">
                <div class="card-header">
                    <i class="fas fa-calendar-alt"></i> Предстоящие тренировки
                </div>
                <div class="card-body">
                    @if($upcomingBookings->count() > 0)
                        <!-- Десктопная версия таблицы -->
                        <div class="desktop-view">
                            <div class="table-responsive">
                                <table class="table dashboard-table">
                                    <thead>
                                        <tr>
                                            <th>Дата</th>
                                            <th>Время</th>
                                            <th>Тренировка</th>
                                            <th>Тренер</th>
                                            <th>Действия</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($upcomingBookings as $booking)
                                        <tr>
                                            <td><strong>{{ $booking->schedule->date->format('d.m.Y') }}</strong></td>
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
                                                    <button type="submit" class="btn btn-outline-danger btn-action">
                                                        <i class="fas fa-times me-1"></i>Отменить
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Мобильная версия карточек -->
                        <div class="mobile-view">
                            @foreach($upcomingBookings as $booking)
                                <div class="mobile-booking-card">
                                    <div class="booking-row">
                                        <span class="booking-label">Дата и время:</span>
                                        <span class="booking-value">
                                            {{ $booking->schedule->date->format('d.m.Y') }} 
                                            {{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}
                                        </span>
                                    </div>
                                    <div class="booking-row">
                                        <span class="booking-label">Тренировка:</span>
                                        <span class="booking-value">{{ $booking->schedule->workout->name }}</span>
                                    </div>
                                    <div class="booking-row">
                                        <span class="booking-label">Тренер:</span>
                                        <span class="booking-value">{{ $booking->schedule->trainer->name }}</span>
                                    </div>
                                    <div class="booking-actions">
                                        <form action="{{ route('client.bookings.cancel', $booking) }}" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Вы уверены, что хотите отменить бронирование?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="fas fa-times me-1"></i>Отменить
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-3 text-center text-md-start">
                            <a href="{{ route('client.schedule') }}" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-2"></i>Забронировать еще
                            </a>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h4>Нет предстоящих тренировок</h4>
                            <p>У вас пока нет запланированных тренировок</p>
                            <a href="{{ route('client.schedule') }}" class="btn btn-primary">
                                <i class="fas fa-calendar-alt me-2"></i>Посмотреть расписание
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Правая колонка (Абонемент + Быстрые действия) -->
        <div class="col-lg-4">
            <!-- Улучшенный активный абонемент (НОВЫЙ ДИЗАЙН) -->
            <div class="main-card fade-in" style="animation-delay: 0.7s">
                <div class="card-header">
                    <i class="fas fa-id-card"></i> Ваш абонемент
                </div>
                <div class="card-body">
                    @if($activeSubscription)
                        @php
                            $subscription = $activeSubscription->subscription;
                            $totalWorkouts = $subscription->workouts_count ?? 0;
                            $remaining = $activeSubscription->remaining_workouts;
                            $used = max(0, $totalWorkouts - $remaining);
                            $percentage = $totalWorkouts > 0 ? round(($used / $totalWorkouts) * 100) : 0;
                        @endphp
                    
                        <div class="text-center mb-4">
                            <div class="subscription-icon mb-3">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <h4 class="fw-bold mb-1">{{ $subscription->name ?? 'Абонемент' }}</h4>
                            <p class="text-muted small">{{ $subscription->description ?? '' }}</p>
                        </div>
                        
                        <!-- Прогресс бар -->
                        <div class="mb-4">
                            <div class="subscription-progress">
                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between text-muted small mt-2">
                                <span><i class="fas fa-check-circle text-success me-1"></i>Использовано: <strong>{{ $used }}</strong></span>
                                <span><i class="fas fa-clock text-warning me-1"></i>Осталось: <strong>{{ $remaining }}</strong></span>
                                <span><i class="fas fa-list text-info me-1"></i>Всего: <strong>{{ $totalWorkouts }}</strong></span>
                            </div>
                        </div>
                        
                        <!-- Детали абонемента в карточках -->
                        <div class="subscription-details-grid mb-4">
                            <div class="detail-item">
                                <i class="fas fa-calendar-alt text-primary"></i>
                                <div>
                                    <small class="text-muted d-block">Начало</small>
                                    <strong>{{ \Carbon\Carbon::parse($activeSubscription->start_date)->format('d.m.Y') }}</strong>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <i class="fas fa-calendar-check text-success"></i>
                                <div>
                                    <small class="text-muted d-block">Действует до</small>
                                    <strong>{{ \Carbon\Carbon::parse($activeSubscription->end_date)->format('d.m.Y') }}</strong>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <i class="fas fa-tag text-info"></i>
                                <div>
                                    <small class="text-muted d-block">Статус</small>
                                    <strong>
                                        @if($activeSubscription->status === 'active')
                                            <span class="badge bg-success">Активен</span>
                                        @elseif($activeSubscription->status === 'expired')
                                            <span class="badge bg-danger">Истек</span>
                                        @elseif($activeSubscription->status === 'frozen')
                                            <span class="badge bg-warning">Заморожен</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $activeSubscription->status }}</span>
                                        @endif
                                    </strong>
                                </div>
                            </div>
                            
                            <div class="detail-item">
                                <i class="fas fa-ruble-sign text-warning"></i>
                                <div>
                                    <small class="text-muted d-block">Цена</small>
                                    <strong>{{ number_format($subscription->price ?? 0, 0, ',', ' ') }} ₽</strong>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Кнопка с отступом -->
                        <div class="subscription-action mt-4">
                            <a href="{{ route('client.subscriptions') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-sync-alt me-2"></i>Продлить или купить новый
                            </a>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-id-card"></i>
                            <h4>Нет активного абонемента</h4>
                            <p class="mb-4">Приобретите абонемент, чтобы начать тренировки</p>
                            <a href="{{ route('client.subscriptions') }}" class="btn btn-primary w-100 py-3">
                                <i class="fas fa-shopping-cart me-2"></i>Купить абонемент
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Быстрые действия -->
            <div class="main-card mt-4 fade-in" style="animation-delay: 0.8s">
                <div class="card-header">
                    <i class="fas fa-bolt"></i> Быстрые действия
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('client.schedule') }}" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-calendar-alt me-2"></i> Расписание
                        </a>
                        <a href="{{ route('client.subscriptions') }}" class="btn btn-outline-success btn-lg">
                            <i class="fas fa-credit-card me-2"></i> Абонементы
                        </a>
                        <a href="{{ route('client.profile') }}" class="btn btn-outline-info btn-lg">
                            <i class="fas fa-user-cog me-2"></i> Профиль
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- История посещений (вынесена вниз) -->
    <div class="row">
        <div class="col-12">
            <div class="main-card mt-4 fade-in" style="animation-delay: 0.6s">
                <div class="card-header">
                    <i class="fas fa-history"></i> История посещений
                </div>
                <div class="card-body">
                    @if($pastBookings->count() > 0)
                        <!-- Десктопная версия таблицы -->
                        <div class="desktop-view">
                            <div class="table-responsive">
                                <table class="table dashboard-table">
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
                                                        'attended' => 'badge-attended',
                                                        'missed' => 'badge-missed',
                                                        'cancelled' => 'badge-cancelled',
                                                        default => 'bg-secondary'
                                                    };
                                                    $statusText = match($booking->status) {
                                                        'attended' => 'Посещено',
                                                        'missed' => 'Пропущено',
                                                        'cancelled' => 'Отменено',
                                                        default => $booking->status
                                                    };
                                                @endphp
                                                <span class="badge-custom {{ $badgeClass }}">
                                                    <i class="fas fa-{{ $booking->status == 'attended' ? 'check-circle' : ($booking->status == 'missed' ? 'times-circle' : 'exclamation-circle') }} me-1"></i>
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Мобильная версия карточек -->
                        <div class="mobile-view">
                            @foreach($pastBookings as $booking)
                                <div class="mobile-booking-card {{ $booking->status }}">
                                    <div class="booking-row">
                                        <span class="booking-label">Дата:</span>
                                        <span class="booking-value">{{ $booking->schedule->date->format('d.m.Y') }}</span>
                                    </div>
                                    <div class="booking-row">
                                        <span class="booking-label">Тренировка:</span>
                                        <span class="booking-value">{{ $booking->schedule->workout->name }}</span>
                                    </div>
                                    <div class="booking-row">
                                        <span class="booking-label">Статус:</span>
                                        <span class="booking-value">
                                            @php
                                                $badgeClass = match($booking->status) {
                                                    'attended' => 'badge-attended',
                                                    'missed' => 'badge-missed',
                                                    'cancelled' => 'badge-cancelled',
                                                    default => 'bg-secondary'
                                                };
                                                $statusText = match($booking->status) {
                                                    'attended' => 'Посещено',
                                                    'missed' => 'Пропущено',
                                                    'cancelled' => 'Отменено',
                                                    default => $booking->status
                                                };
                                            @endphp
                                            <span class="badge-custom {{ $badgeClass }}">
                                                {{ $statusText }}
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-history"></i>
                            <h4>Нет истории посещений</h4>
                            <p>У вас пока нет завершенных тренировок</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection