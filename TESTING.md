# Testing Checklist for Laravel Installer Package

## Pre-Release Testing Steps

### 1. Automated Testing
```bash
# Install dependencies
composer install

# Run all tests
composer test

# Run with coverage
composer test-coverage

# Check code style
composer cs-check

# Fix code style
composer cs-fix

# Run static analysis
composer phpstan

# Run all quality checks
composer quality
```

### 2. Manual Testing

#### Package Installation Test
```bash
# Create fresh Laravel app
composer create-project laravel/laravel test-installer
cd test-installer

# Add local package
composer config repositories.local path ../laravel-installer
composer require jmrashed/laravel-installer:@dev

# Publish assets
php artisan vendor:publish --provider="Jmrashed\LaravelInstaller\Providers\LaravelInstallerServiceProvider"
```

#### Installation Flow Test
1. Start server: `php artisan serve`
2. Visit: `http://localhost:8000/install`
3. Test each step:
   - [ ] Welcome page loads
   - [ ] Requirements check passes
   - [ ] Permissions check passes
   - [ ] Environment setup works
   - [ ] Database configuration works
   - [ ] Purchase code validation (if enabled)
   - [ ] Final installation completes

#### Error Handling Test
- [ ] Test with missing PHP extensions
- [ ] Test with wrong file permissions
- [ ] Test with invalid database credentials
- [ ] Test with invalid purchase code
- [ ] Test installation when already installed

### 3. Cross-Platform Testing
- [ ] Windows (XAMPP/Laragon)
- [ ] macOS (Valet/MAMP)
- [ ] Linux (Apache/Nginx)

### 4. Laravel Version Compatibility
- [ ] Laravel 9.x
- [ ] Laravel 10.x  
- [ ] Laravel 11.x

### 5. PHP Version Compatibility
- [ ] PHP 8.1
- [ ] PHP 8.2
- [ ] PHP 8.3

### 6. Database Testing
- [ ] MySQL
- [ ] PostgreSQL
- [ ] SQLite
- [ ] SQL Server

### 7. Security Testing
- [ ] CSRF protection works
- [ ] Input validation works
- [ ] File upload security
- [ ] SQL injection prevention
- [ ] XSS prevention

### 8. Performance Testing
- [ ] Installation completes under 30 seconds
- [ ] Memory usage stays reasonable
- [ ] No memory leaks during installation

### 9. Documentation Testing
- [ ] README instructions work
- [ ] Installation guide is accurate
- [ ] Configuration examples work
- [ ] API documentation is correct

### 10. Package Distribution Testing
```bash
# Test package creation
composer archive --format=zip

# Test from Packagist (after publishing)
composer create-project laravel/laravel fresh-test
cd fresh-test
composer require jmrashed/laravel-installer
```

## Release Checklist

### Before Release
- [ ] All tests pass
- [ ] Code coverage > 80%
- [ ] No critical security issues
- [ ] Documentation updated
- [ ] CHANGELOG.md updated
- [ ] Version number updated in composer.json

### Release Process
- [ ] Create release tag
- [ ] Push to GitHub
- [ ] Verify Packagist auto-update
- [ ] Test installation from Packagist
- [ ] Update documentation site (if applicable)

### Post Release
- [ ] Monitor for issues
- [ ] Respond to user feedback
- [ ] Plan next version improvements