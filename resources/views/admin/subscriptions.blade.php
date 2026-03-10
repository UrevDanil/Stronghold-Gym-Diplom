<!-- Управление абонементами -->
@extends('layouts.app')

@section('title', 'Управление абонементами')

@section('content')
<div class="container-fluid py-4">
    <!-- Заголовок с градиентом -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-gradient-primary rounded-3 shadow-sm">
        <div>
            <h1 class="mb-0 text-white">
                <i class="fas fa-id-card me-2"></i>Управление абонементами
            </h1>
            <p class="text-white-50 mb-0 mt-1">Создание и редактирование абонементов</p>
        </div>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-light me-2">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-warning">
                <i class="fas fa-plus me-2"></i>Новый абонемент
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-check-circle fa-2x"></i>
                </div>
                <div>
                    <strong class="d-block">Успешно!</strong>
                    {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="fas fa-exclamation-circle fa-2x"></i>
                </div>
                <div>
                    <strong class="d-block">Ошибка!</strong>
                    {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Статистика абонементов -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-primary bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-id-card fa-2x text-primary"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Всего абонементов</h6>
                            <h3 class="mb-0">{{ $subscriptions->total() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-success bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Активных</h6>
                            <h3 class="mb-0">{{ $subscriptions->where('is_active', true)->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-danger bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-ban fa-2x text-danger"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Неактивных</h6>
                            <h3 class="mb-0">{{ $subscriptions->where('is_active', false)->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0 bg-info bg-opacity-10 p-3 rounded-3">
                            <i class="fas fa-users fa-2x text-info"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="text-muted mb-1">Всего продаж</h6>
                            <h3 class="mb-0">{{ \App\Models\UserSubscription::count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Фильтры и поиск -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0">
                <i class="fas fa-filter me-2 text-primary"></i>Фильтры
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label for="search" class="form-label fw-semibold">Поиск</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 ps-0" id="search" name="search" 
                               placeholder="Название или описание..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label fw-semibold">Статус</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Все абонементы</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Только активные</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Только неактивные</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search me-2"></i>Применить
                    </button>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Сбросить
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Таблица абонементов -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>Список абонементов
            </h5>
            <span class="badge bg-primary">{{ $subscriptions->total() }} записей</span>
        </div>
        <div class="card-body p-0">
            @if($subscriptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">ID</th>
                                <th>Название</th>
                                <th>Срок</th>
                                <th>Тренировки</th>
                                <th>Цена</th>
                                <th>Тип</th>
                                <th>Статус</th>
                                <th>Продажи</th>
                                <th class="text-end px-4">Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $subscription)
                                <tr>
                                    <td class="px-4 fw-semibold">#{{ $subscription->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="color-circle me-3 shadow-sm" style="background-color: {{ $subscription->color ?? 'linear-gradient(135deg, #667eea, #764ba2)' }}"></div>
                                            <div>
                                                <strong class="d-block">{{ $subscription->name }}</strong>
                                                @if($subscription->description)
                                                    <small class="text-muted">{{ Str::limit($subscription->description, 40) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-info bg-opacity-10 text-info px-3 py-2">
                                            <i class="fas fa-calendar-alt me-1"></i>{{ $subscription->duration_days }} дн.
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                            <i class="fas fa-dumbbell me-1"></i>{{ $subscription->workouts_count }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-primary fs-6">{{ number_format($subscription->price, 0, ',', ' ') }} ₽</strong>
                                    </td>
                                    <td>
                                        @if($subscription->type == 'time')
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-2">
                                                <i class="fas fa-clock me-1"></i>По времени
                                            </span>
                                        @else
                                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2">
                                                <i class="fas fa-hashtag me-1"></i>По кол-ву
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($subscription->is_active)
                                            <span class="badge bg-success px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i>Активен
                                            </span>
                                        @else
                                            <span class="badge bg-secondary px-3 py-2">
                                                <i class="fas fa-ban me-1"></i>Неактивен
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $activeCount = \App\Models\UserSubscription::where('subscription_id', $subscription->id)
                                                ->where('status', 'active')
                                                ->count();
                                            $totalCount = \App\Models\UserSubscription::where('subscription_id', $subscription->id)->count();
                                        @endphp
                                        <span class="badge bg-info px-3 py-2" title="Активных/Всего">
                                            <i class="fas fa-users me-1"></i>{{ $activeCount }}/{{ $totalCount }}
                                        </span>
                                    </td>
                                    <td class="text-end px-4">
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Редактировать"
                                               data-bs-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.subscriptions.delete', $subscription->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        title="Удалить"
                                                        data-bs-toggle="tooltip"
                                                        onclick="return confirm('Вы уверены, что хотите удалить абонемент? Это действие нельзя отменить.')">
                                                    <i class="fas fa-trash"></i>
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
                <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top">
                    <div class="text-muted small">
                        Показано с {{ $subscriptions->firstItem() }} по {{ $subscriptions->lastItem() }} из {{ $subscriptions->total() }} записей
                    </div>
                    <div>
                        {{ $subscriptions->withQueryString()->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-id-card fa-4x text-muted opacity-50"></i>
                    </div>
                    <h4 class="text-muted mb-3">Абонементы не найдены</h4>
                    <p class="text-muted mb-4">
                        @if(request('search'))
                            По вашему запросу "<strong>{{ request('search') }}</strong>" ничего не найдено
                        @else
                            В системе пока нет абонементов. Создайте первый абонемент!
                        @endif
                    </p>
                    @if(request('search'))
                        <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Сбросить поиск
                        </a>
                    @else
                        <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Создать первый абонемент
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Добавляем стили -->
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .text-white-50 {
        color: rgba(255, 255, 255, 0.7) !important;
    }
    
    .color-circle {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        transition: transform 0.2s;
    }
    
    .color-circle:hover {
        transform: scale(1.1);
    }
    
    .table td, .table th {
        vertical-align: middle;
        padding: 1rem 0.75rem;
    }
    
    .btn-group .btn {
        padding: 0.4rem 0.8rem;
        border-radius: 6px !important;
        margin: 0 2px;
        transition: all 0.2s;
    }
    
    .btn-group .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    
    .badge {
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    
    .input-group-text {
        border-radius: 8px 0 0 8px;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 0.6rem 1rem;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.1);
    }
    
    .alert {
        border-radius: 10px;
        border-left: 4px solid;
    }
    
    .alert-success {
        border-left-color: #28a745;
    }
    
    .alert-danger {
        border-left-color: #dc3545;
    }
    
    /* Анимация для карточек статистики */
    .card.h-100 {
        transition: all 0.3s;
    }
    
    .card.h-100:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>

<!-- Инициализация tooltips -->
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