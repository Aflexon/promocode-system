# Promocode System

## Требования
* docker 24+
* docker compose v2.19+

## Установка
```shell
./init.sh
```
После установки сайт доступен по адресу http://localhost:8000/

PHPMyAdmin доступен по адресу http://localhost:8081/ User: `root` Password: `secret`

По умолчанию, промокоды будут сгенерированы в процессе выполнения `./init.sh`.
Если нужно сгенерировать промокоды вручную их можно сгенерировать скриптом `./seeds/generate-promocodes.php`
```shell
./workspace.sh php ./seeds/generate-promocodes.php
```

## Запуск тестов

```shell
./tests.sh
```


## Задача

- Система выдачи промокодов
- Генерация 500к уникальных промокодов(может быть одноразовый скрипт)
- Промокод - это строка длиной 10 символов
- Один промокод может быть выдан только одному пользователю
- Один пользователь не может получить больше одного промокода
- С одного IP адреса может быть выдано не более 1000 промокодов
- Необходимо сохранять дату выдачи каждого промокода

### Результат

- Страница с формой выдачи промокода
- Кликнув на кнопку, пользователь получает промокод, и происходит
  редирект с указанным промокодом на сайт партнера: https://www.google.com/?query=PROMOCODE
- Повторный клик тем же пользователем на кнопку приводит к редиректу на ту же страницу(с промокодом выданным ранее)
- Фунционал выдачи промокода должен быть покрыт тестами

### Ограничения

- База данных Mysql 5.7+
- Промокодов в таблице: 500.000
- Версия PHP 8+(без использования фреймворков)
- Нельзя использовать javascript
