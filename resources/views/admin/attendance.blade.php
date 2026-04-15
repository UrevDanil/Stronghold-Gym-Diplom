<!-- Отметка посещаемости клиентов (админ) -->
@extends('layouts.app')

@section('title', 'Отметка посещаемости')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/admin/attendance.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 admin-attendance-page">
    <!-- Заголовок -->
    <div class="attendance-header">
        <h1 class="mb-0">
            <i class="fas fa-clipboard-list me-3"></i>Отметка посещаемости
        </h1>
        <a href="{{ route('admin.dashboard') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
    </div>

    @if(session('success'))
        <div class="alert attendance-alert success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert attendance-alert error alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Поиск -->
    <div class="search-card">
        <div class="card-body">
            <div class="search-wrapper">
                <div class="search-input-group">
                    <span class="search-icon">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" id="searchInput" class="search-input" 
                           placeholder="Поиск по имени, email или телефону...">
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
            <div class="col-md-6 col-lg-4 client-card" 
                 data-name="{{ strtolower($client->name) }}" 
                 data-email="{{ strtolower($client->email) }}" 
                 data-phone="{{ $client->phone ?? '' }}">
                <div class="attendance-card">
                    <div class="card-body">
                        <div class="d-flex align-items-start gap-3">
                            <div class="client-avatar">
                                {{ strtoupper(substr($client->name, 0, 1)) }}
                            </div>
                            <div class="client-info">
                                <div class="client-name">{{ $client->name }}</div>
                                <div class="client-email">
                                    <i class="fas fa-envelope"></i>
                                    <span>{{ $client->email }}</span>
                                </div>
                                @if($client->phone)
                                    <div class="client-phone">
                                        <i class="fas fa-phone-alt"></i>
                                        <span>{{ $client->phone }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mt-3">
                            @if($hasSubscription)
                                <div class="d-flex flex-wrap gap-2 mb-2">
                                    <span class="badge-subscription">
                                        <i class="fas fa-id-card"></i> {{ $activeSub->subscription->name ?? 'Абонемент' }}
                                    </span>
                                    @if(!$isUnlimited)
                                        <span class="badge-remaining">
                                            <i class="fas fa-dumbbell"></i> {{ $remaining }} тренировок
                                        </span>
                                    @else
                                        <span class="badge-unlimited">
                                            <i class="fas fa-infinity"></i> Безлимит
                                        </span>
                                    @endif
                                </div>
                                <div class="subscription-date">
                                    <i class="fas fa-calendar-alt"></i> До {{ \Carbon\Carbon::parse($activeSub->end_date)->format('d.m.Y') }}
                                </div>
                            @else
                                <span class="badge-no-subscription">
                                    <i class="fas fa-times-circle"></i> Нет активного абонемента
                                </span>
                            @endif
                        </div>

                        <div class="button-group mt-3">
                            <button type="button" class="btn-mark {{ !$canMark ? 'btn-mark-disabled' : '' }}"
                                    data-bs-toggle="modal" data-bs-target="#markAttendanceModal"
                                    data-client-id="{{ $client->id }}"
                                    data-client-name="{{ $client->name }}"
                                    data-client-phone="{{ $client->phone }}"
                                    data-has-subscription="{{ $hasSubscription ? 'true' : 'false' }}"
                                    data-remaining="{{ $remaining }}"
                                    data-is-unlimited="{{ $isUnlimited ? 'true' : 'false' }}"
                                    data-subscription-name="{{ $hasSubscription ? ($activeSub->subscription->name ?? 'Абонемент') : '' }}"
                                    {{ !$canMark ? 'disabled' : '' }}>
                                <i class="fas fa-check-circle"></i> Отметить посещение
                            </button>
                            
                            <button type="button" class="btn-refund mt-2" 
                                    onclick="showRefundModal({{ $client->id }}, '{{ addslashes($client->name) }}')">
                                <i class="fas fa-undo-alt me-1"></i>Вернуть тренировку
                            </button>
                        </div>
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
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-check-circle"></i> Отметить посещение
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
                    
                    <div class="alert" id="remainingInfo">
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

<!-- Модальное окно для возврата тренировки (упрощенное) -->
<div class="modal fade" id="refundModal" tabindex="-1" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #198cf7, #17dd8b); color: white;">
                <h5 class="modal-title" id="refundModalLabel">
                    <i class="fas fa-undo-alt me-2"></i>Возврат тренировки
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="refundForm" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="client_id" id="refundClientId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Клиент</label>
                        <p class="form-control-plaintext" id="refundClientName"></p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="refundReason" class="form-label">Причина возврата (необязательно)</label>
                        <textarea class="form-control" id="refundReason" name="reason" rows="2" 
                                  placeholder="Например: клиент не пришел, ошибка учета, техническая проблема и т.д."></textarea>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Возврат добавит <strong>+1 тренировку</strong> в абонемент клиента.
                    </div>
                    
                    <div class="alert alert-warning" id="currentBalanceInfo">
                        <i class="fas fa-chart-line me-2"></i>
                        <span id="currentBalanceText">Загрузка информации об абонементе...</span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Отмена</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-undo-alt me-2"></i>Вернуть тренировку
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
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            
            clientCards.forEach(card => {
                const name = (card.dataset.name || '').toLowerCase();
                const email = (card.dataset.email || '').toLowerCase();
                const phone = (card.dataset.phone || '').toLowerCase();
                
                const matches = name.includes(searchTerm) || 
                               email.includes(searchTerm) || 
                               phone.includes(searchTerm);
                
                if (searchTerm === '') {
                    card.style.display = '';
                } else if (matches) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
    
    // Модальное окно отметки посещения
    const modal = document.getElementById('markAttendanceModal');
    const form = document.getElementById('markAttendanceForm');
    const modalClientName = document.getElementById('modalClientName');
    const modalClientPhone = document.getElementById('modalClientPhone');
    const modalSubscription = document.getElementById('modalSubscription');
    const remainingText = document.getElementById('remainingText');
    const remainingInfo = document.getElementById('remainingInfo');
    
    if (modal) {
        modal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const clientId = button.getAttribute('data-client-id');
            const clientName = button.getAttribute('data-client-name');
            const clientPhone = button.getAttribute('data-client-phone');
            const hasSubscription = button.getAttribute('data-has-subscription') === 'true';
            const remaining = parseInt(button.getAttribute('data-remaining'));
            const isUnlimited = button.getAttribute('data-is-unlimited') === 'true';
            const subscriptionName = button.getAttribute('data-subscription-name');
            
            form.action = `/admin/attendance/mark/${clientId}`;
            
            modalClientName.textContent = clientName;
            modalClientPhone.textContent = clientPhone ? `📞 ${clientPhone}` : '';
            
            if (hasSubscription) {
                modalSubscription.textContent = subscriptionName;
                if (isUnlimited) {
                    remainingText.innerHTML = 'У клиента безлимитный абонемент. Тренировка не будет списываться.';
                    remainingInfo.className = 'alert alert-success';
                } else {
                    const newRemaining = remaining - 1;
                    if (newRemaining >= 0) {
                        remainingText.innerHTML = `После отметки у клиента останется ${newRemaining} тренировок из абонемента.`;
                    } else {
                        remainingText.innerHTML = `У клиента закончились тренировки! Осталось 0 из ${remaining + newRemaining + 1}.`;
                    }
                    remainingInfo.className = 'alert alert-warning';
                }
                document.querySelector('#markAttendanceModal .btn-primary').disabled = false;
            } else {
                modalSubscription.textContent = 'Нет активного абонемента';
                remainingText.innerHTML = 'У клиента нет активного абонемента! Отметка невозможна.';
                remainingInfo.className = 'alert alert-danger';
                document.querySelector('#markAttendanceModal .btn-primary').disabled = true;
            }
        });
        
        modal.addEventListener('hidden.bs.modal', function() {
            form.action = '';
            document.querySelector('#markAttendanceModal .btn-primary').disabled = false;
        });
    }
});

// Функция открытия модального окна возврата (упрощенная)
function showRefundModal(clientId, clientName) {
    document.getElementById('refundClientId').value = clientId;
    document.getElementById('refundClientName').innerText = clientName;
    document.getElementById('refundForm').action = `/admin/attendance/refund/${clientId}`;
    document.getElementById('refundReason').value = '';
    
    // Получаем информацию об абонементе клиента
    fetch(`/admin/attendance/client/${clientId}`)
        .then(response => response.json())
        .then(data => {
            const balanceSpan = document.getElementById('currentBalanceText');
            if (data.has_subscription) {
                if (data.is_unlimited) {
                    balanceSpan.innerHTML = `<i class="fas fa-infinity"></i> У клиента <strong>${data.name}</strong> безлимитный абонемент. Возврат добавит +1 к счетчику (хотя это не требуется для безлимита).`;
                    document.querySelector('#refundModal .btn-danger').disabled = false;
                } else {
                    balanceSpan.innerHTML = `<i class="fas fa-dumbbell"></i> У клиента <strong>${data.name}</strong> осталось <strong>${data.remaining_workouts}</strong> тренировок. После возврата станет <strong>${data.remaining_workouts + 1}</strong>.`;
                    document.querySelector('#refundModal .btn-danger').disabled = false;
                }
            } else {
                balanceSpan.innerHTML = `<i class="fas fa-exclamation-triangle"></i> У клиента <strong>${data.name}</strong> НЕТ активного абонемента! Возврат невозможен.`;
                document.querySelector('#refundModal .btn-danger').disabled = true;
            }
        })
        .catch(error => {
            console.error('Ошибка:', error);
            document.getElementById('currentBalanceText').innerHTML = 'Не удалось загрузить информацию об абонементе';
        });
    
    new bootstrap.Modal(document.getElementById('refundModal')).show();
}
</script>
@endsection