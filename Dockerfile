# Frontend build stage
FROM node:18-alpine AS frontend-build

WORKDIR /app

# Копируем только файлы зависимостей NPM для кеширования
COPY package*.json ./

# Устанавливаем NPM зависимости (кешируется если package.json не изменился)
RUN npm ci

# Копируем остальные файлы и собираем
COPY . .
RUN npm run build

# Main application stage
FROM php:8.2-fpm-alpine

# Устанавливаем системные зависимости (кешируется)
RUN apk add --no-cache \
    mysql-client \
    zip \
    unzip \
    git \
    curl \
    oniguruma-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev

# Устанавливаем PHP расширения (кешируется)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
        pdo_mysql \
        mbstring \
        zip \
        exif \
        pcntl \
        bcmath \
        gd

# Устанавливаем Composer (кешируется)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Создаем пользователя (кешируется)
RUN addgroup -g 1000 www && adduser -u 1000 -G www -s /bin/sh -D www

# Устанавливаем рабочую директорию
WORKDIR /var/www

# Конфигурационные файлы не нужны для встроенного сервера

# Копируем файлы зависимостей и основные Laravel файлы для кеширования
COPY --chown=www:www composer.json composer.lock artisan ./
COPY --chown=www:www bootstrap/ bootstrap/

# Создаем необходимые директории ДО composer install
RUN mkdir -p /var/www/storage/logs \
    && mkdir -p /var/www/storage/framework/cache \
    && mkdir -p /var/www/storage/framework/sessions \
    && mkdir -p /var/www/storage/framework/views \
    && mkdir -p /var/www/bootstrap/cache \
    && chown -R www:www /var/www/storage \
    && chown -R www:www /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage \
    && chmod -R 775 /var/www/bootstrap/cache

# Устанавливаем PHP зависимости включая dev для faker (кешируется если composer.lock не изменился)
RUN composer install --optimize-autoloader --no-interaction --no-scripts

# Копируем скомпилированные фронтенд ассеты
COPY --from=frontend-build --chown=www:www /app/public/build /var/www/public/build

# Копируем остальной код приложения (выполняется при каждом изменении кода)
COPY --chown=www:www . /var/www

# Завершаем установку composer скриптов после копирования всего кода
RUN composer run-script post-autoload-dump

# Делаем инициализационный скрипт исполняемым
RUN chmod +x /var/www/docker/init.sh

# Переключаемся на пользователя root
USER root

# Открываем веб-сервер порт  
EXPOSE 80

# Запускаем встроенный Laravel сервер с созданием symbolic link
CMD ["/bin/sh", "-c", "php artisan storage:link && php artisan serve --host=0.0.0.0 --port=80"] 