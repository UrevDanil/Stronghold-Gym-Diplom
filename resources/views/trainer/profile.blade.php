@extends('layouts.app')

@section('title', 'Мой профиль')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Мой профиль</h1>
        <div>
            <a href="{{ route('trainer.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Основная информация -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-circle bg-primary text-white mx-auto mb-3">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <h3>{{ $user->name }}</h3>
                    <p class="text-muted">{{ $user->qualification ?? 'Тренер' }}</p>
                    
                    <div class="text-start mt-4">
                        <p class="mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            {{ $user->email }}
                        </p>
                        @if($user->phone)
                        <p class="mb-2">
                            <i class="fas fa-phone text-success me-2"></i>
                            {{ $user->phone }}
                        </p>
                        @endif
                        <p class="mb-2">
                            <i class="fas fa-calendar text-info me-2"></i>
                            Зарегистрирован: {{ $user->created_at->format('d.m.Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Редактирование профиля -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Редактировать профиль</h5>
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

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Сохранить изменения
                        </button>
                    </form>
                </div>
            </div>

            <!-- Квалификация -->
            <div class="card mt-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">Квалификация и специализация</h5>
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

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-certificate me-2"></i>Обновить квалификацию
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 40px;
        font-weight: bold;
    }
</style>
@endsection