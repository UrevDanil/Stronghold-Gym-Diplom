@extends('layouts.app')

@section('title', 'Редактирование абонемента')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Редактирование абонемента</h1>
        <div>
            <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>К списку абонементов
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <h5 class="alert-heading">Ошибки валидации:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">{{ $subscription->name }}</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.subscriptions.update', $subscription->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label">Название *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name', $subscription->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description', $subscription->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="duration_days" class="form-label">Срок действия (дней) *</label>
                        <input type="number" class="form-control @error('duration_days') is-invalid @enderror" 
                               id="duration_days" name="duration_days" value="{{ old('duration_days', $subscription->duration_days) }}" 
                               min="1" required>
                        @error('duration_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="workouts_count" class="form-label">Количество тренировок *</label>
                        <input type="number" class="form-control @error('workouts_count') is-invalid @enderror" 
                               id="workouts_count" name="workouts_count" value="{{ old('workouts_count', $subscription->workouts_count) }}" 
                               min="1" required>
                        @error('workouts_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="price" class="form-label">Цена (₽) *</label>
                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                               id="price" name="price" value="{{ old('price', $subscription->price) }}" 
                               min="0" step="100" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label">Тип абонемента *</label>
                        <select class="form-select @error('type') is-invalid @enderror" 
                                id="type" name="type" required>
                            <option value="time" {{ (old('type', $subscription->type) == 'time') ? 'selected' : '' }}>По времени</option>
                            <option value="count" {{ (old('type', $subscription->type) == 'count') ? 'selected' : '' }}>По количеству тренировок</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">time - абонемент на время, count - на количество тренировок</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="is_active" class="form-label">Статус</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" id="is_active" 
                                   name="is_active" value="1" {{ old('is_active', $subscription->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Абонемент активен и доступен для покупки
                            </label>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Всего продано: {{ \App\Models\UserSubscription::where('subscription_id', $subscription->id)->count() }} шт.
                    Активных сейчас: {{ \App\Models\UserSubscription::where('subscription_id', $subscription->id)->where('status', 'active')->count() }}
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Сохранить изменения
                    </button>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn btn-outline-secondary ms-2">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.15);
    }
    .form-label {
        font-weight: 500;
    }
</style>
@endsection