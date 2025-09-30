@echo off
echo Fixing Laravel setup...
echo.

echo Creating .env file...
copy .env.example .env

echo.
echo Generating app key...
php artisan key:generate

echo.
echo Done! Now run migrations:
echo php artisan migrate
echo.
echo Then start server:
echo php artisan serve
echo.
pause
