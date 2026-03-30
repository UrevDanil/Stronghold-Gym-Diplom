<!-- Регистрация -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация - Stronghold Gym</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ asset('assets/css/auth/register.css') }}" rel="stylesheet">
</head>
<body class="auth-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="register-card">
                    <div class="card-header text-center">
                        <h3 class="mb-0">
                            <i class="fas fa-dumbbell me-2"></i>Stronghold Gym
                        </h3>
                    </div>
                    <div class="card-body">
                        <h4>Создание аккаунта</h4>
                        
                        @if($errors->any())
                            <div class="alert-modern alert-danger alert-dismissible fade show" role="alert">
                                <div class="d-flex align-items-center">
                                    <div class="alert-icon">
                                        <i class="fas fa-exclamation-circle"></i>
                                    </div>
                                    <div class="alert-content">
                                        @foreach($errors->all() as $error)
                                            <p class="mb-1">{{ $error }}</p>
                                        @endforeach
                                    </div>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('register') }}">
                            @csrf
                            
                            <!-- Имя -->
                            <div class="mb-4">
                                <label for="name" class="form-label required-field">Имя</label>
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
                                <div class="form-text">Как к вам обращаться</div>
                            </div>
                            
                            <!-- Email -->
                            <div class="mb-4">
                                <label for="email" class="form-label required-field">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="Ваша почта" 
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
                                <label for="phone" class="form-label required-field">Телефон</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" 
                                           value="{{ old('phone') }}" 
                                           placeholder="+7 (___) ___-__-__" 
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="form-text">Для связи с вами</div>
                            </div>
                            
                            <!-- Пароль -->
                            <div class="mb-4">
                                <label for="password" class="form-label required-field">Пароль</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" 
                                           placeholder="Минимум 8 символов" 
                                           required>
                                    <button class="password-toggle" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                                <div class="password-strength-container">
                                    <div class="password-strength" id="passwordStrength"></div>
                                    <div class="strength-text" id="strengthText">Введите пароль</div>
                                </div>
                            </div>
                            
                            <!-- Подтверждение пароля -->
                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label required-field">Подтверждение пароля</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" 
                                           placeholder="Повторите пароль" 
                                           required>
                                    <button class="password-toggle" type="button" id="toggleConfirmPassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <div class="form-text">Пароли должны совпадать</div>
                            </div>
                            
                            <button type="submit" class="btn-register w-100 py-2 mb-3">
                                <i class="fas fa-user-plus me-2"></i>Зарегистрироваться
                            </button>
                            
                            <div class="text-center">
                                <p class="mb-0">
                                    Уже есть аккаунт? 
                                    <div class="text-center">
                                    <a href="{{ route('login') }}" class="auth-link">
                                        <i class="fas fa-sign-in-alt me-1"></i>Войти
                                    </a>
                                    </div>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="auth-footer">
                    <small>© 2026 Stronghold Gym. Все права защищены.</small>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        const togglePasswordBtn = document.getElementById('togglePassword');
        const toggleConfirmBtn = document.getElementById('toggleConfirmPassword');
        const passwordStrength = document.getElementById('passwordStrength');
        const strengthText = document.getElementById('strengthText');
        
        function setupPasswordToggle(button, input) {
            if (button && input) {
                button.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    
                    if (input.type === 'password') {
                        input.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        input.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            }
        }
        
        setupPasswordToggle(togglePasswordBtn, passwordInput);
        setupPasswordToggle(toggleConfirmBtn, confirmPasswordInput);
        
        function checkPasswordStrength(password) {
            let strength = 0;
            let text = '';
            let color = '#dc3545';
            
            if (password.length >= 8) strength += 1;
            if (password.length >= 12) strength += 1;
            if (/[A-Z]/.test(password)) strength += 1;
            if (/[0-9]/.test(password)) strength += 1;
            if (/[^A-Za-z0-9]/.test(password)) strength += 1;
            
            if (password.length === 0) {
                text = 'Введите пароль';
                color = '#6c757d';
                strength = 0;
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
            
            if (passwordStrength) {
                passwordStrength.style.backgroundColor = color;
                passwordStrength.style.width = (strength * 20) + '%';
            }
            
            if (strengthText) {
                strengthText.textContent = text;
                strengthText.style.color = color;
            }
        }
        
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                checkPasswordStrength(this.value);
                
                if (confirmPasswordInput && confirmPasswordInput.value) {
                    if (this.value !== confirmPasswordInput.value) {
                        confirmPasswordInput.style.borderColor = '#dc3545';
                    } else {
                        confirmPasswordInput.style.borderColor = '#198754';
                    }
                }
            });
        }
        
        if (confirmPasswordInput && passwordInput) {
            confirmPasswordInput.addEventListener('input', function() {
                if (this.value !== passwordInput.value) {
                    this.style.borderColor = '#dc3545';
                } else {
                    this.style.borderColor = '#198754';
                }
            });
        }
        
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
        
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-modern');
            alerts.forEach(function(alert) {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            });
        }, 5000);
    });
    </script>
</body>
</html>