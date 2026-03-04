@extends('layouts.app')

@section('title', 'Добавление занятия')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Добавление занятия в расписание</h1>
        <div>
            <a href="{{ route('admin.schedule.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>К расписанию
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
            <h5 class="mb-0">Новое занятие</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.schedule.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="workout_id" class="form-label">Тип тренировки *</label>
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
                        <label for="trainer_id" class="form-label">Тренер *</label>
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
                        <label for="date" class="form-label">Дата *</label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" 
                               id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" 
                               min="{{ now()->format('Y-m-d') }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="start_time" class="form-label">Время начала *</label>
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                               id="start_time" name="start_time" value="{{ old('start_time', '10:00') }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="end_time" class="form-label">Время окончания *</label>
                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                               id="end_time" name="end_time" value="{{ old('end_time', '11:00') }}" required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="room" class="form-label">Зал</label>
                    <input type="text" class="form-control @error('room') is-invalid @enderror" 
                           id="room" name="room" value="{{ old('room') }}" 
                           placeholder="Например: Основной зал">
                    @error('room')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Вместимость занятия берется из типа тренировки ({{ $workouts->first()->capacity ?? '?' }} мест).
                    После создания занятия клиенты смогут записываться через расписание.
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Добавить занятие
                    </button>
                    <a href="{{ route('admin.schedule.index') }}" class="btn btn-outline-secondary ms-2">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Автоматический расчет времени окончания при выборе тренировки
    document.getElementById('workout_id').addEventListener('change', function() {
        const workoutId = this.value;
        if (!workoutId) return;
        
        // Получаем длительность тренировки из выбранного option
        const selectedOption = this.options[this.selectedIndex];
        const durationText = selectedOption.text;
        const durationMatch = durationText.match(/(\d+)\s*мин/);
        
        if (durationMatch && durationMatch[1]) {
            const duration = parseInt(durationMatch[1]);
            const startTime = document.getElementById('start_time').value;
            
            if (startTime) {
                const [hours, minutes] = startTime.split(':').map(Number);
                const startDate = new Date();
                startDate.setHours(hours, minutes, 0);
                
                const endDate = new Date(startDate.getTime() + duration * 60000);
                const endHours = endDate.getHours().toString().padStart(2, '0');
                const endMinutes = endDate.getMinutes().toString().padStart(2, '0');
                
                document.getElementById('end_time').value = `${endHours}:${endMinutes}`;
            }
        }
    });

    // При изменении времени начала пересчитываем время окончания
    document.getElementById('start_time').addEventListener('change', function() {
        const workoutId = document.getElementById('workout_id').value;
        if (!workoutId) return;
        
        const selectedOption = document.getElementById('workout_id').options[document.getElementById('workout_id').selectedIndex];
        const durationText = selectedOption.text;
        const durationMatch = durationText.match(/(\d+)\s*мин/);
        
        if (durationMatch && durationMatch[1]) {
            const duration = parseInt(durationMatch[1]);
            const startTime = this.value;
            
            if (startTime) {
                const [hours, minutes] = startTime.split(':').map(Number);
                const startDate = new Date();
                startDate.setHours(hours, minutes, 0);
                
                const endDate = new Date(startDate.getTime() + duration * 60000);
                const endHours = endDate.getHours().toString().padStart(2, '0');
                const endMinutes = endDate.getMinutes().toString().padStart(2, '0');
                
                document.getElementById('end_time').value = `${endHours}:${endMinutes}`;
            }
        }
    });
</script>

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