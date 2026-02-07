<!-- Регистрация -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Stronghold Gym</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome для иконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background-color: #0e2778;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .register-card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            max-width: 550px;
            margin: 0 auto;
        }
        .password-toggle {
            cursor: pointer;
            background: none;
            border: none;
            color: #0e2778;
            padding: 8px 12px;
            transition: color 0.2s;
        }
        .password-toggle:hover {
            color: #fd9e2e;
        }
        .input-group .form-control {
            border-right: 0;
        }
        .input-group .btn-outline-secondary {
            border-left: 0;
            border-color: #d9ceda;
        }
        .input-group .btn-outline-secondary:hover {
            background-color: #f8f9fa;
        }
        .password-strength {
            height: 5px;
            margin-top: 5px;
            border-radius: 3px;
            transition: all 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card register-card">
                    <div class="card-header bg-dark text-white">
                        <h3 class="text-center mb-0">
                            <i class="fas fa-dumbbell me-2"></i>Stronghold Gym
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4">Создание аккаунта</h4>
                        
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                @foreach($errors->all() as $error)
                                    <p class="mb-1">{{ $error }}</p>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <!-- Имя -->
                            <div class="mb-4">
                                <label for="name" class="form-label fw-bold required-field">Имя</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Ваше имя" 
                                           required autofocus>
                                    @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Как к вам обращаться
                                </small>
                            </div>
                            
                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold required-field">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="example@mail.ru" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Телефон -->
                            <div class="mb-4">
                                <label for="phone" class="form-label fw-bold required-field">Телефон</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" 
                                           value="{{ old('phone') }}" 
                                           
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">
                                    Для связи с вами
                                </small>
                            </div>
                            
                            <!-- Пароль -->
                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold required-field">Пароль</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" 
                                           placeholder="Минимум 8 символов" 
                                           required>
                                    <button class="btn btn-outline-secondary password-toggle" 
                                            type="button" 
                                            id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <!-- Индикатор сложности пароля -->
                                <div class="password-strength" id="passwordStrength"></div>
                                <small class="form-text text-muted d-block mt-1">
                                    <span id="strengthText">Введите пароль</span>
                                </small>
                            </div>
                            
                            <!-- Подтверждение пароля -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label fw-bold required-field">Подтверждение пароля</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" 
                                           placeholder="Повторите пароль" 
                                           required>
                                    <button class="btn btn-outline-secondary password-toggle" 
                                            type="button" 
                                            id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="form-text text-muted">
                                    Пароли должны совпадать
                                </small>
                                
                            </div>         
                            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                                <i class="fas fa-user-plus me-2"></i>Зарегистрироваться
                            </button>
                            
                            <div class="text-center mt-4">
                                <p class="mb-0">
                                    Уже есть аккаунт? 
                                    <a href="{{ route('login') }}" class="text-decoration-none fw-bold">
                                        <i class="fas fa-sign-in-alt me-1"></i>Войти
                                    </a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
                
             <div class="text-center mt-4" style="color: white;">
                 <small>© 2026 Stronghold Gym. Все права защищены.</small>
             </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Скрипт для переключения видимости пароля и проверки сложности -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Элементы для паролей
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const togglePasswordBtn = document.getElementById('togglePassword');
        const toggleConfirmBtn = document.getElementById('toggleConfirmPassword');
        const passwordStrength = document.getElementById('passwordStrength');
        const strengthText = document.getElementById('strengthText');
        
        // Функция для переключения видимости пароля
        function setupPasswordToggle(button, input) {
            if (button && input) {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                        this.setAttribute('title', 'Скрыть пароль');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                        this.setAttribute('title', 'Показать пароль');
                    }
                });
                
                button.setAttribute('title', 'Показать пароль');
            }
        }
        
        // Настраиваем переключатели
        setupPasswordToggle(togglePasswordBtn, passwordInput);
        setupPasswordToggle(toggleConfirmBtn, confirmPasswordInput);
        
        // Функция для проверки сложности пароля
        function checkPasswordStrength(password) {
            let strength = 0;
            let text = '';
            let color = '#dc3545'; // красный
            
            if (password.length >= 8) strength += 1;
            if (password.length >= 12) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            // Определяем уровень сложности
            if (password.length === 0) {
                text = 'Введите пароль';
                color = 'transparent';
            } else if (password.length < 8) {
                text = 'Слишком короткий';
                color = '#dc3545';
            } else if (strength <= 2) {
                text = 'Слабый';
                color = '#dc3545';
            } else if (strength <= 3) {
                text = 'Средний';
                color = '#ffc107';
            } else {
                text = 'Сильный';
                color = '#198754';
            }
            
            // Обновляем индикатор
            if (passwordStrength) {
                passwordStrength.style.backgroundColor = color;
                passwordStrength.style.width = (strength * 20) + '%';
            }
            
            if (strengthText) {
                strengthText.textContent = text;
                strengthText.style.color = color;
            }
        }
        
        // Проверка при вводе пароля
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                
                // Проверка совпадения паролей
                if (confirmPasswordInput && confirmPasswordInput.value) {
                    if (this.value !== confirmPasswordInput.value) {
                        confirmPasswordInput.style.borderColor = '#dc3545';
                    } else {
                        confirmPasswordInput.style.borderColor = '#198754';
                    }
                }
            });
        }
        
        // Проверка при вводе подтверждения пароля
        if (confirmPasswordInput && passwordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                if (this.value !== passwordInput.value) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = '#198754';
                }
            });
        }
        

        
        // Простое форматирование телефона (опционально)
        const phoneInput = document.getElementById('phone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                
                if (value.length > 0) {
                    if (!value.startsWith('7') && !value.startsWith('8')) {
                        value = '7' + value;
                    }
                    
                    let formatted = '+7';
                    if (value.length > 1) {
                        formatted += ' (' + value.substring(1, 4);
                    }
                    if (value.length >= 5) {
                        formatted += ') ' + value.substring(4, 7);
                    }
                    if (value.length >= 8) {
                        formatted += '-' + value.substring(7, 9);
                    }
                    if (value.length >= 10) {
                        formatted += '-' + value.substring(9, 11);
                    }
                    
                    this.value = formatted.substring(0, 18);
                }
            });
        }
        
        // Автоматическое скрытие алертов через 5 секунд
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
    </script>
</body>
</html>