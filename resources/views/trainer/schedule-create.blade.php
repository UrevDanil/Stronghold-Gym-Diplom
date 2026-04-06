<!-- Создание тренировки -->
@extends('layouts.app')

@section('title', 'Создание тренировки')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/trainer/trainer-schedule-create.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4 schedule-create-page">
    <div class="create-header d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fas fa-plus-circle me-3"></i>Создание тренировки
        </h1>
        <a href="{{ route('trainer.schedule') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
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

    <div class="form-card">
        <div class="card-header">
            <i class="fas fa-calendar-plus"></i> Новая тренировка
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('trainer.schedule.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="workout_id" class="form-label required-field">Тип тренировки</label>
                        <select class="form-select @error('workout_id') is-invalid @enderror" 
                                id="workout_id" name="workout_id" required>
                            <option value="">Выберите тренировку</option>
                            @foreach($workouts as $workout)
                                <option value="{{ $workout->id }}" {{ old('workout_id') == $workout->id ? 'selected' : '' }}>
                                    {{ $workout->name }} ({{ $workout->duration }} мин)
                                </option>
                            @endforeach
                        </select>
                        @error('workout_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="date" class="form-label required-field">Дата</label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" 
                               id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" 
                               min="{{ now()->format('Y-m-d') }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label required-field">Время начала</label>
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                               id="start_time" name="start_time" value="{{ old('start_time', '10:00') }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label required-field">Время окончания</label>
                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                               id="end_time" name="end_time" value="{{ old('end_time', '11:00') }}" required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="capacity" class="form-label required-field">Вместимость (макс. участников)</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                               id="capacity" name="capacity" value="{{ old('capacity', 10) }}" 
                               min="1" max="100" required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="room" class="form-label">Зал</label>
                        <input type="text" class="form-control @error('room') is-invalid @enderror" 
                               id="room" name="room" value="{{ old('room') }}" 
                               placeholder="Основной зал">
                        @error('room')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="info-block">
                    <i class="fas fa-info-circle"></i>
                    <div class="info-content">
                        <div class="info-title">Информация</div>
                        <div class="info-text">
                            После создания тренировки клиенты смогут записываться через расписание.
                            Вы сможете редактировать тренировку до её начала.
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn-create">
                        <i class="fas fa-save"></i> Создать тренировку
                    </button>
                    <a href="{{ route('trainer.schedule') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection