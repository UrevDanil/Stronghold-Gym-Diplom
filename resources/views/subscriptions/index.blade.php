@extends('layouts.app')

@section('title', 'Абонементы - Stronghold Gym')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/subscriptions.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-5 subscriptions-page">
    <!-- Заголовок -->
    <div class="subscriptions-header">
        <h1 class="mb-2">Выберите свой абонемент</h1>
        <p class="subtitle">Начните свой путь к здоровому телу уже сегодня</p>
    </div>

    @if(session('success'))
        <div class="alert subscription-alert success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert subscription-alert warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @foreach($subscriptions as $subscription)
            <div class="col-md-6 col-lg-4">
                <div class="subscription-card">
                    @if($subscription->workouts_count >= 12)
                        <div class="popular-badge">
                            <i class="fas fa-fire me-1"></i>Популярный
                        </div>
                    @endif
                    
                    <div class="card-header">
                        <h4 class="mb-0">{{ $subscription->name }}</h4>
                    </div>
                    
                    <div class="card-body">
                        <div class="subscription-price">
                            <span class="price-amount">{{ number_format($subscription->price, 0, ',', ' ') }} ₽</span>
                            <span class="price-period">/ {{ $subscription->duration_days }} дней</span>
                        </div>
                        
                        <ul class="features-list">
                            <li>
                                <i class="fas fa-dumbbell"></i>
                                <span>{{ $subscription->workouts_count }} тренировок</span>
                            </li>
                            <li>
                                <i class="fas fa-calendar-alt"></i>
                                <span>Срок действия: {{ $subscription->duration_days }} дней</span>
                            </li>
                            <li>
                                <i class="fas fa-clock"></i>
                                <span>Доступ ко всем тренажерам</span>
                            </li>
                            
                            @if(in_array($subscription->id, [6, 7, 8]) || str_contains($subscription->name, 'тренер'))
                                <li>
                                    <i class="fas fa-chalkboard-user"></i>
                                    <span>Персональные тренировки с тренером</span>
                                </li>
                                <li>
                                    <i class="fas fa-clipboard-list"></i>
                                    <span>Индивидуальный план тренировок</span>
                                </li>
                            @endif
                        </ul>
                        
                        @if($subscription->description)
                            <div class="subscription-description">
                                <i class="fas fa-info-circle me-1"></i>
                                {{ $subscription->description }}
                            </div>
                        @endif
                        
                        <div class="mt-auto">
                            @auth
                                @if(auth()->user()->isClient())
                                    @php
                                        $activeSub = auth()->user()->activeSubscription();
                                        $hasActiveSub = $activeSub !== null;
                                        $isTrainerSubscription = str_contains($subscription->name, 'тренер') || in_array($subscription->id, [6, 7, 8]);
                                        $hasTrainerSub = auth()->user()->hasActiveTrainerSubscription();
                                        
                                        $canPurchase = false;
                                        if (!$hasActiveSub) {
                                            $canPurchase = true;
                                        } elseif ($isTrainerSubscription && !$hasTrainerSub) {
                                            $canPurchase = true;
                                        }
                                    @endphp
                                    
                                    @if($canPurchase)
                                        <form action="{{ route('subscriptions.purchase', $subscription) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Вы уверены, что хотите приобрести абонемент «{{ $subscription->name }}»?');">
                                            @csrf
                                            <button type="submit" class="btn-purchase">
                                                <i class="fas fa-shopping-cart me-2"></i>Приобрести
                                            </button>
                                        </form>
                                    @else
                                        @if($hasActiveSub && !$isTrainerSubscription)
                                            <button class="btn-purchase disabled" disabled>
                                                <i class="fas fa-lock me-2"></i>У вас уже есть активный абонемент
                                            </button>
                                        @elseif($hasTrainerSub && $isTrainerSubscription)
                                            <button class="btn-purchase disabled" disabled>
                                                <i class="fas fa-lock me-2"></i>У вас уже есть абонемент с тренером
                                            </button>
                                        @else
                                            <button class="btn-purchase disabled" disabled>
                                                <i class="fas fa-clock me-2"></i>Дождитесь окончания текущего абонемента
                                            </button>
                                        @endif
                                    @endif
                                @elseif(auth()->user()->isAdmin())
                                    <a href="{{ route('admin.subscriptions.index') }}" class="btn-outline-purchase">
                                        <i class="fas fa-cog me-2"></i>Управление абонементами
                                    </a>
                                @else
                                    <button class="btn-purchase disabled" disabled>
                                        <i class="fas fa-user-lock me-2"></i>Доступно только клиентам
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('register') }}" class="btn-purchase">
                                    <i class="fas fa-user-plus me-2"></i>Зарегистрироваться
                                </a>
                                <p class="text-center text-muted small mt-2">
                                    Уже есть аккаунт? <a href="{{ route('login') }}">Войдите</a>
                                </p>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- FAQ секция -->
    <div class="faq-card">
        <div class="card-header">
            <i class="fas fa-question-circle me-2"></i>Часто задаваемые вопросы
        </div>
        <div class="card-body">
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#faq1">
                            <i class="fas fa-sync-alt me-2 text-primary"></i>
                            Как продлить абонемент?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Вы можете продлить абонемент в любое время после окончания текущего. 
                            Новый абонемент начнет действовать сразу после истечения старого.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#faq2">
                            <i class="fas fa-snowflake me-2 text-info"></i>
                            Можно ли заморозить абонемент?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Да, вы можете заморозить абонемент на срок до 14 дней по уважительной причине 
                            (болезнь, командировка, отпуск). Для этого обратитесь к администратору.
                        </div>
                    </div>
                </div>
                
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#faq3">
                            <i class="fas fa-question-circle me-2 text-warning"></i>
                            Что происходит с неиспользованными тренировками?
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Неиспользованные тренировки сгорают по истечении срока действия абонемента. 
                            Рекомендуем планировать свои тренировки заранее.
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" 
                                data-bs-target="#faq4">
                            <i class="fas fa-exchange-alt me-2 text-success"></i>
                            Можно ли купить два абонемента одновременно?
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            Вы можете приобрести обычный абонемент и абонемент с тренером одновременно. 
                            Они будут действовать параллельно.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection