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
                    <a class="nav-link" href="{{ route('contact') }}">Связаться с нами</a>
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
      <div class="slider_container">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
          </ol>
          <div class="carousel-inner">
            <div class="carousel-item active">
              <div class="container">
                <div class="row">
                  <div class="col-lg-6 col-md-7 offset-md-6 offset-md-5">
                    <div class="detail-box">
                      <h2>
                        Работай над телом
                      </h2>
                      <h1>
                        Тренируйся с нами
                      </h1>
                      <p>
                        Выбери программу и стартуй!
                        Твоя трансформация начинается здесь, чтобы ты смог создать тело своей мечты.
                      </p>
                      <div class="btn-box">
                        <a href="{{ route('service') }}" class="btn-1">
                          Подробнее
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="carousel-item ">
              <div class="container">
                <div class="row">
                  <div class="col-lg-6 col-md-7 offset-md-6 offset-md-5">
                    <div class="detail-box">
                      <h2>
                        Измени свое питание
                      </h2>
                      <h1>
                        Доверься нам
                      </h1>
                      <p>
                        Выбери программу и стартуй! Твоя трансформация начинается с тарелки — создай тело своей мечты через правильные привычки.
                      </p>
                      <div class="btn-box">
                        <a href="{{ route('nutrition') }}" class="btn-1">
                          Подробнее
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="carousel-item ">
              <div class="container">
                <div class="row">
                  <div class="col-lg-6 col-md-7 offset-md-6 offset-md-5">
                    <div class="detail-box">
                      <h2>
                        ВКЛЮЧАЙСЯ!
                      </h2>
                      <h1>
                        Достигай цели
                      </h1>
                      <p>
                        Хотите больше? Тогда записывайтесь! Ваш путь к телу мечты начинается с первого шага — здесь и сейчас.
                      </p>
                      <div class="btn-box">
                        <a href="{{ route('contact') }}" class="btn-1">
                          Подробнее
                        </a>
                      </div>
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
        <h2>
          О нас Stronghold Gym
        </h2>
      </div>
      <div class="box">
        <div class="detail-box">
          <p>
           Stronghold Gym — это не просто тренажёрный зал, а место, где сила становится образом жизни. Мы создаём пространство, в котором каждый может достичь своих фитнес-целей: будь то набор мышечной массы, похудение, повышение выносливости или просто улучшение самочувствия.
          </p>
          <a href="{{ route('about') }}">
            Подробнее
          </a>
        </div>
      </div>
    </div>
  </section>
  <!-- end about section -->

  <!-- service section -->

  <section class="service_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Наши услуги
        </h2>
      </div>
      <div class="service_container">
       <div class="box">
         <img src="{{ asset('assets/images/s-1.png') }}" alt="" />
          <h6 class="visible_heading">
            Бодибилдинг
          </h6>
          <div class="link_box">
           <a href="{{ route('bodybuilding') }}">
             <img src="{{ asset('assets/images/link.png') }}" alt="" />
            </a>
           <h6>
             Бодибилдинг
           </h6>
         </div>
        </div>
        <div class="box">
          <img src="{{ asset('assets/images/s-2.png') }}" alt="" />
          <h6 class="visible_heading">
            Пауэрлифтинг
          </h6>
          <div class="link_box">
            <a href="{{ route('powerlifting') }}">
              <img src="{{ asset('assets/images/link.png') }}" alt="" />
            </a>
            <h6>
              Пауэрлифтинг
            </h6>
          </div>
        </div>

        <div class="box">
          <img src="{{ asset('assets/images/s-3.png') }}" alt="" />
          <h6 class="visible_heading">
            Кроссфит
          </h6>
          <div class="link_box">
            <a href="{{ route('crossfit') }}">
              <img src="{{ asset('assets/images/link.png') }}" alt="" />
            </a>
            <h6>
              Кроссфит
            </h6>
          </div>
        </div>

        <div class="box">
          <img src="{{ asset('assets/images/s-43.jpeg') }}" alt="" />
          <h6 class="visible_heading">
            Питание
          </h6>
          <div class="link_box">
            <a href="{{ route('nutrition') }}">
              <img src="{{ asset('assets/images/link.png') }}" alt="" />
            </a>
            <h6>
              Питание
            </h6>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- end service section -->
  
  <!-- Us section -->

  <section class="us_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Почему выбирают нас
        </h2>
      </div>
      <div class="us_container">
        <div class="box">
          <div class="img-box">
            <img src="{{ asset('assets/images/u-1.png') }}" alt="" />
          </div>
          <div class="detail-box">
            <h5>
              КАЧЕСТВЕННОЕ ОБОРУДОВАНИЕ
            </h5>
            <p>
              для безопасных и эффективных тренировок, ваш комфорт и результат превыше всего, на котором приятно и безопасно работать.
            </p>
          </div>
        </div>
        <div class="box">
          <div class="img-box">
            <img src="{{ asset('assets/images/u-2.png') }}" alt="" />
          </div>
          <div class="detail-box">
            <h5>
              ПЛАН ЗДОРОВОГО ПИТАНИЯ
            </h5>
            <p>
              составленный персонально под ваши цели и образ жизни, который работает на ваш результат: похудение, массу, тонус; с учетом ваших предпочтений и особенностей организма.
            </p>
          </div>
        </div>
        <div class="box">
          <div class="img-box">
            <img src="{{ asset('assets/images/u-3.png') }}" alt="" />
          </div>
          <div class="detail-box">
            <h5>
              КОМФОРТ ПОСЛЕ ТРЕНИРОВКИ
            </h5>
            <p>
              это больше, чем просто душ. Это ваше личное пространство для восстановления.
            </p>
          </div>
        </div>
        <div class="box">
          <div class="img-box">
            <img src="{{ asset('assets/images/u-4.png') }}" alt="" />
          </div>
          <div class="detail-box">
            <h5>
              ТОЛЬКО ПОД ВАШИ ЦЕЛИ
            </h5>
            <p>
              похудеть, набрать массу, подготовиться к марафону, избавиться от боли в спине.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- end us section -->

 <!-- price calculator section -->
