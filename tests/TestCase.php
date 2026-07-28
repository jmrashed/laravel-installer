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
        $this->artisan('vendor:publish', ['--tag' => 'laravelinstaller', '--force' => true]);

        // Mirror this repo's composer.lock into the testbench sandbox so
        // DependencyMiddleware sees a realistic "already installed" host app,
        // the same as it would in any properly composer-installed deployment.
        // testbench-core ships its own skeleton composer.lock, so this must
        // overwrite it rather than defer to file_exists().
        copy(dirname(__DIR__).'/composer.lock', base_path('composer.lock'));
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

    /**
     * ProgressMiddleware only unlocks a wizard step once the prior steps have
     * been visited in order, matching real usage. Walk the flow up to (and
     * including) $path so feature tests can reach a given page directly.
     *
     * The test HTTP client doesn't carry a session cookie between requests
     * by default, so ProgressTracker (keyed on session id) would see a
     * "fresh" session on every call. Pin a fixed, unencrypted session
     * cookie for the duration of the walk so state persists like it would
     * across real browser requests in the same session.
     */
    protected function visitInstallerStep(string $path)
    {
        $flow = [
            '/install',
            '/install/dependencies',
            '/install/server-requirements',
            '/install/permissions',
            '/install/environment-setting',
            '/install/database-setting',
            '/install/database-backup',
            '/install/cache-queue',
            '/install/installation-finished',
        ];

        \Illuminate\Cookie\Middleware\EncryptCookies::except(config('session.cookie'));
        $this->disableCookieEncryption();
        $this->withUnencryptedCookie(config('session.cookie'), \Illuminate\Support\Str::random(40));

        $response = null;

        foreach ($flow as $step) {
            $response = $this->get($step);

            if ($step === $path) {
                break;
            }
        }

        return $response;
    }
}