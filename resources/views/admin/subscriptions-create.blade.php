<!-- Создание абонемента -->
@extends('layouts.app')

@section('title', 'Создание абонемента')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/admin/subscription-create.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 admin-create-subscription-page">
    <!-- Заголовок -->
    <div class="create-header">
        <h1 class="mb-0">
            <i class="fas fa-plus-circle me-3"></i>Создание абонемента
        </h1>
        <a href="{{ route('admin.subscriptions.index') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>К списку абонементов
        </a>
    </div>

    @if($errors->any())
        <div class="alert-modern error">
            <div class="alert-icon"><i class="fas fa-exclamation-circle"></i></div>
            <div class="alert-content">
                <div class="alert-title">Ошибки валидации</div>
                <div class="alert-message">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="alert-close"><i class="fas fa-times"></i></button>
        </div>
    @endif

    <div class="form-card">
        <div class="card-header">
            <i class="fas fa-id-card"></i> Новый абонемент
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.subscriptions.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label required-field">Название</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" 
                           placeholder="Например: Месячный безлимит" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3" 
                              placeholder="Краткое описание абонемента...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="duration_days" class="form-label required-field">Срок действия (дней)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-calendar-alt text-primary"></i>
                            </span>
                            <input type="number" class="form-control border-start-0 @error('duration_days') is-invalid @enderror" 
                                   id="duration_days" name="duration_days" value="{{ old('duration_days', 30) }}" 
                                   min="1" required>
                        </div>
                        @error('duration_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="workouts_count" class="form-label required-field">Количество тренировок</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-dumbbell text-success"></i>
                            </span>
                            <input type="number" class="form-control border-start-0 @error('workouts_count') is-invalid @enderror" 
                                   id="workouts_count" name="workouts_count" value="{{ old('workouts_count', 8) }}" 
                                   min="1" required>
                        </div>
                        @error('workouts_count')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="price" class="form-label required-field">Цена (₽)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-ruble-sign text-warning"></i>
                            </span>
                            <input type="number" class="form-control border-start-0 @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price', 3000) }}" 
                                   min="0" step="100" required>
                        </div>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="type" class="form-label required-field">Тип абонемента</label>
                        <select class="form-select @error('type') is-invalid @enderror" 
                                id="type" name="type" required>
                            <option value="time" {{ old('type') == 'time' ? 'selected' : '' }}>По времени</option>
                            <option value="count" {{ old('type') == 'count' ? 'selected' : '' }}>По количеству тренировок</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>По времени</strong> - абонемент на время (безлимит), 
                            <strong>По количеству</strong> - на определенное количество тренировок
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <div class="form-check form-switch mt-4 pt-2">
                            <input class="form-check-input" type="checkbox" id="has_trainer" name="has_trainer" value="1" 
                                   {{ old('has_trainer') ? 'checked' : '' }} style="width: 50px; height: 25px; cursor: pointer;">
                            <label class="form-check-label ms-2 fw-bold" for="has_trainer" style="cursor: pointer;">
                                <i class="fas fa-chalkboard-user me-1"></i> Абонемент с тренером
                            </label>
                            <div class="form-text text-muted mt-1">
                                <small>При включении этой опции клиент сможет записываться на тренировки с тренером и видеть расписание.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="is_active" class="form-label">Статус</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" 
                                   name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Абонемент активен и доступен для покупки</label>
                        </div>
                    </div>
                </div>

                <div class="info-block">
                    <i class="fas fa-info-circle"></i>
                    <div class="info-content">
                        <div class="info-title">Информация</div>
                        <div class="info-text">
                            После создания абонемент появится в каталоге для клиентов. 
                            Вы всегда сможете отредактировать или отключить его позже.
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn-create">
                        <i class="fas fa-save"></i> Создать абонемент
                    </button>
                    <a href="{{ route('admin.subscriptions.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection