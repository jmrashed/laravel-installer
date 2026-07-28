<?php

namespace Tests;

use Jmrashed\LaravelInstaller\Providers\LaravelInstallerServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        $this->artisan('config:clear');
        $this->artisan('view:clear');
        $this->artisan('route:clear');
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelInstallerServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
    }
}