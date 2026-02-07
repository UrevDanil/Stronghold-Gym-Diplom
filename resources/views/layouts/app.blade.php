<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Stronghold Gym')</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .main-content {
            padding: 20px;
        }
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
    </style>
    @yield('styles')
</head>
<body>
    @auth
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-2 d-none d-md-block sidebar">
                <div class="sidebar-sticky">
                    <h4 class="mb-4">Stronghold Gym</h4>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="{{ route('dashboard') }}">
                                📊 Дашборд
                            </a>
                        </li>
                        
                        @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                        <!-- Меню для админа -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.clients') }}">
                                👥 Клиенты
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.trainers') }}">
                                💪 Тренеры
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.schedules') }}">
                                📅 Расписание
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('admin.bookings') }}">
                                🎫 Записи
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->isClient())
                        <!-- Меню для клиента -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('client.schedule') }}">
                                📅 Расписание
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('client.subscriptions') }}">
                                💳 Абонементы
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->isTrainer())
                        <!-- Меню для тренера -->
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('trainer.schedule') }}">
                                📅 Мое расписание
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('trainer.clients') }}">
                                👥 Мои клиенты
                            </a>
                        </li>
                        @endif
                        
                        <li class="nav-item mt-4">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">
                                    🚪 Выйти
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <!-- Main content -->
            <main class="col-md-10 main-content">
                @yield('content')
            </main>
        </div>
    </div>
    @else
        @yield('content')
    @endauth
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
    <script>
// Глобальная функция для переключения видимости пароля
document.addEventListener('DOMContentLoaded', function() {
    // Ищем все кнопки переключения пароля
    document.querySelectorAll('[id^="toggle"]').forEach(function(button) {
        if (button.id.includes('Password')) {
            const inputId = button.id.replace('toggle', '').replace('Password', '')
                .toLowerCase();
            const input = document.getElementById(inputId) || 
                         document.getElementById('password') || 
                         document.getElementById('password-confirm') ||
                         document.getElementById('current_password');
            
            if (input) {
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
    });
});
</script>
</body>
</html>