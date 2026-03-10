<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Stronghold Gym')</title>
    
    <!-- CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome для иконок -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        
        /* Стили для уведомлений */
        .notification-badge {
            position: absolute;
            top: 0;
            right: -5px;
            font-size: 0.7rem;
            padding: 0.25rem 0.4rem;
        }
        .notification-item {
            transition: background-color 0.2s;
            white-space: normal;
            word-wrap: break-word;
        }
        .notification-item:hover {
            background-color: #f8f9fa;
        }
        .notification-item.unread {
            background-color: #e8f0fe;
        }
        .dropdown-menu {
            max-height: 400px;
            overflow-y: auto;
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
                    <div class="d-flex justify-content-between align-items-center mb-4 px-3">
                        <h4 class="mb-0">Stronghold</h4>
                        
                        <!-- Уведомления в шапке сайдбара -->
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm position-relative" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-bell"></i>
                                @php
                                    $unreadCount = App\Models\Notification::where('user_id', auth()->id())
                                        ->where('is_read', false)
                                        ->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger notification-badge">
                                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                    </span>
                                @endif
                            </button>
                            <div class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                                @php
                                    $notifications = App\Models\Notification::where('user_id', auth()->id())
                                        ->latest()
                                        ->limit(5)
                                        ->get();
                                @endphp
                                <div class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span>Уведомления</span>
                                    @if($unreadCount > 0)
                                        <small class="text-muted">{{ $unreadCount }} новых</small>
                                    @endif
                                </div>
                                <div class="dropdown-divider"></div>
                                @if($notifications->count() > 0)
                                    @foreach($notifications as $notification)
                                        <a class="dropdown-item notification-item {{ !$notification->is_read ? 'unread' : '' }}" href="#">
                                            <div class="d-flex align-items-start">
                                                <div class="flex-shrink-0 me-2">
                                                    @if($notification->type == 'booking')
                                                        <i class="fas fa-calendar-check text-primary"></i>
                                                    @elseif($notification->type == 'subscription')
                                                        <i class="fas fa-id-card text-success"></i>
                                                    @else
                                                        <i class="fas fa-bell text-warning"></i>
                                                    @endif
                                                </div>
                                                <div class="flex-grow-1">
                                                    <small class="d-block text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                                    <span class="small">{{ Str::limit($notification->message, 50) }}</span>
                                                </div>
                                            </div>
                                        </a>
                                    @endforeach
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item text-center small" href="{{ route('notifications') }}">
                                        Все уведомления
                                    </a>
                                @else
                                    <div class="dropdown-item text-center text-muted py-3">
                                        <i class="fas fa-bell-slash mb-2"></i>
                                        <br>Нет уведомлений
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                📊 Дашборд
                            </a>
                        </li>
                        
                        @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
                         <!--Меню для админа-->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.clients') ? 'active' : '' }}" href="{{ route('admin.clients') }}">
                                👥 Клиенты
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.trainers') ? 'active' : '' }}" href="{{ route('admin.trainers') }}">
                                💪 Тренеры
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.schedule*') ? 'active' : '' }}" href="{{ route('admin.schedule.index') }}">
                                📅 Расписание
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('admin.subscriptions*') ? 'active' : '' }}" href="{{ route('admin.subscriptions.index') }}">
                                📦 Абонементы
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->isClient())
                        <!-- Меню для клиента -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.schedule') ? 'active' : '' }}" href="{{ route('client.schedule') }}">
                                📅 Расписание
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.subscriptions') ? 'active' : '' }}" href="{{ route('client.subscriptions') }}">
                                💳 Абонементы
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('client.profile') ? 'active' : '' }}" href="{{ route('client.profile') }}">
                                👤 Профиль
                            </a>
                        </li>
                        @endif
                        
                        @if(auth()->user()->isTrainer())
                        <!-- Меню для тренера -->
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('trainer.schedule') ? 'active' : '' }}" href="{{ route('trainer.schedule') }}">
                                📅 Мое расписание
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('trainer.clients') ? 'active' : '' }}" href="{{ route('trainer.clients') }}">
                                👥 Мои клиенты
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('trainer.attendance') ? 'active' : '' }}" href="{{ route('trainer.attendance') }}">
                                📋 Посещаемость
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('trainer.profile') ? 'active' : '' }}" href="{{ route('trainer.profile') }}">
                                👤 Профиль
                            </a>
                        </li>
                        @endif
                        
                        <li class="nav-item mt-4">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm w-100">
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
        
        // Автоматическое обновление счетчика уведомлений (можно добавить позже)
        function checkNewNotifications() {
            // Здесь можно добавить AJAX запрос для проверки новых уведомлений
        }
        // setInterval(checkNewNotifications, 60000); // Проверять каждую минуту
    </script>
</body>
</html>