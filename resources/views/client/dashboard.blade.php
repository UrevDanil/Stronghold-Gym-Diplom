<!-- Личный кабинет -->
@extends('layouts.app')

@section('title', 'Личный кабинет')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/common.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/dashboard/client.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4">
    <!-- Приветствие с датой -->
    <div class="row mb-5 fade-in">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div>
                    <h1 class="display-5 fw-bold greeting-title mb-2">
                        Добро пожаловать, {{ $user->name }}!
                    </h1>
                    <div class="date-badge">
                        <i class="fas fa-calendar-alt me-2"></i>
                        {{ now()->isoFormat('dddd, D MMMM YYYY') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Проверяем, есть ли замороженный абонемент -->
    @php
        $hasFrozen = false;
        $frozenSub = null;
        if(isset($userSubscriptions) && $userSubscriptions->count() > 0) {
            $frozenSub = $userSubscriptions->first(function($sub) {
                return $sub->status === 'frozen';
            });
            if($frozenSub) {
                $hasFrozen = true;
            }
        }
    @endphp

    <!-- Активный абонемент -->
    <div class="main-card fade-in mb-5" style="animation-delay: 0.7s">
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
                    <h4 class="subscription-title">{{ $subscription->name ?? 'Абонемент' }}</h4>
                    <p class="subscription-description">{{ $subscription->description ?? '' }}</p>
                </div>            
                
                <!-- Прогресс бар -->
                <div class="progress-wrapper mb-4">
                    <div class="progress-label">
                        <span>Прогресс абонемента</span>
                        <span class="progress-percentage">{{ $percentage }}%</span>
                    </div>
                    <div class="subscription-progress">
                        <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                
                <!-- Кнопка -->
                <div class="subscription-action">
                    <a href="{{ route('client.subscriptions') }}" class="btn-gradient btn-gradient-primary w-100 py-3">
                        <i class="fas fa-sync-alt me-2"></i>Продлить или купить новый
                        <span class="btn-glow"></span>
                    </a>
                </div>
                @elseif($hasFrozen)
                    {{-- Абонемент заморожен --}}
                    <div class="frozen-subscription-container">
                        <!-- Декоративные снежинки -->
                        <div class="snowflake">❄️</div>
                        <div class="snowflake">❄️</div>
                        <div class="snowflake">❄️</div>
                        <div class="snowflake">❄️</div>
                        
                        <div class="text-center">
                            <div class="frozen-icon-large">
                                <i class="fas fa-snowflake"></i>
                            </div>
                            
                            <h2 class="frozen-title">❄️ Ваш абонемент заморожен</h2>
                            
                            <p class="frozen-message">
                                Абонемент временно заморожен. Вы можете разморозить его в любой момент 
                                в разделе "Абонементы".
                            </p>
                            
                            <a href="{{ route('client.subscriptions') }}" class="btn-frozen">
                                <i class="fas fa-play"></i>
                                Разморозить абонемент
                                <span class="btn-glow"></span>
                            </a>
                        </div>
                    </div>
                @else
                {{-- Нет абонемента --}}
                <div class="empty-state">
                    <i class="fas fa-id-card"></i>
                    <h4>Нет активного абонемента</h4>
                    <p class="mb-4">Приобретите абонемент, чтобы начать тренировки</p>
                    <a href="{{ route('client.subscriptions') }}" class="btn-gradient btn-gradient-primary w-100 py-3">
                        <i class="fas fa-shopping-cart me-2"></i>Купить абонемент
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- 4 маленьких блока статистики -->
    <div class="row g-4 mb-5">
        <!-- Активный абонемент (краткая информация) -->
        <div class="col-6 col-md-3 fade-in" style="animation-delay: 0.1s">
            <div class="stat-card bg-primary-gradient text-white">
                <div class="card-body">
                    <h6 class="card-title">Активный абонемент</h6>
                    @if($hasFrozen && $frozenSub)
                        <div class="stat-value">Заморожен</div>
                        <small class="stat-label">{{ $frozenSub->subscription->workouts_count ?? 0 }} тренировок</small>
                    @elseif($activeSubscription)
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
                    @if($hasFrozen && $frozenSub)
                        <div class="stat-value">{{ $frozenSub->remaining_workouts }}</div>
                        <small class="stat-label">из {{ $frozenSub->subscription->workouts_count ?? 0 }}</small>
                    @elseif($activeSubscription)
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
                    @if($hasFrozen && $frozenSub)
                        <div class="stat-value">{{ \Carbon\Carbon::parse($frozenSub->end_date)->format('d.m') }}</div>
                        <small class="stat-label">{{ \Carbon\Carbon::parse($frozenSub->end_date)->format('Y') }}</small>
                    @elseif($activeSubscription)
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

    <!-- Предстоящие тренировки -->
    <div class="row">
        <div class="col-lg-12">
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
                                                    <button type="submit" class="btn-outline-danger btn-action">
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
                                              class="d-inline w-100"
                                              onsubmit="return confirm('Вы уверены, что хотите отменить бронирование?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-gradient btn-gradient-danger w-100 py-2">
                                                <i class="fas fa-times me-2"></i>Отменить
                                                <span class="btn-glow"></span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Кнопка -->
                        <div class="mt-3 text-center text-md-start">
                            <a href="{{ route('client.schedule') }}" class="btn-gradient btn-gradient-success">
                                <i class="fas fa-calendar-plus me-2"></i>Забронировать еще
                                <span class="btn-glow"></span>
                            </a>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-calendar-times"></i>
                            <h4>Нет предстоящих тренировок</h4>
                            <p>У вас пока нет запланированных тренировок</p>
                            <a href="{{ route('client.schedule') }}" class="btn-gradient btn-gradient-success">
                                <i class="fas fa-calendar-alt me-2"></i>Посмотреть расписание
                                <span class="btn-glow"></span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- История посещений -->
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