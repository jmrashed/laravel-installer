<?php

namespace Jmrashed\LaravelInstaller\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Jmrashed\LaravelInstaller\Middleware\canInstall;
use Jmrashed\LaravelInstaller\Middleware\canUpdate;

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
        $this->loadViewsFrom(__DIR__.'/../Views', 'installer');
        $this->loadTranslationsFrom(__DIR__.'/../Lang', 'installer');
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
        
        $this->publishFiles();
    }

    /**
     * Publish config file for the installer.
     *
     * @return void
     */
    protected function publishFiles()
    {
        // Publish config and force replace if it exists
        $this->publishes([
            __DIR__.'/../Config/installer.php' => base_path('config/installer.php'),
        ], 'laravelinstaller', 'force');

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
