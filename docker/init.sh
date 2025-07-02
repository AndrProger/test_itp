#!/bin/sh

echo "🔄 Инициализация Laravel..."

echo "🚀 Запуск миграций..."
php artisan migrate:fresh --force

echo "📦 Генерация данных..."
php artisan db:seed --class=TireServiceSeeder --force

echo "🔗 Символическая ссылка будет создана автоматически при запуске app контейнера..."

echo "🧹 Очистка кеша..."
php artisan cache:clear
php artisan config:clear
php artisan view:clear

echo "✅ Инициализация завершена!"
echo "🌐 Приложение доступно на http://localhost" 