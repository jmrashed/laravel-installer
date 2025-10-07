<?php

/**
 * Quick Test Script for Laravel Installer Package
 */

echo "Laravel Installer Package - Quick Test\n";
echo "====================================\n\n";

// Test 1: Check if we're in the right directory
if (!file_exists('test-app/artisan')) {
    echo "❌ Test app not found. Run create-test-app.bat first.\n";
    exit(1);
}

echo "✅ Test app found\n";

// Test 2: Check if package is installed
$composerJson = json_decode(file_get_contents('test-app/composer.json'), true);
if (!isset($composerJson['require']['jmrashed/laravel-installer'])) {
    echo "❌ Package not installed in test app\n";
    exit(1);
}

echo "✅ Package installed in test app\n";

// Test 3: Check if routes are available
chdir('test-app');
$routes = shell_exec('php artisan route:list --name=install 2>&1');
if (strpos($routes, 'install') === false) {
    echo "❌ Installer routes not found\n";
    exit(1);
}

echo "✅ Installer routes registered\n";

// Test 4: Test HTTP response
$testUrl = 'http://127.0.0.1:8000/install';
echo "\n📋 Manual Testing Steps:\n";
echo "1. Run: cd test-app && php artisan serve\n";
echo "2. Visit: {$testUrl}\n";
echo "3. Follow the installation wizard\n";
echo "4. Test each step of the installation process\n\n";

echo "✅ Package is ready for testing!\n";
echo "🚀 Start the server and visit the installer URL above.\n";