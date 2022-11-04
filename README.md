# Базовая схема организации очередей RabbitMQ

ТЗ:

    Есть веб-api, непрерывно принимающее события (ограничимся 10000 событий) для группы аккаунтов (1000 аккаунтов) и складывающее их в очередь.
    Каждое событие связано с определенным аккаунтом и важно, чтобы события аккаунта обрабатывались в том же порядке, в котором поступили в очередь. Обработка события занимает 1 секунду (эмулировать с помощью sleep).  
    Сделать обработку очереди событий максимально быстрой на данной конкретной машине.
    Код писать на PHP. Можно использовать фреймворки и инструменты такие как RabbitMQ, Redis, MySQL и т. д.

Решение:

    1 producer
    2 consumers
    2 queues (разделение сообщений на основании четности|нечетности user_id)

Возможные улучшения:

    Если понадобится больше консьюмеров, можно создавать очереди на основании остатка от деления на 10 того же user_id.
    Таким образом у нас будет 10 очередей [0-9].

Как запустить:

    1. docker-compose up
    2. composer install
    3. php producer.php
    4.1 php consumer.php queue_odd
    4.2 php consumer.php queue_even

Дополнительные требования:
    
    Раскомментить в php.ini:
        extension=sockets

Мониторинг сообщений:

    http://localhost:15672/
    Login: guest
    Password: guest
