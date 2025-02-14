# Laravel Web Installer Documentation

Welcome to the comprehensive guide for integrating the **Laravel Web Installer** into your Laravel project. This package streamlines the installation process, making it accessible even for users who may not be well-versed in Composer, SSH, or other complex setup techniques.

Whether you are building a Content Management System (CMS), application framework, or any Laravel-based solution, the Laravel Web Installer offers a user-friendly interface that simplifies the installation process.

## Prerequisites

Before diving into the installation, please ensure your environment meets the following requirements:

### Laravel Version
The Laravel Web Installer requires **Laravel version 8.0** or higher. You can check your current Laravel version by executing:

```bash
php artisan -V
```

**Expected Output:**
```
Laravel Framework 8.83.27
```

### Composer Version
You also need **Composer version 2.3** or newer. Verify your Composer version with the following command:

```bash
composer -V
```

**Expected Output:**
```
Composer version 2.3.7 2022-06-06 16:43:28
```

## Installation Steps

With the prerequisites verified, follow the steps below to integrate the Laravel Web Installer into your project.

### 1. Package Setup

Currently, the installation process for the Laravel Web Installer is limited to local packages. To proceed, download the package and move the folder to the root directory of your Laravel project. Next, you’ll need to add an entry to the repositories section in your `composer.json` file. If the `repositories` key doesn't exist, create it:

```json
"repositories": [
    {
        "type": "path",
        "url": "./laravel-installer"
    }
],
```

Also, set the `minimum-stability` to `dev` to allow for the installation of development versions:

```json
"minimum-stability": "dev",
```

### 2. Install the Package

Navigate to your Laravel project’s root directory in the terminal and run the following command to add the package to your dependencies:

```bash
composer require laravel-installer
```

This command will install the local version of the Laravel Web Installer in your project.

### 3. Customization

Now that the Laravel Web Installer is installed, you can customize the installer’s interface and workflow to suit your application’s needs. Follow the usage instructions provided below to integrate it effectively into your application.

## Configuration

### Adding Installation Steps

To enhance the installation process with additional steps, you can generate a new installation step class. For example, to create a step for application configuration, run:

```bash
php artisan make:step ApplicationConfig
```

This will create an `ApplicationConfig` class in the `app/Installer/Steps` directory. Customize the generated class (`app/Installer/Steps/ApplicationConfig.php`) to define the route, title, and description for your new step:

```php
<?php

namespace App\Installer\Steps;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Jmrashed\Installer\Steps\Step;
use Jmrashed\Installer\Steps\StepInterface;

class ApplicationConfig extends Step implements StepInterface
{
    public function __construct()
    {
        parent::__construct(
            route: 'step-route',
            title: 'Application Configuration',
            description: 'Configure your application settings.',
        );
    }

    public function view(): View
    {
        return view('web-installer::application-config', [
            'step' => $this,
        ]);
    }

    public function process(): RedirectResponse
    {
        return redirect()->route($this->next()->route());
    }
}
```

This command will also generate a controller for the step in `app/Http/Controllers/ApplicationConfigController.php`:

```php
<?php

namespace App\Http\Controllers;

use Jmrashed\Installer\Controllers\Controller;
use App\Installer\Steps\ApplicationConfig;

class ApplicationConfigController extends Controller
{
    public function __construct()
    {
        parent::__construct(new ApplicationConfig);
    }
}
```

Finally, a view file will be created for your new step at `resources/views/vendor/installer/application-config.blade.php`. Customize this file to suit your needs:

```php
@extends('web-installer::layout')

@section('content')
    <h2>Application Configuration</h2>
    <p>Your custom step content goes here. Build something amazing!</p>
@endsection
```

After generating the step, publish the configuration files to include your new step in the installer:

```bash
php artisan vendor:publish --tag=web-installer
```

Next, modify the `config/installer.php` file to include your new step in the steps array, placing it in the desired order:

```php
'steps' => [
    new \Jmrashed\Installer\Steps\Welcome,
    new \App\Installer\Steps\ApplicationConfig,
    new \Jmrashed\Installer\Steps\ServerRequirements,
    new \Jmrashed\Installer\Steps\Database,
    new \Jmrashed\Installer\Steps\Finished,
],
```

### Ordering Installation Steps

You have complete control over the order of installation steps. Simply rearrange the steps in the `config/installer.php` file. This allows you to tailor the user experience and streamline the installation flow.

```php
return [
    new \Jmrashed\Installer\Steps\Welcome,
    new \App\Installer\Steps\YourCustomStep,
    new \Jmrashed\Installer\Steps\ServerRequirements,
    new \Jmrashed\Installer\Steps\Database,
    new \App\Installer\Steps\AnotherCustomStep,
    new \Jmrashed\Installer\Steps\Finished,
];
```

### Route Prefix Customization

By default, the Laravel Web Installer prefixes all existing installer step routes with `install::`. If this conflicts with your existing routes or if you prefer a different prefix, you can easily adjust it by modifying the `routes_prefix` key in the `config/installer.php` file:

```php
'routes_prefix' => 'install::',
```

### Redirecting Upon Completion

After installation, the Laravel Web Installer typically redirects users to the home route. If your application does not have a `home` route or you want to redirect users elsewhere, change the `homepage` key in `config/installer.php`:

```php
'homepage' => 'dashboard',
```

### Security Measures

To enhance security during installation, the Laravel Web Installer checks for a specific file in the `storage` directory to ensure that installation only occurs once in production environments. You can customize the filename used for this check by modifying the `installed_key_name` key in the `config/installer.php` file.

For local environments, you can perform multiple installations as needed, ensuring flexibility for development and testing.

### Server Requirements

The Laravel Web Installer comes with a set of predefined server requirements essential for a successful installation. You can customize these requirements by modifying the `config/installer.php` file to add, modify, or remove specific server requirements:

```php
'requirements' => [
    'php_version' => '7.3.0',
    'extensions' => [
        'openssl',
        'pdo',
        'mbstring',
        'tokenizer',
        'json',
        'curl',
    ],
    'apache_modules' => [
        'mod_rewrite',
    ],
],
```

### Folder Permissions

The Laravel Web Installer also checks specific folder permissions required for your application to function correctly. You can modify the permissions in the `config/installer.php` file:

```php
'permissions' => [
    'storage/framework/' => '775',
    'storage/logs/' => '775',
    'bootstrap/cache/' => '775',
],
```

This flexibility allows you to adjust folder permissions to fit your specific application requirements.

### Migrations and Seeding

The installation process automatically handles running migrations and seeding the database, ensuring that necessary tables and data are created without manual intervention. If you wish to disable this automated process, simply comment out the migration and seeding lines in the steps key of the `config/installer.php`:

```php
DB::transaction(function () {
    Artisan::call('migrate', ['--force' => true]);
    Artisan::call('db:seed', ['--force' => true]);
});
```

### Environment Variables

The Laravel Web Installer provides an intuitive interface for configuring environment variables and application settings. You can customize the variables displayed during installation by editing the `environment` array in `config/installer.php`:

```php
'environment' => [
    'APP_NAME' => 'Laravel',
    'APP_ENV' => 'production',
    'APP_KEY' => '',
    'APP_DEBUG' => 'true',
    'APP_URL' => 'http://localhost',
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_DATABASE' => 'laravel',
    'DB_USERNAME' => 'root',
    'DB_PASSWORD' => '',
],
```

### Events and Listeners

Throughout the installation process, the Laravel Web Installer triggers various events that allow you to hook into the installation workflow. You can listen for these events to perform additional actions or logging, ensuring that your application is as responsive as possible.
 