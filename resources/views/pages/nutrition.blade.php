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

  <!-- nutrition section -->
  <section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Питание
        </h2>
      </div>
      <div class="box">
        <div class="img-box">
          <img src="{{ asset('assets/images/bju.png') }}" alt="" />
        </div>
        <div class="detail-box">
          <h3>
           Основа основ — это БЖУ: Белки, Жиры, Углеводы. Давай посмотрим на каждый из них.
          </h3>
        </div>
      </div>
    </div>
  </section>

  <section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Белки (Протеины) — «Строители»
        </h2>
      </div>
      <div class="box">
        <div class="img-box">
          <img src="{{ asset('assets/images/protein.png') }}" alt="" />
        </div>
        <div class="detail-box">
          <h4>
            Зачем нужны:
          </h4>
          <h5>
            Восстановление и рост мышечных волокон, которые повреждаются во время тренировок. Синтез гормонов и ферментов. Укрепление иммунитета. 
          </h5>

          <h4>
            Нормы для спортсменов:
          </h4>
          <h5>
            Силовые виды (бодибилдинг, пауэрлифтинг): 1.6 - 2.2 г на кг веса тела. Выносливость (бег, плавание, велоспорт): 1.4 - 1.8 г на кг веса тела.
          </h5>

          <h4>
            Чем богаты (лучшие источники):
          </h4>
          <h5>
            Животные (полноценные, содержат все незаменимые аминокислоты): Куриная грудка, индейка: нежирное, классика. Говядина, телятина: много белка и креатина, железа. Рыба (тунец, лосось, треска, минтай): лосось еще богат омега-3. Яйца (особенно яичный белок): эталонный белок. Творог (особенно обезжиренный или малой жирности): медленный белок, идеален на ночь. Греческий йогурт: много белка, мало углеводов. 
          </h5>
          <h5>
            Растительные (часто неполноценные, нужно комбинировать): Чечевица, нут, фасоль, горох: много белка и клетчатки. Тофу, темпе, эдамаме: соевые продукты. Киноа, гречка: крупы с высоким содержанием белка. Орехи и семена (миндаль, арахис, тыквенные семечки): но помни, они очень жирные.
          </h5>
        </div>
      </div>
    </div>
  </section>

    <section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Углеводы (Карбогидраты) — «Топливо»
        </h2>
      </div>
      <div class="box">
        <div class="img-box">
          <img src="{{ asset('assets/images/carbohydrates.png') }}" alt="" />
        </div>
        <div class="detail-box">

          <h4>
            Зачем нужны:
          </h4>
          <h5>
            Основной источник энергии для мышц и мозга. Восполняют запасы гликогена в мышцах и печени, которые тратятся на тренировке. Без них ты будешь чувствовать усталость, вялость и не сможешь тренироваться интенсивно.
          </h5>

          <h4>
            Нормы для спортсменов:
          </h4>
          <h5>
            Зависят от нагрузки! Обычно 4 - 7 г на кг веса тела, а в дни очень интенсивных тренировок или соревнований может доходить и до 10 г.
          </h5>

          <h4>
            Чем богаты (делим на "медленные" и "быстрые"):
          </h4>
          <h5>
            Сложные (медленные) углеводы — основа рациона: Крупы: гречка, овсянка (не быстрого приготовления!), бурый рис, киноа, булгур, перловка. Цельнозерновые продукты: макароны из твердых сортов пшеницы, цельнозерновой хлеб. Бобовые: чечевица, фасоль, нут. Овощи: брокколи, сладкий перец, листовой салат, кабачки, помидоры. Крахмалистые овощи: картофель (лучше запеченный), батат, кукуруза. Простые (быстрые) углеводы — для конкретных целей: Фрукты: бананы, яблоки, апельсины, ягоды. Сухофрукты: изюм, финики, курага. Мед, сиропы. Спортивные гели/напитки. Белый хлеб, сладости (употреблять ограниченно, в основном для быстрого восполнения энергии после тяжелой тренировки).
          </h5>

          <h4>
            Когда что есть:
          </h4>
          <h5>
            Сложные — за 1.5-2 часа до тренировки (дают стабильную энергию) и в основные приемы пищи. Простые — сразу после тренировки (окно возможностей), чтобы быстро закрыть углеводное окно и запустить восстановление.
          </h5>
        </div>
      </div>
    </div>
  </section>

    <section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Жиры (Липиды) — «Стратегический запас и гормоны»
        </h2>
      </div>
      <div class="box">
        <div class="img-box">
          <img src="{{ asset('assets/images/fats.png') }}" alt="" />
        </div>
        <div class="detail-box">
          <h4>
            Зачем нужны:
          </h4>
          <h5>
            Источник энергии в состоянии покоя и при длительной низкоинтенсивной нагрузке. Необходимы для выработки гормонов (включая тестостерон). Усвоение жирорастворимых витаминов (A, D, E, K). Здоровье суставов, мозга и нервной системы.
          </h5>

          <h4>
            Нормы для спортсменов:
          </h4>
          <h5>
            Обычно 0.8 - 1.5 г на кг веса тела. Ни в коем случае нельзя сильно урезать жиры!
          </h5>

          <h4>
            Чем богаты (делим на "хорошие" и "плохие"):
          </h4>
          <h5>
            Ненасыщенные (полезные) жиры — основа: Растительные масла: оливковое, льняное, авокадо. Авокадо: уникальный фрукт, очень жирный и полезный. Орехи и семена: грецкие орехи, миндаль, семена чиа, льна. Жирная рыба: лосось, сельдь, скумбрия (источник Омега-3).
          </h5>
          <h5>
            Насыщенные жиры — ограничить, но не исключать: Красное мясо, сливочное масло, сыр, яичные желтки. Важны в меру, но их избыток вреден для сердца.
          </h5>
          <h5>
            Трансжиры (вредные) — избегать: Фастфуд, чипсы, магазинная выпечка, маргарин.
          </h5>
        </div>
      </div>
    </div>
  </section>

    <section class="about_section layout_padding">
    <div class="container">
      <div class="box">
        <div class="detail-box">
          <h3>Шаблоны оставим новичкам. Будем делать питание, которое работает именно на вас. Под ваш метаболизм и график.</h3>
        </div>
      </div>
    </div>
  </section>

  <!-- end nutrition section -->

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
