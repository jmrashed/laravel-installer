# Laravel Installer

[![Latest Stable Version](https://poser.pugx.org/jmrashed/laravel-installer/v/stable)](https://packagist.org/packages/jmrashed/laravel-installer)
[![Total Downloads](https://poser.pugx.org/jmrashed/laravel-installer/downloads)](https://packagist.org/packages/jmrashed/laravel-installer)
[![Monthly Downloads](https://poser.pugx.org/jmrashed/laravel-installer/d/monthly)](https://packagist.org/packages/jmrashed/laravel-installer)
[![License](https://poser.pugx.org/jmrashed/laravel-installer/license)](https://packagist.org/packages/jmrashed/laravel-installer)
[![Stars](https://img.shields.io/github/stars/jmrashed/laravel-installer.svg?style=social&label=Stars)](https://github.com/jmrashed/laravel-installer)
[![Forks](https://img.shields.io/github/forks/jmrashed/laravel-installer.svg?style=social&label=Forks)](https://github.com/jmrashed/laravel-installer)

**Laravel Installer** is a comprehensive installation package designed to streamline the process of setting up your Laravel project, handling everything from environment configuration to database migration and purchase code validation.

---

## 📊 Statistics

- **Total Downloads**: ![Total Downloads](https://poser.pugx.org/jmrashed/laravel-installer/downloads)
- **Monthly Downloads**: ![Monthly Downloads](https://poser.pugx.org/jmrashed/laravel-installer/d/monthly)
- **GitHub Stars**: ![Stars](https://img.shields.io/github/stars/jmrashed/laravel-installer.svg?style=social)
- **GitHub Forks**: ![Forks](https://img.shields.io/github/forks/jmrashed/laravel-installer.svg?style=social)

---

## 🌟 Features

- **System Requirements Check**: Verifies PHP version and required extensions before proceeding with the installation.
- **Environment File Setup**: Automatically creates and configures the `.env` file with your database credentials and other necessary settings.
- **Database Configuration**: Offers options to either run migrations and seeders or import an SQL dump to set up the database.
- **Purchase Code Validation**: Integrated validation logic for license or purchase code verification.
- **User-Friendly Interface**: Provides a simple, step-by-step guided installer with a welcome screen and status updates.

## 🛠️ Installation

Install the package via Composer:

```bash
composer require jmrashed/laravel-installer
```

After installation, publish the configuration file:

```bash
php artisan vendor:publish --tag=installer-config
```

## 🚀 How to Use

To run the installer, simply execute:

```bash
php artisan installer:run
```

The installer will guide you through the following steps:

1. **System Requirements Check**: Ensures your PHP version and necessary extensions are installed.
2. **Create `.env` File**: Prompts for database details and automatically creates the `.env` file.
3. **Database Setup**: Choose between running database migrations and seeders or importing a SQL dump.
4. **Purchase Code Validation**: If enabled, the user is required to enter their purchase code.
5. **Installation Completion**: Displays a success message and other helpful information.

### Manual Commands

- Run migrations and seeders manually:

  ```bash
  php artisan migrate --seed
  ```

- Import an SQL dump file directly:

  ```bash
  php artisan installer:import-sql /path/to/dump.sql
  ```



  # Directory structure of: .
- 📁 **src/**
   - 📁 **Config/**
      - 📄 installer.php
   - 📁 **Controllers/**
      - 📄 DatabaseController.php
      - 📄 EnvironmentController.php
   - 📁 **Events/**
      - 📄 EnvironmentSaved.php
      - 📄 LaravelInstallerFinished.php
   - 📁 **Helpers/**
      - 📄 DatabaseManager.php
      - 📄 EnvironmentManager.php
   - 📁 **Lang/**
      - 📁 **ar/**
         - 📄 installer_messages.php
      - 📁 **de/**
         - 📄 installer_messages.php
      - 📁 **en/**
         - 📄 installer_messages.php
      - 📁 **es/**
         - 📄 installer_messages.php
      - 📁 **et/**
         - 📄 installer_messages.php
      - 📁 **fa/**
         - 📄 installer_messages.php
      - 📁 **fr/**
         - 📄 installer_messages.php
      - 📁 **gr/**
         - 📄 installer_messages.php
      - 📁 **id/**
         - 📄 installer_messages.php
      - 📁 **it/**
         - 📄 installer_messages.php
      - 📁 **nl/**
         - 📄 installer_messages.php
      - 📁 **pl/**
         - 📄 installer_messages.php
      - 📁 **pt/**
         - 📄 installer_messages.php
      - 📁 **pt-br/**
         - 📄 installer_messages.php
      - 📁 **ro/**
         - 📄 installer_messages.php
      - 📁 **ru/**
         - 📄 installer_messages.php
      - 📁 **th/**
         - 📄 installer_messages.php
      - 📁 **tr/**
         - 📄 installer_messages.php
      - 📁 **zh-CN/**
         - 📄 installer_messages.php
      - 📁 **zh-TW/**
         - 📄 installer_messages.php
   - 📁 **Middleware/**
      - 📄 canInstall.php
      - 📄 canUpdate.php
   - 📁 **Providers/**
      - 📄 LaravelInstallerServiceProvider.php
   - 📁 **Routes/**
      - 📄 web.php
   - 📁 **Views/**
      - 📁 **layouts/**
         - 📄 master-update.blade.php
         - 📄 master.blade.php
      - 📁 **update/**
         - 📄 finished.blade.php
         - 📄 overview.blade.php
      - 📄 environment-classic.blade.php
      - 📄 environment-wizard.blade.php
   - 📁 **assets/**
      - 📁 **css/**
         - 📁 **sass/**
            - 📄 _variables.sass
            - 📄 style.sass
         - 📁 **scss/**
            - 📁 **font-awesome/**
               - 📄 _animated.scss
               - 📄 _bordered-pulled.scss
            - 📄 _variables.scss
            - 📄 style.scss
         - 📄 style.css
         - 📄 style.css.map
      - 📁 **fonts/**
         - 📄 FontAwesome.otf
         - 📄 fontawesome-webfont.eot
      - 📁 **img/**
         - 📁 **favicon/**
            - 📄 favicon-16x16.png
            - 📄 favicon-32x32.png
         - 📄 background.png
         - 📄 pattern.png
- 📄 LICENSE
- 📄 README.md



## ⚙️ Configuration

Once published, the configuration file can be found at:

```
config/installer.php
```

Here you can customize the installer behavior, such as adding custom checks or configuring the SQL import path.

## 📥 Purchase Code Validation

If you are using a purchase code validation system, the installer will prompt users to provide their code. You can modify the validation logic based on your licensing system:

```php
if (!$this->validatePurchaseCode($code)) {
    throw new Exception('Invalid purchase code.');
}
```

## 📷 Screenshots

Here are some screenshots of the installer process:

| **Welcome Screen** | **Database Setup** | **Purchase Code Validation** |
|--------------------|--------------------|------------------------------|
| ![Welcome](path_to_screenshot) | ![DB Setup](path_to_screenshot) | ![Purchase Code](path_to_screenshot) |

## 🔧 System Requirements

To ensure the package works as expected, ensure your environment meets these requirements:

- **PHP**: 8.0 or higher
- **Laravel**: 9.0 or higher
- **PHP Extensions**: 
  - `mbstring`
  - `openssl`
  - `pdo`
  - `tokenizer`
  - `xml`
  - `ctype`
  - `json`

## 🤝 Contributing

We welcome contributions! Feel free to fork the repository and submit pull requests. To contribute:

1. Fork the project.
2. Create a new branch for your feature: `git checkout -b feature-branch`.
3. Commit your changes: `git commit -m 'Add feature'`.
4. Push the changes: `git push origin feature-branch`.
5. Submit a pull request.

## 📝 License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## 📬 Support

If you encounter any issues or have questions, feel free to open an issue on the [GitHub repository](https://github.com/jmrashed/laravel-installer/issues) or reach out directly.