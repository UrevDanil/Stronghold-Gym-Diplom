@extends('layouts.app')

@section('title', 'Профиль пользователя')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Профиль пользователя</h1>
        <div>
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>К списку пользователей
            </a>
            <a href="{{ route('admin.users.edit', $profile->id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Редактировать
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Основная информация -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Информация о пользователе</h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar-circle bg-{{ $profile->role->name == 'client' ? 'primary' : ($profile->role->name == 'trainer' ? 'success' : 'warning') }} text-white mx-auto mb-3">
                            {{ strtoupper(substr($profile->name, 0, 1)) }}
                        </div>
                        <h4>{{ $profile->name }}</h4>
                        <p class="text-muted">
                            @if($profile->role->name == 'client')
                                <span class="badge bg-primary">Клиент</span>
                            @elseif($profile->role->name == 'trainer')
                                <span class="badge bg-success">Тренер</span>
                            @elseif($profile->role->name == 'admin')
                                <span class="badge bg-warning">Администратор</span>
                            @elseif($profile->role->name == 'owner')
                                <span class="badge bg-danger">Владелец</span>
                            @endif
                        </p>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Email:</span>
                            <strong>{{ $profile->email }}</strong>
                        </li>
                        @if($profile->phone)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Телефон:</span>
                            <strong>{{ $profile->phone }}</strong>
                        </li>
                        @endif
                        @if($profile->birth_date)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Дата рождения:</span>
                            <strong>{{ \Carbon\Carbon::parse($profile->birth_date)->format('d.m.Y') }}</strong>
                        </li>
                        @endif
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Дата регистрации:</span>
                            <strong>{{ $profile->created_at->format('d.m.Y H:i') }}</strong>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span>Статус:</span>
                            @if($profile->deleted_at)
                                <span class="badge bg-danger">Заблокирован</span>
                            @elseif($profile->email_verified_at)
                                <span class="badge bg-success">Активен</span>
                            @else
                                <span class="badge bg-warning">Не подтвержден</span>
                            @endif
                        </li>
                    </ul>

                    <!-- КВАЛИФИКАЦИЯ ТРЕНЕРА (ИСПРАВЛЕНО) -->
                    @if($profile->role->name == 'trainer')
                        <div class="mt-3">
                            <h6 class="border-bottom pb-2">Квалификация и специализация:</h6>
                            @if($profile->qualification)
                                <p class="mb-2">
                                    <i class="fas fa-certificate text-success me-2"></i>
                                    <strong>Квалификация:</strong> {{ $profile->qualification }}
                                </p>
                            @else
                                <p class="text-muted mb-2">
                                    <i class="fas fa-certificate text-muted me-2"></i>
                                    <strong>Квалификация:</strong> не указана
                                </p>
                            @endif
                            
                            @if($profile->specialization)
                                <p class="mb-0">
                                    <i class="fas fa-dumbbell text-info me-2"></i>
                                    <strong>Специализация:</strong> {{ $profile->specialization }}
                                </p>
                            @else
                                <p class="text-muted mb-0">
                                    <i class="fas fa-dumbbell text-muted me-2"></i>
                                    <strong>Специализация:</strong> не указана
                                </p>
                            @endif
                        </div>
                    @endif

                    @if($profile->role->name == 'client' && $profile->notes)
                    <div class="mt-3">
                        <h6 class="border-bottom pb-2">Заметки/Здоровье:</h6>
                        <p class="text-muted">{{ $profile->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Статистика и активность -->
        <div class="col-md-8 mb-4">
            <!-- Статистика для клиентов -->
            @if($profile->role->name == 'client')
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <h3>{{ $attendanceStats['total'] }}</h3>
                            <small>Всего записей</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <h3>{{ $attendanceStats['attended'] }}</h3>
                            <small>Посетил</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body text-center">
                            <h3>{{ $attendanceStats['missed'] }}</h3>
                            <small>Пропустил</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <h3>{{ $attendanceRate }}%</h3>
                            <small>Посещаемость</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- История абонементов (ТОЛЬКО ДЛЯ КЛИЕНТОВ) -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">История абонементов</h5>
                </div>
                <div class="card-body">
                    @if($subscriptions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Абонемент</th>
                                        <th>Начало</th>
                                        <th>Окончание</th>
                                        <th>Статус</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subscriptions as $sub)
                                        <tr>
                                            <td>{{ $sub->subscription->name }}</td>
                                            <td>{{ \Carbon\Carbon::parse($sub->start_date)->format('d.m.Y') }}</td>
                                            <td>{{ \Carbon\Carbon::parse($sub->end_date)->format('d.m.Y') }}</td>
                                            <td>
                                                @if($sub->status == 'active')
                                                    <span class="badge bg-success">Активен</span>
                                                @elseif($sub->status == 'frozen')
                                                    <span class="badge bg-warning">Заморожен</span>
                                                @elseif($sub->status == 'expired')
                                                    <span class="badge bg-secondary">Истек</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $sub->status }}</span>
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

            <!-- Последние бронирования (ТОЛЬКО ДЛЯ КЛИЕНТОВ) -->
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Последние бронирования</h5>
                </div>
                <div class="card-body">
                    @if($recentBookings->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
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
                                            <td>{{ \Carbon\Carbon::parse($booking->schedule->date)->format('d.m.Y') }}</td>
                                            <td>{{ substr($booking->schedule->start_time, 0, 5) }}</td>
                                            <td>{{ $booking->schedule->workout->name }}</td>
                                            <td>{{ $booking->schedule->trainer->name }}</td>
                                            <td>
                                                @if($booking->status == 'booked')
                                                    <span class="badge bg-success">Забронировано</span>
                                                @elseif($booking->status == 'attended')
                                                    <span class="badge bg-info">Посещено</span>
                                                @elseif($booking->status == 'cancelled')
                                                    <span class="badge bg-warning">Отменено</span>
                                                @elseif($booking->status == 'missed')
                                                    <span class="badge bg-danger">Пропущено</span>
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

            <!-- БЛОК ДЛЯ ТРЕНЕРА (вместо статистики клиента) -->
            @if($profile->role->name == 'trainer')
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0">Информация о тренере</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-body text-center">
                                                <h3>{{ $profile->trainings_count ?? 0 }}</h3>
                                                <small class="text-muted">Проведено тренировок</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card mb-3">
                                            <div class="card-body text-center">
                                                <h3>{{ $profile->clients_count ?? 0 }}</h3>
                                                <small class="text-muted">Уникальных клиентов</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Расписание тренера (ближайшие тренировки) -->
                                @if(isset($upcomingTrainings) && $upcomingTrainings->count() > 0)
                                    <h6 class="mt-3 mb-2">Ближайшие тренировки:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
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
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($training->date)->format('d.m.Y') }}</td>
                                                        <td>{{ substr($training->start_time, 0, 5) }}</td>
                                                        <td>{{ $training->workout->name }}</td>
                                                        <td>{{ $training->bookings_count }}/{{ $training->capacity() }}</td>
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
                    </div>
                </div>
            @endif

            <!-- БЛОК ДЛЯ АДМИНА/ВЛАДЕЛЬЦА (простая информация) -->
            @if($profile->role->name == 'admin' || $profile->role->name == 'owner')
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">Информация о сотруднике</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">Пользователь является администратором системы.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        font-weight: bold;
    }
    .card .card-header {
        font-weight: 600;
    }
</style>
@endsection