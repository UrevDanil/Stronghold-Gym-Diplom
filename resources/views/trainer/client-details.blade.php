<!-- Профиль клиента -->
@extends('layouts.app')

@section('title', 'Профиль клиента')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/trainer/trainer-client-details.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4 client-profile-page">
    <!-- Заголовок -->
    <div class="profile-header d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-user-circle me-3"></i>Профиль клиента
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('trainer.clients') }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>К списку клиентов
            </a>
            <span class="trainer-info">
                <i class="fas fa-user me-2"></i>{{ auth()->user()->name }}
            </span>
        </div>
    </div>

    <div class="row">
        <!-- Левая колонка - Основная информация -->
        <div class="col-lg-4 mb-4">
            <!-- Карточка профиля -->
            <div class="profile-card">
                <div class="profile-card__header">
                    <div class="profile-avatar {{ $client->isActive() ? 'online' : '' }}">
                        {{ strtoupper(substr($client->name, 0, 1)) }}
                        @if($client->isActive())
                            <span class="online-badge" title="В сети"></span>
                        @endif
                    </div>
                    <div class="profile-status">
                        @if($client->hasActiveSubscription())
                            <span class="status-badge active">
                                <i class="fas fa-crown me-1"></i>Активный абонемент
                            </span>
                        @else
                            <span class="status-badge inactive">
                                <i class="fas fa-clock me-1"></i>Нет абонемента
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="profile-card__body">
                    <h2 class="profile-name">{{ $client->name }}</h2>
                    
                    <div class="profile-info-grid">
                        <!-- Email -->
                        <div class="info-row">
                            <div class="info-icon email">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Email</span>
                                <a href="mailto:{{ $client->email }}" class="info-value email-value">
                                    {{ $client->email }}
                                </a>
                            </div>
                        </div>

                        <!-- Телефон -->
                        @if($client->phone)
                        <div class="info-row">
                            <div class="info-icon phone">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Телефон</span>
                                <a href="tel:{{ $client->phone }}" class="info-value phone-value">
                                    {{ $client->formatted_phone ?? $client->phone }}
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- Дата рождения -->
                        @if($client->birth_date)
                        <div class="info-row">
                            <div class="info-icon birthday">
                                <i class="fas fa-birthday-cake"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Дата рождения</span>
                                <span class="info-value">
                                    {{ \Carbon\Carbon::parse($client->birth_date)->format('d.m.Y') }}
                                    <span class="age-badge">{{ \Carbon\Carbon::parse($client->birth_date)->age }} лет</span>
                                </span>
                            </div>
                        </div>
                        @endif

                        <!-- Дата регистрации -->
                        <div class="info-row">
                            <div class="info-icon registered">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Клиент с</span>
                                <span class="info-value">
                                    {{ \Carbon\Carbon::parse($client->created_at)->format('d.m.Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Заметки о клиенте (из поля notes) -->
                    @if($client->notes)
                    <div class="client-notes">
                        <div class="notes-header">
                            <i class="fas fa-sticky-note me-2"></i>
                            <span>Заметки о клиенте</span>
                        </div>
                        <div class="notes-content">
                            {{ $client->notes }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Правая колонка - Статистика и история -->
        <div class="col-lg-8 mb-4">
            <!-- Статистика тренировок -->
            <div class="stats-card">
                <div class="stats-card__header">
                    <i class="fas fa-chart-line me-2"></i>
                    Статистика тренировок
                </div>
                <div class="stats-card__body">
                    <div class="row g-4">
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <div class="stat-icon bg-primary-soft">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-label">Всего</span>
                                    <span class="stat-value">{{ $totalTrainings }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <div class="stat-icon bg-success-soft">
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-label">Посетил</span>
                                    <span class="stat-value text-success">{{ $attendedTrainings }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <div class="stat-icon bg-danger-soft">
                                    <i class="fas fa-times-circle text-danger"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-label">Пропустил</span>
                                    <span class="stat-value text-danger">{{ $missedTrainings }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="stat-item">
                                <div class="stat-icon bg-warning-soft">
                                    <i class="fas fa-ban text-warning"></i>
                                </div>
                                <div class="stat-content">
                                    <span class="stat-label">Отменено</span>
                                    <span class="stat-value text-warning">{{ $cancelledTrainings }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Прогресс-бар посещаемости -->
                    <div class="attendance-progress mt-4">
                        <div class="progress-label">
                            <span>Посещаемость</span>
                            <span class="progress-percent">{{ $attendanceRate }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" 
                                 style="width: {{ $attendanceRate }}%; 
                                        background: linear-gradient(90deg, #28a745, #20c997);">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- История тренировок -->
            <div class="history-card mt-4">
                <div class="history-card__header">
                    <i class="fas fa-history me-2"></i>
                    История тренировок
                </div>
                <div class="history-card__body">
                    @if($recentBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="history-table">
                                <thead>
                                    <tr>
                                        <th>Дата</th>
                                        <th>Время</th>
                                        <th>Тренировка</th>
                                        <th>Тренер</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentBookings as $booking)
                                        <tr>
                                            <td data-label="Дата">
                                                <span class="date-cell">
                                                    {{ \Carbon\Carbon::parse($booking->schedule->date)->format('d.m') }}
                                                    <small>{{ \Carbon\Carbon::parse($booking->schedule->date)->format('Y') }}</small>
                                                </span>
                                            </td>
                                            <td data-label="Время">
                                                <span class="time-badge">
                                                    {{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}
                                                </span>
                                            </td>
                                            <td data-label="Тренировка">
                                                <span class="workout-name">
                                                    {{ $booking->schedule->workout->name }}
                                                </span>
                                            </td>
                                            <td data-label="Тренер">
                                                <span class="trainer-name">
                                                    {{ $booking->schedule->trainer->name ?? '—' }}
                                                </span>
                                            </td>
                                            <td data-label="Статус">
                                                @if($booking->status === 'attended')
                                                    <span class="status-badge-mini success">
                                                        <i class="fas fa-check-circle me-1"></i>Посетил
                                                    </span>
                                                @elseif($booking->status === 'missed')
                                                    <span class="status-badge-mini danger">
                                                        <i class="fas fa-times-circle me-1"></i>Пропустил
                                                    </span>
                                                @elseif($booking->status === 'cancelled')
                                                    <span class="status-badge-mini warning">
                                                        <i class="fas fa-ban me-1"></i>Отменено
                                                    </span>
                                                @else
                                                    <span class="status-badge-mini secondary">
                                                        {{ $booking->status }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-history">
                            <i class="fas fa-calendar-times"></i>
                            <h4>Нет истории тренировок</h4>
                            <p class="text-muted">У клиента пока нет записей на тренировки</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection