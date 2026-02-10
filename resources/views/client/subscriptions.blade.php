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
    
    <!-- Активный абонемент -->
    @if($user->activeSubscription())
        @php
            $activeSub = $user->activeSubscription();
            $remaining = $activeSub->pivot->remaining_workouts;
            $total = $activeSub->workouts_count; // ИЗМЕНИЛ: session_count → workouts_count
            $used = max(0, $total - $remaining);
            $percentage = $total > 0 ? min(100, ($used / $total) * 100) : 0;
            $daysLeft = \Carbon\Carbon::parse($activeSub->pivot->end_date)->diffInDays(now());
        @endphp
        
        <div class="card mb-4 shadow">
            <div class="card-header text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <h4 class="mb-0">
                    <i class="fas fa-crown me-2"></i>Текущий абонемент
                </h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <i class="fas fa-id-card fa-4x text-success mb-3"></i>
                            <h3>{{ $activeSub->name }}</h3>
                            <p class="text-muted">{{ $activeSub->description }}</p>
                        </div>
                    </div>
                    
                    <div class="col-md-8">
                        <!-- Прогресс бар -->
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
                        
                        <!-- Статистика -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Осталось тренировок</h6>
                                        <h2 class="text-center text-success">{{ $remaining }}</h2>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Осталось дней</h6>
                                        <h2 class="text-center text-info">{{ $daysLeft }}</h2>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Срок действия -->
                        <div class="alert alert-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <i class="fas fa-calendar-plus me-2"></i>
                                    <strong>Начало:</strong> 
                                    {{ \Carbon\Carbon::parse($activeSub->pivot->start_date)->format('d.m.Y') }}
                                </div>
                                <div class="col-md-6">
                                    <i class="fas fa-calendar-minus me-2"></i>
                                    <strong>Окончание:</strong> 
                                    {{ \Carbon\Carbon::parse($activeSub->pivot->end_date)->format('d.m.Y') }}
                                </div>
                            </div>
                        </div>
                        
                        <!-- Действия -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button class="btn btn-outline-warning me-2" data-bs-toggle="modal" 
                                    data-bs-target="#freezeModal">
                                <i class="fas fa-snowflake me-2"></i>Заморозить
                            </button>
                            <a href="{{ route('subscriptions.index') }}" class="btn btn-primary">
                                <i class="fas fa-sync-alt me-2"></i>Продлить
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Нет активного абонемента -->
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
            @if($userSubscriptions->count() > 0)
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
                                    $subscription = $userSub->pivot;
                                    $isActive = $subscription->status === 'active' && 
                                               $subscription->end_date >= now()->toDateString() &&
                                               $subscription->remaining_workouts > 0;
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $userSub->name }}</strong>
                                        @if($isActive)
                                            <span class="badge bg-success ms-2">Активен</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($subscription->start_date)->format('d.m.Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($subscription->end_date)->format('d.m.Y') }}</td>
                                    <td>
                                        {{ $subscription->remaining_workouts }}/{{ $userSub->workouts_count }} <!-- ИЗМЕНИЛ -->
                                    </td>
                                    <td>
                                        @if($subscription->status === 'active')
                                            @if($subscription->end_date < now()->toDateString())
                                                <span class="badge bg-danger">Истек</span>
                                            @elseif($subscription->remaining_workouts <= 0)
                                                <span class="badge bg-warning">Использован</span>
                                            @else
                                                <span class="badge bg-success">Активен</span>
                                            @endif
                                        @elseif($subscription->status === 'expired')
                                            <span class="badge bg-secondary">Истек</span>
                                        @elseif($subscription->status === 'frozen')
                                            <span class="badge bg-info">Заморожен</span>
                                        @elseif($subscription->status === 'canceled')
                                            <span class="badge bg-danger">Отменен</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($userSub->price, 0, ',', ' ') }} ₽</td>
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
    
    <!-- Кнопка для покупки нового -->
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
                    <label for="freezeReason" class="form-label">Причина заморозки</label>
                    <textarea class="form-control" id="freezeReason" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <label for="freezeDays" class="form-label">Количество дней (макс. 14)</label>
                    <input type="number" class="form-control" id="freezeDays" min="1" max="14" value="7">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                <button type="button" class="btn btn-warning">Заморозить</button>
            </div>
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
}

.badge {
    font-size: 0.8rem;
    padding: 0.35em 0.65em;
}
</style>
@endsection