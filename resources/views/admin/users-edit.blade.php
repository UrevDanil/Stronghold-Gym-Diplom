@extends('layouts.app')

@section('title', 'Редактирование пользователя')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Редактирование пользователя</h1>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>К списку пользователей
            </a>
            <a href="{{ route('admin.users.show', $user->id) }}" class="btn btn-info">
                <i class="fas fa-eye me-2"></i>Просмотр
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <h5 class="alert-heading">Ошибки валидации:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $user->name }}</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')

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

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="role_id" class="form-label">Роль *</label>
                        <select class="form-select @error('role_id') is-invalid @enderror" 
                                id="role_id" name="role_id" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ (old('role_id', $user->role_id) == $role->id) ? 'selected' : '' }}>
                                    @if($role->name == 'client') 👤 Клиент
                                    @elseif($role->name == 'trainer') 💪 Тренер
                                    @elseif($role->name == 'admin') 👑 Администратор
                                    @elseif($role->name == 'owner') 🏢 Владелец
                                    @else {{ $role->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('role_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

<div class="col-md-6 mb-3">
    <label for="is_active" class="form-label">Статус пользователя</label>
    <div class="form-check mt-2">
        <input class="form-check-input" type="checkbox" id="is_active" 
               name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">
            Пользователь активен
        </label>
    </div>
    <small class="text-muted">
        Если отмечено - пользователь может входить в систему и пользоваться сервисом
    </small>
</div>

                <!-- Поля для пароля (оставить пустыми, если не нужно менять) -->
<hr>
<h5 class="mb-3">Смена пароля</h5>
<p class="text-muted small">Заполните только если хотите изменить пароль. Минимум 8 символов.</p>

<div class="row">
    <div class="col-md-6 mb-3">
        <label for="password" class="form-label">Новый пароль</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" 
               id="password" name="password" 
               placeholder="Оставьте пустым, если не нужно менять"
               autocomplete="off">  <!-- ДОБАВЬ ЭТО -->
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6 mb-3">
        <label for="password_confirmation" class="form-label">Подтверждение пароля</label>
        <input type="password" class="form-control @error('password') is-invalid @enderror" 
               id="password_confirmation" name="password_confirmation" 
               placeholder="Повторите новый пароль"
               autocomplete="off">  <!-- ДОБАВЬ ЭТО -->
        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

                <!-- Дополнительные поля для тренера -->
                <div class="trainer-fields" style="{{ $user->role->name == 'trainer' ? 'display:block' : 'display:none' }}">
                    <hr>
                    <h5 class="mb-3">Информация для тренера</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="qualification" class="form-label">Квалификация</label>
                            <input type="text" class="form-control" id="qualification" name="qualification" 
                                   value="{{ old('qualification', $user->qualification) }}">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="specialization" class="form-label">Специализация</label>
                            <input type="text" class="form-control" id="specialization" name="specialization" 
                                   value="{{ old('specialization', $user->specialization) }}">
                        </div>
                    </div>
                </div>

                <!-- Дополнительные поля для клиента -->
                <div class="client-fields" style="{{ $user->role->name == 'client' ? 'display:block' : 'display:none' }}">
                    <hr>
                    <h5 class="mb-3">Информация для клиента</h5>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="notes" class="form-label">Заметки / Информация о здоровье</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3">{{ old('notes', $user->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Сохранить изменения
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary ms-2">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Показ дополнительных полей в зависимости от роли
    document.getElementById('role_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const roleName = selectedOption.text.toLowerCase();
        
        document.querySelector('.trainer-fields').style.display = 'none';
        document.querySelector('.client-fields').style.display = 'none';
        
        if (roleName.includes('тренер')) {
            document.querySelector('.trainer-fields').style.display = 'block';
        } else if (roleName.includes('клиент')) {
            document.querySelector('.client-fields').style.display = 'block';
        }
    });
</script>

<style>
    .card {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
    .form-label {
        font-weight: 500;
    }
</style>
@endsection