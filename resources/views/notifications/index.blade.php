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
                $unreadCount = $notifications->where('is_read', false)->count();
            @endphp
            @if($unreadCount > 0)
                <span class="badge-notification">{{ $unreadCount }}</span>
            @endif
        </h1>
        <div class="d-flex gap-2">
            @if($unreadCount > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST" id="markAllForm">
                    @csrf
                    <button type="submit" class="btn-mark-all">
                        <i class="fas fa-check-double me-2"></i>Все прочитано
                    </button>
                </form>
            @endif
            <a href="{{ url()->previous() }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
        </div>
    </div>

    <!-- Фильтры уведомлений -->
    <div class="notifications-filters mb-4">
        <div class="filter-buttons">
            <button class="filter-btn active" data-filter="all">
                <i class="fas fa-list me-1"></i>Все
            </button>
            <button class="filter-btn" data-filter="booking">
                <i class="fas fa-calendar-check me-1"></i>Бронирования
            </button>
            <button class="filter-btn" data-filter="subscription">
                <i class="fas fa-id-card me-1"></i>Абонементы
            </button>
            <button class="filter-btn" data-filter="attendance">
                <i class="fas fa-user-check me-1"></i>Посещаемость
            </button>
            @if(auth()->user()->isTrainer() || auth()->user()->isAdmin())
                <button class="filter-btn" data-filter="schedule">
                    <i class="fas fa-calendar-alt me-1"></i>Расписание
                </button>
            @endif
            <button class="filter-btn" data-filter="system">
                <i class="fas fa-bell me-1"></i>Системные
            </button>
        </div>
    </div>

    <div class="notifications-card">
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="notifications-list" id="notificationsList">
                    @foreach($notifications as $notification)
                        <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}" 
                             data-type="{{ $notification->type }}">
                            <div class="notification-main">
                                <div class="notification-icon 
                                    @if($notification->type == 'booking') booking
                                    @elseif($notification->type == 'subscription') subscription
                                    @elseif($notification->type == 'attendance') attendance
                                    @elseif($notification->type == 'schedule') schedule
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
                                            @else
                                                <i class="fas fa-bell"></i> Система
                                            @endif
                                        </span>
                                        @if(!$notification->is_read)
                                            <span class="unread-badge">Новое</span>
                                        @endif
                                    </div>
                                    <p class="notification-message">{{ $notification->message }}</p>
                                    <div class="notification-time">
                                        <i class="fas fa-clock"></i>
                                        {{ $notification->created_at->diffForHumans() }}
                                        <span class="separator">•</span>
                                        {{ $notification->created_at->format('d.m.Y H:i') }}
                                    </div>
                                    @if($notification->data)
                                        <div class="notification-meta">
                                            @if(isset($notification->data['link']))
                                                <a href="{{ $notification->data['link'] }}" class="notification-link">
                                                    <i class="fas fa-external-link-alt"></i> Подробнее
                                                </a>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="notification-actions">
                                @if(!$notification->is_read)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="mark-read-btn" title="Отметить как прочитанное">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('notifications.delete', $notification->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-notification-btn" title="Удалить уведомление" 
                                            onclick="return confirm('Удалить это уведомление?')">
                                        <i class="fas fa-trash-alt"></i>
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
                            {{ $notifications->links() }}
                        </div>
                    </div>
                @endif
            @else
                <div class="empty-notifications">
                    <div class="empty-icon">
                        <i class="fas fa-bell-slash"></i>
                    </div>
                    <h4>Нет уведомлений</h4>
                    <p class="text-muted">У вас пока нет уведомлений</p>
                    <a href="{{ route('dashboard') }}" class="btn-primary">
                        <i class="fas fa-home me-2"></i>На главную
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Фильтрация уведомлений
    document.querySelectorAll('.filter-btn').forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            document.querySelectorAll('.notification-item').forEach(item => {
                if (filter === 'all' || item.dataset.type === filter) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</script>
@endpush
@endsection