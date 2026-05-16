<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>Stronghold Gym</title>

  <!-- slider stylesheet -->
  <link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.1.3/assets/owl.carousel.min.css" />

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
    <!-- header section strats -->
    <header class="header_section">
      <div class="container">
        <nav class="navbar navbar-expand-lg custom_nav-container">
          <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="" />
            <span>
              Stronghold Gym
            </span>
          </a>
          <div class="contact_nav" id="">
            <ul class="navbar-nav ">
              <li class="nav-item">
                <a class="nav-link" href="{{ route('service') }}">
                  <img src="{{ asset('assets/images/location.png') }}" alt="" />
                  <span>Donetsk</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="{{ route('service') }}">
                  <img src="{{ asset('assets/images/call.png') }}" alt="" />
                  <span>Call : +7 (949) 413-56-16</span>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="{{ route('service') }}">
                  <img src="{{ asset('assets/images/envelope.png') }}" alt="" />
                  <span>Stronghold@gmail.com</span>
                </a>
              </li>
            </ul>
          </div>
        </nav>
      </div>

    </header>
    <!-- end header section -->
    <!-- slider section -->
    <section class=" slider_section position-relative">
      <div class="container">
        <div class="custom_nav2">
          <nav class="navbar navbar-expand-lg custom_nav-container ">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
              aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
              <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <div class="d-flex  flex-column flex-lg-row align-items-center">
                <ul class="navbar-nav  ">
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('home') }}">Главная <span class="sr-only">(current)</span></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">О нас </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('service') }}">Услуги </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('subscriptions.index') }}">Абонементы</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('login') }}">Войти</a>
                  </li>
                </ul>
                <form class="form-inline my-2 my-lg-0 ml-0 ml-lg-4 mb-3 mb-lg-0">
                  <button class="btn  my-2 my-sm-0 nav_search-btn" type="submit"></button>
                </form>
              </div>
            </div>
          </nav>
        </div>
      </div>
    </section>
    <!-- end slider section -->
  </div>

  <!-- contact section -->
  <section class="contact_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          <span>
            Связаться
          </span>
        </h2>
      </div>
      <div class="layout_padding2-top">
        <div class="row">
          <div class="col-md-6 ">
            <form action="">
              <div class="contact_form-container">
                <div>
                  <div>
                    <input type="text" placeholder="Логин" />
                  </div>
                  <div>
                    <input type="email" placeholder="Электронная почта" />
                  </div>
                  <div>
                    <input type="text" placeholder="Мобильный телефон" />
                  </div>
                  <div class="mt-5">
                    <input type="text" placeholder="Сообщение" />
                  </div>
                  <div class="mt-5">
                    <button type="submit">
                      Написать
                    </button>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="col-md-6">
            <div class="map_container">
              <div class="map-responsive">
                <img src="{{ asset('assets/images/map.png') }}" alt="" width="600" height="300" frameborder="0" style="border:0; width: 100%; height:100%"/> 
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end contact section -->


  <!-- info section -->

  <section class="info_section layout_padding2-top">
    <div class="container">
      <div class="info_form">
        <h4>
          Будьте в курсе
        </h4>
        <form action="">
          <input type="text" placeholder="Введите свой адрес электронной почты">
          <div class="d-flex justify-content-end">
            <button>
              подписка
            </button>
          </div>
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
            Stronghold Gym — ваш путь к силе и энергии! 🏋️‍♂️ Мы объединяем: Современное оборудование (силовые тренажёры, свободные веса, кардиозона); Опытных тренеров, готовых составить индивидуальную программу; Дружелюбную атмосферу без барьеров — для новичков и профи.
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
              <a href="https://primekraft.ru/?ysclid=mi80c7s1m3296347272">
                primekraft
              </a>
            </li>
            <li>
              <a href="https://www.bombbar.ru/?ysclid=mi80b6b5bd138431534">
                bombbar
              </a>
            </li>
            <li>
              <a href="https://sport-magic.ru/catalog/?ysclid=mi80ampnyv577049394">
                sportmagic
              </a>
            </li>
            <li>
              <a href="https://gls.store/catalog/">
                gls
              </a>
            </li>
          </ul>
        </div>
        <div class="col-md-3">
          <h6>
            Связаться с нами
          </h6>
          <div class="info_link-box">
            <a href="">
              <img src="{{ asset('assets/images/location-white.png') }}" alt="" />
              <span> Ленинский просп., 77А</span>
            </a>
            <a href="">
              <img src="{{ asset('assets/images/call-white.png') }}" alt="" />
              <span>+7 (949) 413-56-16</span>
            </a>
            <a href="">
              <img src="{{ asset('assets/images/mail-white.png') }}" alt="" />
              <span> Stronghold@gmail.com</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- end info section -->

  <!-- footer section -->
  <section class="container-fluid footer_section ">
    <p>
      &copy; 2026 All Rights Reserved. Design by
      <a href="https://html.design/">bodybuilders</a>
    </p>
  </section>
  <!-- footer section -->

  <script src="{{ asset('assets/js/jquery-3.4.1.min.js') }}"></script>
  <script src="{{ asset('assets/js/bootstrap.js') }}"></script>

  <script>
    function openNav() {
      document.getElementById("myNav").classList.toggle("menu_width");
      document
        .querySelector(".custom_menu-btn")
        .classList.toggle("menu_btn-style");
    }
  </script>
</body>

</html>
