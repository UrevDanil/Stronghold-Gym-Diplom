<!-- Управление пользователями -->
 @extends('layouts.app')

@section('title', 'Управление пользователями')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Управление пользователями</h1>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Добавить пользователя
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
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Применить
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Таблица пользователей -->
    <div class="card">
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
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
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>#{{ $user->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-{{ $user->role->name == 'client' ? 'primary' : ($user->role->name == 'trainer' ? 'success' : 'warning') }} text-white me-2">
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
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone ?? '—' }}</td>
                                    <td>
                                        @if($user->role->name == 'client')
                                            <span class="badge bg-primary">Клиент</span>
                                        @elseif($user->role->name == 'trainer')
                                            <span class="badge bg-success">Тренер</span>
                                        @elseif($user->role->name == 'admin')
                                            <span class="badge bg-warning">Админ</span>
                                        @elseif($user->role->name == 'owner')
                                            <span class="badge bg-danger">Владелец</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->role->name == 'client')
                                            @php
                                                $activeSub = $user->activeSubscription();
                                            @endphp
                                            @if($activeSub)
                                                <span class="badge bg-success">
                                                    {{ $activeSub->subscription->name ?? 'Абонемент' }}
                                                </span>
                                                <br>
                                                <small>до {{ \Carbon\Carbon::parse($activeSub->end_date)->format('d.m.Y') }}</small>
                                            @else
                                                <span class="badge bg-secondary">Нет</span>
                                            @endif
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $user->created_at->format('d.m.Y') }}</td>
                                    <td>
                                        @if($user->deleted_at)
                                            <span class="badge bg-danger">Заблокирован</span>
                                        @elseif($user->email_verified_at)
                                            <span class="badge bg-success">Активен</span>
                                        @else
                                            <span class="badge bg-warning">Не подтвержден</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.show', $user->id) }}" 
                                               class="btn btn-sm btn-outline-info" 
                                               title="Просмотр">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user->id) }}" 
                                               class="btn btn-sm btn-outline-primary"
                                               title="Редактировать">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                @if($user->deleted_at)
                                                    <form action="{{ route('admin.users.restore', $user->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                title="Восстановить"
                                                                onclick="return confirm('Восстановить пользователя?')">
                                                            <i class="fas fa-undo"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                title="Заблокировать"
                                                                onclick="return confirm('Заблокировать пользователя?')">
                                                            <i class="fas fa-ban"></i>
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
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <div>
                        Показано с {{ $users->firstItem() }} по {{ $users->lastItem() }} из {{ $users->total() }} записей
                    </div>
                    <div>
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h4>Пользователи не найдены</h4>
                    <p class="text-muted mb-4">
                        @if(request('search') || request('role') || request('status'))
                            По вашему запросу ничего не найдено
                        @else
                            В системе пока нет пользователей
                        @endif
                    </p>
                    @if(request('search') || request('role') || request('status'))
                        <a href="{{ route('admin.users.index') }}" class="btn btn-primary">
                            <i class="fas fa-times me-2"></i>Сбросить фильтры
                        </a>
                    @else
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Добавить пользователя
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
    }
    .table td, .table th {
        vertical-align: middle;
    }
    .btn-group .btn {
        padding: 0.25rem 0.5rem;
    }
</style>
@endsection