@extends('layouts.app')

@section('title', 'Профиль клиента')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Профиль клиента</h1>
        <div>
            <a href="{{ route('trainer.clients') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>К списку клиентов
            </a>
            <span class="text-muted">
                <i class="fas fa-user me-2"></i>{{ auth()->user()->name }}
            </span>
        </div>
    </div>

    <div class="row">
        <!-- Основная информация -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <div class="avatar-circle bg-primary text-white mx-auto mb-3" style="width: 80px; height: 80px; font-size: 32px;">
                        {{ strtoupper(substr($client->name, 0, 1)) }}
                    </div>
                    <h3>{{ $client->name }}</h3>
                    
                    <div class="text-start mt-3">
                        <p class="mb-2">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            {{ $client->email }}
                        </p>
                        @if($client->phone)
                        <p class="mb-2">
                            <i class="fas fa-phone text-success me-2"></i>
                            {{ $client->phone }}
                        </p>
                        @endif
                        @if($client->birth_date)
                        <p class="mb-2">
                            <i class="fas fa-birthday-cake text-info me-2"></i>
                            {{ \Carbon\Carbon::parse($client->birth_date)->format('d.m.Y') }}
                            ({{ \Carbon\Carbon::parse($client->birth_date)->age }} лет)
                        </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Статистика -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Статистика тренировок</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-3">
                            <div class="p-3 bg-light rounded">
                                <h3 class="text-primary mb-0">{{ $totalTrainings }}</h3>
                                <small class="text-muted">Всего</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-3 bg-light rounded">
                                <h3 class="text-success mb-0">{{ $attendedTrainings }}</h3>
                                <small class="text-muted">Посетил</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-3 bg-light rounded">
                                <h3 class="text-danger mb-0">{{ $missedTrainings }}</h3>
                                <small class="text-muted">Пропустил</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="p-3 bg-light rounded">
                                <h3 class="text-warning mb-0">{{ $cancelledTrainings }}</h3>
                                <small class="text-muted">Отменено</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Посещаемость</span>
                            <span class="fw-bold">{{ $attendanceRate }}%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar bg-success" style="width: {{ $attendanceRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- История тренировок -->
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">История тренировок</h5>
        </div>
        <div class="card-body">
            @if($recentBookings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Время</th>
                                <th>Тренировка</th>
                                <th>Статус</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($booking->schedule->date)->format('d.m.Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($booking->schedule->start_time)->format('H:i') }}</td>
                                    <td>{{ $booking->schedule->workout->name }}</td>
                                    <td>
                                        @if($booking->status === 'attended')
                                            <span class="badge bg-success">Посетил</span>
                                        @elseif($booking->status === 'missed')
                                            <span class="badge bg-danger">Пропустил</span>
                                        @elseif($booking->status === 'cancelled')
                                            <span class="badge bg-warning">Отменено</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $booking->status }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted text-center py-4">Нет истории тренировок</p>
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
    .bg-light {
        background-color: #f8f9fa;
    }
</style>
@endsection