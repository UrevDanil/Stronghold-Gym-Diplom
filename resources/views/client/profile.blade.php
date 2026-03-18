@extends('layouts.app')

@section('title', 'Мой профиль')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/client/client-profile.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4 client-profile-page">
    <!-- Заголовок -->
    <div class="client-profile-header d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-user-circle me-3"></i>Мой профиль
        </h1>
        <a href="{{ route('client.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
    </div>

    @if(session('success'))
        <div class="alert client-alert success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert client-alert error alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Левая колонка: Аватар и информация -->
        <div class="col-md-4 mb-4">
            <div class="client-profile-card">
                <div class="card-body">
                    <div class="client-avatar">
                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    
                    <div class="text-center">
                        <h2 class="client-name">{{ $user->name }}</h2>
                        <p class="client-email">{{ $user->email }}</p>
                    </div>
                    
                    <!-- Блок информации -->
                    <div class="client-info-grid">
                        <!-- Телефон (если есть) -->
                        @if($user->phone)
                        <div class="client-info-row">
                            <div class="client-info-icon phone">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="client-info-content">
                                <span class="client-info-label">Телефон</span>
                                <span class="client-info-value phone-value">{{ $user->phone }}</span>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Дата рождения (если есть) -->
                        @if($user->birth_date)
                        <div class="client-info-row">
                            <div class="client-info-icon calendar">
                                <i class="fas fa-birthday-cake"></i>
                            </div>
                            <div class="client-info-content">
                                <span class="client-info-label">Дата рождения</span>
                                <span class="client-info-value">{{ \Carbon\Carbon::parse($user->birth_date)->format('d.m.Y') }}</span>
                            </div>
                        </div>
                        @endif
                        
                        <!-- ID пользователя -->
                        <div class="client-info-row">
                            <div class="client-info-icon id">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="client-info-content">
                                <span class="client-info-label">ID</span>
                                <span class="client-info-value">#{{ $user->id }}</span>
                            </div>
                        </div>

                        <!-- Заметки (кратко) -->
                        @if($user->notes)
                        <div class="client-info-row">
                            <div class="client-info-icon notes">
                                <i class="fas fa-sticky-note"></i>
                            </div>
                            <div class="client-info-content">
                                <span class="client-info-label">Заметки</span>
                                <span class="client-info-value">{{ Str::limit($user->notes, 30) }}</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Правая колонка: Формы -->
        <div class="col-md-8">
            <!-- Форма редактирования профиля -->
            <div class="client-form-card">
                <div class="card-header">
                    <i class="fas fa-edit"></i> Редактировать профиль
                </div>
                <div class="card-body">
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

                        <!-- Заметки пользователя (ВАЖНО: сохраняем) -->
                        <div class="mb-4">
                            <label for="notes" class="form-label">Информация для тренера</label>
                            <textarea class="form-control notes-field @error('notes') is-invalid @enderror" 
                                      id="notes" name="notes" rows="4" 
                                      placeholder="Здесь вы можете указать информацию, которую хотите сообщить тренеру...">{{ old('notes', $user->notes) }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Эта информация будет доступна вашим тренерам
                            </div>
                        </div>

                        <button type="submit" class="btn-save">
                            <i class="fas fa-save me-2"></i> Сохранить изменения
                        </button>
                    </form>
                </div>
            </div>

            <!-- Форма смены пароля -->
            <div class="client-form-card mt-4">
                <div class="card-header">
                    <i class="fas fa-lock"></i> Смена пароля
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('client.password.update') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Текущий пароль *</label>
                            <input type="text" class="form-control @error('current_password') is-invalid @enderror" 
                                id="current_password" name="current_password" 
                                value="{{ old('current_password') }}" 
                                placeholder="Введите текущий пароль" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Новый пароль *</label>
                            <input type="text" class="form-control @error('password') is-invalid @enderror" 
                                id="password" name="password" 
                                value="{{ old('password') }}" 
                                placeholder="Введите новый пароль" required>
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Минимум 8 символов</div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Подтверждение пароля *</label>
                            <input type="text" class="form-control" 
                                id="password_confirmation" name="password_confirmation" 
                                value="{{ old('password_confirmation') }}" 
                                placeholder="Подтвердите новый пароль" required>
                        </div>

                        <button type="submit" class="btn-save">
                            <i class="fas fa-key me-2"></i> Сменить пароль
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Информация об аккаунте -->
    <div class="account-info-card">
        <div class="card-header">
            <i class="fas fa-info-circle me-2"></i> Информация об аккаунте
        </div>
        <div class="info-grid">
            <div class="info-item">
                <span class="label">Роль</span>
                <span class="value">{{ $user->role->name ?? 'Клиент' }}</span>
            </div>
            <div class="info-item">
                <span class="label">ID пользователя</span>
                <span class="value">#{{ $user->id }}</span>
            </div>
            <div class="info-item">
                <span class="label">Дата регистрации</span>
                <span class="value">{{ $user->created_at->format('d.m.Y') }}</span>
            </div>
            <div class="info-item">
                <span class="label">Последнее обновление</span>
                <span class="value">{{ $user->updated_at->format('d.m.Y') }}</span>
            </div>
        </div>
    </div>
</div>
@endsection