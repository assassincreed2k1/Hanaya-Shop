@echo off
echo.
echo ========================================
echo   Hanaya Shop - Local Test Deployment
echo ========================================
echo.

REM Check if Docker is running
docker --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ❌ Docker is not running or not installed
    echo Please install Docker Desktop and make sure it's running
    pause
    exit /b 1
)

echo ✅ Docker is available

REM Stop and remove existing containers
echo.
echo 🛑 Stopping existing containers...
docker-compose down --remove-orphans

REM Start the application
echo.
echo 🚀 Starting Hanaya Shop...
docker-compose -f docker-compose.production.yml up -d

REM Wait for services to start
echo.
echo ⏳ Waiting for services to start...
timeout /t 30 /nobreak >nul

REM Check container status
echo.
echo 📊 Container status:
docker-compose -f docker-compose.production.yml ps

echo.
echo 🎉 Deployment completed!
echo.
echo 🌐 Access your application at: http://localhost
echo 📝 View logs: docker-compose -f docker-compose.production.yml logs -f
echo 🛑 Stop application: docker-compose -f docker-compose.production.yml down
echo.
pause
