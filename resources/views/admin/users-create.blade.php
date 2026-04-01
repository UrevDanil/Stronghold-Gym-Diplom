<!-- Добавление пользователей -->
@extends('layouts.app')

@section('title', 'Добавление пользователя')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/admin/user-create.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 admin-create-page">
    <!-- Заголовок -->
    <div class="create-header">
        <h1 class="mb-0">
            <i class="fas fa-user-plus me-3"></i>Добавление пользователя
        </h1>
        <a href="{{ route('admin.users.index') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>К списку пользователей
        </a>
    </div>

    @if($errors->any())
        <div class="alert create-alert danger alert-dismissible fade show">
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
            <i class="fas fa-user-circle"></i> Новый пользователь
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                <!-- Основные поля -->
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label required-field">Имя</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label required-field">Email</label>
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

                <!-- Пароль -->
                <div class="section-divider"></div>
                <h5 class="section-title">
                    <i class="fas fa-key"></i> Пароль
                </h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label required-field">Пароль</label>
                        <input type="text" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" value="{{ old('password') }}" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Минимум 8 символов. Пароль отображается в открытом виде для удобства.</div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="password_confirmation" class="form-label required-field">Подтверждение пароля</label>
                        <input type="text" class="form-control" 
                               id="password_confirmation" name="password_confirmation" 
                               value="{{ old('password_confirmation') }}" required>
                    </div>
                </div>

                <!-- Роль и статус -->
                <div class="section-divider"></div>
                <h5 class="section-title">
                    <i class="fas fa-user-tag"></i> Настройки аккаунта
                </h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="role_id" class="form-label required-field">Роль</label>
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
                        <div class="form-text">Если отмечено - пользователь может входить в систему</div>
                    </div>
                </div>

                <!-- Дополнительные поля для тренера -->
                <div class="trainer-fields" style="display: none;">
                    <div class="section-divider"></div>
                    <h5 class="section-title">
                        <i class="fas fa-chalkboard-user"></i> Информация для тренера
                    </h5>
                    
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

                <!-- Дополнительные поля для клиента -->
                <div class="client-fields" style="display: none;">
                    <div class="section-divider"></div>
                    <h5 class="section-title">
                        <i class="fas fa-heartbeat"></i> Информация для клиента
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="health_info" class="form-label">Заметки / Информация о здоровье</label>
                            <textarea class="form-control" id="health_info" name="health_info" rows="4" 
                                      placeholder="Аллергии, травмы, противопоказания, цели тренировок...">{{ old('health_info') }}</textarea>
                            <div class="form-text">Эта информация будет видна тренерам</div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn-create">
                        <i class="fas fa-save"></i> Создать пользователя
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role_id');
        
        function toggleRoleFields() {
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            const roleText = selectedOption.text.toLowerCase();
            
            document.querySelector('.trainer-fields').style.display = 'none';
            document.querySelector('.client-fields').style.display = 'none';
            
            if (roleText.includes('тренер')) {
                document.querySelector('.trainer-fields').style.display = 'block';
            } else if (roleText.includes('клиент')) {
                document.querySelector('.client-fields').style.display = 'block';
            }
        }
        
        if (roleSelect) {
            roleSelect.addEventListener('change', toggleRoleFields);
            
            // Проверяем при загрузке, если уже выбрана роль
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
@endsection