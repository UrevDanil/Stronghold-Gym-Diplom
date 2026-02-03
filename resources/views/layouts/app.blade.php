!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Stronghold Gym')</title>
    
    <!-- Подключение CSS -->
    <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/responsive.css') }}" rel="stylesheet">
    
    <!-- Иконка сайта -->
    <img src="{{ asset('assets/images/logo.jpg') }}">
    
    @yield('styles')
</head>
<body>

    <!-- Навигационное меню (скопируйте из index.html) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" height="40">
                <span class="fw-bold">STRONGHOLD GYM</span>
            </a>
            <!-- Остальное меню... -->
        </div>
    </nav>

    <!-- Основной контент -->
    <main>
        @yield('content')
    </main>

    <!-- Футер (скопируйте из index.html) -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <!-- Контент футера... -->
            </div>
        </div>
    </footer>

    <!-- Подключение JavaScript -->
    <script src="{{ asset('assets/js/jquery-3.4.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.js') }}"></script>
    
    @yield('scripts')
</body>
</html>