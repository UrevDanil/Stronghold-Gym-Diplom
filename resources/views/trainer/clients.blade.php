<!-- Мои клиенты -->
@extends('layouts.app')

@section('title', 'Мои клиенты')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/trainer/trainer-clients.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4 clients-page">
    <!-- Заголовок -->
    <div class="clients-header d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-users me-3"></i>Мои клиенты
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('trainer.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left me-2"></i>Назад
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert clients-alert success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Поиск и фильтры -->
<div class="filters-card">
    <div class="filters-header">
        <i class="fas fa-filter me-2"></i> Поиск и фильтры
    </div>
    <div class="filters-body">
        <form method="GET" action="{{ route('trainer.clients') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Поиск клиента</label>
                <input type="text" name="search" id="search" class="form-control" 
                       placeholder="Имя, email или телефон..." value="{{ request('search') }}">
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
            <div class="col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn-filter flex-grow-1">
                    <i class="fas fa-search me-2"></i>Найти
                </button>
                <a href="{{ route('trainer.clients') }}" class="btn-reset" title="Сбросить фильтры">
                    <i class="fas fa-times"></i>
                </a>
            </div>
        </form>
    </div>
</div>

    <!-- Статистика клиентов -->
    <div class="stats-row">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="stat-card bg-primary">
                    <div class="card-body">
                        <div class="stat-left">
                            <div class="stat-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="stat-label">Всего клиентов</div>
                        </div>
                        <div class="stat-number">{{ $clients->total() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-success">
                    <div class="card-body">
                        <div class="stat-left">
                            <div class="stat-icon">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stat-label">Тренировок сегодня</div>
                        </div>
                        <div class="stat-number">{{ $todayTrainings ?? 0 }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card bg-warning">
                    <div class="card-body">
                        <div class="stat-left">
                            <div class="stat-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="stat-label">Ср. посещаемость</div>
                        </div>
                        <div class="stat-number">{{ $avgAttendance ?? 0 }}%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Список клиентов -->
    @if($clients->count() > 0)
        <div class="row g-4">
            @foreach($clients as $client)
                <div class="col-lg-4 col-md-6">
                    <div class="client-card">
                        <div class="client-card__header">
                            <div class="client-avatar {{ $client->isActive() ? 'active' : '' }}">
                                {{ strtoupper(substr($client->name, 0, 1)) }}
                                @if($client->isActive())
                                    <span class="online-badge" title="Недавно был(а)"></span>
                                @endif
                            </div>
                            <div class="client-badges">
                                <span class="trainings-badge">
                                    {{ $client->trainings_count ?? 0 }} тренировок
                                </span>
                                @if($client->hasActiveSubscription())
                                    <span class="subscription-badge" title="Активный абонемент">
                                        <i class="fas fa-crown"></i>
                                    </span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="client-card__body">
                            <h5 class="client-name">{{ $client->name }}</h5>
                            
                            <div class="client-contacts">
                                <a href="mailto:{{ $client->email }}" class="contact-item">
                                    <i class="fas fa-envelope"></i>
                                    <span>{{ $client->email }}</span>
                                </a>
                                @if($client->phone)
                                    <a href="tel:{{ $client->phone }}" class="contact-item">
                                        <i class="fas fa-phone"></i>
                                        <span>{{ $client->phone }}</span>
                                    </a>
                                @endif
                                @if($client->birth_date)
                                    <div class="contact-item">
                                        <i class="fas fa-birthday-cake"></i>
                                        <span>{{ \Carbon\Carbon::parse($client->birth_date)->format('d.m.Y') }} 
                                            ({{ \Carbon\Carbon::parse($client->birth_date)->age }} лет)</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Прогресс посещаемости -->
                            @if(isset($client->progress) && $client->progress > 0)
                                <div class="progress-section">
                                    <div class="progress-label">
                                        <span>Посещаемость</span>
                                        <span class="progress-value">{{ $client->progress }}%</span>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar bg-success" 
                                             style="width: {{ $client->progress }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <!-- Последняя тренировка -->
                            @if($client->last_booking)
                                <div class="last-training">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                    <span>Последняя тренировка:</span>
                                    <strong>{{ \Carbon\Carbon::parse($client->last_booking->schedule->date)->isoFormat('D MMM') }}</strong>
                                    <span class="workout-name">{{ $client->last_booking->schedule->workout->name ?? '—' }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="client-card__footer">
                            <a href="{{ route('trainer.client-details', $client->id) }}" 
                               class="btn-action btn-profile">
                                <i class="fas fa-user me-2"></i>Профиль
                            </a>
                            <a href="{{ route('trainer.schedule') }}?client_id={{ $client->id }}" 
                               class="btn-action btn-history">
                                <i class="fas fa-history me-2"></i>История
                            </a>
                            <button type="button" class="btn-action btn-contact" 
                                    onclick="contactClient({{ $client->id }})"
                                    title="Связаться">
                                <i class="fas fa-phone"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Пагинация -->
        <div class="pagination-wrapper">
            {{ $clients->withQueryString()->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="fas fa-users-slash"></i>
            </div>
            <h4>Клиенты не найдены</h4>
            <p class="text-muted">
                @if(request('search'))
                    По вашему запросу "{{ request('search') }}" ничего не найдено
                @else
                    У вас пока нет клиентов. Они появятся после проведения тренировок.
                @endif
            </p>
            <div class="action-buttons">
                <a href="{{ route('trainer.schedule') }}" class="btn-primary">
                    <i class="fas fa-calendar-alt me-2"></i>Посмотреть расписание
                </a>
                @if(request('search') || request('workout_id') || request('sort'))
                    <a href="{{ route('trainer.clients') }}" class="btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Сбросить фильтры
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    function contactClient(clientId) {
        // Здесь можно добавить модальное окно с контактами
        // Или открывать чат/звонок
        alert('Функция связи с клиентом будет доступна позже');
    }

    // Анимация при наведении
    document.querySelectorAll('.client-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
</script>
@endpush
@endsection