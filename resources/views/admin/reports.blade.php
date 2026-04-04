<!-- Отчеты -->
@extends('layouts.app')

@section('title', 'Отчеты')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/admin/reports.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 admin-reports-page">
    <!-- Заголовок -->
    <div class="reports-header">
        <h1 class="mb-0">
            <i class="fas fa-chart-line me-3"></i>Отчеты и аналитика
        </h1>
        <a href="{{ route('admin.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
    </div>

    <!-- Фильтр периода -->
    <div class="filters-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-calendar-alt me-2"></i>Выберите период
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.reports') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="period" class="form-label">Период</label>
                    <select class="form-select" id="period" name="period">
                        <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Текущий месяц</option>
                        <option value="quarter" {{ $period == 'quarter' ? 'selected' : '' }}>Текущий квартал</option>
                        <option value="year" {{ $period == 'year' ? 'selected' : '' }}>Текущий год</option>
                        <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Произвольный</option>
                    </select>
                </div>
                <div class="col-md-3 date-range" style="{{ $period == 'custom' ? 'display:block' : 'display:none' }}">
                    <label for="date_from" class="form-label">Дата с</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ $dateFrom }}">
                </div>
                <div class="col-md-3 date-range" style="{{ $period == 'custom' ? 'display:block' : 'display:none' }}">
                    <label for="date_to" class="form-label">Дата по</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ $dateTo }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn-filter w-100">
                        <i class="fas fa-chart-line me-2"></i>Показать отчет
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Общая статистика -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon bg-primary-soft">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Всего клиентов</span>
                    <span class="stat-value">{{ $totalClients }}</span>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon bg-success-soft">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Всего тренеров</span>
                    <span class="stat-value">{{ $totalTrainers }}</span>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon bg-info-soft">
                    <i class="fas fa-dumbbell"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Видов тренировок</span>
                    <span class="stat-value">{{ $totalWorkouts }}</span>
                </div>
            </div>
        </div>
        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon bg-warning-soft">
                    <i class="fas fa-ruble-sign"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Выручка за период</span>
                    <span class="stat-value">{{ number_format($totalRevenue, 0, ',', ' ') }} ₽</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Финансовая статистика -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-2"></i>Выручка по типам абонементов
                </div>
                <div class="card-body">
                    @if($revenueBySubscription->count() > 0)
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Абонемент</th>
                                        <th>Продано</th>
                                        <th>Выручка</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($revenueBySubscription as $name => $data)
                                        <tr>
                                            <td data-label="Абонемент"><strong>{{ $name }}</strong></td>
                                            <td data-label="Продано">{{ $data['count'] }} шт.</td>
                                            <td data-label="Выручка" class="text-primary fw-bold">{{ number_format($data['revenue'], 0, ',', ' ') }} ₽</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state-mini">
                            <i class="fas fa-chart-line"></i>
                            <p>Нет данных за выбранный период</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Статистика тренировок -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="card-header">
                    <i class="fas fa-calendar-alt me-2"></i>Статистика тренировок
                </div>
                <div class="card-body">
                    <div class="stats-mini-grid">
                        <div class="stat-mini">
                            <span class="stat-mini-label">Всего</span>
                            <span class="stat-mini-value text-primary">{{ $totalTrainings }}</span>
                        </div>
                        <div class="stat-mini">
                            <span class="stat-mini-label">Проведено</span>
                            <span class="stat-mini-value text-success">{{ $completedTrainings }}</span>
                        </div>
                        <div class="stat-mini">
                            <span class="stat-mini-label">Отменено</span>
                            <span class="stat-mini-value text-danger">{{ $cancelledTrainings }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Статистика посещаемости -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="card-header">
                    <i class="fas fa-clipboard-list me-2"></i>Статистика посещаемости
                </div>
                <div class="card-body">
                    <div class="stats-mini-grid">
                        <div class="stat-mini">
                            <span class="stat-mini-label">Записей</span>
                            <span class="stat-mini-value text-primary">{{ $totalBookings }}</span>
                        </div>
                        <div class="stat-mini">
                            <span class="stat-mini-label">Посетили</span>
                            <span class="stat-mini-value text-success">{{ $attendedCount }}</span>
                        </div>
                        <div class="stat-mini">
                            <span class="stat-mini-label">Пропустили</span>
                            <span class="stat-mini-value text-danger">{{ $missedCount }}</span>
                        </div>
                        <div class="stat-mini">
                            <span class="stat-mini-label">Отменили</span>
                            <span class="stat-mini-value text-warning">{{ $cancelledCount }}</span>
                        </div>
                    </div>
                    <div class="attendance-progress">
                        <div class="progress-label">
                            <span>Посещаемость</span>
                            <span class="progress-percent">{{ $attendanceRate }}%</span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $attendanceRate }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Популярные тренировки -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="card-header">
                    <i class="fas fa-chart-simple me-2"></i>Популярные тренировки
                </div>
                <div class="card-body">
                    @if($popularWorkouts->count() > 0)
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Тренировка</th>
                                        <th>Кол-во</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($popularWorkouts as $workout)
                                        <tr>
                                            <td data-label="Тренировка"><strong>{{ $workout->name }}</strong></td>
                                            <td data-label="Кол-во"><span class="badge bg-primary">{{ $workout->schedules_count }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state-mini">
                            <i class="fas fa-dumbbell"></i>
                            <p>Нет данных</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Топ тренеров -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="card-header">
                    <i class="fas fa-trophy me-2"></i>Топ тренеров
                </div>
                <div class="card-body">
                    @if($topTrainers->count() > 0)
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Тренер</th>
                                        <th>Тренировок</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topTrainers as $trainer)
                                        <tr>
                                            <td data-label="Тренер"><strong>{{ $trainer->name }}</strong></td>
                                            <td data-label="Тренировок"><span class="badge bg-success">{{ $trainer->trainings_count }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state-mini">
                            <i class="fas fa-user-tie"></i>
                            <p>Нет данных</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Топ клиентов -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="card-header">
                    <i class="fas fa-crown me-2"></i>Топ клиентов
                </div>
                <div class="card-body">
                    @if($topClients->count() > 0)
                        <div class="table-responsive">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Клиент</th>
                                        <th>Записей</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topClients as $client)
                                        <tr>
                                            <td data-label="Клиент"><strong>{{ $client->name }}</strong></td>
                                            <td data-label="Записей"><span class="badge bg-info">{{ $client->bookings_count }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state-mini">
                            <i class="fas fa-users"></i>
                            <p>Нет данных</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const periodSelect = document.getElementById('period');
        const dateRange = document.querySelectorAll('.date-range');
        
        if (periodSelect) {
            periodSelect.addEventListener('change', function() {
                if (this.value === 'custom') {
                    dateRange.forEach(el => el.style.display = 'block');
                } else {
                    dateRange.forEach(el => el.style.display = 'none');
                }
            });
        }
    });
</script>
@endsection