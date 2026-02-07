<!-- Профиль -->
 @extends('layouts.app')

@section('title', 'Мой профиль')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Заголовок -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Мой профиль</h2>
                <a href="{{ route('client.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Назад
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <!-- Вкладки -->
                    <ul class="nav nav-tabs mb-4" id="profileTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" 
                                    data-bs-target="#personal" type="button" role="tab">
                                <i class="fas fa-user me-2"></i>Личные данные
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="security-tab" data-bs-toggle="tab" 
                                    data-bs-target="#security" type="button" role="tab">
                                <i class="fas fa-lock me-2"></i>Безопасность
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="subscription-tab" data-bs-toggle="tab" 
                                    data-bs-target="#subscription" type="button" role="tab">
                                <i class="fas fa-id-card me-2"></i>Мой абонемент
                            </button>
                        </li>
                    </ul>

                    <!-- Содержимое вкладок -->
                    <div class="tab-content" id="profileTabContent">
                        <!-- Вкладка 1: Личные данные -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel">
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
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Сохранить изменения
                                </button>
                            </form>
                        </div>

                        <!-- Вкладка 2: Безопасность -->
                        <div class="tab-pane fade" id="security" role="tabpanel">
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

                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-key me-2"></i>Сменить пароль
                                </button>
                            </form>
                        </div>

                        <!-- Вкладка 3: Абонемент -->
                        <div class="tab-pane fade" id="subscription" role="tabpanel">
                            @if($user->activeSubscription())
                                @php
                                    $subscription = $user->activeSubscription();
                                    $remaining = $subscription->pivot->remaining_workouts;
                                    $totalWorkouts = $subscription->session_count;
                                    $used = max(0, $totalWorkouts - $remaining);
                                    $percentage = $totalWorkouts > 0 ? min(100, ($used / $totalWorkouts) * 100) : 0;
                                @endphp
                                
                                <div class="text-center mb-4">
                                    <i class="fas fa-id-card fa-4x text-primary mb-3"></i>
                                    <h4>{{ $subscription->name }}</h4>
                                    <p class="text-muted">{{ $subscription->description }}</p>
                                </div>
                                
                                <div class="progress mb-4" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $percentage }}%"
                                         aria-valuenow="{{ $percentage }}">
                                        {{ $used }}/{{ $totalWorkouts }} тренировок
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6 class="card-title">Статистика</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-dumbbell text-success me-2"></i>
                                                        Осталось: <strong>{{ $remaining }}</strong>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-calendar-check text-info me-2"></i>
                                                        Использовано: <strong>{{ $used }}</strong>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-percentage text-primary me-2"></i>
                                                        Прогресс: <strong>{{ round($percentage, 1) }}%</strong>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-body">
                                                <h6 class="card-title">Срок действия</h6>
                                                <ul class="list-unstyled">
                                                    <li class="mb-2">
                                                        <i class="fas fa-calendar-plus text-success me-2"></i>
                                                        Начало: 
                                                        <strong>{{ \Carbon\Carbon::parse($subscription->pivot->start_date)->format('d.m.Y') }}</strong>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-calendar-minus text-danger me-2"></i>
                                                        Окончание: 
                                                        <strong>{{ \Carbon\Carbon::parse($subscription->pivot->end_date)->format('d.m.Y') }}</strong>
                                                    </li>
                                                    <li class="mb-2">
                                                        <i class="fas fa-clock text-warning me-2"></i>
                                                        Осталось дней: 
                                                        <strong>{{ \Carbon\Carbon::parse($subscription->pivot->end_date)->diffInDays(now()) }}</strong>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="text-center mt-4">
                                    <a href="{{ route('client.subscriptions') }}" class="btn btn-primary">
                                        <i class="fas fa-sync-alt me-2"></i>Продлить абонемент
                                    </a>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-exclamation-triangle fa-4x text-warning mb-3"></i>
                                    <h4>У вас нет активного абонемента</h4>
                                    <p class="text-muted mb-4">Приобретите абонемент, чтобы начать тренировки</p>
                                    <a href="{{ route('client.subscriptions') }}" class="btn btn-primary btn-lg">
                                        <i class="fas fa-shopping-cart me-2"></i>Купить абонемент
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Информация об аккаунте -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Информация об аккаунте</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Роль:</span>
                                    <strong class="text-capitalize">{{ $user->role->name }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Дата регистрации:</span>
                                    <strong>{{ $user->created_at->format('d.m.Y H:i') }}</strong>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>ID пользователя:</span>
                                    <strong>#{{ $user->id }}</strong>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span>Последнее обновление:</span>
                                    <strong>{{ $user->updated_at->format('d.m.Y H:i') }}</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-radius: 0.25rem 0.25rem 0 0;
    }
    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }
    .tab-pane {
        padding-top: 1rem;
    }
    .progress {
        border-radius: 10px;
        overflow: hidden;
    }
    .progress-bar {
        font-weight: 600;
    }
</style>

<script>
    // Сохранение активной вкладки при обновлении страницы
    document.addEventListener('DOMContentLoaded', function() {
        var triggerTabList = [].slice.call(document.querySelectorAll('#profileTab button'));
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl);
            triggerEl.addEventListener('click', function (event) {
                event.preventDefault();
                tabTrigger.show();
            });
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Функция для переключения видимости пароля
    function togglePasswordVisibility(inputId, buttonId) {
        const passwordInput = document.getElementById(inputId);
        const toggleButton = document.getElementById(buttonId);
        const icon = toggleButton.querySelector('i');
        
        toggleButton.addEventListener('click', function() {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                toggleButton.setAttribute('title', 'Скрыть пароль');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                toggleButton.setAttribute('title', 'Показать пароль');
            }
        });
    }
    
    // Применяем ко всем полям пароля
    togglePasswordVisibility('current_password', 'toggleCurrentPassword');
    togglePasswordVisibility('password', 'togglePassword');
    togglePasswordVisibility('password_confirmation', 'togglePasswordConfirmation');
});
</script>
@endsection