<!-- Мои абонементы -->
@extends('layouts.app')

@section('title', 'Мои абонементы')

@section('content')
<div class="container py-4">
    <h1 class="mb-4">Мои абонементы</h1>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <!-- Активный абонемент -->
    @if($user->activeSubscription())
        @php
            $activeUserSub = $user->activeSubscription(); // Это UserSubscription
            $subscription = $activeUserSub->subscription; // Это Subscription
            
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
        
        <div class="card mb-4 shadow {{ $isFrozen ? 'border-warning' : '' }}">
            <div class="card-header text-white" style="background: linear-gradient(135deg, {{ $isFrozen ? '#ffc107' : '#28a745' }}, {{ $isFrozen ? '#fd7e14' : '#20c997' }});">
                <h4 class="mb-0 d-flex justify-content-between align-items-center">
                    <span>
                        <i class="fas {{ $isFrozen ? 'fa-snowflake' : 'fa-crown' }} me-2"></i>
                        {{ $isFrozen ? 'Замороженный абонемент' : 'Текущий абонемент' }}
                    </span>
                    @if($isFrozen)
                        <span class="badge bg-light text-dark">
                            Заморожен до: {{ \Carbon\Carbon::parse($activeUserSub->paused_until)->format('d.m.Y H:i') }}
                        </span>
                    @endif
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-id-card fa-4x {{ $isFrozen ? 'text-warning' : 'text-success' }} mb-3"></i>
                            <h3>{{ $subscription->name ?? 'Абонемент' }}</h3>
                            <p class="text-muted">{{ $subscription->description ?? '' }}</p>
                            @if($isFrozen && $activeUserSub->pause_reason)
                                <p class="text-warning">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Причина: {{ $activeUserSub->pause_reason }}
                                </p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        @if(!$isFrozen)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Использовано тренировок</span>
                                    <span><strong>{{ $used }}/{{ $total }}</strong></span>
                                </div>
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                        style="width: {{ $percentage }}%"
                                        aria-valuenow="{{ $percentage }}">
                                        {{ round($percentage, 1) }}%
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <div class="row">
                            @if(!$isFrozen)
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Осталось тренировок</h6>
                                        <h2 class="text-success">{{ $remaining }}</h2>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Осталось дней</h6>
                                        <h2 class="text-info">{{ $daysLeft }}</h2>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="card mb-3">
                                    <div class="card-body text-center">
                                        <h6 class="card-title">Точное время</h6>
                                        <h4 class="text-warning">{{ $hoursLeft }}:{{ str_pad($minutesLeft, 2, '0', STR_PAD_LEFT) }}:{{ str_pad($secondsLeft, 2, '0', STR_PAD_LEFT) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <i class="fas fa-calendar-plus me-2"></i>
                                    <strong>Начало:</strong> 
                                    {{ \Carbon\Carbon::parse($activeUserSub->start_date)->format('d.m.Y') }}
                                </div>
                                <div class="col-md-6">
                                    <i class="fas fa-calendar-minus me-2"></i>
                                    <strong>Окончание:</strong> 
                                    {{ \Carbon\Carbon::parse($activeUserSub->end_date)->format('d.m.Y') }}
                                </div>
                            </div>
                            @if($activeUserSub->original_end_date)
                            <div class="row mt-2">
                                <div class="col-12">
                                    <small class="text-muted">
                                        <i class="fas fa-history me-1"></i>
                                        Исходная дата окончания: {{ \Carbon\Carbon::parse($activeUserSub->original_end_date)->format('d.m.Y') }}
                                    </small>
                                </div>
                            </div>
                            @endif
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            @if(!$isFrozen)
                                <button class="btn btn-outline-warning me-2" data-bs-toggle="modal" 
                                        data-bs-target="#freezeModal" {{ $isExpired ? 'disabled' : '' }}>
                                    <i class="fas fa-snowflake me-2"></i>Заморозить
                                </button>
                                <a href="{{ route('subscriptions.index') }}" class="btn btn-primary">
                                    <i class="fas fa-sync-alt me-2"></i>Продлить
                                </a>
                            @else
                                <form action="{{ route('client.subscriptions.resume') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-play me-2"></i>Разморозить
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card mb-4 shadow">
            <div class="card-header text-white bg-warning">
                <h4 class="mb-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Нет активного абонемента
                </h4>
            </div>
            <div class="card-body text-center py-5">
                <i class="fas fa-id-card fa-5x text-warning mb-4"></i>
                <h3 class="mb-3">У вас нет активного абонемента</h3>
                <p class="text-muted mb-4">Приобретите абонемент, чтобы начать тренировки</p>
                <a href="{{ route('subscriptions.index') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-cart me-2"></i>Выбрать абонемент
                </a>
            </div>
        </div>
    @endif
    
    <!-- История абонементов -->
    <div class="card shadow">
        <div class="card-header">
            <h4 class="mb-0">
                <i class="fas fa-history me-2"></i>История абонементов
            </h4>
        </div>
        <div class="card-body">
            @if(isset($userSubscriptions) && $userSubscriptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
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
                                    // Проверяем, что это за объект
                                    $isUserSubscription = method_exists($userSub, 'isActive');
                                    
                                    if ($isUserSubscription) {
                                        // Это UserSubscription
                                        $isActive = $userSub->isActive();
                                        $isFrozen = $userSub->isPaused();
                                        $subscriptionName = $userSub->subscription->name ?? 'Абонемент';
                                        $workoutsCount = $userSub->subscription->workouts_count ?? 0;
                                        $remainingWorkouts = $userSub->remaining_workouts;
                                        $status = $userSub->status;
                                        $startDate = $userSub->start_date;
                                        $endDate = $userSub->end_date;
                                        $price = $userSub->subscription->price ?? 0;
                                    } else {
                                        // Это Subscription с pivot (старая структура)
                                        $isActive = $userSub->pivot->status === 'active' && 
                                                   $userSub->pivot->end_date >= now()->toDateString() &&
                                                   $userSub->pivot->remaining_workouts > 0;
                                        $isFrozen = $userSub->pivot->status === 'frozen';
                                        $subscriptionName = $userSub->name;
                                        $workoutsCount = $userSub->workouts_count ?? 0;
                                        $remainingWorkouts = $userSub->pivot->remaining_workouts;
                                        $status = $userSub->pivot->status;
                                        $startDate = $userSub->pivot->start_date;
                                        $endDate = $userSub->pivot->end_date;
                                        $price = $userSub->price;
                                    }
                                @endphp
                                <tr class="{{ $isActive ? 'table-success' : ($isFrozen ? 'table-warning' : '') }}">
                                    <td>
                                        <strong>{{ $subscriptionName }}</strong>
                                        @if($isActive)
                                            <span class="badge bg-success ms-2">Активен</span>
                                        @elseif($isFrozen)
                                            <span class="badge bg-warning ms-2">Заморожен</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($startDate)->format('d.m.Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($endDate)->format('d.m.Y') }}</td>
                                    <td>
                                        {{ $remainingWorkouts }}/{{ $workoutsCount }}
                                    </td>
                                    <td>
                                        @if($status === 'active')
                                            @if($endDate < now()->toDateString())
                                                <span class="badge bg-danger">Истек</span>
                                            @elseif($remainingWorkouts <= 0)
                                                <span class="badge bg-warning">Использован</span>
                                            @else
                                                <span class="badge bg-success">Активен</span>
                                            @endif
                                        @elseif($status === 'expired')
                                            <span class="badge bg-secondary">Истек</span>
                                        @elseif($status === 'frozen')
                                            <span class="badge bg-info">Заморожен</span>
                                        @elseif($status === 'canceled')
                                            <span class="badge bg-danger">Отменен</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($price, 0, ',', ' ') }} ₽</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <p class="text-muted">У вас еще нет истории абонементов</p>
                </div>
            @endif
        </div>
    </div>
    
    <div class="text-center mt-4">
        <a href="{{ route('subscriptions.index') }}" class="btn btn-primary btn-lg">
            <i class="fas fa-plus-circle me-2"></i>Купить новый абонемент
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
                    <h5 class="modal-title">Заморозка абонемента</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Вы можете заморозить абонемент на срок до 14 дней.</p>
                    <p>Причины заморозки:</p>
                    <ul>
                        <li>Болезнь</li>
                        <li>Командировка</li>
                        <li>Отпуск</li>
                    </ul>
                    
                    <div class="mb-3">
                        <label for="reason" class="form-label">Причина заморозки</label>
                        <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="days" class="form-label">Количество дней (макс. 14)</label>
                        <input type="number" class="form-control" id="days" name="days" min="1" max="14" value="7" required>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        После заморозки срок действия абонемента будет продлен на указанное количество дней.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-snowflake me-2"></i>Заморозить
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.progress {
    border-radius: 10px;
    overflow: hidden;
}

.progress-bar {
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
}

.card {
    border: none;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

.badge {
    font-size: 0.8rem;
    padding: 0.35em 0.65em;
}

.table-success {
    background-color: rgba(40, 167, 69, 0.1);
}

.table-warning {
    background-color: rgba(255, 193, 7, 0.1);
}
</style>
@endsection