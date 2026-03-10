@extends('layouts.app')

@section('title', 'Уведомления')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Мои уведомления</h1>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
    </div>

    @php
        $notifications = App\Models\Notification::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);
    @endphp

    <div class="card">
        <div class="card-body">
            @if($notifications->count() > 0)
                <div class="list-group">
                    @foreach($notifications as $notification)
                        <div class="list-group-item {{ !$notification->is_read ? 'list-group-item-primary' : '' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        @if($notification->type == 'booking')
                                            <i class="fas fa-calendar-check fa-2x text-primary"></i>
                                        @elseif($notification->type == 'subscription')
                                            <i class="fas fa-id-card fa-2x text-success"></i>
                                        @else
                                            <i class="fas fa-bell fa-2x text-warning"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="mb-1">{{ $notification->message }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>{{ $notification->created_at->format('d.m.Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                                @if(!$notification->is_read)
                                    <form action="{{ route('notifications.read', $notification->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-check"></i> Отметить прочитанным
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-4">
                    {{ $notifications->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                    <h4>Нет уведомлений</h4>
                    <p class="text-muted">У вас пока нет уведомлений</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection