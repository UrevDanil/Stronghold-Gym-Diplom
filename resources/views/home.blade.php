<!DOCTYPE html>
<html>

<head>
  <!-- Basic -->
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!-- Mobile Metas -->
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <!-- Site Metas -->
  <meta name="keywords" content="Stronghold Gym, фитнес, тренировки, бодибилдинг, кроссфит" />
  <meta name="description" content="Stronghold Gym - профессиональный фитнес-центр с современным оборудованием и опытными тренерами" />
  <meta name="author" content="Stronghold Gym" />

  <title>Stronghold Gym - Ваш путь к силе и энергии</title>

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
            <span>Stronghold Gym</span>
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

    <!-- slider section -->
    <section class="slider_section">
      <!-- ВАЖНО: Фоновый блок для красоты на внутренних страницах -->
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
                <li class="nav-item active">
                  <a class="nav-link" href="{{ route('home') }}">Главная</a>
                </li>
                <li class="nav-item">
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

      <div class="slider_container">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
          </ol>
          <div class="carousel-inner">
            <!-- Слайд 1 -->
            <div class="carousel-item active">
              <div class="container">
                <div class="row">
                  <div class="col-lg-8 mx-auto">
                    <div class="detail-box text-center">
                      <h2>Работай над телом</h2>
                      <h1>Тренируйся с нами</h1>           
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Слайд 2 -->
            <div class="carousel-item">
              <div class="container">
                <div class="row">
                  <div class="col-lg-8 mx-auto">
                    <div class="detail-box text-center">
                      <h2>Измени свое питание</h2>
                      <h1>Доверься нам</h1>                  
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Слайд 3 -->
            <div class="carousel-item">
              <div class="container">
                <div class="row">
                  <div class="col-lg-8 mx-auto">
                    <div class="detail-box text-center">
                      <h2>ВКЛЮЧАЙСЯ!</h2>
                      <h1>Достигай цели</h1>                     
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- end slider section -->
  </div>

  <!-- about section -->
  <section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>О нас Stronghold Gym</h2>
      </div>
      <div class="box">
        <div class="detail-box">
          <p>Stronghold Gym — это не просто тренажёрный зал, а место, где сила становится образом жизни. Мы создаём пространство, в котором каждый может достичь своих фитнес-целей: будь то набор мышечной массы, похудение, повышение выносливости или просто улучшение самочувствия.</p>
          <a href="{{ route('about') }}">Подробнее</a>
        </div>
      </div>
    </div>
  </section>
  <!-- end about section -->

  <!-- service section -->
  <section class="service_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>Наши услуги</h2>
      </div>
      <div class="service_container">
        <div class="box">
          <img src="{{ asset('assets/images/s-1.png') }}" alt="Бодибилдинг" />
          <div class="visible_heading">
            <h6>Бодибилдинг</h6>
          </div>
          <div class="link_box">
            <a href="{{ route('bodybuilding') }}">
              <img src="{{ asset('assets/images/link.png') }}" alt="Подробнее" />
            </a>
            <h6>Бодибилдинг</h6>
          </div>
        </div>
        <div class="box">
          <img src="{{ asset('assets/images/s-2.png') }}" alt="Пауэрлифтинг" />
          <div class="visible_heading">
            <h6>Пауэрлифтинг</h6>
          </div>
          <div class="link_box">
            <a href="{{ route('powerlifting') }}">
              <img src="{{ asset('assets/images/link.png') }}" alt="Подробнее" />
            </a>
            <h6>Пауэрлифтинг</h6>
          </div>
        </div>
        <div class="box">
          <img src="{{ asset('assets/images/s-3.png') }}" alt="Кроссфит" />
          <div class="visible_heading">
            <h6>Кроссфит</h6>
          </div>
          <div class="link_box">
            <a href="{{ route('crossfit') }}">
              <img src="{{ asset('assets/images/link.png') }}" alt="Подробнее" />
            </a>
            <h6>Кроссфит</h6>
          </div>
        </div>
        <div class="box">
          <img src="{{ asset('assets/images/s-43.jpeg') }}" alt="Питание" />
          <div class="visible_heading">
            <h6>Питание</h6>
          </div>
          <div class="link_box">
            <a href="{{ route('nutrition') }}">
              <img src="{{ asset('assets/images/link.png') }}" alt="Подробнее" />
            </a>
            <h6>Питание</h6>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end service section -->

  <!-- Why choose us section -->
  <section class="us_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>Почему выбирают нас</h2>
      </div>
      <div class="us_container">
        <div class="box">
          <div class="img-box">
            <img src="{{ asset('assets/images/u-1.png') }}" alt="Качественное оборудование" />
          </div>
          <div class="detail-box">
            <h5>КАЧЕСТВЕННОЕ ОБОРУДОВАНИЕ</h5>
            <p>для безопасных и эффективных тренировок, ваш комфорт и результат превыше всего.</p>
          </div>
        </div>
        <div class="box">
          <div class="img-box">
            <img src="{{ asset('assets/images/u-2.png') }}" alt="План здорового питания" />
          </div>
          <div class="detail-box">
            <h5>ПЛАН ЗДОРОВОГО ПИТАНИЯ</h5>
            <p>составленный персонально под ваши цели и образ жизни.</p>
          </div>
        </div>
        <div class="box">
          <div class="img-box">
            <img src="{{ asset('assets/images/u-3.png') }}" alt="Комфорт после тренировки" />
          </div>
          <div class="detail-box">
            <h5>КОМФОРТ ПОСЛЕ ТРЕНИРОВКИ</h5>
            <p>это больше, чем просто душ. Ваше личное пространство для восстановления.</p>
          </div>
        </div>
        <div class="box">
          <div class="img-box">
            <img src="{{ asset('assets/images/u-4.png') }}" alt="Индивидуальный подход" />
          </div>
          <div class="detail-box">
            <h5>ТОЛЬКО ПОД ВАШИ ЦЕЛИ</h5>
            <p>похудеть, набрать массу, подготовиться к марафону.</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end why choose us section -->

  <!-- client reviews section -->
  <section class="client_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>Что говорят наши клиенты</h2>
      </div>
      <div id="carouselClient" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#carouselClient" data-slide-to="0" class="active"></li>
          <li data-target="#carouselClient" data-slide-to="1"></li>
          <li data-target="#carouselClient" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/client1.png') }}" alt="Клиент Арчоморис" />
              </div>
              <div class="detail-box">
                <h5>Арчоморис</h5>
                <p>И вот он, момент, к которому шёл 15 лет: долгожданная сотня! Эта заветная планка в 100 кг покорилась наконец-то, и в этом — огромная заслуга моего наставника.</p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/client2.png') }}" alt="Клиент Юра спорт" />
              </div>
              <div class="detail-box">
                <h5>Юра спорт</h5>
                <p>Лучший пинок к победе над собой — это тренер Данил. Всегда увидит, где можно выложиться сильнее, и подбодрит в нужный момент.</p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/client3.png') }}" alt="Клиент Марат андеграунд" />
              </div>
              <div class="detail-box">
                <h5>Марат андеграунд</h5>
                <p>Лучший совет по жиму, приседу и становой — всегда от Константина. С ним тренировки становятся осознанными, а результаты — предсказуемыми.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end client reviews section -->

  <!-- result section -->
  <section class="result_section">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6 px-0">
          <div class="img-box">
            <img src="{{ asset('assets/images/result-img.jpg') }}" alt="Результаты тренировок" />
          </div>
        </div>
        <div class="col-lg-4 col-md-5">
          <div class="detail-box">
            <h2>СОЗДАНЫ, ЧТОБЫ ДОСТИГАТЬ МАКСИМУМА</h2>
            <p>Здесь не добиваются средних результатов. Только осознанные тренировки, научный подход и безграничная поддержка для тех, кто хочет выйти за рамки "просто похудеть" или "подкачаться".</p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end result section -->

  <!-- coaches section -->
  <section class="client_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>Наши тренеры</h2>
      </div>
      <div id="carouselCoaches" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#carouselCoaches" data-slide-to="0" class="active"></li>
          <li data-target="#carouselCoaches" data-slide-to="1"></li>
          <li data-target="#carouselCoaches" data-slide-to="2"></li>
          <li data-target="#carouselCoaches" data-slide-to="3"></li>
        </ol>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/coach1.png') }}" alt="Тренер Евгений" />
              </div>
              <div class="detail-box">
                <h5>Тренер Евгений</h5>
                <p>Основа основ — техника. Для Евгения нет мелочей. Он будет раз за разом поправлять угол наклона спины в тяге, траекторию грифа в жиме и положение колена в приседе.</p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/coach2.png') }}" alt="Тренер Данил" />
              </div>
              <div class="detail-box">
                <h5>Тренер Данил</h5>
                <p>Психолог и мотиватор. Он умеет считать не только повторения, но и «считывать» состояние подопечных. Видит, когда человек психологически «сломался».</p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/coach3.png') }}" alt="Тренер Константин" />
              </div>
              <div class="detail-box">
                <h5>Тренер Константин</h5>
                <p>Константин не признаёт антагонизма между «качками» и «кроссфитерами». Он видит тело целостно. От бодибилдинга он берёт акцент на изоляцию и эстетику.</p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/coach4.png') }}" alt="Тренер Александр" />
              </div>
              <div class="detail-box">
                <h5>Тренер Александр</h5>
                <p>Тренер Александр в боксе и ММА выступает в роли мультиспециалиста и менеджера процесса подготовки.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end coaches section -->

  <!-- contact section -->
  <section class="contact_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>Связаться с нами</h2>
      </div>
      <div class="layout_padding2-top">
        <div class="row">
          <div class="col-md-6">
            <form action="" method="POST">
              @csrf
              <div class="contact_form-container">
                <div>
                  <input type="text" placeholder="Ваше имя" required />
                  <input type="email" placeholder="Электронная почта" required />
                  <input type="tel" placeholder="Мобильный телефон" />
                  <textarea rows="4" placeholder="Ваше сообщение" required></textarea>
                  <button type="submit">Отправить</button>
                </div>
              </div>
            </form>
          </div>
          <div class="col-md-6">
            <div class="map_container">
              <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2712.3456789!2d37.805!3d48.015!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x40e1e5c5c5c5c5c5%3A0x0!2z0JTQvtC90LXRhtC6LCDQn9C70L7RidCw0LTRjA!5e0!3m2!1sru!2sru!4v1234567890" 
                width="100%" 
                height="350" 
                style="border:0; border-radius: 12px;" 
                allowfullscreen="" 
                loading="lazy"
                title="Stronghold Gym на карте">
              </iframe>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- end contact section -->

  <!-- author section -->
  <section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>Автор проекта</h2>
      </div>
      <div class="box">
        <div class="detail-box">
          <h3>Юрьев Данил Антонович</h3>
          <p>Создатель Stronghold Gym — твой проводник в мир силы и здоровья!</p>
        </div>
      </div>
    </div>
  </section>
  <!-- end author section -->

  <!-- info section -->
  <section class="info_section layout_padding2-top">
    <div class="container">
      <div class="info_form">
        <h4>Будьте в курсе</h4>
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
          <h6>О нас Stronghold Gym</h6>
          <p>Stronghold Gym — ваш путь к силе и энергии! 🏋️‍♂️ Мы объединяем современное оборудование, опытных тренеров и дружелюбную атмосферу.</p>
        </div>
        <div class="col-md-2 offset-md-1">
          <h6>Меню</h6>
          <ul>
            <li><a href="{{ route('home') }}">Главная</a></li>
            <li><a href="{{ route('about') }}">О нас</a></li>
            <li><a href="{{ route('service') }}">Услуги</a></li>
            <li><a href="{{ route('contact') }}">Контакты</a></li>
          </ul>
        </div>
        <div class="col-md-3">
          <h6>Полезные ссылки</h6>
          <ul>
            <li><a href="https://primekraft.ru/" target="_blank">Primekraft</a></li>
            <li><a href="https://www.bombbar.ru/" target="_blank">Bombbar</a></li>
            <li><a href="https://sport-magic.ru/" target="_blank">Sportmagic</a></li>
            <li><a href="https://gls.store/" target="_blank">GLS Store</a></li>
          </ul>
        </div>
        <div class="col-md-3">
          <h6>Контакты</h6>
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
    <p>&copy; 2026 Stronghold Gym. Все права защищены. Design by <a href="https://html.design/" target="_blank">bodybuilders</a></p>
  </footer>
  <!-- end footer section -->

  <script src="{{ asset('assets/js/jquery-3.4.1.min.js') }}"></script>
  <script src="{{ asset('assets/js/bootstrap.js') }}"></script>
</body>

</html>