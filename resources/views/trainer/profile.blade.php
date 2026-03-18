@extends('layouts.app')

@section('title', 'Мой профиль')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/trainer/trainer-profile.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4 trainer-profile-page">
    <!-- Заголовок -->
    <div class="trainer-profile-header d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-user-circle me-3"></i>Мой профиль
        </h1>
        <a href="{{ route('trainer.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
    </div>

    @if(session('success'))
        <div class="alert trainer-alert success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Левая колонка: Аватар и информация -->
        <div class="col-md-4 mb-4">
            <div class="profile-card">
                <div class="card-body">
                    <div class="trainer-avatar">
                        <span>{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    
                    <div class="text-center">
                        <h2 class="trainer-name">{{ $user->name }}</h2>
                        <p class="trainer-title">{{ $user->qualification ?? 'Тренер' }}</p>
                    </div>
                    
                    <!-- Блок информации -->
                    <div class="profile-info-grid">
                        <!-- Email -->
                        <div class="profile-info-row">
                            <div class="profile-info-icon email">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="profile-info-content">
                                <span class="profile-info-label">Email</span>
                                <span class="profile-info-value email-value">{{ $user->email }}</span>
                            </div>
                        </div>
                        
                        <!-- Телефон (если есть) -->
                        @if($user->phone)
                        <div class="profile-info-row">
                            <div class="profile-info-icon phone">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="profile-info-content">
                                <span class="profile-info-label">Телефон</span>
                                <span class="profile-info-value phone-value">{{ $user->phone }}</span>
                            </div>
                        </div>
                        @endif
                        
                        <!-- Дата регистрации -->
                        <div class="profile-info-row">
                            <div class="profile-info-icon calendar">
                                <i class="fas fa-calendar"></i>
                            </div>
                            <div class="profile-info-content">
                                <span class="profile-info-label">Регистрация</span>
                                <span class="profile-info-value">{{ $user->created_at->format('d.m.Y') }}</span>
                            </div>
                        </div>
                        
                        <!-- ID пользователя -->
                        <div class="profile-info-row">
                            <div class="profile-info-icon id">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div class="profile-info-content">
                                <span class="profile-info-label">ID</span>
                                <span class="profile-info-value">#{{ $user->id }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Правая колонка: Формы -->
        <div class="col-md-8">
            <!-- Форма редактирования профиля -->
            <div class="form-card">
                <div class="card-header">
                    <i class="fas fa-edit"></i> Редактировать профиль
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('trainer.profile.update') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="name" class="form-label">Имя *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="phone" class="form-label">Телефон</label>
                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-save">
                            <i class="fas fa-save me-2"></i> Сохранить изменения
                        </button>
                    </form>
                </div>
            </div>

            <!-- Форма квалификации -->
            <div class="form-card mt-4">
                <div class="card-header">
                    <i class="fas fa-certificate"></i> Квалификация и специализация
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('trainer.profile.qualification') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="qualification" class="form-label">Квалификация</label>
                            <input type="text" class="form-control @error('qualification') is-invalid @enderror" 
                                   id="qualification" name="qualification" 
                                   value="{{ old('qualification', $user->qualification) }}">
                            @error('qualification')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="specialization" class="form-label">Специализация</label>
                            <textarea class="form-control @error('specialization') is-invalid @enderror" 
                                      id="specialization" name="specialization" rows="3">{{ old('specialization', $user->specialization) }}</textarea>
                            @error('specialization')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <button type="submit" class="btn-qualification">
                            <i class="fas fa-certificate me-2"></i> Обновить квалификацию
                        </button>
                    </form>
                </div>
            </div>

            <!-- Форма смены пароля -->
            <div class="form-card mt-4">
                <div class="card-header">
                    <i class="fas fa-lock"></i> Смена пароля
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('trainer.profile.password') }}">
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
</div>
@endsection