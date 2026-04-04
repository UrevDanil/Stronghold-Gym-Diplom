<!-- Управление абонементами -->
@extends('layouts.app')

@section('title', 'Управление абонементами')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/admin/subscriptions.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 admin-subscriptions-page">
    <!-- Заголовок -->
    <div class="subscriptions-header">
        <h1 class="mb-0">
            <i class="fas fa-id-card me-3"></i>Управление абонементами
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
            <a href="{{ route('admin.subscriptions.create') }}" class="create-btn">
                <i class="fas fa-plus me-2"></i>Новый абонемент
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-modern alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center gap-3">
                <div class="alert-icon">
                    <i class="fas fa-check-circle fa-lg"></i>
                </div>
                <div class="flex-grow-1">
                    <strong class="d-block">Успешно!</strong>
                    {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-modern alert-danger alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center gap-3">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-circle fa-lg"></i>
                </div>
                <div class="flex-grow-1">
                    <strong class="d-block">Ошибка!</strong>
                    {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Статистика -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon bg-primary-soft">
                    <i class="fas fa-id-card"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Всего абонементов</span>
                    <span class="stat-value">{{ $subscriptions->total() }}</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon bg-success-soft">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Активных</span>
                    <span class="stat-value">{{ $subscriptions->where('is_active', true)->count() }}</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon bg-danger-soft">
                    <i class="fas fa-ban"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Неактивных</span>
                    <span class="stat-value">{{ $subscriptions->where('is_active', false)->count() }}</span>
                </div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="card-body">
                <div class="stat-icon bg-info-soft">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <span class="stat-label">Всего продаж</span>
                    <span class="stat-value">{{ \App\Models\UserSubscription::count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Фильтры -->
    <div class="filters-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2"></i>Фильтры
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Поиск</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Название или описание..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Статус</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Все абонементы</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Только активные</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Только неактивные</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end gap-2">
                    <button type="submit" class="btn-filter w-100">
                        <i class="fas fa-search me-2"></i>Применить
                    </button>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn-reset">
                        <i class="fas fa-times me-2"></i>Сбросить
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Таблица абонементов -->
    <div class="subscriptions-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Список абонементов
            </h5>
            <span class="records-badge">{{ $subscriptions->total() }} записей</span>
        </div>
        <div class="card-body p-0">
            @if($subscriptions->count() > 0)
                <div class="table-responsive">
                    <table class="subscriptions-table">
                        <thead>
                            <tr>
                                <th style="width: 5%">ID</th>
                                <th style="width: 30%">Название</th>
                                <th style="width: 10%">Срок</th>
                                <th style="width: 10%">Тренировки</th>
                                <th style="width: 10%">Цена</th>
                                <th style="width: 10%">Тип</th>
                                <th style="width: 10%">Статус</th>
                                <th style="width: 10%">Продажи</th>
                                <th style="width: 5%" class="text-end">Действия</th>
                            </thead>
                        <tbody>
                            @foreach($subscriptions as $subscription)
                                 <tr>
                                    <td data-label="ID" class="fw-semibold">#{{ $subscription->id }}</td>
                                    <td data-label="Название">
                                        <div>
                                            <strong class="d-block">{{ $subscription->name }}</strong>
                                            @if($subscription->description)
                                                <small class="text-muted">{{ Str::limit($subscription->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td data-label="Срок">
                                        <span class="badge-custom badge-days">
                                            <i class="fas fa-calendar-alt me-1"></i>{{ $subscription->duration_days }} дн.
                                        </span>
                                    </td>
                                    <td data-label="Тренировки">
                                        <span class="badge-custom badge-workouts">
                                            <i class="fas fa-dumbbell me-1"></i>{{ $subscription->workouts_count }}
                                        </span>
                                    </td>
                                    <td data-label="Цена">
                                        <strong class="text-primary">{{ number_format($subscription->price, 0, ',', ' ') }} ₽</strong>
                                    </td>
                                    <td data-label="Тип">
                                        @if($subscription->type == 'time')
                                            <span class="badge-custom badge-time">
                                                <i class="fas fa-clock me-1"></i>По времени
                                            </span>
                                        @else
                                            <span class="badge-custom badge-count">
                                                <i class="fas fa-hashtag me-1"></i>По кол-ву
                                            </span>
                                        @endif
                                    </td>
                                    <td data-label="Статус">
                                        @if($subscription->is_active)
                                            <span class="badge-custom badge-active">
                                                <i class="fas fa-check-circle me-1"></i>Активен
                                            </span>
                                        @else
                                            <span class="badge-custom badge-inactive">
                                                <i class="fas fa-ban me-1"></i>Неактивен
                                            </span>
                                        @endif
                                    </td>
                                    <td data-label="Продажи">
                                        @php
                                            $totalCount = \App\Models\UserSubscription::where('subscription_id', $subscription->id)->count();
                                        @endphp
                                        <span class="badge-custom badge-sales">
                                            <i class="fas fa-shopping-cart me-1"></i>{{ $totalCount }} шт.
                                        </span>
                                    </td>
                                    <td data-label="Действия" class="text-end">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" 
                                               class="action-btn edit" 
                                               title="Редактировать">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.subscriptions.delete', $subscription->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-btn delete" 
                                                        title="Удалить"
                                                        onclick="return confirm('Вы уверены, что хотите удалить абонемент? Это действие нельзя отменить.')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Пагинация -->
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        <i class="fas fa-info-circle me-1"></i>
                        Показано с {{ $subscriptions->firstItem() }} по {{ $subscriptions->lastItem() }} из {{ $subscriptions->total() }} записей
                    </div>
                    <div class="pagination-links">
                        {{ $subscriptions->withQueryString()->onEachSide(1)->links() }}
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <h4>Абонементы не найдены</h4>
                    <p>
                        @if(request('search'))
                            По вашему запросу "<strong>{{ request('search') }}</strong>" ничего не найдено
                        @else
                            В системе пока нет абонементов. Создайте первый абонемент!
                        @endif
                    </p>
                    @if(request('search'))
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn-reset">
                            <i class="fas fa-times me-2"></i>Сбросить поиск
                        </a>
                    @else
                        <a href="{{ route('admin.subscriptions.create') }}" class="btn-create">
                            <i class="fas fa-plus me-2"></i>Создать первый абонемент
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
@endsection