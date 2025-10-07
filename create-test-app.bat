@echo off
echo Creating test Laravel application...

:: Remove existing test-app if exists
if exist test-app rmdir /s /q test-app

:: Create a fresh Laravel app with stable version
composer create-project laravel/laravel:^11.0 test-app --prefer-dist

:: Navigate to the test app
cd test-app

:: Add the local package to composer.json
echo Adding local package...
composer config repositories.local path ../
composer config minimum-stability dev
composer config prefer-stable true

:: Require the package
composer require jmrashed/laravel-installer:@dev

:: Publish the package assets
php artisan vendor:publish --provider="Jmrashed\LaravelInstaller\Providers\LaravelInstallerServiceProvider" --force

:: Clear caches
php artisan config:clear
php artisan view:clear
php artisan route:clear

echo Test application created successfully!
echo Navigate to test-app directory and run: php artisan serve
echo Then visit: http://localhost:8000/install