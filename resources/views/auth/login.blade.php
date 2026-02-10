<!-- Вход -->
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход - Stronghold Gym</title>
    
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
        .login-card {
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
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
            border-color: #ced4da;
        }
        .input-group .btn-outline-secondary:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card login-card">
                    <div class="card-header bg-dark text-white">
                        <h3 class="text-center mb-0">
                            <i class="fas fa-dumbbell me-2"></i>Stronghold Gym
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <h4 class="text-center mb-4">Вход в систему</h4>
                        
                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show">
                                @foreach($errors->all() as $error)
                                    <p class="mb-1">{{ $error }}</p>
                                @endforeach
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @if(session('status'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('status') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="mb-4">
                                <label for="email" class="form-label fw-bold">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" 
                                           value="{{ old('email') }}" 
                                           placeholder="Введите ваш email" 
                                           required autofocus>
                                    @error('email')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="password" class="form-label fw-bold">Пароль</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" 
                                           placeholder="Введите ваш пароль" 
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
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Войти
                            </button>
                            
                            <div class="text-center mt-4">
                                <div class="mb-2">
                                    <a href="{{ route('register') }}" class="text-decoration-none">
                                        <i class="fas fa-user-plus me-1"></i>Нет аккаунта? Зарегистрироваться
                                    </a>
                                </div>
                                
                                @if (Route::has('password.request'))
                                    <div>
                                        <a href="{{ route('password.request') }}" class="text-decoration-none">
                                            <i class="fas fa-key me-1"></i>Забыли пароль?
                                        </a>
                                    </div>
                                @endif
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
    
    <!-- Скрипт для переключения видимости пароля -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        
        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function() {
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    // Показываем пароль
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                    this.setAttribute('title', 'Скрыть пароль');
                } else {
                    // Скрываем пароль
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    this.setAttribute('title', 'Показать пароль');
                }
            });
            
            // Добавляем всплывающую подсказку
            toggleBtn.setAttribute('title', 'Показать пароль');
            
            // Закрываем пароль при потере фокуса (опционально)
            passwordInput.addEventListener('blur', function() {
                if (this.type === 'text') {
                    this.type = 'password';
                    const icon = toggleBtn.querySelector('i');
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                    toggleBtn.setAttribute('title', 'Показать пароль');
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