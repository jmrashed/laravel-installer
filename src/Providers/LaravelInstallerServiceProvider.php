<?php

namespace Jmrashed\LaravelInstaller\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Jmrashed\LaravelInstaller\Middleware\canInstall;
use Jmrashed\LaravelInstaller\Middleware\canUpdate;
use Jmrashed\LaravelInstaller\Middleware\SecurityMiddleware;
use Jmrashed\LaravelInstaller\Middleware\PerformanceMiddleware;
use Jmrashed\LaravelInstaller\Middleware\ProgressMiddleware;
use Jmrashed\LaravelInstaller\Middleware\DependencyMiddleware;
use Jmrashed\LaravelInstaller\Middleware\ExceptionHandlerMiddleware;

class LaravelInstallerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->publishFiles();
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
        $this->loadRoutesFrom(__DIR__.'/../Routes/backup.php');
        $this->loadViewsFrom(__DIR__.'/../Views', 'installer');
        $this->loadTranslationsFrom(__DIR__.'/../Lang', 'installer');
        
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                \Jmrashed\LaravelInstaller\Commands\InstallerRunCommand::class,
                \Jmrashed\LaravelInstaller\Commands\ClearInstallerCaches::class,
            ]);
        }
    }

    /**
     * Bootstrap the application events.
     *
     * @param  \Illuminate\Routing\Router  $router
     */
    public function boot(Router $router)
    {
        $router->middlewareGroup('install', [CanInstall::class]);
        $router->middlewareGroup('update', [CanUpdate::class]);
        
        // Register new v2.0.0 middleware
        $router->aliasMiddleware('security', SecurityMiddleware::class);
        $router->aliasMiddleware('performance', PerformanceMiddleware::class);
        $router->aliasMiddleware('progress', ProgressMiddleware::class);
        $router->aliasMiddleware('dependency', DependencyMiddleware::class);
        $router->aliasMiddleware('exception-handler', ExceptionHandlerMiddleware::class);
        
        $this->publishFiles();
    }

    /**
     * Publish config file for the installer.
     *
     * @return void
     */
    protected function publishFiles()
    {
        // Publish config files
        $this->publishes([
            __DIR__.'/../Config/installer.php' => base_path('config/installer.php'),
            __DIR__.'/../Config/audit.php' => base_path('config/audit.php'),
            __DIR__.'/../Config/logging.php' => base_path('config/logging.php'),
        ], 'installer-config');

        // Publish assets and force replace if they exist
        $this->publishes([
            __DIR__.'/../assets' => public_path('installer'),
        ], 'laravelinstaller', 'force');

        // Publish views and force replace if they exist
        $this->publishes([
            __DIR__.'/../Views' => base_path('resources/views/vendor/installer'),
        ], 'laravelinstaller', 'force');

        // Publish language files and force replace if they exist
        $this->publishes([
            __DIR__.'/../Lang' => base_path('resources/lang'),
        ], 'laravelinstaller', 'force');
    }
}
