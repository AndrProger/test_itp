@echo off
echo ================================================
echo Automatic Project Deployment (Development Mode)
echo Tire Services Search System (MySQL Only)
echo ================================================
echo.

echo Step 1: Installing PHP dependencies...
call composer install --ignore-platform-req=ext-fileinfo
if %errorlevel% neq 0 (
    echo ERROR: Failed to install PHP dependencies
    pause
    exit /b 1
)

echo.
echo Step 2: Installing JavaScript dependencies...
call npm install
if %errorlevel% neq 0 (
    echo ERROR: Failed to install JavaScript dependencies
    pause
    exit /b 1
)

echo.
echo Step 3: Environment setup...
if not exist .env (
    if exist ENV-LOCAL-EXAMPLE.txt (
        copy ENV-LOCAL-EXAMPLE.txt .env
        echo SUCCESS: .env file created from ENV-LOCAL-EXAMPLE.txt
    ) else if exist .env.example (
        copy .env.example .env
        echo SUCCESS: .env file created from .env.example
        echo INFO: Updating database settings for MySQL Docker...
        REM Обновляем настройки для MySQL в Docker
        powershell -Command "(Get-Content .env) -replace 'DB_USERNAME=.*', 'DB_USERNAME=root' | Set-Content .env"
        powershell -Command "(Get-Content .env) -replace 'DB_PASSWORD=.*', 'DB_PASSWORD=root_password' | Set-Content .env"
        powershell -Command "(Get-Content .env) -replace 'APP_NAME=.*', 'APP_NAME=\"Tire Services Search - shinka.kz\"' | Set-Content .env"
        echo SUCCESS: Database settings updated for Docker MySQL
    ) else (
        echo ERROR: Neither ENV-LOCAL-EXAMPLE.txt nor .env.example found
        echo Creating minimal .env configuration...
        (
            echo APP_NAME="Tire Services Search - shinka.kz"
            echo APP_ENV=local
            echo APP_KEY=
            echo APP_DEBUG=true
            echo APP_URL=http://localhost:8000
            echo DB_CONNECTION=mysql
            echo DB_HOST=127.0.0.1
            echo DB_PORT=3306
            echo DB_DATABASE=tire_services
            echo DB_USERNAME=root
            echo DB_PASSWORD=root_password
            echo MYSQL_ROOT_PASSWORD=root_password
        ) > .env
        echo SUCCESS: Minimal .env file created
    )
) else (
    echo INFO: .env file already exists
)

echo.
echo Step 4: Configuring MySQL connection...

echo SUCCESS: MySQL configuration applied

call php artisan key:generate --force
if %errorlevel% neq 0 (
    echo ERROR: Failed to generate application key
    pause
    exit /b 1
)

echo.
echo Step 5: Testing MySQL connection...
echo INFO: Connecting to MySQL Docker container...

echo.
echo Current connection settings:
echo   • Host: 127.0.0.1
echo   • Port: 3306  
echo   • Database: tire_services
echo   • Username: root
echo   • Password: [SET]

echo.
echo Checking Docker containers...
docker container ls -q >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: Docker is not running or not accessible
    echo SOLUTION: Start Docker Desktop and try again
    pause
    exit /b 1
)

REM Проверяем наличие MySQL контейнера простым способом
docker inspect tire_services_mysql >nul 2>&1
if %errorlevel% neq 0 (
    echo WARNING: No MySQL containers found running
    echo INFO: Starting MySQL and phpMyAdmin from docker-compose...
    echo.
    
    call docker-compose up -d mysql phpmyadmin
    
    echo INFO: Waiting for containers to initialize...
    timeout /t 15 /nobreak >nul
    
    echo INFO: Verifying MySQL container status...
    docker inspect tire_services_mysql >nul 2>&1
    if %errorlevel% neq 0 (
        echo WARNING: MySQL container not ready yet, waiting additional 10 seconds...
        timeout /t 10 /nobreak >nul
        docker inspect tire_services_mysql >nul 2>&1
        if %errorlevel% neq 0 (
            echo ERROR: MySQL container failed to start properly
            echo INFO: Container logs:
            docker logs tire_services_mysql
            echo SOLUTION: Check Docker Desktop and container health
            pause
            exit /b 1
        )
    )
    
    echo INFO: Waiting for MySQL to be ready...
    timeout /t 15 /nobreak >nul
    
    echo INFO: Checking MySQL startup...
    set /a mysql_tries=0
    :mysql_check
    set /a mysql_tries+=1
    docker exec tire_services_mysql mysqladmin ping -h localhost --silent >nul 2>&1
    if %errorlevel% neq 0 (
        if %mysql_tries% geq 24 (
            echo ERROR: MySQL failed to start after 2 minutes
            docker logs tire_services_mysql
            pause
            exit /b 1
        )
        echo INFO: MySQL still starting up (%mysql_tries%/24), waiting 5 seconds...
        timeout /t 5 /nobreak >nul
        goto mysql_check
    )
    
    echo INFO: MySQL is responding, checking database initialization...
    timeout /t 5 /nobreak >nul
    
    echo SUCCESS: MySQL and phpMyAdmin started successfully
    echo INFO: phpMyAdmin available at: http://localhost:8080
) else (
    echo SUCCESS: MySQL container is already running
    docker ps
)

echo.
echo Testing connection...

REM Создаем временный файл для проверки вывода
php artisan migrate:status > temp_output.txt 2>&1

REM Проверяем на наличие различных ошибок
findstr /C:"could not find driver" temp_output.txt >nul 2>&1
if %errorlevel% equ 0 (
    del temp_output.txt
    echo ERROR: MySQL PDO driver not found
    echo SOLUTION: Install PHP MySQL extension: php_pdo_mysql
    pause
    exit /b 1
)

