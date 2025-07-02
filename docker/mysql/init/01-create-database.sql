-- Инициализация базы данных для Laravel приложения "Tire Services"
-- Этот файл автоматически выполняется при первом запуске MySQL контейнера

-- Создание базы данных с правильной кодировкой
CREATE DATABASE IF NOT EXISTS tire_services 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Создание пользователя для Laravel приложения
CREATE USER IF NOT EXISTS 'laravel_user'@'%' IDENTIFIED BY 'laravel_password';
CREATE USER IF NOT EXISTS 'laravel_user'@'localhost' IDENTIFIED BY 'laravel_password';

-- Предоставление всех прав пользователю на базу данных
GRANT ALL PRIVILEGES ON tire_services.* TO 'laravel_user'@'%';
GRANT ALL PRIVILEGES ON tire_services.* TO 'laravel_user'@'localhost';

-- Предоставление прав на создание временных таблиц (для Laravel)
GRANT CREATE TEMPORARY TABLES ON tire_services.* TO 'laravel_user'@'%';
GRANT CREATE TEMPORARY TABLES ON tire_services.* TO 'laravel_user'@'localhost';

-- Применение всех изменений
FLUSH PRIVILEGES;

-- Выбор созданной базы данных
USE tire_services;

-- Информационные сообщения
SELECT 'Database tire_services created successfully!' as message;
SELECT 'User laravel_user created with full privileges!' as status;
SELECT 'Ready for Laravel migrations!' as ready_status; 