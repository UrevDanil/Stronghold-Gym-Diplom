<!-- Мои клиенты -->

@extends('layouts.app')

@section('title', 'Мои клиенты')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Мои клиенты</h1>
        <div>
            <a href="{{ route('trainer.dashboard') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
            <span class="text-muted">
                <i class="fas fa-user me-2"></i>{{ auth()->user()->name }}
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Поиск и фильтры -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('trainer.clients') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Поиск клиента</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Имя или телефон..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label for="workout_id" class="form-label">Тип тренировки</label>
                    <select name="workout_id" id="workout_id" class="form-select">
                        <option value="">Все тренировки</option>
                        @foreach($workouts ?? [] as $workout)
                            <option value="{{ $workout->id }}" {{ request('workout_id') == $workout->id ? 'selected' : '' }}>
                                {{ $workout->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort" class="form-label">Сортировка</label>
                    <select name="sort" id="sort" class="form-select">
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Имя (А-Я)</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Имя (Я-А)</option>
                        <option value="trainings_desc" {{ request('sort') == 'trainings_desc' ? 'selected' : '' }}>Больше тренировок</option>
                        <option value="trainings_asc" {{ request('sort') == 'trainings_asc' ? 'selected' : '' }}>Меньше тренировок</option>
                        <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Недавние</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Найти
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Список клиентов -->
    @if($clients->count() > 0)
        <div class="row">
            @foreach($clients as $client)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="avatar-circle bg-primary text-white">
                                        {{ strtoupper(substr($client->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="card-title mb-1">{{ $client->name }}</h5>
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-envelope me-1"></i>{{ $client->email }}
                                            </p>
                                            @if($client->phone)
                                                <p class="text-muted small mb-2">
                                                    <i class="fas fa-phone me-1"></i>{{ $client->phone }}
                                                </p>
                                            @endif
                                            @if($client->birth_date)
                                                <p class="text-muted small mb-2">
                                                    <i class="fas fa-birthday-cake me-1"></i>
                                                    {{ \Carbon\Carbon::parse($client->birth_date)->format('d.m.Y') }} 
                                                    ({{ \Carbon\Carbon::parse($client->birth_date)->age }} лет)
                                                </p>
                                            @endif
                                        </div>
                                        <span class="badge bg-primary">
                                            {{ $client->trainings_count ?? 0 }} тренировок
                                        </span>
                                    </div>

                                    <!-- Прогресс -->
                                    @if(isset($client->progress) && $client->progress > 0)
                                        <div class="mt-3">
                                            <div class="d-flex justify-content-between mb-1">
                                                <small>Посещаемость</small>
                                                <small>{{ $client->progress }}%</small>
                                            </div>
                                            <div class="progress" style="height: 5px;">
                                                <div class="progress-bar bg-success" style="width: {{ $client->progress }}%"></div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Последняя тренировка -->
                                    @if($client->last_booking)
                                        <div class="mt-3 small">
                                            <i class="fas fa-calendar-alt text-primary me-1"></i>
                                            Последняя тренировка: 
                                            {{ \Carbon\Carbon::parse($client->last_booking->schedule->date)->format('d.m.Y') }}
                                            ({{ $client->last_booking->schedule->workout->name ?? '—' }})
                                        </div>
                                    @endif

                                    <!-- Кнопки действий -->
                                    <div class="mt-3 d-flex gap-2">
                                        <a href="{{ route('trainer.client-details', $client->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-user me-1"></i>Профиль
                                        </a>
                                        <a href="{{ route('trainer.schedule') }}?client_id={{ $client->id }}" 
                                           class="btn btn-sm btn-outline-success">
                                            <i class="fas fa-calendar-alt me-1"></i>История
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                onclick="contactClient({{ $client->id }})">
                                            <i class="fas fa-phone me-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Пагинация -->
        <div class="d-flex justify-content-center mt-4">
            {{ $clients->withQueryString()->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h4>Клиенты не найдены</h4>
                <p class="text-muted mb-4">
                    @if(request('search'))
                        По вашему запросу "{{ request('search') }}" ничего не найдено
                    @else
                        У вас пока нет клиентов. Они появятся после проведения тренировок.
                    @endif
                </p>
                <a href="{{ route('trainer.schedule') }}" class="btn btn-primary">
                    <i class="fas fa-calendar-alt me-2"></i>Посмотреть расписание
                </a>
            </div>
        </div>
    @endif
</div>

<style>
    .avatar-circle {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: bold;
    }
    
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15) !important;
    }
    
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
    }
    
    .progress-bar {
        border-radius: 10px;
    }
</style>

@push('scripts')
<script>
    function contactClient(clientId) {
        // Здесь можно добавить модальное окно с контактами
        alert('Функция связи с клиентом будет доступна позже');
    }
</script>
@endpush
@endsection