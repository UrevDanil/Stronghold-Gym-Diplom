<!-- Мои абонементы -->
@extends('layouts.app')

@section('title', 'Мои абонементы')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/client/client-subscriptions.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4">
    <!-- Заголовок с кнопкой назад -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-5 fw-bold greeting-title mb-0">Мои абонементы</h1>
        <a href="{{ route('client.dashboard') }}" class="back-btn-subscription">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
    </div>
    
    @if(session('success'))
        <div class="alert-modern success alert-dismissible fade show" role="alert">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="alert-content">
                <div class="alert-title">Отлично!</div>
                <div class="alert-message">{{ session('success') }}</div>
            </div>
            <button type="button" class="alert-close" data-bs-dismiss="alert">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <!-- Определяем, есть ли замороженный абонемент для показа -->
    @php
        $subscriptionToShow = null;
        $subscriptionType = '';
        
        // Сначала проверяем, есть ли замороженный абонемент
        if(isset($userSubscriptions) && $userSubscriptions->count() > 0) {
            $frozenSub = $userSubscriptions->first(function($sub) {
                return $sub->status === 'frozen';
            });
            
            if($frozenSub) {
                $subscriptionToShow = $frozenSub;
                $subscriptionType = 'frozen';
            }
        }
        
        // Если нет замороженного, проверяем активный
        if(!$subscriptionToShow && $user->activeSubscription()) {
            $subscriptionToShow = $user->activeSubscription();
            $subscriptionType = 'active';
        }
    @endphp
    
    <!-- Блок с абонементом (активным или замороженным) -->
    @if($subscriptionToShow)
        @php
            $activeUserSub = $subscriptionToShow;
            $subscription = $activeUserSub->subscription;
            
            $remaining = $activeUserSub->remaining_workouts;
            $total = $subscription->workouts_count ?? 0;
            $used = max(0, $total - $remaining);
            $percentage = $total > 0 ? min(100, ($used / $total) * 100) : 0;
            
            $endDate = \Carbon\Carbon::parse($activeUserSub->end_date);
            $now = \Carbon\Carbon::now();
            
            if ($now->greaterThan($endDate)) {
                $daysLeft = 0;
                $hoursLeft = 0;
                $minutesLeft = 0;
                $secondsLeft = 0;
                $timeLeft = 'Истек';
                $isExpired = true;
            } else {
                $diffInSeconds = $now->diffInSeconds($endDate);
                $daysLeft = floor($diffInSeconds / 86400);
                $hoursLeft = floor(($diffInSeconds % 86400) / 3600);
                $minutesLeft = floor(($diffInSeconds % 3600) / 60);
                $secondsLeft = $diffInSeconds % 60;
                $isExpired = false;
                
                if ($daysLeft > 0) {
                    $timeLeft = $daysLeft . ' д ' . $hoursLeft . ' ч';
                } else {
                    $timeLeft = $hoursLeft . ':' . str_pad($minutesLeft, 2, '0', STR_PAD_LEFT) . ':' . str_pad($secondsLeft, 2, '0', STR_PAD_LEFT);
                }
            }
            
            $isFrozen = $activeUserSub->isPaused();
        @endphp
        
        <div class="subscription-card fade-in">
            <div class="subscription-header {{ $subscriptionType === 'frozen' ? 'frozen' : 'active' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas {{ $subscriptionType === 'frozen' ? 'fa-snowflake' : 'fa-crown' }} me-2"></i>
                        {{ $subscriptionType === 'frozen' ? 'Абонемент заморожен' : 'Текущий абонемент' }}
                    </h4>
                    @if($subscriptionType === 'frozen' && $activeUserSub->paused_until)
                        <span class="badge bg-light text-dark">
                            Заморожен до: {{ \Carbon\Carbon::parse($activeUserSub->paused_until)->format('d.m.Y H:i') }}
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="card-body">
                <div class="text-center mb-4">
                    <div class="subscription-icon-large">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h2 class="subscription-title">{{ $subscription->name ?? 'Абонемент' }}</h2>
                    <p class="subscription-description">{{ $subscription->description ?? '' }}</p>
                </div>
                
                @if($subscriptionType !== 'frozen')
                    <div class="mb-4">
                        <div class="progress-label">
                            <span>Прогресс абонемента</span>
                            <span class="progress-percentage">{{ round($percentage, 1) }}%</span>
                        </div>
                        <div class="subscription-progress-large">
                            <div class="progress-bar" style="width: {{ $percentage }}%">
                                {{ $used }}/{{ $total }}
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Статистика СВЕРХУ (как было) -->
                <div class="subscription-stats">
                    @if($subscriptionType !== 'frozen')
                        <div class="stat-box">
                            <span class="stat-label">Осталось тренировок</span>
                            <span class="stat-number success">{{ $remaining }}</span>
                        </div>
                    @endif
                    
                    <div class="stat-box">
                        <span class="stat-label">Осталось дней</span>
                        <span class="stat-number info">{{ $daysLeft }}</span>
                    </div>
                    
                    <div class="stat-box">
                        <span class="stat-label">Точное время</span>
                        <span class="stat-number warning">{{ $hoursLeft }}:{{ str_pad($minutesLeft, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($secondsLeft, 2, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>
                
                <!-- Кнопка действия (только заморозка/разморозка, без продлить) -->
                <div class="subscription-actions">
                    @if($subscriptionType === 'frozen')
                        <form action="{{ route('client.subscriptions.resume') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn-gradient btn-gradient-success">
                                <i class="fas fa-play me-2"></i>Разморозить абонемент
                                <span class="btn-glow"></span>
                            </button>
                        </form>
                    @elseif($subscriptionType === 'active')
                        <button class="btn-gradient btn-gradient-warning" data-bs-toggle="modal" 
                                data-bs-target="#freezeModal" {{ $isExpired ? 'disabled' : '' }}>
                            <i class="fas fa-snowflake me-2"></i>Заморозить абонемент
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @else
        {{-- Нет абонемента вообще --}}
        <div class="empty-state-large fade-in">
            <i class="fas fa-id-card fa-4x text-muted mb-4"></i>
            <h3 class="mb-3">Нет активного абонемента</h3>
            <p class="text-muted mb-4">Приобретите абонемент, чтобы начать тренировки</p>
            <a href="{{ route('subscriptions.index') }}" class="btn-gradient btn-gradient-primary btn-lg">
                <i class="fas fa-shopping-cart me-2"></i>Выбрать абонемент
                <span class="btn-glow"></span>
            </a>
        </div>
    @endif
    
    <!-- История абонементов (всегда показываем) -->
    <div class="main-card mt-5 fade-in">
        <div class="card-header">
            <i class="fas fa-history"></i> История абонементов
        </div>
        <div class="card-body">
            @if(isset($userSubscriptions) && $userSubscriptions->count() > 0)
                <!-- Десктопная версия таблицы (только на компьютере) -->
                <div class="table-responsive">
                    <table class="table subscription-history-table">
                        <thead>
                            <tr>
                                <th>Абонемент</th>
                                <th>Дата активации</th>
                                <th>Окончание</th>
                                <th>Тренировок</th>
                                <th>Статус</th>
                                <th>Цена</th>
                             </tr>
                        </thead>
                        <tbody>
                            @foreach($userSubscriptions as $userSub)
                                @php
                                    $isUserSubscription = method_exists($userSub, 'isActive');
                                    
                                    if ($isUserSubscription) {
                                        $subscriptionName = $userSub->subscription->name ?? 'Абонемент';
                                        $workoutsCount = $userSub->subscription->workouts_count ?? 0;
                                        $remainingWorkouts = $userSub->remaining_workouts;
                                        $status = $userSub->status;
                                        $startDate = $userSub->start_date;
                                        $endDate = $userSub->end_date;
                                        $price = $userSub->subscription->price ?? 0;
                                    } else {
                                        $subscriptionName = $userSub->name;
                                        $workoutsCount = $userSub->workouts_count ?? 0;
                                        $remainingWorkouts = $userSub->pivot->remaining_workouts;
                                        $status = $userSub->pivot->status;
                                        $startDate = $userSub->pivot->start_date;
                                        $endDate = $userSub->pivot->end_date;
                                        $price = $userSub->price;
                                    }
                                @endphp
                                <tr>
                                    <td><strong>{{ $subscriptionName }}</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}</td>
                                    <td>
                                        <span class="badge-custom {{ $remainingWorkouts > 0 ? 'badge-attended' : 'badge-missed' }}">
                                            {{ $remainingWorkouts }}/{{ $workoutsCount }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($status === 'active')
                                            @if($endDate < now()->toDateString())
                                                <span class="badge-custom bg-secondary">Истек</span>
                                            @elseif($remainingWorkouts <= 0)
                                                <span class="badge-custom badge-missed"><i class="fas fa-exclamation-circle me-1"></i>Использован</span>
                                            @else
                                                <span class="badge-custom badge-attended">Активен</span>
                                            @endif
                                        @elseif($status === 'expired')
                                            <span class="badge-custom bg-secondary">Истек</span>
                                        @elseif($status === 'frozen')
                                            <span class="badge-custom badge-cancelled"><i class="fas fa-snowflake me-1"></i>Заморожен</span>
                                        @elseif($status === 'canceled')
                                            <span class="badge-custom badge-missed">Отменен</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ number_format($price, 0, ',', ' ') }} ₽</strong></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Мобильная версия карточек -->
                <div class="mobile-subscription-cards">
                    @foreach($userSubscriptions as $userSub)
                        @php
                            $isUserSubscription = method_exists($userSub, 'isActive');
                            
                            if ($isUserSubscription) {
                                $subscriptionName = $userSub->subscription->name ?? 'Абонемент';
                                $workoutsCount = $userSub->subscription->workouts_count ?? 0;
                                $remainingWorkouts = $userSub->remaining_workouts;
                                $status = $userSub->status;
                                $startDate = $userSub->start_date;
                                $endDate = $userSub->end_date;
                                $price = $userSub->subscription->price ?? 0;
                            } else {
                                $subscriptionName = $userSub->name;
                                $workoutsCount = $userSub->workouts_count ?? 0;
                                $remainingWorkouts = $userSub->pivot->remaining_workouts;
                                $status = $userSub->pivot->status;
                                $startDate = $userSub->pivot->start_date;
                                $endDate = $userSub->pivot->end_date;
                                $price = $userSub->price;
                            }
                            
                            if ($status === 'active') {
                                if ($endDate < now()->toDateString()) {
                                    $badgeClass = 'bg-secondary';
                                    $badgeText = 'Истек';
                                } elseif ($remainingWorkouts <= 0) {
                                    $badgeClass = 'badge-missed';
                                    $badgeText = 'Использован';
                                } else {
                                    $badgeClass = 'badge-attended';
                                    $badgeText = 'Активен';
                                }
                            } elseif ($status === 'expired') {
                                $badgeClass = 'bg-secondary';
                                $badgeText = 'Истек';
                            } elseif ($status === 'frozen') {
                                $badgeClass = 'badge-cancelled';
                                $badgeText = 'Заморожен';
                            } elseif ($status === 'canceled') {
                                $badgeClass = 'badge-missed';
                                $badgeText = 'Отменен';
                            } else {
                                $badgeClass = 'bg-secondary';
                                $badgeText = $status;
                            }
                        @endphp
                        
                        <div class="subscription-mobile-card" data-status="{{ $status }}">
                            <div class="card-header-mobile">
                                <h5>{{ $subscriptionName }}</h5>
                                <span class="price">{{ number_format($price, 0, ',', ' ') }} ₽</span>
                            </div>
                            
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="label">Активация</span>
                                    <span class="value">{{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Окончание</span>
                                    <span class="value">{{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Тренировки</span>
                                    <span class="value">
                                        <span class="badge-custom {{ $remainingWorkouts > 0 ? 'badge-attended' : 'badge-missed' }}">
                                            {{ $remainingWorkouts }}/{{ $workoutsCount }}
                                        </span>
                                    </span>
                                </div>
                                <div class="info-item">
                                    <span class="label">Цена</span>
                                    <span class="value">{{ number_format($price, 0, ',', ' ') }} ₽</span>
                                </div>
                            </div>
                            
                            <div class="status-row">
                                <span class="badge-custom {{ $badgeClass }}">
                                    @if($status === 'frozen')
                                        <i class="fas fa-snowflake me-1"></i>
                                    @elseif($status === 'active' && $remainingWorkouts > 0 && $endDate >= now()->toDateString())
                                        <i class="fas fa-check-circle me-1"></i>
                                    @elseif($status === 'active' && $remainingWorkouts <= 0)
                                        <i class="fas fa-exclamation-circle me-1"></i>
                                    @elseif($status === 'expired' || ($status === 'active' && $endDate < now()->toDateString()))
                                        <i class="fas fa-clock me-1"></i>
                                    @elseif($status === 'canceled')
                                        <i class="fas fa-times-circle me-1"></i>
                                    @endif
                                    {{ $badgeText }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-history"></i>
                    <h4>Нет истории абонементов</h4>
                    <p>У вас пока нет истории покупок</p>
                </div>
            @endif
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('subscriptions.index') }}" class="btn-gradient btn-gradient-primary btn-lg">
            <i class="fas fa-plus-circle me-2"></i>Купить новый абонемент
            <span class="btn-glow"></span>
        </a>
    </div>
</div>

<!-- Модальное окно заморозки -->
<div class="modal fade" id="freezeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('freeze-subscription') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-snowflake me-2"></i>Заморозка абонемента
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Вы можете заморозить абонемент на срок до 14 дней.</p>
                    <p>Причины заморозки:</p>
                    <ul>
                        <li><i class="fas fa-thermometer-half"></i> Болезнь</li>
                        <li><i class="fas fa-briefcase"></i> Командировка</li>
                        <li><i class="fas fa-umbrella-beach"></i> Отпуск</li>
                    </ul>
                    
                    <div class="mb-3">
                        <label for="reason" class="form-label">Причина заморозки</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Укажите причину заморозки..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="days" class="form-label">Количество дней (макс. 14)</label>
                        <input type="number" class="form-control" id="days" name="days" min="1" max="14" value="7" required>
                    </div>
                    
                    <div class="alert-info">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Информация</strong><br>
                            После заморозки срок действия абонемента будет продлен на указанное количество дней.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn-gradient btn-gradient-warning">
                        <i class="fas fa-snowflake me-2"></i>Заморозить
                        <span class="btn-glow"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection