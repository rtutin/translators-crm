# Translators CRM

CRM на базе Yii2 Basic для управления переводчиками бюро переводов.

## Требования

- Docker и Docker Compose
- Composer (для локальной установки пакетов)

---

## Установка зависимостей

```bash
composer install
```

Или через Docker-контейнер (если Composer не установлен локально):

```bash
docker-compose run --rm php composer install
```

---

## Развёртывание в Docker

Запустить контейнеры (PHP + MySQL):

```bash
docker-compose up -d
```

Применить миграции (создание таблиц):

```bash
docker-compose exec php php yii migrate --interactive=0
```

Приложение будет доступно по адресу: [http://127.0.0.1:8000](http://127.0.0.1:8000)

---

## Сидинг базы данных

Заполнить таблицу тестовыми переводчиками:

```bash
docker-compose exec php php yii seed
```

Можно передать количество записей (по умолчанию 10):

```bash
docker-compose exec php php yii seed 20
```

---

## Авторизация

Доступ к CRM только для авторизованных пользователей.

- Логин: `admin`
- Пароль: `admin`
