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
    <section class="slider_section position-relative">
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
                  <li class="nav-item active">
                    <a class="nav-link" href="{{ route('home') }}">Главная <span class="sr-only">(current)</span></a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('about') }}">О нас </a>
                  </li>
                  <li class="nav-item active">
                    <a class="nav-link" href="{{ route('service') }}">Услуги </a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" href="{{ route('contact') }}">Связаться с нами</a>
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

  <!-- crossfit section -->

<section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Кроссфит
        </h2>
      </div>
      <div class="box">
        <div class="img-box">
         <img src="{{ asset('assets/images/Сrossfit.png') }}" alt="" />
        </div>
        <div class="detail-box">
          <h3>
           Кроссфит — это высокоинтенсивная система функционального фитнеса.
          </h3>
          <h4>
            Дэвид Гогинс — бывший военный (морской котик), ультрамарафонец и мотивационный спикер. Он не является "лицом" Кроссфита в привычном понимании, но его философия идеально накладывается на суть этого направления.
          </h4>
          <h4>
            Гогинс известен своей концепцией "40% правила". Он утверждает, что когда ваш мозг говорит вам "я больше не могу", вы исчерпали лишь 40% своих реальных возможностей. Ваше тело способно на гораздо большее, но ум сдается первым.
          </h4>
          <h4>
            Как это связано с Кроссфитом? Любая кроссфит-тренировка — это прямое применение правила 40%.
          </h4>
        </div>
      </div>
    </div>
  </section>

    <section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Подтягивание
        </h2>
      </div>
      <div class="box">
        <div class="video-container">
            <video controls>
                <source src="{{ asset('assets/images/vc-1.mp4') }}" alt="" />
                Ваш браузер не поддерживает видео.
            </video>
        <div class="detail-box">
          <h4>
            Базовое силовое упражнение
          </h4>
          <h5>
            где ты поднимаешь всё своё тело руками, подтягиваясь на перекладине.
          </h5>

          <h4>
            Какой смысл?
          </h4>
          <h5>
           Развитие мышц спины (особенно широчайших), рук (бицепсов, предплечий) и плечевого пояса.
          </h5>

          <h4>
            Два главных вида:
          </h4>
          <h5>
            Прямым хватом (ладони от себя) — больше нагружает спину. Обратным хватом (ладони к себе) — сильнее включаются бицепсы.
          </h5>

          <h4>
           В двух словах:
          </h4>
          <h5>
            Повис на турнике → подтянулся подбородком выше перекладины → опустился. Повторил.
          </h5>

           <h4>
           Подтягивание — это показатель твоей относительной силы (силы по отношению к весу собственного тела).
          </h4>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Отжимания на брусьях
        </h2>
      </div>
      <div class="box">
        <div class="video-container">
            <video controls>
                <source src="{{ asset('assets/images/vc-2.mp4') }}" alt="" />
                Ваш браузер не поддерживает видео.
            </video>
        <div class="detail-box">
          <h4>
            Как делать правильно?
          </h4>
          <h5>
            Исходное положение: Запрыгнул на брусья. Руки прямые. Взгляд перед собой. Чуть подай корпус вперёд (для акцента на грудь) или оставь его вертикальным (для акцента на трицепс). Ноги скрести или слегка согни.
          </h5>

          <h4>
            Зачем это делать?
          </h4>
          <h5>
           Прокачка груди. Если наклонить корпус вперёд и развести локти — жжёт грудные мышцы. Прокачка трицепса. Если держать корпус вертикально и прижимать локти к себе — бьёт точно в трицепс. Укрепление плеч. Серьёзно нагружает весь плечевой пояс.
          </h5>

          <h4>
           Важные моменты (чтобы не облажаться):
          </h4>
          <h5>
            Локти: Не разводи их сильно в стороны, если ты новичок — это опасно для плеч. Лучше прижимай ближе к корпусу. Плечи: Не опускайся слишком низко, если гибкость плеч не позволяет. Иначе будет больно и травмоопасно. Корпус: Не раскачивайся. Работай мышцами, а не инерцией. Дыхание: Опускаешься — вдох, поднимаешься — выдох.
          </h5>

           <h4>
           Брусья — это мощное и уважаемое упражнение. Делай его с умом, и будет тебе рельефная грудь и мощные трицепсы. 💪
          </h4>
        </div>
      </div>
    </div>
  </div>
</section>

    <section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Отжимания
        </h2>
      </div>
      <div class="box">
        <div class="video-container">
            <video controls>
                <source src="{{ asset('assets/images/vc-3.mp4') }}" alt="" />
                Ваш браузер не поддерживает видео.
            </video>
        <div class="detail-box">
          <h4>
           Коротко и ясно:
          </h4>
          <h5>
           Принял упор лёжа — тело прямое, как струна, руки на ширине плеч. Опустился — грудь почти касается пола, локти не разлетаются сильно в стороны. Выжал себя наверх — в исходное положение.
          </h5>

          <h4>
           Что качает:
          </h4>
          <h5>
          Грудь (основная нагрузка); Плечи (передние дельты); Трицепс (задняя часть руки); Пресс и кор (всё время напряжены, чтобы держать тело прямым).
          </h5>

          <h4>
           Два главных нюанса:
          </h4>
          <h5>
           Таз не провисает и не торчит кверху — тело прямое. Локти под углом ~45° к корпусу (так безопаснее для плеч).
          </h5>

          <h4>
           Всё просто: опустился-поднялся. Базовое упражнение, которое всегда в моде.
          </h4>
        </div>
      </div>
    </div>
  </div>
</section>

    <section class="about_section layout_padding">
    <div class="container">
      <div class="box">
        <div class="detail-box">
          <h3>Теория кончилась. Теперь только практика, пот и секундомер. Все следующие тренировки — в зале. 3, 2, 1… Go!</h3>
        </div>
      </div>
    </div>
  </section>

  <!-- end crossfit section -->

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
            <li class=" active">
              <a class="" href="{{ route('home') }}">Главная <span class="sr-only">(current)</span></a>
            </li>
            <li class="">
              <a class="" href="{{ route('about') }}">О нас </a>
            </li>
            <li class="">
              <a class="" href="{{ route('service') }}">Услуги </a>
            </li>
            <li class="">
              <a class="" href="{{ route('contact') }}">Связаться с нами</a>
            </li>
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
          <div class="info_social">
            <div>
              <a href="">
                <img src="{{ asset('assets/images/facebook-logo-button.png') }}" alt="" />
              </a>
            </div>
            <div>
              <a href="">
                <img src="{{ asset('assets/images/twitter-logo-button.png') }}" alt="" /> 
              </a>
            </div>
            <div>
              <a href="">
                <img src="{{ asset('assets/images/linkedin.png') }}" alt="" />
              </a>
            </div>
            <div>
              <a href="https://vk.com/id320753965">
                <img src="{{ asset('assets/images/instagram.png') }}" alt="" />
              </a>
            </div>
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
