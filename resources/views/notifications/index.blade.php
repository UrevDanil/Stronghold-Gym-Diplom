@extends('layouts.app')

@section('title', 'Уведомления')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/notifications.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4 notifications-page">
    <!-- Заголовок -->
    <div class="notifications-header d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-bell me-3"></i>Мои уведомления
            @php
                $unreadCount = $notifications->filter(function($notification) {
                    return !$notification->is_read;
                })->count();
            @endphp
            @if($unreadCount > 0)
                <span class="badge-notification">{{ $unreadCount }}</span>
            @endif
        </h1>
        <div class="d-flex gap-2">
            @if($unreadCount > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-mark-all">
                        <i class="fas fa-check-double me-2"></i>Все прочитано
                    </button>
                </form>
            @endif
            <a href="{{ route('dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>На главную
            </a>
        </div>
    </div>

    <!-- Фильтры уведомлений -->
    <div class="notifications-filters mb-4">
        <div class="filter-buttons">
            <a href="{{ route('notifications', ['filter' => 'all']) }}" 
               class="filter-btn {{ request('filter', 'all') == 'all' ? 'active' : '' }}">
                <i class="fas fa-list me-1"></i>Все
            </a>
            <a href="{{ route('notifications', ['filter' => 'booking']) }}" 
               class="filter-btn {{ request('filter') == 'booking' ? 'active' : '' }}">
                <i class="fas fa-calendar-check me-1"></i>Бронирования
            </a>
            <a href="{{ route('notifications', ['filter' => 'subscription']) }}" 
               class="filter-btn {{ request('filter') == 'subscription' ? 'active' : '' }}">
                <i class="fas fa-id-card me-1"></i>Абонементы
            </a>
            <a href="{{ route('notifications', ['filter' => 'attendance']) }}" 
               class="filter-btn {{ request('filter') == 'attendance' ? 'active' : '' }}">
                <i class="fas fa-user-check me-1"></i>Посещаемость
            </a>
            @if(auth()->user()->isTrainer() || auth()->user()->isAdmin())
                <a href="{{ route('notifications', ['filter' => 'schedule']) }}" 
                   class="filter-btn {{ request('filter') == 'schedule' ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt me-1"></i>Расписание
                </a>
            @endif
            <a href="{{ route('notifications', ['filter' => 'system']) }}" 
               class="filter-btn {{ request('filter') == 'system' ? 'active' : '' }}">
                <i class="fas fa-bell me-1"></i>Системные
            </a>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('notifications', ['filter' => 'warning']) }}" 
                   class="filter-btn {{ request('filter') == 'warning' ? 'active' : '' }}">
                    <i class="fas fa-exclamation-triangle me-1"></i>Предупреждения
                </a>
            @endif
        </div>
    </div>

    <div class="notifications-card">
        <div class="card-body p-0">
            @if($notifications->count() > 0)
                <div class="notifications-list" id="notificationsList">
                    @foreach($notifications as $notification)
                        <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}" 
                             data-id="{{ $notification->id }}"
                             data-type="{{ $notification->type }}">
                            <div class="notification-main">
                                <div class="notification-icon 
                                    @if($notification->type == 'booking') booking
                                    @elseif($notification->type == 'subscription') subscription
                                    @elseif($notification->type == 'attendance') attendance
                                    @elseif($notification->type == 'schedule') schedule
                                    @elseif($notification->type == 'warning') warning
                                    @else system
                                    @endif">
                                    @if($notification->type == 'booking')
                                        <i class="fas fa-calendar-check"></i>
                                    @elseif($notification->type == 'subscription')
                                        <i class="fas fa-id-card"></i>
                                    @elseif($notification->type == 'attendance')
                                        <i class="fas fa-user-check"></i>
                                    @elseif($notification->type == 'schedule')
                                        <i class="fas fa-calendar-alt"></i>
                                    @elseif($notification->type == 'warning')
                                        <i class="fas fa-exclamation-triangle"></i>
                                    @else
                                        <i class="fas fa-bell"></i>
                                    @endif
                                </div>
                                <div class="notification-content">
                                    <div class="notification-header">
                                        <span class="notification-type-badge {{ $notification->type }}">
                                            @if($notification->type == 'booking')
                                                <i class="fas fa-calendar-check"></i> Бронирование
                                            @elseif($notification->type == 'subscription')
                                                <i class="fas fa-id-card"></i> Абонемент
                                            @elseif($notification->type == 'attendance')
                                                <i class="fas fa-user-check"></i> Посещаемость
                                            @elseif($notification->type == 'schedule')
                                                <i class="fas fa-calendar-alt"></i> Расписание
                                            @elseif($notification->type == 'warning')
                                                <i class="fas fa-exclamation-triangle"></i> Предупреждение
                                            @else
                                                <i class="fas fa-bell"></i> Система
                                            @endif
                                        </span>
                                        @if(!$notification->is_read)
                                            <span class="unread-badge">Новое</span>
                                        @endif
                                    </div>
                                    <p class="notification-message">{{ $notification->message }}</p>
                                    <div class="notification-meta">
                                        <div class="notification-time">
                                            <i class="fas fa-clock"></i>
                                            <span>{{ $notification->created_at->diffForHumans() }}</span>
                                            <span class="separator">•</span>
                                            <span>{{ $notification->created_at->format('d.m.Y H:i') }}</span>
                                        </div>
                                        @if($notification->data && isset($notification->data['link']))
                                            <a href="{{ $notification->data['link'] }}" class="notification-link">
                                                <i class="fas fa-external-link-alt"></i> Подробнее
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="notification-actions">
                                @if(!$notification->is_read)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="mark-read-btn" title="Отметить как прочитанное">
                                            <i class="fas fa-check"></i>
                                            <span class="d-none d-md-inline">Прочитано</span>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-notification-btn" title="Удалить уведомление" 
                                            onclick="return confirm('Удалить это уведомление?')">
                                        <i class="fas fa-trash-alt"></i>
                                        <span class="d-none d-md-inline">Удалить</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if($notifications->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            <i class="fas fa-bell"></i>
                            Показано с <strong>{{ $notifications->firstItem() }}</strong> по <strong>{{ $notifications->lastItem() }}</strong> 
                            из <strong>{{ $notifications->total() }}</strong> уведомлений
                        </div>
                        
                        <div class="pagination-container">
                            <ul class="pagination">
                                {{-- Кнопка "Назад" --}}
                                @if($notifications->onFirstPage())
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-left"></i>
                                        </span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $notifications->previousPageUrl() }}&filter={{ request('filter') }}">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                @endif

                                {{-- Номера страниц --}}
                                @php
                                    $currentPage = $notifications->currentPage();
                                    $lastPage = $notifications->lastPage();
                                    $start = max(1, $currentPage - 2);
                                    $end = min($lastPage, $currentPage + 2);
                                    
                                    if ($start > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="' . $notifications->url(1) . '&filter=' . request('filter') . '">1</a></li>';
                                        if ($start > 2) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                    }
                                    
                                    for ($i = $start; $i <= $end; $i++) {
                                        if ($i == $currentPage) {
                                            echo '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                                        } else {
                                            echo '<li class="page-item"><a class="page-link" href="' . $notifications->url($i) . '&filter=' . request('filter') . '">' . $i . '</a></li>';
                                        }
                                    }
                                    
                                    if ($end < $lastPage) {
                                        if ($end < $lastPage - 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                        echo '<li class="page-item"><a class="page-link" href="' . $notifications->url($lastPage) . '&filter=' . request('filter') . '">' . $lastPage . '</a></li>';
                                    }
                                @endphp

                                {{-- Кнопка "Вперед" --}}
                                @if($notifications->hasMorePages())
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $notifications->nextPageUrl() }}&filter={{ request('filter') }}">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                @else
                                    <li class="page-item disabled">
                                        <span class="page-link">
                                            <i class="fas fa-chevron-right"></i>
                                        </span>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-notifications">
                    <div class="empty-icon">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <h4>Нет уведомлений</h4>
                    <p class="text-muted">
                        @if(request('filter') && request('filter') != 'all')
                            В этой категории пока нет уведомлений
                        @else
                            У вас пока нет уведомлений
                        @endif
                    </p>
                    <a href="{{ route('dashboard') }}" class="btn-primary">
                        <i class="fas fa-home me-2"></i>На главную
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const successMessages = document.querySelectorAll('.alert-success');
        successMessages.forEach(function(message) {
            setTimeout(function() {
                message.style.transition = 'opacity 0.5s ease';
                message.style.opacity = '0';
                setTimeout(function() {
                    message.remove();
                }, 500);
            }, 3000);
        });
        
        const markAllForm = document.querySelector('form[action*="read-all"]');
        if (markAllForm) {
            markAllForm.addEventListener('submit', function(e) {
                if (!confirm('Отметить все уведомления как прочитанные?')) {
                    e.preventDefault();
                }
            });
        }
        
        const notificationItems = document.querySelectorAll('.notification-item');
        notificationItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateY(20px)';
            setTimeout(() => {
                item.style.transition = 'all 0.3s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateY(0)';
            }, index * 50);
        });
    });
</script>
@endpush