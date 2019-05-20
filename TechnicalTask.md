## Agenda
Мы очень ценим наших пользователей и стремимся предоставлять им самое лучшее качество сервиса.
Для того чтобы иметь полную картину того каким видят наш продукт наши пользователи, 
мы собираем массу продуктовых метрик.

За агрегацию и хранение данных отвечает аналитический микросервис(ms Analytics).
Наша аналитическая база очень хорошо оптимизирована для запросов на чтение(в порядки быстрее чем обычный MySQL), 
что позволяет нашим продуктологам и аналитикам быстро получать актуальные отчеты, практически в режиме реального времени.
Но на ряду с этим мы столкнулись с техническим вызовом.
Поскольку база оптимизирована под выборки, она оказалась,
на наших объемах данных, очень медленной на вставку новых данных. Время записи иногда составляет 5 секунд.
 Естественно, мы не можем заставлять пользователя, при каждой загрузке страницы, 
ожидать 5 секунд пока произойдет запись аналитических данных.
Очевидно что запись в ms Analytics нужно проводить асинхронно, используя при этом систему очередей.
Мы планируем проводить рефакторинг нашего приложения небольшими шагами, 
для начала предлагается этот подход протестировать на странице регистрации/авторизации пользователя.


 ## ToDo:
Реализовать RESTfull API or GraphQL, для сервиса, который позволит авторизировать пользователя, и записывать его действия на сайте.
Необходимый функционал:
1. Регистрация (обязательные поля: firstname, lastname, nickname, age, password)
2. Авторизация (по полям nickname, password)
3. Трекинг действий пользователя на сайте(нажатие каждой кнопки, переходы по страницам и т.д.), 
в том случае если пользователь не авторизирован на сайте, то нужно присвоить ему уникальный идентификатор(чтобы понимать
 что это один и тот же пользователь посещяет страницы сайта).
Трекинг разных действий будет отличаться лишь параметром «source_label».

 
##### Структура данных для трекинга:
 ```
 {
     "id" : 1, 
     "id_user" : 9841,
     "source_label" : "search_page", 
     "date_created" : "2018-04-10 12:09:07"
 }
```

### Дополнительные условия:
* Пользовательские данные должны хранится в json файлах(без использования БД).
* Отправка данных в ms Analytics должна происходить посредством системы очередей(RabbitMQ).
* Придерживатся PSR-(1,2,4).
* Предоставить документацию к проекту.

### Плюсом будет: 
* Покрыть код Unit тестами.
* Конфигурацию сервиса вынести в файлы .env.

<b> P.S.: можно использовать как любой современный фреймворк так и написать всё с нуля.</b>