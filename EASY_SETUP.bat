@echo off
echo ========================================
echo Laravel AI API - EASY Setup (SQLite)
echo ========================================
echo.

echo [1/4] Creating SQLite database...
type nul > database\database.sqlite
echo SQLite database created!

echo.
echo [2/4] Updating .env for SQLite...
powershell -Command "(Get-Content .env) -replace 'DB_CONNECTION=mysql', 'DB_CONNECTION=sqlite' | Set-Content .env"
powershell -Command "(Get-Content .env) -replace 'DB_DATABASE=laravel_ai_api', 'DB_DATABASE=%cd%\database\database.sqlite' | Set-Content .env"
echo .env updated!

echo.
echo [3/4] Generating app key...
php artisan key:generate

echo.
echo [4/4] Running migrations...
php artisan migrate

echo.
echo ========================================
echo Setup Complete!
echo ========================================
echo.
echo Starting server...
php artisan serve