<section class="calculator_section layout_padding" id="calculator">
    <div class="container">
        <div class="heading_container">
            <h2>
                Записаться на тренировку
            </h2>
        </div>
        <div class="calculator_container">
            <div class="row">
                <div class="col-md-6">
                    <form id="gymCalculator">
                        <div class="form-group">
                            <label for="membershipType">Абонемент</label> <!-- ID изменен -->
                            <select class="form-control" id="membershipType" required> <!-- ID изменен -->
                                <option value="" disabled selected>Выбрать абонемент</option>
                                <option value="1300">Разовое посещение - 1300 рублей</option>
                                <option value="3000">Месячная обычная - 3000 рублей</option>
                                <option value="5000">Месячная (премиум) - 5000 рублей</option>
                                <option value="20000">Годовая обычная - 20000 рублей</option>
                                <option value="45000">Годовая премиум - 45000 рублей</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="trainingType">Персональные тренировки:</label>
                            <select class="form-control" id="trainingType">
                                <option value="0">Без персональных тренировок</option>
                                <option value="6000">5 занятий - 6000 рублей/месяц</option>
                                <option value="15000">10 занятий - 15000 рублей</option>
                                <option value="40000">Безлимит - 40000 рублей</option>
                            </select>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="lockerRental" value="500">
                            <label class="form-check-label" for="lockerRental">
                                Аренда шкафчика - 500 рублей/месяц
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="towelService" value="300">
                            <label class="form-check-label" for="towelService">
                                Полотенца - 300 рублей/месяц
                            </label>
                        </div>

                        <div class="form-group mt-4">
                            <label for="months">Количество месяцев (для ежемесячных планов):</label>
                            <input type="number" class="form-control" id="months" min="1" max="12" value="1">
                        </div>

                        <button type="button" class="btn btn-primary mt-3" onclick="calculateTotal()">
                            Рассчитать сумму
                        </button>
                    </form>
                </div>

                <div class="col-md-6">
                    <div class="result_box">
                        <h4>Информация о заказе</h4>
                        <div class="result_display">
                            <p id="membershipDetail">Абонемент: не выбрано</p>
                            <p id="trainingDetail">Персональные тренировки: не выбрано</p>
                            <p id="extrasDetail">Дополнительные услуги: не выбрано</p>
                            <hr>
                            <h3 id="totalPrice">Итого: 0 рублей</h3>
                            <p id="priceNote" class="text-muted"></p>
                        </div>
                        <button type="button" class="btn btn-success mt-3" id="proceedBtn" style="display:none;" onclick="proceedToPayment()">
                            Перейти к оплате
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- end price calculator section -->

<!-- client section -->

  <section class="client_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Что говорят наши клиенты
        </h2>
      </div>
      <div id="carouselExample2Indicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#carouselExample2Indicators" data-slide-to="0" class="active"></li>
          <li data-target="#carouselExample2Indicators" data-slide-to="1"></li>
          <li data-target="#carouselExample2Indicators" data-slide-to="2"></li>
        </ol>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/client1.png') }}" alt="" />
              </div>
              <div class="detail-box">
                <h5>
                  Арчоморис
                </h5>
                <p>
                  И вот он, момент, к которому шёл 15 лет: долгожданная сотня! Эта заветная планка в 100 кг покорилась наконец-то, и в этом — огромная заслуга моего наставника. Спасибо тебе, Евгений, за веру, которые сильнее железа!
                </p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/client2.png') }}" alt="" />
              </div>
              <div class="detail-box">
                <h5>
                  Юра спорт
                </h5>
                <p>
                  Лучший пинок к победе над собой — это тренер Данил. Всегда увидит, где можно выложиться сильнее, и подбодрит в нужный момент.
                </p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/client3.png') }}" alt="" />
              </div>
              <div class="detail-box">
                <h5>
                  Марат андеграунд
                </h5>
                <p>
                  Лучший совет по жиму, приседу и становой — всегда от Константина. С ним тренировки становятся осознанными, а результаты — предсказуемыми. Профи!
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- end client section -->

  <!-- result section -->

  <section class="result_section">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-6 px-0">
          <div class="img-box">
            <img src="{{ asset('assets/images/result-img.jpg') }}" alt="" />
          </div>
        </div>
        <div class="col-lg-4 col-md-5">
          <div class="detail-box">
            <h2>
              СОЗДАНЫ, ЧТОБЫ ДОСТИГАТЬ МАКСИМУМА
            </h2>
            <p>
              Здесь не добиваются средних результатов. Только осознанные тренировки, научный подход и безграничная поддержка для тех, кто хочет выйти за рамки "просто похудеть" или "подкачаться". Здесь создают чемпионов — в спорте, в карьере, в жизни.
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- end result section -->

  <!-- coach section -->

  <section class="client_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Наши тренеры
        </h2>
      </div>
      <div id="carouselExample2Indicators" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
          <li data-target="#carouselExample2Indicators" data-slide-to="0" class="active"></li>
          <li data-target="#carouselExample2Indicators" data-slide-to="1"></li>
          <li data-target="#carouselExample2Indicators" data-slide-to="2"></li>
          <li data-target="#carouselExample2Indicators" data-slide-to="3"></li>
        </ol>
        <div class="carousel-inner">
          <div class="carousel-item active">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/coach1.png') }}" alt="" />
              </div>
              <div class="detail-box">
                <h5>
                  Тренер Евгений
                </h5>
                <p>
                  Основа основ — техника. Для Евгения нет мелочей. Он будет раз за разом поправлять угол наклона спины в тяге, траекторию грифа в жиме и положение колена в приседе. «Лучше меньше, но идеально» — его частый девиз. Он верит, что безупречная техника — это и профилактика травм, и гарантия роста целевых мышц.
                </p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/coach2.png') }}" alt="" />
              </div>
              <div class="detail-box">
                <h5>
                 Тренер Данил
                </h5>
                <p>
                  Психолог и мотиватор. Он умеет считать не только повторения, но и «считывать» состояние подопечных. Видит, когда человек психологически «сломался» в середине комплекса, и найдёт нужные слова («Соберись, ты же сильнее!», «Дыши, работай!»), чтобы вернуть его в строй.
                </p>
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/coach3.png') }}" alt="" />
              </div>
              <div class="detail-box">
                <h5>
                  Тренер Константин
                </h5>
                <p>
                  Константин не признаёт антагонизма между «качками» и «кроссфитерами». Он видит тело целостно. От бодибилдинга он берёт: Акцент на изоляцию и эстетику: Умение «добить» отстающую мышцу, создать баланс и красивую форму. Контроль и ментальную связь: Упор на чувство целевой мышцы в каждом повторении, работу в полную амплитуду.
              </div>
            </div>
          </div>
          <div class="carousel-item">
            <div class="box">
              <div class="img-box">
                <img src="{{ asset('assets/images/coach4.png') }}" alt="" />
              </div>
              <div class="detail-box">
                <h5>
                  Тренер Александр
                </h5>
                <p>
                  Тренер Александр в боксе и ММА выступает в роли мультиспециалиста и менеджера процесса подготовки. Его задачи шире, чем просто обучение ударам.
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- end coach section -->

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

  <!-- the authors section -->

    <section class="about_section layout_padding">
    <div class="container">
      <div class="heading_container">
        <h2>
          Автор
        </h2>
      </div>
      <div class="box">
        <div class="detail-box">
          <h3>Юрьев Данил Антонович</h3>
        </div>
      </div>
    </div>
  </section>

  <!-- end the authors section -->

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

