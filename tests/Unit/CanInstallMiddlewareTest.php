<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Jmrashed\LaravelInstaller\Middleware\canInstall;

class CanInstallMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        $installedFlag = storage_path('installed');
        if (file_exists($installedFlag)) {
            unlink($installedFlag);
        }

        parent::tearDown();
    }

    public function test_not_installed_calls_next()
    {
        $middleware = new canInstall();
        $request = Request::create('/install');

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return response('next-called');
        });

        $this->assertTrue($called);
        $this->assertSame('next-called', $response->getContent());
    }

    public function test_already_installed_defaults_to_404()
    {
        config(['installer.installedAlreadyAction' => 'default']);
        file_put_contents(storage_path('installed'), '');

        $middleware = new canInstall();
        $request = Request::create('/install');

        try {
            $middleware->handle($request, function ($req) {
                return response('should-not-be-called');
            });
            $this->fail('Expected HttpException was not thrown.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    public function test_already_installed_with_404_action_aborts_404()
    {
        config(['installer.installedAlreadyAction' => '404']);
        file_put_contents(storage_path('installed'), '');

        $middleware = new canInstall();
        $request = Request::create('/install');

        try {
            $middleware->handle($request, function ($req) {
                return response('should-not-be-called');
            });
            $this->fail('Expected HttpException was not thrown.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(404, $e->getStatusCode());
        }
    }

    public function test_already_installed_with_abort_action_aborts_configured_code()
    {
        config(['installer.installedAlreadyAction' => 'abort']);
        config(['installer.installed.redirectOptions.abort.type' => 403]);
        file_put_contents(storage_path('installed'), '');

        $middleware = new canInstall();
        $request = Request::create('/install');

        try {
            $middleware->handle($request, function ($req) {
                return response('should-not-be-called');
            });
            $this->fail('Expected HttpException was not thrown.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_already_installed_with_route_action_redirects()
    {
        config(['installer.installedAlreadyAction' => 'route']);
        config(['installer.installed.redirectOptions.route.name' => 'LaravelInstaller::welcome']);
        file_put_contents(storage_path('installed'), '');

        $middleware = new canInstall();
        $request = Request::create('/install');

        $response = $middleware->handle($request, function ($req) {
            return response('should-not-be-called');
        });

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
    }

    public function test_already_installed_returns_true_when_flag_file_exists()
    {
        file_put_contents(storage_path('installed'), '');

        $middleware = new canInstall();

        $this->assertTrue($middleware->alreadyInstalled());
    }

    public function test_already_installed_returns_false_when_flag_file_missing()
    {
        $middleware = new canInstall();

        $this->assertFalse($middleware->alreadyInstalled());
    }

    public function test_access_token_restriction_blocks_request_without_token()
    {
        config(['installer.security.access_token' => 'secret-token']);

        $middleware = new canInstall();
        $request = Request::create('/install');

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $middleware->handle($request, function ($req) {
            return response('should-not-be-called');
        });
    }

    public function test_access_token_restriction_allows_request_with_valid_token()
    {
        config(['installer.security.access_token' => 'secret-token']);

        $middleware = new canInstall();
        $request = Request::create('/install?installer_token=secret-token');

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return response('next-called');
        });

        $this->assertTrue($called);
        $this->assertSame('next-called', $response->getContent());
    }

    public function test_ip_allowlist_restriction_blocks_request_from_other_ip()
    {
        config(['installer.security.allowed_ips' => ['203.0.113.5']]);

        $middleware = new canInstall();
        $request = Request::create('/install', 'GET', [], [], [], ['REMOTE_ADDR' => '198.51.100.9']);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        $middleware->handle($request, function ($req) {
            return response('should-not-be-called');
        });
    }
}
