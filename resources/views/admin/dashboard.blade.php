<!-- Панель администратора -->
 @extends('layouts.app')

@section('title', 'Админ панель')

@section('content')
<div class="container">
    <h1>Панель администратора</h1>
    <p>Добро пожаловать, {{ auth()->user()->name }}!</p>
    <p>Ваша роль: {{ auth()->user()->role->name }}</p>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Клиенты</h5>
                    <p class="card-text">Управление клиентами</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Тренеры</h5>
                    <p class="card-text">Управление тренерами</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Расписание</h5>
                    <p class="card-text">Управление расписанием</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection