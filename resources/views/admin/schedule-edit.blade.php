@extends('layouts.app')

@section('title', 'Редактирование занятия')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Редактирование занятия</h1>
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
            <h5 class="mb-0">Редактирование занятия</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.schedule.update', $schedule->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="workout_id" class="form-label">Тип тренировки *</label>
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
                        <label for="trainer_id" class="form-label">Тренер *</label>
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
                        <label for="date" class="form-label">Дата *</label>
                        <input type="date" class="form-control @error('date') is-invalid @enderror" 
                               id="date" name="date" value="{{ old('date', $schedule->date) }}" required>
                        @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="start_time" class="form-label">Время начала *</label>
                        <input type="time" class="form-control @error('start_time') is-invalid @enderror" 
                               id="start_time" name="start_time" value="{{ old('start_time', $schedule->start_time) }}" required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="end_time" class="form-label">Время окончания *</label>
                        <input type="time" class="form-control @error('end_time') is-invalid @enderror" 
                               id="end_time" name="end_time" value="{{ old('end_time', $schedule->end_time) }}" required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="room" class="form-label">Зал</label>
                        <input type="text" class="form-control @error('room') is-invalid @enderror" 
                               id="room" name="room" value="{{ old('room', $schedule->room) }}">
                        @error('room')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="status" class="form-label">Статус *</label>
                        <select class="form-select @error('status') is-invalid @enderror" 
                                id="status" name="status" required>
                            <option value="scheduled" {{ (old('status', $schedule->status) == 'scheduled') ? 'selected' : '' }}>Запланировано</option>
                            <option value="cancelled" {{ (old('status', $schedule->status) == 'cancelled') ? 'selected' : '' }}>Отменено</option>
                            <option value="completed" {{ (old('status', $schedule->status) == 'completed') ? 'selected' : '' }}>Завершено</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label">Примечания</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" 
                              id="notes" name="notes" rows="3">{{ old('notes', $schedule->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Сохранить изменения
                    </button>
                    <a href="{{ route('admin.schedule.index') }}" class="btn btn-outline-secondary ms-2">
                        Отмена
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection