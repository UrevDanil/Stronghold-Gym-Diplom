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
        </h1>
        <div class="d-flex gap-2">
            @if($notifications->where('is_read', false)->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
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

    @php
        $notifications = App\Models\Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);
    @endphp

    <div class="notifications-card">
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="notifications-list">
                    @foreach($notifications as $notification)
                        <div class="notification-item d-flex align-items-center justify-content-between {{ !$notification->is_read ? 'unread' : '' }}">
                            <div class="d-flex align-items-center">
                                <div class="notification-icon 
                                    @if($notification->type == 'booking') booking
                                    @elseif($notification->type == 'subscription') subscription
                                    @else system
                                    @endif">
                                    @if($notification->type == 'booking')
                                        <i class="fas fa-calendar-check"></i>
                                    @elseif($notification->type == 'subscription')
                                        <i class="fas fa-id-card"></i>
                                    @else
                                        <i class="fas fa-bell"></i>
                                    @endif
                                </div>
                                <div class="notification-content">
                                    <p class="notification-message">{{ $notification->message }}</p>
                                    <div class="notification-time">
                                        <i class="fas fa-clock"></i>
                                        {{ $notification->created_at->format('d.m.Y H:i') }}
                                    </div>
                                </div>
                            </div>
                            
                            @if(!$notification->is_read)
                                <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="mark-read-btn">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
                
                @if($notifications->hasPages())
                    <div class="pagination-wrapper">
                        <div class="pagination-info">
                            Показано с {{ $notifications->firstItem() }} по {{ $notifications->lastItem() }} из {{ $notifications->total() }} уведомлений
                        </div>
                        
                        <div class="pagination-container">
                            {{-- Кнопка "Назад" --}}
                            @if($notifications->onFirstPage())
                                <span class="pagination-prev disabled">
                                    <i class="fas fa-chevron-left"></i>
                                </span>
                            @else
                                <a href="{{ $notifications->previousPageUrl() }}" class="pagination-prev">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            @endif
                            
                            {{-- Номера страниц --}}
                            <div class="pagination-pages">
                                @foreach(range(1, $notifications->lastPage()) as $i)
                                    @if($i == $notifications->currentPage())
                                        <span class="pagination-page active">{{ $i }}</span>
                                    @elseif($i >= $notifications->currentPage() - 2 && $i <= $notifications->currentPage() + 2)
                                        <a href="{{ $notifications->url($i) }}" class="pagination-page">{{ $i }}</a>
                                    @elseif($i == 1 || $i == $notifications->lastPage())
                                        <a href="{{ $notifications->url($i) }}" class="pagination-page">{{ $i }}</a>
                                    @elseif($i == $notifications->currentPage() - 3 || $i == $notifications->currentPage() + 3)
                                        <span class="pagination-dots">...</span>
                                    @endif
                                @endforeach
                            </div>
                            
                            {{-- Кнопка "Вперед" --}}
                            @if($notifications->hasMorePages())
                                <a href="{{ $notifications->nextPageUrl() }}" class="pagination-next">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            @else
                                <span class="pagination-next disabled">
                                    <i class="fas fa-chevron-right"></i>
                                </span>
                            @endif
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
                    <a href="{{ route('client.dashboard') }}" class="btn-primary">
                        <i class="fas fa-home me-2"></i>На главную
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection