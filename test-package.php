<?php

/**
 * Manual Testing Script for Laravel Installer Package
 * 
 * Run this script to test the package in a fresh Laravel installation
 */

echo "Laravel Installer Package Testing Script\n";
echo "=====================================\n\n";

// Test 1: Check if package can be installed
echo "1. Testing package installation...\n";
$composerInstall = shell_exec('composer install --no-dev 2>&1');
if (strpos($composerInstall, 'error') === false) {
    echo "✓ Package dependencies installed successfully\n";
} else {
    echo "✗ Package installation failed\n";
    echo $composerInstall . "\n";
}

// Test 2: Run PHPUnit tests
echo "\n2. Running PHPUnit tests...\n";
$testResult = shell_exec('./vendor/bin/phpunit 2>&1');
echo $testResult;

// Test 3: Run PHP CS Fixer
echo "\n3. Checking code style...\n";
$csResult = shell_exec('./vendor/bin/php-cs-fixer fix --dry-run --diff 2>&1');
if (strpos($csResult, 'Fixed') === false) {
    echo "✓ Code style is consistent\n";
} else {
    echo "⚠ Code style issues found:\n";
    echo $csResult . "\n";
}

// Test 4: Run PHPStan
echo "\n4. Running static analysis...\n";
$phpstanResult = shell_exec('./vendor/bin/phpstan analyse src --level=5 2>&1');
echo $phpstanResult;

// Test 5: Check package structure
echo "\n5. Checking package structure...\n";
$requiredFiles = [
    'src/Providers/LaravelInstallerServiceProvider.php',
    'src/Controllers/WelcomeController.php',
    'src/Routes/web.php',
    'composer.json',
    'README.md'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✓ {$file} exists\n";
    } else {
        echo "✗ {$file} missing\n";
    }
}

echo "\nTesting completed!\n";