<!-- Добавление занятий -->
@extends('layouts.app')

@section('title', 'Добавление занятия')

@section('styles')
    <link href="{{ asset('assets/css/dashboard/admin/schedule-create.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid py-4 admin-schedule-create-page">
    <!-- Заголовок -->
    <div class="create-header">
        <h1 class="mb-0">
            <i class="fas fa-plus-circle me-3"></i>Добавление занятия в расписание
        </h1>
        <a href="{{ route('admin.schedule.index') }}" class="back-btn">
            <i class="fas fa-arrow-left me-2"></i>К расписанию
        </a>
    </div>

    @if($errors->any())
        <div class="alert create-alert danger alert-dismissible fade show">
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
            <i class="fas fa-calendar-plus"></i> Новое занятие
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.schedule.store') }}">
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
                        <label for="trainer_id" class="form-label required-field">Тренер</label>
                        <select class="form-select @error('trainer_id') is-invalid @enderror" 
                                id="trainer_id" name="trainer_id" required>
                            <option value="">Выберите тренера</option>
                            @foreach($trainers as $trainer)
                                <option value="{{ $trainer->id }}" {{ old('trainer_id') == $trainer->id ? 'selected' : '' }}>
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
                                   id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" 
                                   min="{{ now()->format('Y-m-d') }}" required>
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
                                   id="start_time" name="start_time" value="{{ old('start_time', '10:00') }}" required>
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
                                   id="end_time" name="end_time" value="{{ old('end_time', '11:00') }}" required>
                        </div>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="room" class="form-label">Зал</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-door-open text-info"></i>
                        </span>
                        <input type="text" class="form-control border-start-0 @error('room') is-invalid @enderror" 
                               id="room" name="room" value="{{ old('room') }}" 
                               placeholder="Основной зал">
                    </div>
                    @error('room')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="info-block">
                    <i class="fas fa-info-circle"></i>
                    <div class="info-content">
                        <div class="info-title">Информация</div>
                        <div class="info-text">
                            Вместимость занятия берется из типа тренировки 
                            ({{ $workouts->first()->capacity ?? '?' }} мест).
                            После создания занятия клиенты смогут записываться через расписание.
                        </div>
                    </div>
                </div>

                <div class="mt-4 d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn-create">
                        <i class="fas fa-save"></i> Добавить занятие
                    </button>
                    <a href="{{ route('admin.schedule.index') }}" class="btn-cancel">
                        <i class="fas fa-times"></i> Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Автоматический расчет времени окончания при выборе тренировки
        const workoutSelect = document.getElementById('workout_id');
        const startTimeInput = document.getElementById('start_time');
        const endTimeInput = document.getElementById('end_time');
        
        function calculateEndTime() {
            const workoutId = workoutSelect.value;
            if (!workoutId) return;
            
            const selectedOption = workoutSelect.options[workoutSelect.selectedIndex];
            const durationText = selectedOption.text;
            const durationMatch = durationText.match(/(\d+)\s*мин/);
            
            if (durationMatch && durationMatch[1]) {
                const duration = parseInt(durationMatch[1]);
                const startTime = startTimeInput.value;
                
                if (startTime) {
                    const [hours, minutes] = startTime.split(':').map(Number);
                    const startDate = new Date();
                    startDate.setHours(hours, minutes, 0);
                    
                    const endDate = new Date(startDate.getTime() + duration * 60000);
                    const endHours = endDate.getHours().toString().padStart(2, '0');
                    const endMinutes = endDate.getMinutes().toString().padStart(2, '0');
                    
                    endTimeInput.value = `${endHours}:${endMinutes}`;
                }
            }
        }
        
        if (workoutSelect) {
            workoutSelect.addEventListener('change', calculateEndTime);
        }
        
        if (startTimeInput) {
            startTimeInput.addEventListener('change', calculateEndTime);
        }
        
        // Если уже выбрана тренировка и время начала, рассчитываем время окончания
        if (workoutSelect.value && startTimeInput.value) {
            calculateEndTime();
        }
    });
</script>
@endsection