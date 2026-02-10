@extends('layouts.app')

@section('title', 'Абонементы - Stronghold Gym')

@section('content')
<div class="container py-5">
    <h1 class="text-center mb-5">Абонементы на тренировки</h1>
    
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    <div class="row">
        @foreach($subscriptions as $subscription)
            <div class="col-md-4 mb-4">
                <div class="card h-100 subscription-card shadow">
                    <div class="card-header text-white text-center py-3" 
                         style="background: linear-gradient(135deg, #FF8C00, #FF4500);">
                        <h4 class="mb-0">{{ $subscription->name }}</h4>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <div class="text-center mb-4">
                            <h2 class="text-primary">{{ number_format($subscription->price, 0, ',', ' ') }} ₽</h2>
                            <p class="text-muted">за {{ $subscription->duration_days }} дней</p>
                        </div>
                        
                        <div class="mb-4">
                            <h5 class="mb-3">Что включено:</h5>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-dumbbell text-success me-2"></i>
                                    {{ $subscription->workouts_count }} тренировок <!-- ИЗМЕНИЛ: session_count → workouts_count -->
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-calendar-alt text-info me-2"></i>
                                    Срок действия: {{ $subscription->duration_days }} дней
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    Доступ ко всем тренажерам
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-user-friends text-primary me-2"></i>
                                    Консультация тренера
                                </li>
                            </ul>
                        </div>
                        
                        @if($subscription->description)
                            <div class="mb-4">
                                <p class="text-muted">{{ $subscription->description }}</p>
                            </div>
                        @endif
                        
                        <div class="mt-auto">
                            @auth
                                @if(auth()->user()->isClient())
                                    @if(auth()->user()->hasActiveSubscription())
                                        <button class="btn btn-outline-primary w-100" disabled>
                                            У вас уже есть активный абонемент
                                        </button>
                                    @else
                                        <form action="{{ route('subscriptions.purchase', $subscription) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Вы уверены, что хотите приобрести этот абонемент?');">
                                            @csrf
                                            <button type="submit" class="btn btn-primary w-100 py-2">
                                                <i class="fas fa-shopping-cart me-2"></i>Приобрести
                                            </button>
                                        </form>
                                    @endif
                                @elseif(auth()->user()->isAdmin())
                                    <a href="#" class="btn btn-outline-secondary w-100">
                                        <i class="fas fa-cog me-2"></i>Управление
                                    </a>
                                @else
                                    <button class="btn btn-outline-secondary w-100" disabled>
                                        Доступно только клиентам
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-user-plus me-2"></i>Зарегистрироваться
                                </a>
                                <p class="text-center text-muted small mt-2">
                                    Или <a href="{{ route('login') }}">войдите</a> если уже есть аккаунт
                                </p>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- FAQ секция -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Часто задаваемые вопросы</h4>
                </div>
                <div class="card-body">
                    <div class="accordion" id="faqAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#faq1">
                                    Как продлить абонемент?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Вы можете продлить абонемент в любое время. При продлении неиспользованные тренировки сгорают.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#faq2">
                                    Можно ли заморозить абонемент?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Да, абонемент можно заморозить на срок до 14 дней по уважительной причине (болезнь, командировка).
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                        data-bs-target="#faq3">
                                    Что происходит с неиспользованными тренировками?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Неиспользованные тренировки сгорают по истечении срока действия абонемента.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.subscription-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: none;
}

.subscription-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
}

.card-header {
    border-radius: 0.375rem 0.375rem 0 0 !important;
}

.btn-primary {
    background: linear-gradient(135deg, #FF8C00, #FF4500);
    border: none;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #FF4500, #FF8C00);
    transform: scale(1.02);
}
</style>
@endsection