findstr /C:"Connection refused" temp_output.txt >nul 2>&1
if %errorlevel% equ 0 (
    del temp_output.txt
    echo ERROR: Connection refused - MySQL not accessible
    echo SOLUTION: Check if MySQL container is running on correct port
    pause
    exit /b 1
)

findstr /C:"Access denied" temp_output.txt >nul 2>&1
if %errorlevel% equ 0 (
    del temp_output.txt
    echo ERROR: Access denied - Wrong username/password
    echo SOLUTION: Check DB_USERNAME and DB_PASSWORD in .env file
    pause
    exit /b 1
)

findstr /C:"Unknown database" temp_output.txt >nul 2>&1
if %errorlevel% equ 0 (
    del temp_output.txt
    echo WARNING: Database does not exist, creating automatically...
    
    REM Используем значения по умолчанию
    set DB_NAME=tire_services
    set ROOT_PASSWORD=root_password
    
    echo INFO: Creating database '%DB_NAME%'...
    docker exec tire_services_mysql mysql -uroot -p%ROOT_PASSWORD% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
    
    if %errorlevel% neq 0 (
        echo ERROR: Failed to create database automatically
        echo MANUAL SOLUTION: docker exec -it tire_services_mysql mysql -uroot -p -e "CREATE DATABASE %DB_NAME%;"
        pause
        exit /b 1
    )
    
    echo SUCCESS: Database '%DB_NAME%' created successfully!
    echo INFO: Waiting for database to be ready...
    timeout /t 3 /nobreak >nul
    
    REM Повторная проверка соединения после создания базы
    echo INFO: Verifying connection to newly created database...
    php artisan migrate:status >nul 2>&1
    if %errorlevel% neq 0 (
        echo ERROR: Still cannot connect to database after creation
        echo INFO: Trying to connect manually...
        docker exec tire_services_mysql mysql -uroot -p%ROOT_PASSWORD% -e "SHOW DATABASES LIKE '%DB_NAME%';"
        pause
        exit /b 1
    )
    echo SUCCESS: Connection to newly created database verified!
)

REM Если временный файл все еще существует, удаляем его
if exist temp_output.txt del temp_output.txt

php artisan migrate:status >nul 2>&1
if %errorlevel% neq 0 (
    echo WARNING: Connection failed with unknown error, attempting database creation...
    echo.
    echo Full error details:
    php artisan migrate:status
    echo.
    
    REM Используем значения по умолчанию для fallback
    set DB_NAME=tire_services
    set ROOT_PASSWORD=root_password
    
    echo INFO: Attempting to create database '%DB_NAME%' as a fallback...
    docker exec tire_services_mysql mysql -uroot -p%ROOT_PASSWORD% -e "CREATE DATABASE IF NOT EXISTS %DB_NAME% CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>nul
    
    timeout /t 3 /nobreak >nul
    
    php artisan migrate:status >nul 2>&1
    if %errorlevel% neq 0 (
        echo ERROR: Database creation attempt failed, manual intervention required
        echo.
        echo Manual solutions:
        echo   1. Check if MySQL Docker container is running: docker ps
        echo   2. Verify database exists: docker exec -it tire_services_mysql mysql -uroot -p -e "SHOW DATABASES;"
        echo   3. Create database manually: docker exec -it tire_services_mysql mysql -uroot -p -e "CREATE DATABASE %DB_NAME%;"
        echo   4. Test manual connection: docker exec -it tire_services_mysql mysql -uroot -p
        echo   5. Check .env file settings above
        echo.
        pause
        exit /b 1
    )
    
    echo SUCCESS: Database created successfully as fallback!
)

REM Окончательная очистка временных файлов
if exist temp_output.txt del temp_output.txt

echo SUCCESS: MySQL connection successful

echo.
echo Step 6: Running migrations...
call php artisan migrate --force
if %errorlevel% neq 0 (
    echo ERROR: Failed to run migrations
    echo SOLUTION: Make sure MySQL database 'tire_services' exists
    pause
    exit /b 1
)

echo.
echo Step 7: Generating test data...
echo WARNING: This may take several minutes (creating 10,000 records)...
call php artisan db:seed --force
if %errorlevel% neq 0 (
    echo ERROR: Failed to generate test data
    pause
    exit /b 1
)

echo.
echo Step 8: Starting development servers...
echo INFO: Starting Vite development server...
powershell -Command "Start-Process -FilePath 'cmd.exe' -ArgumentList '/k npm run dev' -WindowStyle Normal"
timeout /t 3 /nobreak >nul

echo INFO: Starting Laravel development server...
powershell -Command "Start-Process -FilePath 'cmd.exe' -ArgumentList '/k php artisan serve' -WindowStyle Normal"
timeout /t 3 /nobreak >nul

echo.
echo ================================================
echo SUCCESS: Development environment ready!
echo ================================================
echo.
echo Database: MySQL (tire_services) - Docker container
echo Connection: localhost:3306
echo.
echo Development servers started:
echo 1. Vite Dev Server - Frontend assets compilation
echo 2. Laravel Server - Backend API (http://localhost:8000)
echo.
echo Available services:
echo • Main Application: http://localhost:8000
echo • phpMyAdmin (DB Management): http://localhost:8080
echo.
echo Useful commands:
echo   • View MySQL logs: docker logs tire_services_mysql
echo   • View phpMyAdmin logs: docker logs tire_services_phpmyadmin
echo   • Restart services: docker-compose restart
echo   • Stop services: docker-compose down
echo   • Connect to DB: docker exec -it tire_services_mysql mysql -uroot -p
echo   • Clear Laravel cache: php artisan cache:clear
echo ================================================

pause 