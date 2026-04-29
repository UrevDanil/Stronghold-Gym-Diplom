<!-- Редактирование тренировки -->
@extends('layouts.app')

@section('title', 'Редактирование тренировки')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/trainer/trainer-schedule-edit.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container py-4 schedule-edit-page">
    <div class="edit-header d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            Редактирование тренировки
        </h1>
        <a href="{{ route('trainer.schedule') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>Назад
        </a>
    </div>

    @if($errors->any())
        <div class="alert-modern error">
            <div class="alert-icon">
                <i class="fas fa-exclamation-circle"></i>
            </div>
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
            <button type="button" class="alert-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <div class="form-card">
        <div class="card-header">
            <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($schedule->date)->format('d.m.Y') }} | 
            {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('trainer.schedule.update', $schedule->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="workout_id" class="form-label required-field">Тип тренировки</label>
                        <select class="form-select @error('workout_id') is-invalid @enderror" 
                                id="workout_id" name="workout_id" required>
                            <option value="">Выберите тренировку</option>
                            @foreach($workouts as $workout)
                                <option value="{{ $workout->id }}" {{ (old('workout_id', $schedule->workout_id) == $workout->id) ? 'selected' : '' }}>
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
                               id="date" name="date" value="{{ old('date', $schedule->date) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label required-field">Время начала</label>
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                               id="start_time" name="start_time" value="{{ old('start_time', $schedule->start_time) }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="end_time" class="form-label required-field">Время окончания</label>
                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                               id="end_time" name="end_time" value="{{ old('end_time', $schedule->end_time) }}" required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="capacity" class="form-label required-field">Вместимость (макс. участников)</label>
                        <input type="number" class="form-control @error('capacity') is-invalid @enderror" 
                               id="capacity" name="capacity" value="{{ old('capacity', $schedule->capacity) }}" 
                               min="1" max="100" required>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="room" class="form-label">Зал</label>
                        <input type="text" class="form-control @error('room') is-invalid @enderror" 
                               id="room" name="room" value="{{ old('room', $schedule->room) }}" 
                               placeholder="Основной зал">
                        @error('room')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="info-block">
                    <i class="fas fa-chart-line"></i>
                    <div class="info-content">
                        <div class="info-title">Статистика занятия</div>
                        <div class="info-stats">
                            <span class="info-stat">
                                <i class="fas fa-users"></i> Вместимость: <strong>{{ $schedule->capacity() }}</strong>
                            </span>
                            <span class="info-stat booked">
                                <i class="fas fa-check-circle"></i> Записано: <strong>{{ $schedule->bookings->where('status', 'booked')->count() }}</strong>
                            </span>
                            <span class="info-stat">
                                <i class="fas fa-user-check"></i> Посетило: <strong>{{ $schedule->bookings->where('status', 'attended')->count() }}</strong>
                            </span>
                            <span class="info-stat available">
                                <i class="fas fa-chair"></i> Свободно: <strong>{{ $schedule->availableSlots() }}</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Сохранить изменения
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