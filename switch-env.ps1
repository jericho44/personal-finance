# Switch Environment Script
# Usage: .\switch-env.ps1 local   -> Switch to local (Laragon)
# Usage: .\switch-env.ps1 docker  -> Switch to Docker

param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("local", "docker")]
    [string]$Environment
)

$projectRoot = $PSScriptRoot

$envFile = Join-Path $projectRoot ".env"
$sourceFile = Join-Path $projectRoot ".env.$Environment"

if (-not (Test-Path $sourceFile)) {
    Write-Host "ERROR: File .env.$Environment not found!" -ForegroundColor Red
    exit 1
}

Copy-Item -Path $sourceFile -Destination $envFile -Force

Write-Host ""
Write-Host "==============================================" -ForegroundColor Cyan
Write-Host "  Environment switched to: $($Environment.ToUpper())" -ForegroundColor Green
Write-Host "==============================================" -ForegroundColor Cyan
Write-Host ""

if ($Environment -eq "local") {
    Write-Host "  DB_HOST      = 127.0.0.1" -ForegroundColor Yellow
    Write-Host "  REDIS_HOST   = 127.0.0.1" -ForegroundColor Yellow
    Write-Host "  CACHE_DRIVER = file" -ForegroundColor Yellow
    Write-Host "  QUEUE        = sync" -ForegroundColor Yellow
    Write-Host "  SESSION      = file" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "  Run with Laragon:" -ForegroundColor Cyan
    Write-Host "    php artisan serve" -ForegroundColor White
    Write-Host "    npm run dev" -ForegroundColor White
} else {
    Write-Host "  DB_HOST      = postgresql (container)" -ForegroundColor Yellow
    Write-Host "  REDIS_HOST   = redis (container)" -ForegroundColor Yellow
    Write-Host "  CACHE_DRIVER = redis" -ForegroundColor Yellow
    Write-Host "  QUEUE        = redis" -ForegroundColor Yellow
    Write-Host "  SESSION      = redis" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "  Run with Docker:" -ForegroundColor Cyan
    Write-Host "    docker compose up -d" -ForegroundColor White
    Write-Host "    Access: http://localhost:8000" -ForegroundColor White
}

Write-Host ""
Write-Host "  Don't forget to clear cache:" -ForegroundColor Gray
if ($Environment -eq "local") {
    Write-Host "    php artisan config:clear" -ForegroundColor White
} else {
    Write-Host "    docker compose exec app php artisan config:clear" -ForegroundColor White
}
Write-Host ""
