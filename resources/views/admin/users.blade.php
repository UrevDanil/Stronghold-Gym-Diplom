<!-- Управление пользователями -->
@extends('layouts.app')

@section('title', 'Управление пользователями')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/admin/users.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 admin-users-page">
    <!-- Заголовок -->
    <div class="admin-header">
        <h1 class="mb-0">
            <i class="fas fa-users me-3"></i>Управление пользователями
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn-primary">
                <i class="fas fa-plus me-2"></i>Добавить пользователя
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert-modern success">
            <div class="alert-icon"><i class="fas fa-check-circle"></i></div>
            <div class="alert-content">
                <div class="alert-title">Отлично!</div>
                <div class="alert-message">{{ session('success') }}</div>
            </div>
            <button type="button" class="alert-close"><i class="fas fa-times"></i></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-modern error">
            <div class="alert-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="alert-content">
                <div class="alert-title">Ошибка!</div>
                <div class="alert-message">{{ session('error') }}</div>
            </div>
            <button type="button" class="alert-close"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <!-- Фильтры и поиск -->
    <div class="filters-card">
        <div class="filters-header">
            <i class="fas fa-filter"></i> Фильтры
        </div>
        <div class="filters-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Поиск</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Имя, email или телефон" value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label for="role" class="form-label">Роль</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">Все роли</option>
                        @foreach($roles ?? [] as $role)
                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                {{ $role->name == 'client' ? 'Клиент' : ($role->name == 'trainer' ? 'Тренер' : ($role->name == 'admin' ? 'Админ' : 'Владелец')) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Статус</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Все</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Активные</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Неактивные</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Дата регистрации с</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn-search">
                        <i class="fas fa-search me-2"></i>Применить
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Таблица пользователей -->
    <div class="users-card">
        <div class="card-body p-0">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="users-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Имя</th>
                                <th>Email</th>
                                <th>Телефон</th>
                                <th>Роль</th>
                                <th>Абонемент</th>
                                <th>Дата рег.</th>
                                <th>Статус</th>
                                <th>Действия</th>
                            </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td data-label="ID">#{{ $user->id }}</td>
                                    <td data-label="Имя">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle bg-{{ $user->role->name == 'client' ? 'client' : ($user->role->name == 'trainer' ? 'trainer' : ($user->role->name == 'admin' ? 'admin' : 'owner')) }}">
                                                {{ strtoupper(substr($user->name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                @if($user->birth_date)
                                                    <br>
                                                    <small class="text-muted">{{ \Carbon\Carbon::parse($user->birth_date)->age }} лет</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td data-label="Email">{{ $user->email }}</td>
                                    <td data-label="Телефон">{{ $user->phone ?? '—' }}</td>
                                    <td data-label="Роль">
                                        <span class="badge-role {{ $user->role->name }}">
                                            {{ $user->role->name == 'client' ? 'Клиент' : ($user->role->name == 'trainer' ? 'Тренер' : ($user->role->name == 'admin' ? 'Админ' : 'Владелец')) }}
                                        </span>
                                    </td>
                                        <td data-label="Абонемент">
                                            @if($user->role->name == 'client')
                                                @php
                                                    $activeSub = $user->activeSubscription();
                                                    $frozenSub = $user->frozenSubscription();
                                                @endphp
                                                @if($activeSub)
                                                    <span class="badge-subscription">
                                                        <i class="fas fa-id-card me-1"></i>{{ $activeSub->subscription->name ?? 'Абонемент' }}
                                                        @if($activeSub->status === 'frozen')
                                                            <span class="badge-frozen ms-1">
                                                                <i class="fas fa-snowflake"></i> Заморожен
                                                            </span>
                                                        @endif
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">до {{ \Carbon\Carbon::parse($activeSub->end_date)->format('d.m.Y') }}</small>
                                                    @if($activeSub->remaining_workouts > 0)
                                                        <br>
                                                        <small>Осталось: {{ $activeSub->remaining_workouts }} тренировок</small>
                                                    @endif
                                                @elseif($frozenSub)
                                                    <span class="badge-subscription frozen">
                                                        <i class="fas fa-snowflake me-1"></i>{{ $frozenSub->subscription->name ?? 'Абонемент' }}
                                                        <span class="badge-frozen ms-1">
                                                            <i class="fas fa-clock"></i> Заморожен
                                                        </span>
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        Заморожен до: {{ \Carbon\Carbon::parse($frozenSub->paused_until)->format('d.m.Y') }}
                                                    </small>
                                                    <br>
                                                    <small>Активен до: {{ \Carbon\Carbon::parse($frozenSub->end_date)->format('d.m.Y') }}</small>
                                                @else
                                                    <span class="badge-subscription-expired">
                                                        <i class="fas fa-times me-1"></i>Нет
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    <td data-label="Дата рег.">{{ $user->created_at->format('d.m.Y') }}</td>
                                    <td data-label="Статус">
                                        @if($user->deleted_at)
                                            <span class="badge-status blocked">
                                                <i class="fas fa-ban me-1"></i>Удален
                                            </span>
                                        @elseif($user->is_active)
                                            <span class="badge-status active">
                                                <i class="fas fa-check-circle me-1"></i>Активен
                                            </span>
                                        @else
                                            <span class="badge-status inactive">
                                                <i class="fas fa-clock me-1"></i>Неактивен
                                            </span>
                                        @endif
                                    </td>
                                    <td data-label="Действия">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.users.show', $user->id) }}" 
                                               class="action-btn view" title="Просмотр">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" 
                                               class="action-btn edit" title="Редактировать">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                @if($user->deleted_at)
                                                    <form action="{{ route('admin.users.restore', $user->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="action-btn restore" 
                                                                title="Восстановить"
                                                                onclick="return confirm('Восстановить пользователя {{ $user->name }}?')">
                                                            <i class="fas fa-undo-alt"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="action-btn delete" 
                                                                title="Удалить"
                                                                onclick="return confirm('Удалить пользователя {{ $user->name }}?')">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
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
                        Показано с {{ $users->firstItem() }} по {{ $users->lastItem() }} из {{ $users->total() }} записей
                    </div>
                    <div class="pagination-links">
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-users-slash"></i>
                    </div>
                    <h4>Пользователи не найдены</h4>
                    <p>
                        @if(request('search') || request('role') || request('status'))
                            По вашему запросу ничего не найдено
                        @else
                            В системе пока нет пользователей
                        @endif
                    </p>
                    <div class="d-flex gap-3 justify-content-center">
                        @if(request('search') || request('role') || request('status'))
                            <a href="{{ route('admin.users.index') }}" class="btn-reset">
                                <i class="fas fa-times me-2"></i>Сбросить фильтры
                            </a>
                        @else
                            <a href="{{ route('admin.users.create') }}" class="btn-create">
                                <i class="fas fa-plus me-2"></i>Добавить пользователя
                            </a>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection