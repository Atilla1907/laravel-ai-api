Write-Host "========================================" -ForegroundColor Cyan
Write-Host "Laravel AI API - Setup" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "[1/5] Installing Composer dependencies..." -ForegroundColor Yellow
composer install
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: Composer install failed!" -ForegroundColor Red
    pause
    exit 1
}

Write-Host ""
Write-Host "[2/5] Creating .env file..." -ForegroundColor Yellow
if (!(Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host ".env file created!" -ForegroundColor Green
} else {
    Write-Host ".env already exists!" -ForegroundColor Green
}

Write-Host ""
Write-Host "[3/5] Generating application key..." -ForegroundColor Yellow
php artisan key:generate

Write-Host ""
Write-Host "[4/5] Database setup..." -ForegroundColor Yellow
Write-Host "Make sure MySQL is running and database 'laravel_ai_api' exists!" -ForegroundColor Magenta
Write-Host "Press any key to continue with migrations..."
pause

php artisan migrate

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "Setup Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "To start the server, run: php artisan serve" -ForegroundColor Cyan
Write-Host ""
