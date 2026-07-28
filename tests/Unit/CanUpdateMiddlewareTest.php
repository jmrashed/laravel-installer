<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Jmrashed\LaravelInstaller\Middleware\canUpdate;

class CanUpdateMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        $installedFlag = storage_path('installed');
        if (file_exists($installedFlag)) {
            unlink($installedFlag);
        }

        parent::tearDown();
    }

    public function test_updater_disabled_aborts_404()
    {
        config(['installer.updaterEnabled' => 'false']);

        $middleware = new canUpdate();
        $request = Request::create('/update');

        try {
            $middleware->handle($request, function ($req) {
                return response('should-not-be-called');
            });
            $this->fail('Expected HttpException was not thrown.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    public function test_updater_enabled_but_not_installed_redirects_to_welcome()
    {
        config(['installer.updaterEnabled' => 'true']);

        $middleware = new canUpdate();
        $request = Request::create('/update');

        $response = $middleware->handle($request, function ($req) {
            return response('should-not-be-called');
        });

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertSame(route('LaravelInstaller::welcome'), $response->getTargetUrl());
    }

    public function test_updater_enabled_installed_and_already_updated_aborts_404()
    {
        config(['installer.updaterEnabled' => 'true']);
        file_put_contents(storage_path('installed'), '');
        DB::table('migrations')->truncate();

        $middleware = new canUpdate();
        $request = Request::create('/update');

        try {
            $middleware->handle($request, function ($req) {
                return response('should-not-be-called');
            });
            $this->fail('Expected HttpException was not thrown.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    public function test_updater_enabled_installed_and_not_updated_calls_next()
    {
        config(['installer.updaterEnabled' => 'true']);
        file_put_contents(storage_path('installed'), '');

        $middleware = new canUpdate();
        $request = Request::create('/update');

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return response('next-called');
        });

        $this->assertTrue($called);
        $this->assertSame('next-called', $response->getContent());
    }

    public function test_already_updated_returns_true_when_migration_counts_match()
    {
        DB::table('migrations')->truncate();

        $middleware = new canUpdate();

        $this->assertTrue($middleware->alreadyUpdated());
    }

    public function test_already_updated_returns_false_when_migration_counts_differ()
    {
        $middleware = new canUpdate();

        $this->assertFalse($middleware->alreadyUpdated());
    }
}
