@echo off
echo ================================================
echo 🐳 Automatic Project Deployment with Docker
echo Tire Services Search System
echo ================================================
echo.

echo ⏳ Step 1: Checking Docker...
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker not found! Install Docker Desktop
    echo 📥 Download: https://www.docker.com/products/docker-desktop
    pause
    exit /b 1
)

docker-compose --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker Compose not found!
    pause
    exit /b 1
)

echo ✅ Docker found!

echo.
echo ⏳ Step 2: Stopping and cleaning previous containers...
docker-compose down --volumes
docker system prune -f --volumes

echo.
echo ⏳ Step 3: Installing PHP dependencies...
call composer install --ignore-platform-req=ext-fileinfo
if %errorlevel% neq 0 (
    echo ❌ Error installing PHP dependencies
    pause
    exit /b 1
)

echo.
echo ⏳ Step 4: Installing JavaScript dependencies...
call npm install
if %errorlevel% neq 0 (
    echo ❌ Error installing JavaScript dependencies
    pause
    exit /b 1
)

echo.
echo ⏳ Step 5: Configuring Docker environment...
if exist .env (
    echo ℹ️ Creating backup of .env
    copy .env .env.backup
)

copy .env.docker.example .env
echo ✅ Docker environment settings applied

call php artisan key:generate
if %errorlevel% neq 0 (
    echo ❌ Error generating application key
    pause
    exit /b 1
)

echo.
echo ⏳ Step 6: Starting Docker containers...
echo 🐳 Starting MySQL, phpMyAdmin and Redis...
docker-compose up -d
if %errorlevel% neq 0 (
    echo ❌ Error starting Docker containers
    pause
    exit /b 1
)

echo.
echo ⏳ Step 7: Waiting for database readiness...
echo 🕒 Waiting for MySQL startup (45 seconds)...
timeout /t 45 /nobreak

echo.
echo ⏳ Step 8: Checking database connection...
call php artisan migrate:status
if %errorlevel% neq 0 (
    echo ⚠️ Database connection issue. Trying again in 15 seconds...
    timeout /t 15 /nobreak
)

echo.
echo ⏳ Step 9: Running migrations...
call php artisan migrate --force
if %errorlevel% neq 0 (
    echo ❌ Error running migrations
    echo 🔍 Check logs: docker-compose logs mysql
    pause
    exit /b 1
)

echo.
echo ⏳ Step 10: Generating test data...
echo ⚠️ This may take several minutes (creating 10,000 records)...
call php artisan db:seed --force
if %errorlevel% neq 0 (
    echo ❌ Error generating test data
    pause
    exit /b 1
)

echo.
echo ⏳ Step 11: Building frontend...
call npm run build
if %errorlevel% neq 0 (
    echo ❌ Error building frontend
    pause
    exit /b 1
)

echo.
echo ================================================
echo ✅ Docker deployment completed successfully!
echo ================================================
echo.
echo 🐳 Running services:
echo   • MySQL:      localhost:3306
echo   • phpMyAdmin: http://localhost:8080
echo   • Redis:      localhost:6379
echo.
echo 📋 Next steps:
echo 1. Start Laravel: php artisan serve
echo 2. Open application: http://localhost:8000
echo 3. Manage DB: http://localhost:8080
echo.
echo 🔗 Useful commands:
echo   • Start server:      php artisan serve
echo   • Development mode:  npm run dev
echo   • View DB:           http://localhost:8080
echo   • Docker logs:       docker-compose logs
echo   • Stop Docker:       docker-compose down
echo   • Restart Docker:    docker-compose restart
echo.
echo 🗄️ phpMyAdmin credentials:
echo   • Server: mysql
echo   • Username: laravel_user
echo   • Password: laravel_password
echo   • Database: tire_services
echo.
echo ❓ If you have issues:
echo   • Check logs: docker-compose logs
echo   • Refer to README.md
echo ================================================

pause 