<!-- Профиль пользователя -->
@extends('layouts.app')

@section('title', 'Профиль пользователя')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/admin/user-show.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 admin-profile-page">
    <!-- Заголовок -->
    <div class="profile-header">
        <h1 class="mb-0">
            <i class="fas fa-user-circle me-3"></i>Профиль пользователя
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>К списку пользователей
            </a>
            <a href="{{ route('admin.users.edit', $profile->id) }}" class="edit-btn">
                <i class="fas fa-edit me-2"></i>Редактировать
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Левая колонка - Основная информация -->
        <div class="col-lg-4 mb-4">
            <div class="profile-info-card">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Информация о пользователе
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="profile-avatar {{ $profile->role->name }}">
                            {{ strtoupper(substr($profile->name, 0, 1)) }}
                        </div>
                        <h4 class="profile-name">{{ $profile->name }}</h4>
                        <div class="profile-role">
                            <span class="role-badge {{ $profile->role->name }}">
                                {{ $profile->role->name == 'client' ? 'Клиент' : ($profile->role->name == 'trainer' ? 'Тренер' : ($profile->role->name == 'admin' ? 'Администратор' : 'Владелец')) }}
                            </span>
                        </div>
                    </div>

                    <div class="info-grid">
                        <div class="info-row">
                            <div class="info-icon email">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Email</span>
                                <span class="info-value">{{ $profile->email }}</span>
                            </div>
                        </div>

                        @if($profile->phone)
                        <div class="info-row">
                            <div class="info-icon phone">
                                <i class="fas fa-phone-alt"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Телефон</span>
                                <span class="info-value">{{ $profile->phone }}</span>
                            </div>
                        </div>
                        @endif

                        @if($profile->birth_date)
                        <div class="info-row">
                            <div class="info-icon birthday">
                                <i class="fas fa-birthday-cake"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Дата рождения</span>
                                <span class="info-value">{{ \Carbon\Carbon::parse($profile->birth_date)->format('d.m.Y') }}</span>
                            </div>
                        </div>
                        @endif

                        <div class="info-row">
                            <div class="info-icon register">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Дата регистрации</span>
                                <span class="info-value">{{ $profile->created_at->format('d.m.Y H:i') }}</span>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-icon status">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Статус</span>
                                <span class="info-value">
                                    @if($profile->deleted_at)
                                        <span class="status-badge blocked">Заблокирован</span>
                                    @elseif($profile->is_active)
                                        <span class="status-badge active">Активен</span>
                                    @else
                                        <span class="status-badge inactive">Неактивен</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Квалификация тренера -->
                    @if($profile->role->name == 'trainer')
                        <div class="qualification-box">
                            <h6><i class="fas fa-certificate me-2"></i>Квалификация и специализация</h6>
                            <div class="qualification-item">
                                <i class="fas fa-graduation-cap text-success"></i>
                                <span class="label">Квалификация:</span>
                                <span class="value">{{ $profile->qualification ?? 'не указана' }}</span>
                            </div>
                            <div class="qualification-item">
                                <i class="fas fa-dumbbell text-info"></i>
                                <span class="label">Специализация:</span>
                                <span class="value">{{ $profile->specialization ?? 'не указана' }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Заметки клиента -->
                    @if($profile->role->name == 'client' && $profile->notes)
                        <div class="qualification-box mt-3">
                            <h6><i class="fas fa-sticky-note me-2"></i>Заметки / Здоровье</h6>
                            <p class="mb-0">{{ $profile->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Правая колонка - Статистика и активность -->
        <div class="col-lg-8 mb-4">
            @if($profile->role->name == 'client')
                <!-- Статистика клиента -->
                <div class="stats-grid">
                    <div class="stat-card bg-primary text-white">
                        <div class="card-body">
                            <div class="stat-value">{{ $attendanceStats['total'] }}</div>
                            <div class="stat-label">Всего записей</div>
                        </div>
                    </div>
                    <div class="stat-card bg-success text-white">
                        <div class="card-body">
                            <div class="stat-value">{{ $attendanceStats['attended'] }}</div>
                            <div class="stat-label">Посетил</div>
                        </div>
                    </div>
                    <div class="stat-card bg-danger text-white">
                        <div class="card-body">
                            <div class="stat-value">{{ $attendanceStats['missed'] }}</div>
                            <div class="stat-label">Пропустил</div>
                        </div>
                    </div>
                    <div class="stat-card bg-warning text-white">
                        <div class="card-body">
                            <div class="stat-value">{{ $attendanceRate }}%</div>
                            <div class="stat-label">Посещаемость</div>
                        </div>
                    </div>
                </div>

                <!-- История абонементов -->
                <div class="data-card">
                    <div class="card-header subscriptions">
                        <i class="fas fa-id-card"></i> История абонементов
                    </div>
                    <div class="card-body">
                        @if($subscriptions->count() > 0)
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Абонемент</th>
                                            <th>Начало</th>
                                            <th>Окончание</th>
                                            <th>Статус</th>
                                        </thead>
                                    <tbody>
                                        @foreach($subscriptions as $sub)
                                             <tr>
                                                <td data-label="Абонемент">{{ $sub->subscription->name }}</td>
                                                <td data-label="Начало">{{ \Carbon\Carbon::parse($sub->start_date)->format('d.m.Y') }}</td>
                                                <td data-label="Окончание">{{ \Carbon\Carbon::parse($sub->end_date)->format('d.m.Y') }}</td>
                                                <td data-label="Статус">
                                                    @if($sub->status == 'active')
                                                        <span class="badge-status-sm active">Активен</span>
                                                    @elseif($sub->status == 'frozen')
                                                        <span class="badge-status-sm frozen">Заморожен</span>
                                                    @elseif($sub->status == 'expired')
                                                        <span class="badge-status-sm expired">Истек</span>
                                                    @else
                                                        <span class="badge-status-sm expired">{{ $sub->status }}</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">Нет истории абонементов</p>
                        @endif
                    </div>
                </div>

                <!-- Последние бронирования -->
                <div class="data-card">
                    <div class="card-header bookings">
                        <i class="fas fa-calendar-alt"></i> Последние бронирования
                    </div>
                    <div class="card-body">
                        @if($recentBookings->count() > 0)
                            <div class="table-responsive">
                                <table class="data-table">
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
                                                <td data-label="Дата">{{ \Carbon\Carbon::parse($booking->schedule->date)->format('d.m.Y') }}</td>
                                                <td data-label="Время">{{ substr($booking->schedule->start_time, 0, 5) }}</td>
                                                <td data-label="Тренировка">{{ $booking->schedule->workout->name }}</td>
                                                <td data-label="Тренер">{{ $booking->schedule->trainer->name }}</td>
                                                <td data-label="Статус">
                                                    @if($booking->status == 'booked')
                                                        <span class="badge-status-sm booked">Забронировано</span>
                                                    @elseif($booking->status == 'attended')
                                                        <span class="badge-status-sm attended">Посещено</span>
                                                    @elseif($booking->status == 'cancelled')
                                                        <span class="badge-status-sm cancelled">Отменено</span>
                                                    @elseif($booking->status == 'missed')
                                                        <span class="badge-status-sm missed">Пропущено</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">Нет бронирований</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Блок для тренера -->
            @if($profile->role->name == 'trainer')
                <div class="data-card">
                    <div class="card-header trainer">
                        <i class="fas fa-chalkboard-user"></i> Информация о тренере
                    </div>
                    <div class="card-body">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="stat-card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <div class="stat-value">{{ $profile->trainings_count ?? 0 }}</div>
                                        <div class="stat-label">Проведено тренировок</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat-card bg-success text-white">
                                    <div class="card-body text-center">
                                        <div class="stat-value">{{ $profile->clients_count ?? 0 }}</div>
                                        <div class="stat-label">Уникальных клиентов</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if(isset($upcomingTrainings) && $upcomingTrainings->count() > 0)
                            <h6 class="mb-3"><i class="fas fa-calendar-week me-2"></i>Ближайшие тренировки</h6>
                            <div class="table-responsive">
                                <table class="data-table">
                                    <thead>
                                        <tr>
                                            <th>Дата</th>
                                            <th>Время</th>
                                            <th>Тренировка</th>
                                            <th>Запись</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($upcomingTrainings as $training)
                                            @php
                                                $capacity = $training->workout->capacity ?? 10;
                                                $booked = $training->bookings_count ?? 0;
                                            @endphp
                                            <tr>
                                                <td data-label="Дата">{{ \Carbon\Carbon::parse($training->date)->format('d.m.Y') }}</td>
                                                <td data-label="Время">{{ substr($training->start_time, 0, 5) }}</td>
                                                <td data-label="Тренировка">{{ $training->workout->name }}</td>
                                                <td data-label="Запись">
                                                    <span class="badge-status-sm {{ $booked >= $capacity ? 'expired' : 'active' }}">
                                                        {{ $booked }}/{{ $capacity }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center py-3">Нет ближайших тренировок</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Блок для админа/владельца -->
            @if($profile->role->name == 'admin' || $profile->role->name == 'owner')
                <div class="data-card">
                    <div class="card-header" style="background: linear-gradient(135deg, #6c757d, #5a6268); color: white;">
                        <i class="fas fa-shield-alt"></i> Информация о сотруднике
                    </div>
                    <div class="card-body">
                        <p class="mb-0 text-center">Пользователь является {{ $profile->role->name == 'admin' ? 'администратором' : 'владельцем' }} системы.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection