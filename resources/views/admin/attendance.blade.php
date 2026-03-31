<!-- Отметка посещаемости клиентов (админ) -->
@extends('layouts.app')

@section('title', 'Отметка посещаемости')

@section('styles')
<style>
    .attendance-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }
    .attendance-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.1);
    }
    .client-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        font-weight: bold;
        color: white;
    }
    .subscription-badge {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        font-size: 0.7rem;
    }
    .remaining-badge {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: white;
        padding: 0.2rem 0.6rem;
        border-radius: 50px;
        font-size: 0.7rem;
    }
    .btn-mark {
        background: linear-gradient(135deg, #0b34bb, #2aa8e2);
        border: none;
        border-radius: 50px;
        padding: 0.5rem 1.2rem;
        transition: all 0.3s ease;
    }
    .btn-mark:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(11, 52, 187, 0.3);
    }
    .btn-mark-disabled {
        background: #6c757d;
        cursor: not-allowed;
        opacity: 0.6;
    }
    .search-input {
        border-radius: 50px;
        padding: 0.7rem 1rem;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    .search-input:focus {
        border-color: #0b34bb;
        box-shadow: none;
        outline: none;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">
            <i class="fas fa-clipboard-list me-3"></i>Отметка посещаемости
        </h1>
        <div>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Назад
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

    <!-- Поиск -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-primary"></i>
                        </span>
                        <input type="text" id="searchInput" class="form-control search-input border-start-0" 
                               placeholder="Поиск по имени, email или телефону...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Список клиентов -->
    <div class="row" id="clientsContainer">
        @foreach($clients as $client)
            @php
                $activeSub = $client->activeSubscription;
                $hasSubscription = $activeSub !== null;
                $remaining = $hasSubscription ? $activeSub->remaining_workouts : 0;
                $isUnlimited = $hasSubscription && $activeSub->subscription && $activeSub->subscription->workouts_count == 0;
                $canMark = $hasSubscription && ($isUnlimited || $remaining > 0);
            @endphp
            <div class="col-md-6 col-lg-4 mb-4 client-card" data-name="{{ strtolower($client->name) }}" 
                 data-email="{{ strtolower($client->email) }}" data-phone="{{ $client->phone ?? '' }}">
                <div class="card attendance-card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="client-avatar bg-primary me-3">
                                {{ strtoupper(substr($client->name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-1">{{ $client->name }}</h5>
                                <small class="text-muted">
                                    <i class="fas fa-envelope me-1"></i>{{ $client->email }}
                                </small>
                                @if($client->phone)
                                    <br><small class="text-muted">
                                        <i class="fas fa-phone me-1"></i>{{ $client->phone }}
                                    </small>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            @if($hasSubscription)
                                <span class="subscription-badge me-2">
                                    <i class="fas fa-id-card me-1"></i>{{ $activeSub->subscription->name ?? 'Абонемент' }}
                                </span>
                                @if(!$isUnlimited)
                                    <span class="remaining-badge">
                                        <i class="fas fa-dumbbell me-1"></i>{{ $remaining }} тренировок
                                    </span>
                                @else
                                    <span class="remaining-badge" style="background: linear-gradient(135deg, #28a745, #20c997);">
                                        <i class="fas fa-infinity me-1"></i>Безлимит
                                    </span>
                                @endif
                                <div class="mt-2 small text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>До {{ \Carbon\Carbon::parse($activeSub->end_date)->format('d.m.Y') }}
                                </div>
                            @else
                                <span class="badge bg-secondary">Нет активного абонемента</span>
                            @endif
                        </div>

                        <button type="button" class="btn btn-mark w-100 {{ !$canMark ? 'btn-mark-disabled' : '' }}"
                                data-bs-toggle="modal" data-bs-target="#markAttendanceModal"
                                data-client-id="{{ $client->id }}"
                                data-client-name="{{ $client->name }}"
                                data-client-phone="{{ $client->phone }}"
                                data-has-subscription="{{ $hasSubscription ? 'true' : 'false' }}"
                                data-remaining="{{ $remaining }}"
                                data-is-unlimited="{{ $isUnlimited ? 'true' : 'false' }}"
                                data-subscription-name="{{ $hasSubscription ? ($activeSub->subscription->name ?? 'Абонемент') : '' }}"
                                {{ !$canMark ? 'disabled' : '' }}>
                            <i class="fas fa-check-circle me-2"></i>Отметить посещение
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Модальное окно для отметки посещения -->
<div class="modal fade" id="markAttendanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="" id="markAttendanceForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle me-2"></i>Отметить посещение
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Клиент</label>
                        <div class="form-control bg-light" id="modalClientName"></div>
                        <small class="text-muted" id="modalClientPhone"></small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Абонемент</label>
                        <div class="form-control bg-light" id="modalSubscription"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="attendance_date" class="form-label">Дата посещения *</label>
                        <input type="date" class="form-control" id="attendance_date" name="date" 
                               value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="workout_name" class="form-label">Название тренировки</label>
                        <input type="text" class="form-control" id="workout_name" name="workout_name" 
                               placeholder="Например: Силовая тренировка, Кардио...">
                    </div>
                    
                    <div class="mb-3">
                        <label for="comment" class="form-label">Комментарий</label>
                        <textarea class="form-control" id="comment" name="comment" rows="2" 
                                  placeholder="Дополнительная информация..."></textarea>
                    </div>
                    
                    <div class="alert alert-info" id="remainingInfo">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="remainingText"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Отметить посещение
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Поиск клиентов
    const searchInput = document.getElementById('searchInput');
    const clientCards = document.querySelectorAll('.client-card');
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        clientCards.forEach(card => {
            const name = card.dataset.name || '';
            const email = card.dataset.email || '';
            const phone = card.dataset.phone || '';
            
            if (name.includes(searchTerm) || email.includes(searchTerm) || phone.includes(searchTerm)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
    
    // Модальное окно
    const modal = document.getElementById('markAttendanceModal');
    const form = document.getElementById('markAttendanceForm');
    const modalClientName = document.getElementById('modalClientName');
    const modalClientPhone = document.getElementById('modalClientPhone');
    const modalSubscription = document.getElementById('modalSubscription');
    const remainingText = document.getElementById('remainingText');
    
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const clientId = button.getAttribute('data-client-id');
        const clientName = button.getAttribute('data-client-name');
        const clientPhone = button.getAttribute('data-client-phone');
        const hasSubscription = button.getAttribute('data-has-subscription') === 'true';
        const remaining = button.getAttribute('data-remaining');
        const isUnlimited = button.getAttribute('data-is-unlimited') === 'true';
        const subscriptionName = button.getAttribute('data-subscription-name');
        
        // Устанавливаем action формы
        form.action = `/admin/attendance/mark/${clientId}`;
        
        // Заполняем данные
        modalClientName.textContent = clientName;
        modalClientPhone.textContent = clientPhone ? `📞 ${clientPhone}` : '';
        
        if (hasSubscription) {
            modalSubscription.textContent = subscriptionName;
            if (isUnlimited) {
                remainingText.innerHTML = 'У клиента безлимитный абонемент. Тренировка не будет списываться.';
                remainingText.closest('.alert').className = 'alert alert-success';
            } else {
                remainingText.innerHTML = `После отметки у клиента останется ${remaining - 1} тренировок из абонемента.`;
                remainingText.closest('.alert').className = 'alert alert-warning';
            }
        } else {
            modalSubscription.textContent = 'Нет активного абонемента';
            remainingText.innerHTML = 'У клиента нет активного абонемента! Отметка невозможна.';
            remainingText.closest('.alert').className = 'alert alert-danger';
            document.querySelector('#markAttendanceModal .btn-primary').disabled = true;
            return;
        }
        
        document.querySelector('#markAttendanceModal .btn-primary').disabled = false;
    });
    
    modal.addEventListener('hidden.bs.modal', function() {
        form.action = '';
        document.querySelector('#markAttendanceModal .btn-primary').disabled = false;
    });
});
</script>
@endsection