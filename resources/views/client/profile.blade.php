<!-- Профиль -->
@extends('layouts.app')

@section('title', 'Мой профиль')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/common.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/dashboard/client.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4 profile-page">
    <!-- Заголовок -->
    <div class="profile-header d-flex justify-content-between align-items-center">
        <h2 class="mb-0">
            <i class="fas fa-user-circle me-3"></i>Мой профиль
        </h2>
        <a href="{{ route('client.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
    </div>

    @if(session('success'))
        <div class="alert profile-alert success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert profile-alert error alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="profile-card">
        <!-- Вкладки -->
        <div class="profile-tabs">
            <ul class="nav nav-tabs" id="profileTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" 
                            data-bs-target="#personal" type="button" role="tab">
                        <i class="fas fa-user"></i>Личные данные
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="security-tab" data-bs-toggle="tab" 
                            data-bs-target="#security" type="button" role="tab">
                        <i class="fas fa-lock"></i>Безопасность
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="subscription-tab" data-bs-toggle="tab" 
                            data-bs-target="#subscription" type="button" role="tab">
                        <i class="fas fa-id-card"></i>Мой абонемент
                    </button>
                </li>
            </ul>
        </div>

        <!-- Содержимое вкладок -->
        <div class="tab-content" id="profileTabContent">
            <!-- Вкладка 1: Личные данные -->
            <div class="tab-pane fade show active" id="personal" role="tabpanel">
                <h3 class="form-section-title">
                    <i class="fas fa-edit me-2"></i>Редактирование профиля
                </h3>
                
                <form method="POST" action="{{ route('client.profile.update') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Имя *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Телефон</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="birth_date" class="form-label">Дата рождения</label>
                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                   id="birth_date" name="birth_date" 
                                   value="{{ old('birth_date', $user->birth_date ? $user->birth_date->format('Y-m-d') : '') }}">
                            @error('birth_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Заметки пользователя -->
                    <div class="mb-4">
                        <label for="notes" class="form-label">Информация для тренера</label>
                        <textarea class="form-control @error('notes') is-invalid @enderror" 
                                  id="notes" name="notes" rows="4" 
                                  placeholder="Здесь вы можете указать информацию, которую хотите сообщить тренеру...">{{ old('notes', $user->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Эта информация будет доступна вашим тренерам
                        </small>
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i>Сохранить изменения
                    </button>
                </form>
            </div>

            <!-- Вкладка 2: Безопасность -->
            <div class="tab-pane fade" id="security" role="tabpanel">
                <h3 class="form-section-title">
                    <i class="fas fa-key me-2"></i>Смена пароля
                </h3>
                
                <form method="POST" action="{{ route('client.password.update') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Текущий пароль *</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                id="current_password" name="current_password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                <i class="fas fa-eye"></i>
                            </button>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Новый пароль *</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                id="password" name="password" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="fas fa-eye"></i>
                            </button>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text">Минимум 8 символов</div>
                    </div>

                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label">Подтверждение пароля *</label>
                        <div class="input-group">
                            <input type="password" class="form-control" 
                                id="password_confirmation" name="password_confirmation" required>
                            <button class="btn btn-outline-secondary" type="button" id="togglePasswordConfirmation">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="btn-save">
                        <i class="fas fa-key me-2"></i>Сменить пароль
                    </button>
                </form>
            </div>

            <!-- Вкладка 3: Абонемент -->
            <div class="tab-pane fade" id="subscription" role="tabpanel">
                @if($user->activeSubscription())
                    @php
                        $activeUserSub = $user->activeSubscription();
                        $subscription = $activeUserSub->subscription;
                        
                        $remaining = $activeUserSub->remaining_workouts;
                        $totalWorkouts = $subscription->workouts_count ?? 0;
                        $used = max(0, $totalWorkouts - $remaining);
                        $percentage = $totalWorkouts > 0 ? min(100, ($used / $totalWorkouts) * 100) : 0;
                        
                        $startDate = \Carbon\Carbon::parse($activeUserSub->start_date);
                        $endDate = \Carbon\Carbon::parse($activeUserSub->end_date);
                        $daysLeft = max(0, $endDate->diffInDays(now(), false));
                    @endphp
                    
                    <div class="subscription-detail-card">
                        <div class="text-center mb-4">
                            <div class="icon-circle">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <h4>{{ $subscription->name ?? 'Абонемент' }}</h4>
                            <p class="text-muted">{{ $subscription->description ?? '' }}</p>
                            
                            @if($activeUserSub->isPaused())
                                <div class="badge-frozen">
                                    <i class="fas fa-snowflake me-1"></i>
                                    Заморожен до {{ $activeUserSub->paused_until->format('d.m.Y H:i') }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Прогресс</span>
                                <span class="fw-bold">{{ round($percentage, 1) }}%</span>
                            </div>
                            <div class="progress-sm">
                                <div class="progress-bar" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <ul class="stats-list">
                                    <li>
                                        <i class="fas fa-dumbbell"></i>
                                        Осталось тренировок
                                        <strong class="text-success">{{ $remaining }}</strong>
                                    </li>
                                    <li>
                                        <i class="fas fa-calendar-check"></i>
                                        Использовано
                                        <strong>{{ $used }}</strong>
                                    </li>
                                    <li>
                                        <i class="fas fa-percentage"></i>
                                        Прогресс
                                        <strong>{{ round($percentage, 1) }}%</strong>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="col-md-6">
                                <ul class="stats-list">
                                    <li>
                                        <i class="fas fa-calendar-plus"></i>
                                        Начало
                                        <strong>{{ $startDate->format('d.m.Y') }}</strong>
                                    </li>
                                    <li>
                                        <i class="fas fa-calendar-minus"></i>
                                        Окончание
                                        <strong>{{ $endDate->format('d.m.Y') }}</strong>
                                    </li>
                                    <li>
                                        <i class="fas fa-clock"></i>
                                        Осталось дней
                                        <strong class="text-warning">{{ $daysLeft }}</strong>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="text-center mt-4">
                            @if($activeUserSub->isPaused())
                                <form action="{{ route('client.subscriptions.resume') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn-subscription">
                                        <i class="fas fa-play me-2"></i>Разморозить абонемент
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('client.subscriptions') }}" class="btn-subscription me-2">
                                    <i class="fas fa-sync-alt me-2"></i>Продлить
                                </a>
                                <button class="btn-subscription-outline" data-bs-toggle="modal" data-bs-target="#freezeModal">
                                    <i class="fas fa-snowflake me-2"></i>Заморозить
                                </button>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="icon-circle mx-auto mb-4" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <h4 class="mb-3">У вас нет активного абонемента</h4>
                        <p class="text-muted mb-4">Приобретите абонемент, чтобы начать тренировки</p>
                        <a href="{{ route('client.subscriptions') }}" class="btn-subscription">
                            <i class="fas fa-shopping-cart me-2"></i>Купить абонемент
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Информация об аккаунте -->
    <div class="account-info-card mt-4">
        <div class="card-header">
            <i class="fas fa-info-circle"></i> Информация об аккаунте
        </div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Роль</span>
                <span class="value">{{ $user->role->name }}</span>
            </div>
            <div class="info-item">
                <span class="label">ID пользователя</span>
                <span class="value">#{{ $user->id }}</span>
            </div>
            <div class="info-item">
                <span class="label">Дата регистрации</span>
                <span class="value">{{ $user->created_at->format('d.m.Y H:i') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Последнее обновление</span>
                <span class="value">{{ $user->updated_at->format('d.m.Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно заморозки -->
<div class="modal fade freeze-modal" id="freezeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('freeze-subscription') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-snowflake"></i> Заморозка абонемента
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Вы можете заморозить абонемент на срок до 14 дней.</p>
                    <p>Причины заморозки:</p>
                    <ul>
                        <li><i class="fas fa-thermometer-half me-2"></i>Болезнь</li>
                        <li><i class="fas fa-briefcase me-2"></i>Командировка</li>
                        <li><i class="fas fa-umbrella-beach me-2"></i>Отпуск</li>
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Сохранение активной вкладки
        var triggerTabList = [].slice.call(document.querySelectorAll('#profileTab button'));
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl);
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });

        // Переключение видимости пароля
        function togglePasswordVisibility(inputId, buttonId) {
            const passwordInput = document.getElementById(inputId);
            const toggleButton = document.getElementById(buttonId);
            if (!passwordInput || !toggleButton) return;
            
            toggleButton.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        }
        
        togglePasswordVisibility('current_password', 'toggleCurrentPassword');
        togglePasswordVisibility('password', 'togglePassword');
        togglePasswordVisibility('password_confirmation', 'togglePasswordConfirmation');
    });
</script>
@endsection