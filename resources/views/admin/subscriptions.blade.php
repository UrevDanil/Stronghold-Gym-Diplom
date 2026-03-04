<!-- Управление абонементами -->
 @extends('layouts.app')

@section('title', 'Управление абонементами')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Управление абонементами</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Новый абонемент
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Фильтры и поиск -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.subscriptions.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Поиск</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Название или описание" value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Статус</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Все</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активные</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Неактивные</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Применить
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Таблица абонементов -->
    <div class="card">
        <div class="card-body">
            @if($subscriptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Название</th>
                                <th>Дней</th>
                                <th>Тренировок</th>
                                <th>Цена</th>
                                <th>Статус</th>
                                <th>Записей</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $subscription)
                                <tr>
                                    <td>#{{ $subscription->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="color-circle me-2" style="background-color: {{ $subscription->color ?? '#6c757d' }}"></div>
                                            <div>
                                                <strong>{{ $subscription->name }}</strong>
                                                @if($subscription->description)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($subscription->description, 50) }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $subscription->duration_days }}</td>
                                    <td>{{ $subscription->workouts_count }}</td>
                                    <td><strong>{{ number_format($subscription->price, 0, ',', ' ') }} ₽</strong></td>
                                    <td>
                                        @if($subscription->is_active)
                                            <span class="badge bg-success">Активен</span>
                                        @else
                                            <span class="badge bg-secondary">Неактивен</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $activeCount = \App\Models\UserSubscription::where('subscription_id', $subscription->id)
                                                ->where('status', 'active')
                                                ->count();
                                        @endphp
                                        <span class="badge bg-info">{{ $activeCount }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.subscriptions.edit', $subscription->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Редактировать">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.subscriptions.delete', $subscription->id) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        title="Удалить"
                                                        onclick="return confirm('Удалить абонемент?')">
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $subscriptions->withQueryString()->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-id-card fa-4x text-muted mb-3"></i>
                    <h4>Абонементы не найдены</h4>
                    <p class="text-muted mb-4">
                        @if(request('search'))
                            По вашему запросу ничего не найдено
                        @else
                            В системе пока нет абонементов
                        @endif
                    </p>
                    <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Создать первый абонемент
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .color-circle {
        width: 24px;
        height: 24px;
        border-radius: 4px;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
</style>
@endsection