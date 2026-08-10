param([switch]$DryRun)
$ErrorActionPreference = 'Stop'
if ($env:APP_ENV -ne 'production') { Write-Host 'Deploy script expects APP_ENV=production.'; exit 1 }
if ($env:APP_DEBUG -eq 'true') { Write-Host 'APP_DEBUG must be false.'; exit 1 }
if (-not $env:APP_KEY) { Write-Host 'APP_KEY is required.'; exit 1 }
if ($DryRun) { Write-Host 'Dry run: config cache, migration and worker restart would run.'; exit 0 }
php artisan down --render="errors::503" --retry=60
try {
    composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
    php artisan migrate --force
    php artisan optimize
    npm ci
    npm run build
    php artisan up
} catch {
    php artisan up
    throw
}
