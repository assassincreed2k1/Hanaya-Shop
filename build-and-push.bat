@echo off
REM Script để build và push Docker image từ Windows
REM Sử dụng: build-and-push.bat [version]

set VERSION=%1
if "%VERSION%"=="" set VERSION=latest

set IMAGE_NAME=assassincreed2k1/hanaya-shop

echo ======================================================
echo      BUILD VÀ PUSH DOCKER IMAGE TỪ WINDOWS
echo ======================================================
echo Image: %IMAGE_NAME%:%VERSION%
echo ======================================================

REM Build Docker image
echo Building Docker image...
docker build -t %IMAGE_NAME%:%VERSION% .

if %ERRORLEVEL% neq 0 (
    echo Build failed!
    pause
    exit /b 1
)

REM Tag phiên bản latest
if not "%VERSION%"=="latest" (
    docker tag %IMAGE_NAME%:%VERSION% %IMAGE_NAME%:latest
)

REM Đẩy lên Docker Hub
echo Pushing to Docker Hub...
docker push %IMAGE_NAME%:%VERSION%

if %ERRORLEVEL% neq 0 (
    echo Push failed! Kiểm tra đăng nhập Docker Hub: docker login
    pause
    exit /b 1
)

REM Đẩy phiên bản latest
if not "%VERSION%"=="latest" (
    docker push %IMAGE_NAME%:latest
)

echo ======================================================
echo      HOÀN TẤT
echo ======================================================
echo Image đã được đẩy lên Docker Hub:
echo - %IMAGE_NAME%:%VERSION%
if not "%VERSION%"=="latest" echo - %IMAGE_NAME%:latest
echo.
echo Bây giờ bạn có thể sang Ubuntu server và chạy:
echo cd ~/Hanaya-Shop
echo docker pull %IMAGE_NAME%:latest
echo docker-compose -f docker-compose.production.yml up -d
echo ======================================================
pause
