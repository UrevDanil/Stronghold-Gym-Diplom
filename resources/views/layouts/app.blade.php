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
    
    <!-- ===== СТИЛИ ДЛЯ СОВРЕМЕННЫХ АЛЕРТОВ ===== -->
    <style>
        .alert-modern {
            position: relative;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            border: none;
            border-radius: 16px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            animation: alertSlideIn 0.3s ease forwards;
            overflow: hidden;
        }
        .alert-modern::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: 4px 0 0 4px;
        }
        .alert-modern.success::before { background: linear-gradient(135deg, #28a745, #20c997); }
        .alert-modern.info::before { background: linear-gradient(135deg, #007bff, #00bcd4); }
        .alert-modern.warning::before { background: linear-gradient(135deg, #ffc107, #fd7e14); }
        .alert-modern.error::before,
        .alert-modern.danger::before { background: linear-gradient(135deg, #dc3545, #c82333); }
        .alert-modern .alert-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            position: relative;
            z-index: 1;
        }
        .alert-modern.success .alert-icon { background: rgba(40, 167, 69, 0.1); color: #28a745; }
        .alert-modern.info .alert-icon { background: rgba(0, 123, 255, 0.1); color: #007bff; }
        .alert-modern.warning .alert-icon { background: rgba(255, 193, 7, 0.1); color: #fd7e14; }
        .alert-modern.error .alert-icon,
        .alert-modern.danger .alert-icon { background: rgba(220, 53, 69, 0.1); color: #dc3545; }
        .alert-modern .alert-content { flex: 1; position: relative; z-index: 1; }
        .alert-modern .alert-title { font-weight: 700; font-size: 1rem; margin-bottom: 0.25rem; }
        .alert-modern.success .alert-title { color: #28a745; }
        .alert-modern.info .alert-title { color: #007bff; }
        .alert-modern.warning .alert-title { color: #fd7e14; }
        .alert-modern.error .alert-title,
        .alert-modern.danger .alert-title { color: #dc3545; }
        .alert-modern .alert-message { color: #6c757d; font-size: 0.95rem; line-height: 1.5; }
        .alert-modern .alert-close {
            flex-shrink: 0;
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 10px;
            background: transparent;
            color: #adb5bd;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            z-index: 1;
        }
        .alert-modern .alert-close:hover {
            background: #f8f9fa;
            color: #495057;
            transform: rotate(90deg);
        }
        @keyframes alertSlideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 576px) {
            .alert-modern { padding: 0.875rem 1rem; gap: 0.75rem; }
            .alert-modern .alert-icon { width: 36px; height: 36px; font-size: 1.3rem; }
            .alert-modern .alert-title { font-size: 0.95rem; }
            .alert-modern .alert-message { font-size: 0.875rem; }
            .alert-modern .alert-close { width: 28px; height: 28px; }
        }
    </style>
    
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
                
                const icon = menuToggle.querySelector('i');
                if (sidebar.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
            
            if (menuToggle) menuToggle.addEventListener('click', toggleMenu);
            if (overlay) overlay.addEventListener('click', toggleMenu);
            
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992 && sidebar.classList.contains('active')) {
                        toggleMenu();
                    }
                });
            });
            
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
            
            // ===== ЗАКРЫТИЕ СОВРЕМЕННЫХ АЛЕРТОВ =====
            document.querySelectorAll('.alert-close').forEach(function(button) {
                button.addEventListener('click', function() {
                    var alert = this.closest('.alert-modern');
                    if (alert) {
                        alert.style.opacity = '0';
                        alert.style.transform = 'translateY(-20px)';
                        alert.style.transition = 'all 0.3s ease';
                        setTimeout(function() {
                            alert.remove();
                        }, 300);
                    }
                });
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