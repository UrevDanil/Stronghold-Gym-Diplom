@extends('layouts.app')

@section('title', 'Добавление пользователя')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Добавление пользователя</h1>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>К списку пользователей
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
            <h5 class="mb-0">Новый пользователь</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Имя *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Телефон</label>
                        <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                               id="phone" name="phone" value="{{ old('phone') }}" 
                               placeholder="+7 (999) 123-45-67">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="birth_date" class="form-label">Дата рождения</label>
                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                               id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                        @error('birth_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Пароль *</label>
                        <input type="text" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" value="{{ old('password') }}" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Минимум 8 символов. Пароль отображается в открытом виде для удобства.</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label">Подтверждение пароля *</label>
                        <input type="text" class="form-control" 
                               id="password_confirmation" name="password_confirmation" 
                               value="{{ old('password_confirmation') }}" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="role_id" class="form-label">Роль *</label>
                        <select class="form-select @error('role_id') is-invalid @enderror" 
                                id="role_id" name="role_id" required>
                            <option value="">Выберите роль</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                    @if($role->name == 'client')
                                        👤 Клиент
                                    @elseif($role->name == 'trainer')
                                        💪 Тренер
                                    @elseif($role->name == 'admin')
                                        👑 Администратор
                                    @elseif($role->name == 'owner')
                                        🏢 Владелец
                                    @else
                                        {{ $role->name }}
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
                                   name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Пользователь активен
                            </label>
                        </div>
                        <small class="text-muted">
                            Если отмечено - пользователь может входить в систему
                        </small>
                    </div>
                </div>

                <!-- Дополнительные поля для тренера -->
                <div class="trainer-fields" style="display: none;">
                    <hr>
                    <h5 class="mb-3">Информация для тренера</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="qualification" class="form-label">Квалификация</label>
                            <input type="text" class="form-control" id="qualification" name="qualification" 
                                   value="{{ old('qualification') }}" placeholder="Например: Мастер спорта">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="specialization" class="form-label">Специализация</label>
                            <input type="text" class="form-control" id="specialization" name="specialization" 
                                   value="{{ old('specialization') }}" placeholder="Например: Пауэрлифтинг, Кроссфит">
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Создать пользователя
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
    document.addEventListener('DOMContentLoaded', function() {
        // Показ дополнительных полей в зависимости от роли
        const roleSelect = document.getElementById('role_id');
        if (roleSelect) {
            roleSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const roleText = selectedOption.text.toLowerCase();
                
                document.querySelector('.trainer-fields').style.display = 'none';
                document.querySelector('.client-fields').style.display = 'none';
                
                if (roleText.includes('тренер')) {
                    document.querySelector('.trainer-fields').style.display = 'block';
                } else if (roleText.includes('клиент')) {
                    document.querySelector('.client-fields').style.display = 'block';
                }
            });

            if (roleSelect.value) {
                const selectedOption = roleSelect.options[roleSelect.selectedIndex];
                const roleText = selectedOption.text.toLowerCase();
                
                if (roleText.includes('тренер')) {
                    document.querySelector('.trainer-fields').style.display = 'block';
                } else if (roleText.includes('клиент')) {
                    document.querySelector('.client-fields').style.display = 'block';
                }
            }
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