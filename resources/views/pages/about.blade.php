<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="Stronghold Gym, о нас, фитнес-центр, тренажерный зал Донецк" />
  <meta name="description" content="Узнайте больше о Stronghold Gym — профессиональном фитнес-центре в Донецке" />
  <meta name="author" content="Stronghold Gym" />

  <title>О нас - Stronghold Gym</title>

  <!-- bootstrap core css -->
  <link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet">

  <!-- fonts style -->
  <link href="https://fonts.googleapis.com/css?family=Baloo+Chettan|Dosis:400,600,700|Poppins:400,600,700&display=swap"
    rel="stylesheet" />
  <!-- Custom styles for this template -->
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
  <!-- responsive style -->
  <link href="{{ asset('assets/css/responsive.css') }}" rel="stylesheet">
</head>

<body>
  <div class="hero_area">
    <!-- header section starts -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container">
          <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Stronghold Gym Logo" />
            <span>
              Stronghold Gym
            </span>
          </a>
          <div class="contact_nav">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" href="#">
                  <img src="{{ asset('assets/images/location.png') }}" alt="Location" />
                  <span>Донецк, Ленинский пр-т, 77А</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="tel:+79494135616">
                  <img src="{{ asset('assets/images/call.png') }}" alt="Phone" />
                  <span>+7 (949) 413-56-16</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="mailto:Stronghold@gmail.com">
                  <img src="{{ asset('assets/images/envelope.png') }}" alt="Email" />
                  <span>Stronghold@gmail.com</span>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </div>
    </header>
    <!-- end header section -->

    <!-- slider section (с фоном, но без текста) -->
    <section class="slider_section position-relative">
      <!-- Фоновый блок для красоты -->
      <div class="bg-box"></div>
      
      <div class="container">
        <div class="custom_nav2">
          <nav class="navbar navbar-expand-lg custom_nav-container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
              aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav">
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('home') }}">Главная</a>
                </li>
                <li class="nav-item active">
                  <a class="nav-link" href="{{ route('about') }}">О нас</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('service') }}">Услуги</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('subscriptions.index') }}">Абонементы</a>
                </li>
                <li class="nav-item">
                  <a class="nav-link" href="{{ route('login') }}">Войти</a>
                </li>
              </ul>
              <form class="form-inline">
                <button class="nav_search-btn" type="submit" aria-label="Search"></button>
              </form>
            </div>
          </nav>
        </div>
      </div>
      
      <!-- Пустой контейнер для сохранения высоты фона -->
      <div style="height: 200px;"></div>
    </section>
    <!-- end slider section -->
  </div>

  <!-- about section -->
  <section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          О нас Stronghold Gym
        </h2>
      </div>
      <div class="box">
        <div class="img-box">
          <img src="{{ asset('assets/images/logo1.jpg') }}" alt="Stronghold Gym" />
        </div>
        <div class="detail-box">
          <p>
            Stronghold Gym — это не просто тренажёрный зал, а место, где сила становится образом жизни. Мы создаём пространство, в котором каждый может достичь своих фитнес-целей: будь то набор мышечной массы, похудение, повышение выносливости или просто улучшение самочувствия.
          </p>
          <p>
            Наш зал оснащён современным оборудованием от ведущих мировых производителей. У нас вы найдёте всё необходимое для полноценных тренировок: силовые тренажёры, свободные веса, кардиозону и функциональную площадку.
          </p>
          <p>
            Команда Stronghold Gym — это профессионалы своего дела, которые горят желанием помочь вам достичь поставленных целей. Мы верим, что каждый способен на большее, и готовы поддержать вас на этом пути!
          </p>
        </div>
      </div>
    </div>
  </section>
  <!-- end about section -->

  <!-- info section -->
  <section class="info_section layout_padding2-top">
    <div class="container">
      <div class="info_form">
        <h4>
          Будьте в курсе
        </h4>
        <form action="" method="POST">
          @csrf
          <input type="email" placeholder="Введите свой адрес электронной почты" required>
          <button type="submit">Подписаться</button>
        </form>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-md-3">
          <h6>
            О нас Stronghold Gym
          </h6>
          <p>
            Stronghold Gym — ваш путь к силе и энергии! 🏋️‍♂️ Мы объединяем современное оборудование, опытных тренеров и дружелюбную атмосферу — для новичков и профи.
          </p>
        </div>
        <div class="col-md-2 offset-md-1">
          <h6>
            Меню
          </h6>
          <ul>
            <li><a href="{{ route('home') }}">Главная</a></li>
            <li><a href="{{ route('about') }}">О нас</a></li>
            <li><a href="{{ route('service') }}">Услуги</a></li>
            <li><a href="{{ route('subscriptions.index') }}">Абонементы</a></li>
            <li><a href="{{ route('login') }}">Войти</a></li>
          </ul>
        </div>
        <div class="col-md-3">
          <h6>
            Полезные ссылки
          </h6>
          <ul>
            <li>
              <a href="https://primekraft.ru/" target="_blank">Primekraft</a>
            </li>
            <li>
              <a href="https://www.bombbar.ru/" target="_blank">Bombbar</a>
            </li>
            <li>
              <a href="https://sport-magic.ru/" target="_blank">Sportmagic</a>
            </li>
            <li>
              <a href="https://gls.store/" target="_blank">GLS Store</a>
            </li>
          </ul>
        </div>
        <div class="col-md-3">
          <h6>
            Контакты
          </h6>
          <div class="info_link-box">
            <a href="https://maps.google.com/?q=Ленинский+просп.,+77А,+Донецк" target="_blank">
              <img src="{{ asset('assets/images/location-white.png') }}" alt="Адрес" />
              <span>Ленинский просп., 77А, Донецк</span>
            </a>
            <a href="tel:+79494135616">
              <img src="{{ asset('assets/images/call-white.png') }}" alt="Телефон" />
              <span>+7 (949) 413-56-16</span>
            </a>
            <a href="mailto:Stronghold@gmail.com">
              <img src="{{ asset('assets/images/mail-white.png') }}" alt="Email" />
              <span>Stronghold@gmail.com</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end info section -->

  <!-- footer section -->
  <footer class="footer_section">
    <div class="container">
      <p>
        &copy; 2026 Stronghold Gym. Все права защищены. Design by 
        <a href="https://html.design/" target="_blank">bodybuilders</a>
      </p>
    </div>
  </footer>
  <!-- end footer section -->

  <script src="{{ asset('assets/js/jquery-3.4.1.min.js') }}"></script>
  <script src="{{ asset('assets/js/bootstrap.js') }}"></script>
</body>

</html>