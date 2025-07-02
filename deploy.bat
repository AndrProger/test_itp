@echo off
echo ================================================
echo Simple Project Deployment
echo Tire Services Search System
echo ================================================
echo.

echo Step 1: Installing dependencies...
call composer install --ignore-platform-req=ext-fileinfo
call npm install

echo.
echo Step 2: Environment setup...
if not exist .env (
    copy .env.example .env
    echo .env file created
)

echo.
echo Step 3: Generate app key...
call php artisan key:generate --force

echo.
echo Step 4: Starting MySQL container...
call docker-compose up -d mysql phpmyadmin

echo.
echo Step 5: Waiting for MySQL...
timeout /t 30 /nobreak

echo.
echo Step 6: Running migrations...
call php artisan migrate --force

echo.
echo Step 7: Seeding database...
call php artisan db:seed --force

echo.
echo Step 8: Starting development servers...
echo Starting servers in separate windows...
start cmd /k "npm run dev"
start cmd /k "php artisan serve"

echo.
echo ================================================
echo Deployment completed!
echo ================================================
echo.
echo Application: http://localhost:8000
echo phpMyAdmin: http://localhost:8080
echo ================================================

pause 