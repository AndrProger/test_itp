@echo off
chcp 65001 >nul
echo.
echo ========================================
echo 🚗 shinka.kz - Полное Docker развёртывание
echo ========================================
echo.

echo 📋 Проверяем наличие Docker...
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker не найден! Установите Docker Desktop
    pause
    exit /b 1
)
echo ✅ Docker найден

echo.
echo 🔧 Настраиваем окружение...
if not exist .env (
    copy docker.env.example .env
    echo ✅ Создан файл .env
) else (
    echo ✅ Файл .env уже существует
)

echo.
echo 🛑 Останавливаем и удаляем старые контейнеры...
docker compose -f docker-compose-full.yml down --volumes --remove-orphans

echo.
echo 🏗️ Собираем Docker образы...
docker compose -f docker-compose-full.yml build --no-cache

echo.
echo 🚀 Запускаем контейнеры...
docker compose -f docker-compose-full.yml up -d

echo.
echo ⏳ Ждём готовности MySQL и запуска инициализации...
echo 🔧 Инициализация выполняется автоматически отдельным контейнером...

echo.
echo ========================================
echo ✅ Развёртывание завершено!
echo ========================================
echo.
echo 🌐 Приложение: http://localhost
echo 🗄️ phpMyAdmin: http://localhost:8080
echo.
echo 📊 Проверка статуса контейнеров:
docker compose -f docker-compose-full.yml ps
echo.
echo 📋 Для просмотра логов инициализации:
echo    docker compose -f docker-compose-full.yml logs init
echo.
echo ========================================

pause 