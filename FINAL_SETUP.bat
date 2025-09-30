@echo off
echo ========================================
echo Laravel AI API - Final Setup
echo ========================================
echo.

echo [1/3] Generating app key...
php artisan key:generate

echo.
echo [2/3] Creating database...
echo Make sure Herd MySQL is running!
echo Then create database: laravel_ai_api
echo.
pause

echo.
echo [3/3] Running migrations...
php artisan migrate

echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Starting server...
php artisan serve
