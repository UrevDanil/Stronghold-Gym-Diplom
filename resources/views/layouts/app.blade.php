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
    <!-- Наши стили для сайдбара -->
    <link href="{{ asset('assets/css/sidebar.css') }}" rel="stylesheet">
    
    @yield('styles')
</head>
<body>
    @auth
    <!-- Кнопка бургер-меню -->
    <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Затемнение фона -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <!-- Сайдбар -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h4>Stronghold Gym</h4>
            <p>{{ auth()->user()->name }}</p>
        </div>
        
        <!-- Уведомления -->
        <div class="px-3 mb-3">
            <div class="dropdown w-100">
                <button class="btn btn-light w-100 d-flex align-items-center justify-content-between" type="button" data-bs-toggle="dropdown">
                    <span><i class="fas fa-bell me-2"></i>Уведомления</span>
                    @php
                        $unreadCount = App\Models\Notification::where('user_id', auth()->id())
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="badge bg-danger rounded-pill">{{ $unreadCount }}</span>
                    @endif
                </button>
                <div class="dropdown-menu w-100">
                    @php
                        $notifications = App\Models\Notification::where('user_id', auth()->id())
                            ->latest()
                            ->limit(5)
                            ->get();
                    @endphp
                    @if($notifications->count() > 0)
                        @foreach($notifications as $notification)
                            <a class="dropdown-item notification-item {{ !$notification->is_read ? 'unread' : '' }}" href="#">
                                <small class="text-muted d-block">{{ $notification->created_at->diffForHumans() }}</small>
                                <span>{{ Str::limit($notification->message, 40) }}</span>
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
        
        <!-- Навигация -->
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="fas fa-chart-pie"></i> Дашборд
                </a>
            </li>

            @if(auth()->user()->isAdmin() || auth()->user()->isOwner())
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                    <i class="fas fa-users"></i> Управление пользователями
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.schedule*') ? 'active' : '' }}" href="{{ route('admin.schedule.index') }}">
                    <i class="fas fa-calendar-alt"></i> Расписание
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.subscriptions*') ? 'active' : '' }}" href="{{ route('admin.subscriptions.index') }}">
                    <i class="fas fa-id-card"></i> Абонементы
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}" href="{{ route('admin.reports') }}">
                    <i class="fas fa-chart-bar"></i> Отчеты
                </a>
            </li>
                <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.attendance') ? 'active' : '' }}" href="{{ route('admin.attendance') }}">
                    <i class="fas fa-clipboard-list"></i> Отметить посещаемость
                </a>
            </li>
            @endif
            
            @if(auth()->user()->isClient())
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('client.schedule') ? 'active' : '' }}" href="{{ route('client.schedule') }}">
                    <i class="fas fa-calendar-check"></i> Расписание
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('client.subscriptions') ? 'active' : '' }}" href="{{ route('client.subscriptions') }}">
                    <i class="fas fa-credit-card"></i> Абонементы
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('client.profile') ? 'active' : '' }}" href="{{ route('client.profile') }}">
                    <i class="fas fa-user-circle"></i> Профиль
                </a>
            </li>
            @endif
            
            @if(auth()->user()->isTrainer())
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('trainer.schedule') ? 'active' : '' }}" href="{{ route('trainer.schedule') }}">
                    <i class="fas fa-calendar-alt"></i> Мое расписание
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('trainer.clients') ? 'active' : '' }}" href="{{ route('trainer.clients') }}">
                    <i class="fas fa-users"></i> Мои клиенты
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('trainer.profile') ? 'active' : '' }}" href="{{ route('trainer.profile') }}">
                    <i class="fas fa-user-circle"></i> Профиль
                </a>
            </li>
            @endif
        </ul>
        
        <!-- Кнопка выхода -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt me-2"></i> Выйти
            </button>
        </form>
    </div>
    
    <!-- Основной контент -->
    <main class="main-content" id="mainContent">
        @yield('content')
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            
            function toggleMenu() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                
                // Меняем иконку
                const icon = menuToggle.querySelector('i');
                if (sidebar.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
            
            // Открытие/закрытие меню
            if (menuToggle) {
                menuToggle.addEventListener('click', toggleMenu);
            }
            
            // Закрытие при клике на overlay
            if (overlay) {
                overlay.addEventListener('click', toggleMenu);
            }
            
            // Закрытие при клике на ссылку (на мобильных)
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992 && sidebar.classList.contains('active')) {
                        toggleMenu();
                    }
                });
            });
            
            // При изменении размера окна
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    sidebar.classList.remove('active');
                    overlay.classList.remove('active');
                    if (menuToggle) {
                        const icon = menuToggle.querySelector('i');
                        icon.classList.remove('fa-times');
                        icon.classList.add('fa-bars');
                    }
                }
            });
        });
    </script>
    @endauth
    
    @guest
        @yield('content')
    @endguest
    
    <!-- JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>