<!-- Редактирование пользователя -->
@extends('layouts.app')

@section('title', 'Редактирование пользователя')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/admin/user-edit.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 admin-edit-page">
    <!-- Заголовок -->
    <div class="edit-header">
        <h1 class="mb-0">
            <i class="fas fa-user-edit me-3"></i>Редактирование пользователя
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>К списку пользователей
            </a>
            <a href="{{ route('admin.users.show', $user->id) }}" class="view-btn">
                <i class="fas fa-eye me-2"></i>Просмотр
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert edit-alert danger alert-dismissible fade show">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Ошибки валидации:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="form-card">
        <div class="card-header">
            <i class="fas fa-user-circle"></i> {{ $user->name }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <!-- Скрытое поле с ролью (чтобы не потерять) -->
                <input type="hidden" name="role_id" value="{{ $user->role_id }}">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label required-field">Имя</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label required-field">Email</label>
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

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="is_active" class="form-label">Статус пользователя</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="is_active" 
                                   name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Пользователь активен
                            </label>
                        </div>
                        <div class="form-text">Если отмечено - пользователь может входить в систему и пользоваться сервисом</div>
                    </div>
                </div>

                <!-- Смена пароля -->
                <div class="section-divider"></div>
                <h5 class="section-title">
                    <i class="fas fa-key"></i> Смена пароля
                </h5>
                <div class="section-description">Заполните только если хотите изменить пароль. Минимум 8 символов.</div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Новый пароль</label>
                        <input type="text" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" 
                               placeholder="Оставьте пустым, если не нужно менять"
                               autocomplete="off">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Пароль отображается в открытом виде для удобства</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
                        <input type="text" class="form-control" 
                               id="password_confirmation" name="password_confirmation" 
                               placeholder="Повторите новый пароль"
                               autocomplete="off">
                    </div>
                </div>

                <!-- Дополнительные поля для тренера -->
                @if($user->role->name == 'trainer')
                    <div class="section-divider"></div>
                    <h5 class="section-title">
                        <i class="fas fa-chalkboard-user"></i> Информация для тренера
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="qualification" class="form-label">Квалификация</label>
                            <input type="text" class="form-control" id="qualification" name="qualification" 
                                   value="{{ old('qualification', $user->qualification) }}" 
                                   placeholder="Например: Мастер спорта">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="specialization" class="form-label">Специализация</label>
                            <input type="text" class="form-control" id="specialization" name="specialization" 
                                   value="{{ old('specialization', $user->specialization) }}" 
                                   placeholder="Например: Пауэрлифтинг, Кроссфит">
                        </div>
                    </div>
                @endif

                <!-- Дополнительные поля для клиента -->
                @if($user->role->name == 'client')
                    <div class="section-divider"></div>
                    <h5 class="section-title">
                        <i class="fas fa-heartbeat"></i> Информация для клиента
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label">Заметки / Информация о здоровье</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4" 
                                      placeholder="Аллергии, травмы, противопоказания, цели тренировок...">{{ old('notes', $user->notes) }}</textarea>
                            <div class="form-text">Эта информация будет видна тренерам</div>
                        </div>
                    </div>
                @endif

                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Сохранить изменения
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection