# 🛞 Система поиска шиномонтажных сервисов

Веб-приложение для поиска и управления записями шиномонтажных сервисов, созданное на основе Laravel с современным интерфейсом на Vue.js 3.

## 📋 Содержание

- [Описание проекта](#описание-проекта)
- [Технический стек](#технический-стек)
- [Функциональность](#функциональность)
- [Варианты установки](#варианты-установки)
  - [🐳 Docker (Рекомендуемый)](#-docker-рекомендуемый)
  - [💻 Локальная установка + MySQL Docker](#-локальная-установка--mysql-docker)
- [🛠️ Управление MySQL Docker контейнером](#-управление-mysql-docker-контейнером)
- [⚙️ Настройка переменных окружения](#-настройка-переменных-окружения)
- [Использование](#использование)
- [Архитектура](#архитектура)
- [Разработчикам](#разработчикам)

## 🎯 Описание проекта

Современное веб-приложение для поиска шиномонтажных сервисов с возможностями:

- **Умный поиск** - текстовый поиск с автокомплитом и debouncing
- **Многокритериальная фильтрация** - по названию, фото, количеству комнат, площади
- **Живая пагинация** - без перезагрузки страницы
- **Адаптивный дизайн** - от мобильных до 4K мониторов
- **SEO-оптимизация** - сохранение состояния фильтров в URL

## 🚀 Технический стек

### Backend
- **PHP 8.3+** / Laravel 12.19
- **MySQL 8.0** / SQLite
- **Redis** (кеширование)

### Frontend
- **Vue.js 3** + Composition API
- **Inertia.js** (SPA без API)
- **Tailwind CSS** (стилизация)
- **Vite** (сборка)

### DevOps
- **Docker Compose** (развертывание)
- **phpMyAdmin** (управление БД)

## ✨ Функциональность

### 🔍 Поиск и фильтрация
- **Текстовый поиск** с приоритетной сортировкой:
  - Точное совпадение → Начинается с запроса → Содержит запрос
- **Фильтр по фото** - показывать только с изображениями
- **Количество комнат** - множественный выбор (чекбоксы)
- **Диапазон площади** - слайдер "от и до"
- **Live-поиск** с debouncing 500ms

### 📱 Пользовательский интерфейс
- **Адаптивный дизайн** - iPhone X до 4K мониторов
- **Карточки сервисов** с превью изображений
- **Пагинация** с информацией о количестве записей
- **Сохранение состояния** фильтров в URL параметрах
- **Обработка ошибок** загрузки изображений

### 🗄️ База данных
- **10,000 тестовых записей** для демонстрации
- **Оптимизированные индексы** для быстрого поиска
- **Полнотекстовый поиск** с релевантностью

---

## 🚀 Варианты установки

### 🐳 Docker (Рекомендуемый)

**Быстрое развертывание с автоматической настройкой MySQL и Redis**

#### Требования
- Windows 10/11 с WSL2
- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- Git

#### Автоматическая установка

```bash
# 1. Клонирование репозитория
git clone <repository-url>
cd tire-services-search

# 2. Запуск автоматического развертывания
deploy-docker.bat
```

**Что делает скрипт:**
1. ✅ Проверяет наличие Docker
2. 🐳 Запускает MySQL 8.0 + phpMyAdmin + Redis
3. 📦 Устанавливает зависимости (PHP/JS)
4. 🔧 Настраивает окружение для Docker
5. 🗄️ Выполняет миграции базы данных
6. 🎲 Генерирует 10,000 тестовых записей
7. 🏗️ Собирает фронтенд для продакшена

#### После установки

```bash
# Запуск Laravel сервера
php artisan serve

# Открыть приложение
http://localhost:8000

# Управление БД через phpMyAdmin
http://localhost:8080
```

#### Docker сервисы
- **MySQL**: `localhost:3306` (tire_services/laravel_user/laravel_password)
- **phpMyAdmin**: `http://localhost:8080`
- **Redis**: `localhost:6379`

---

### 💻 Локальная установка + MySQL Docker

**Приложение запускается локально, MySQL в Docker контейнере**

#### Требования
- PHP 8.3+ с расширениями: fileinfo, pdo_mysql, mbstring, tokenizer, xml, ctype, json, openssl
- Composer 2.0+
- Node.js 18+ и npm
- Docker (для MySQL контейнера)

#### Автоматическая установка

```bash
# 1. Клонирование и переход в папку
git clone <repository-url>
cd tire-services-search

# 2. Запуск MySQL в Docker контейнере
setup-mysql-docker.bat

# 3. Развертывание приложения (подключается к MySQL Docker)
deploy.bat
```

**Что происходит:**
- `setup-mysql-docker.bat` создает MySQL 8.0 контейнер с базой данных `tire_services`
- `deploy.bat` настраивает приложение для подключения к MySQL Docker
- Приложение запускается локально, подключается к MySQL в Docker

---

## 🛠️ Управление MySQL Docker контейнером

### 🐳 Основные команды MySQL контейнера

```bash
# Запуск MySQL контейнера (если еще не запущен)
setup-mysql-docker.bat

# Проверка статуса контейнера
docker ps | findstr mysql-tire-services

# Просмотр логов MySQL
docker logs mysql-tire-services

# Подключение к MySQL
docker exec -it mysql-tire-services mysql -uroot -p

# Остановка/запуск контейнера
docker stop mysql-tire-services
docker start mysql-tire-services

# Перезапуск контейнера
docker restart mysql-tire-services
```

### 🔧 Параметры подключения

По умолчанию используются следующие параметры:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tire_services
DB_USERNAME=root
DB_PASSWORD=password
```

### 🗂️ Ручная настройка (если нужно)

```bash
# Если у вас свой MySQL контейнер, просто обновите .env:
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Затем запустите приложение:
deploy.bat
```

---

## ⚙️ Настройка переменных окружения

### 🔧 Docker Compose переменные

Docker Compose теперь читает настройки из `.env` файла. Вы можете настроить:

```env
# Database Settings
DB_DATABASE=tire_services
DB_USERNAME=root
DB_PASSWORD=password
DB_PORT=3306

# Docker Container Names
MYSQL_CONTAINER_NAME=mysql-tire-services
PHPMYADMIN_CONTAINER_NAME=phpmyadmin-tire-services
REDIS_CONTAINER_NAME=redis-tire-services

# Docker Ports
PHPMYADMIN_PORT=8080
REDIS_PORT=6379

# MySQL Root Password
MYSQL_ROOT_PASSWORD=password
```

### 📋 Примеры .env файлов

#### Для полного Docker развертывания
```bash
# Используйте .env.docker.example
copy .env.docker.example .env
```

#### Для локального приложения + MySQL Docker
```bash
# Создайте .env из примера
copy ENV-LOCAL-EXAMPLE.txt .env
```

### 🔄 Применение изменений

После изменения `.env` файла:

```bash
# Для Docker Compose
docker-compose down
docker-compose up -d

# Для локального MySQL контейнера
docker stop mysql-tire-services
setup-mysql-docker.bat
```

---

## 🖥️ Использование

### Основные функции

#### 🔍 Поиск
1. Введите название в поле поиска
2. Используйте фильтры для уточнения
3. Результаты обновляются автоматически

#### 🎛️ Фильтры
- **Название**: Частичное совпадение
- **Есть фото**: Только с изображениями  
- **Комнаты**: Множественный выбор
- **Площадь**: Слайдер диапазона

#### 📄 Пагинация
- Переключение страниц без перезагрузки
- Сохранение фильтров при навигации
- Показ общего количества результатов

### Режимы запуска

```bash
# Разработка с hot-reload
npm run dev
php artisan serve

# Продакшен (оптимизированная сборка)
npm run build
php artisan serve

# Дополнительные команды разработки
php artisan queue:work    # Фоновые задачи
php artisan cache:clear   # Очистка кеша
```

---

## 🏗️ Архитектура

### Backend (Laravel)
```
app/
├── Domain/TireService/          # Доменная логика (DDD)
│   ├── Models/TireService.php   # Eloquent модель
│   └── Repositories/            # Репозитории для работы с данными
├── Http/Controllers/            # HTTP контроллеры
│   ├── TireServiceController.php
│   └── Api/TireServiceController.php
└── Actions/                     # Бизнес-логика (Laravel Jetstream)

database/
├── migrations/                  # Схема БД
├── factories/                   # Фабрики для тестовых данных
└── seeders/                     # Сидеры для заполнения БД
```

### Frontend (Vue.js 3)
```
resources/js/Pages/
├── TireServices/
│   ├── Index.vue               # Главная страница поиска
│   ├── Show.vue                # Детальная страница
│   └── Components/
│       ├── ServiceCard.vue     # Карточка сервиса
│       └── Pagination.vue      # Компонент пагинации
└── Layout/
    └── AppLayout.vue           # Основной макет
```

### Базы данных
- **tire_services** - основная таблица с индексами для поиска
- **users** - пользователи (Jetstream)
- **sessions** - сессии
- **cache** - кеш таблица

---

## 👩‍💻 Разработчикам

### Полезные команды

```bash
# Artisan команды
php artisan make:model Domain/TireService/Models/TireService
php artisan make:migration create_tire_services_table
php artisan make:controller TireServiceController
php artisan make:factory TireServiceFactory
php artisan make:seeder TireServiceSeeder

# Frontend команды
npm run dev          # Режим разработки с HMR
npm run build        # Продакшен сборка
npm run preview      # Превью продакшен сборки

# База данных
php artisan migrate:refresh --seed  # Пересоздание БД с данными
php artisan migrate:status          # Статус миграций
php artisan db:seed --class=TireServiceSeeder  # Отдельный сидер

# Docker команды
docker-compose up -d           # Запуск контейнеров
docker-compose down --volumes  # Остановка с удалением данных
docker-compose logs mysql      # Логи MySQL
docker-compose restart         # Перезапуск всех сервисов
```

### Структура проекта
- `/app/Domain/` - доменная логика (DDD подход)
- `/resources/js/` - Vue.js компоненты и логика
- `/database/` - миграции, фабрики, сидеры
- `/docs/` - документация проекта
- `/docker/` - Docker конфигурация

### API Endpoints
```http
GET /api/tire-services              # Поиск с фильтрами
GET /api/tire-services/{id}         # Детальная информация
```

### Параметры поиска
- `search` - текстовый поиск
- `has_image` - фильтр по наличию фото
- `rooms` - массив количества комнат
- `area_min`, `area_max` - диапазон площади
- `page` - номер страницы

---

## 🔧 Устранение неисправностей

### Проблемы с Docker
```bash
# Проверка статуса контейнеров
docker-compose ps

# Логи MySQL
docker-compose logs mysql

# Перезапуск при проблемах
docker-compose down --volumes
docker-compose up -d
```

### Проблемы с базой данных
```bash
# Проверка подключения
php artisan migrate:status

# Пересоздание БД
php artisan migrate:refresh --seed

# Очистка и пересоздание кеша
php artisan config:clear
php artisan cache:clear
```

### Проблемы с фронтендом
```bash
# Очистка node_modules
rm -rf node_modules package-lock.json
npm install

# Пересборка
npm run build
```

---

## 📝 Дополнительная информация

- **Документация**: `/docs/project.md`
- **Changelog**: `/docs/changelog.md`
- **Task Tracker**: `/docs/tasktracker.md`

## 🤝 Поддержка

При возникновении проблем:
1. Проверьте логи: `docker-compose logs`
2. Обратитесь к разделу "Устранение неисправностей"
3. Создайте issue в репозитории

---

**Разработано с ❤️ для демонстрации современных веб-технологий**