<!-- calculator section -->

 <script>
    function calculateTotal() {
        // Получаем значения из формы
        const membershipType = document.getElementById('membershipType');
        const trainingType = document.getElementById('trainingType');
        const lockerRental = document.getElementById('lockerRental');
        const towelService = document.getElementById('towelService');
        const months = document.getElementById('months');

        // Проверяем, выбран ли тип абонемента
        if (!membershipType.value) {
            alert('Сначала выберите абонемент!');
            membershipType.focus();
            return;
        }

        // Базовые стоимости
        let basePrice = parseInt(membershipType.value);
        let trainingPrice = parseInt(trainingType.value);
        let extrasPrice = 0;

        // Собираем детали для отображения
        let membershipText = membershipType.options[membershipType.selectedIndex].text;
        let trainingText = trainingType.options[trainingType.selectedIndex].text;
        let extrasText = [];

        // Проверяем дополнительные услуги
        if (lockerRental.checked) {
            extrasPrice += parseInt(lockerRental.value);
            extrasText.push('Аренда шкафчика');
        }

        if (towelService.checked) {
            extrasPrice += parseInt(towelService.value);
            extrasText.push('Полотенца');
        }

        // Определяем тип плана
        const membershipValue = membershipType.value;
        let isSubscription = true;
        let monthsCount = 1;

        // Разовое посещение (1300 руб)
        if (membershipValue === '1300') {
            isSubscription = false;
            trainingPrice = 0;
            extrasPrice = 0;
        } else {
            monthsCount = parseInt(months.value) || 1;
        }

        // Рассчитываем общую стоимость
        let totalPrice;
        let priceNote = '';

        if (isSubscription) {
            totalPrice = (basePrice + trainingPrice + extrasPrice) * monthsCount;

            if (monthsCount > 1) {
                const monthlyPrice = basePrice + trainingPrice + extrasPrice;
                priceNote = `(${monthlyPrice} руб × ${monthsCount} месяцев)`;
            }
        } else {
            totalPrice = basePrice;
            monthsCount = 1;
        }

        // Обновляем детали на странице
        document.getElementById('membershipDetail').textContent = `Абонемент: ${membershipText}`;
        document.getElementById('trainingDetail').textContent = `Персональные тренировки: ${trainingText}`;

        if (extrasText.length > 0) {
            document.getElementById('extrasDetail').textContent = `Дополнительные услуги: ${extrasText.join(', ')}`;
        } else {
            document.getElementById('extrasDetail').textContent = `Дополнительные услуги: Не выбраны`;
        }

        // Отображаем итоговую цену
        document.getElementById('totalPrice').textContent = `Итого: ${totalPrice} рублей`;
        document.getElementById('priceNote').textContent = priceNote;

        // Показываем кнопку для оплаты
        document.getElementById('proceedBtn').style.display = 'block';

        // Прокручиваем к результатам
        document.querySelector('.result_box').scrollIntoView({ behavior: 'smooth' });
    }

    function proceedToPayment() {
        const totalElement = document.getElementById('totalPrice');
        const totalText = totalElement.textContent;
        
        // Ищем числа в тексте
        const match = totalText.match(/(\d+)/);
        
        if (match) {
            const totalAmount = match[0];
            alert(`Спасибо за ваш заказ!\n\nОбщая сумма: ${totalAmount} рублей\n\nВы будете перенаправлены на страницу оплаты.`);
        } else {
            alert('Не удалось определить сумму заказа');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const inputs = ['membershipType', 'trainingType', 'lockerRental', 'towelService', 'months'];

        inputs.forEach(inputId => {
            const element = document.getElementById(inputId);
            if (element) {
                element.addEventListener('change', function () {
                    if (document.getElementById('proceedBtn').style.display === 'block') {
                        calculateTotal();
                    }
                });
            }
        });

        // Скрываем поле месяцев для разового посещения (1300 руб)
        document.getElementById('membershipType').addEventListener('change', function () {
            const monthsField = document.getElementById('months').parentElement;
            if (this.value === '1300') {
                monthsField.style.display = 'none';
            } else {
                monthsField.style.display = 'block';
            }
        });
    });
</script>

<!-- calculator section -->
</body>

</html>
