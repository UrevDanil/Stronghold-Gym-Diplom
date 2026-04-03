<!-- Редактирование занятий -->
@extends('layouts.app')

@section('title', 'Редактирование занятия')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/admin/schedule-edit.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 admin-schedule-edit-page">
    <!-- Заголовок -->
    <div class="edit-header">
        <h1 class="mb-0">
            <i class="fas fa-edit me-3"></i>Редактирование занятия
        </h1>
        <a href="{{ route('admin.schedule.index') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>К расписанию
        </a>
    </div>

    @if($errors->any())
        <div class="alert edit-alert danger alert-dismissible fade show">
            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Ошибки валидации:</h5>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="form-card">
        <div class="card-header">
            <i class="fas fa-calendar-alt"></i> {{ \Carbon\Carbon::parse($schedule->date)->format('d.m.Y') }} | 
            {{ substr($schedule->start_time, 0, 5) }} - {{ substr($schedule->end_time, 0, 5) }}
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.schedule.update', $schedule->id) }}">
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
                        <label for="trainer_id" class="form-label required-field">Тренер</label>
                        <select class="form-select @error('trainer_id') is-invalid @enderror" 
                                id="trainer_id" name="trainer_id" required>
                            <option value="">Выберите тренера</option>
                            @foreach($trainers as $trainer)
                                <option value="{{ $trainer->id }}" {{ (old('trainer_id', $schedule->trainer_id) == $trainer->id) ? 'selected' : '' }}>
                                    {{ $trainer->name }} 
                                    @if($trainer->qualification)
                                        ({{ $trainer->qualification }})
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('trainer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="date" class="form-label required-field">Дата</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-calendar-alt text-primary"></i>
                            </span>
                            <input type="date" class="form-control border-start-0 @error('date') is-invalid @enderror" 
                                   id="date" name="date" value="{{ old('date', $schedule->date) }}" required>
                        </div>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="start_time" class="form-label required-field">Время начала</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-play text-success"></i>
                            </span>
                            <input type="time" class="form-control border-start-0 @error('start_time') is-invalid @enderror" 
                                   id="start_time" name="start_time" value="{{ old('start_time', $schedule->start_time) }}" required>
                        </div>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="end_time" class="form-label required-field">Время окончания</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-stop text-danger"></i>
                            </span>
                            <input type="time" class="form-control border-start-0 @error('end_time') is-invalid @enderror" 
                                   id="end_time" name="end_time" value="{{ old('end_time', $schedule->end_time) }}" required>
                        </div>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="capacity" class="form-label required-field">Вместимость (макс. участников)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-users text-primary"></i>
                            </span>
                            <input type="number" class="form-control border-start-0 @error('capacity') is-invalid @enderror" 
                                   id="capacity" name="capacity" value="{{ old('capacity', $schedule->capacity) }}" 
                                   min="1" max="100" required>
                        </div>
                        @error('capacity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="room" class="form-label">Зал</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fas fa-door-open text-info"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 @error('room') is-invalid @enderror" 
                                   id="room" name="room" value="{{ old('room', $schedule->room) }}" 
                                   placeholder="Основной зал">
                        </div>
                        @error('room')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label required-field">Статус</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="scheduled" {{ (old('status', $schedule->status) == 'scheduled') ? 'selected' : '' }}>
                                <i class="fas fa-check-circle text-success"></i> Запланировано
                            </option>
                            <option value="cancelled" {{ (old('status', $schedule->status) == 'cancelled') ? 'selected' : '' }}>
                                <i class="fas fa-ban text-danger"></i> Отменено
                            </option>
                            <option value="completed" {{ (old('status', $schedule->status) == 'completed') ? 'selected' : '' }}>
                                <i class="fas fa-check-double text-secondary"></i> Завершено
                            </option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Информация о занятии -->
                @php
                    $capacity = $schedule->capacity();
                    $booked = $schedule->bookings->where('status', 'booked')->count();
                    $attended = $schedule->bookings->where('status', 'attended')->count();
                    $available = $capacity - $booked;
                @endphp
                <div class="info-block">
                    <i class="fas fa-chart-line"></i>
                    <div class="info-content">
                        <div class="info-title">Статистика занятия</div>
                        <div class="info-stats">
                            <span class="info-stat">
                                <i class="fas fa-users"></i> Вместимость: <strong>{{ $capacity }}</strong>
                            </span>
                            <span class="info-stat booked">
                                <i class="fas fa-check-circle"></i> Записано: <strong>{{ $booked }}</strong>
                            </span>
                            <span class="info-stat">
                                <i class="fas fa-user-check"></i> Посетило: <strong>{{ $attended }}</strong>
                            </span>
                            <span class="info-stat available">
                                <i class="fas fa-chair"></i> Свободно: <strong>{{ $available }}</strong>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Сохранить изменения
                    </button>
                    <a href="{{ route('admin.schedule.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection