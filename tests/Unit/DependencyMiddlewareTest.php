<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Jmrashed\LaravelInstaller\Middleware\DependencyMiddleware;

class DependencyMiddlewareTest extends TestCase
{
    protected function tearDown(): void
    {
        $lockPath = base_path('composer.lock');
        if (file_exists($lockPath)) {
            unlink($lockPath);
        }

        parent::tearDown();
    }

    protected function requestForRoute($name)
    {
        $request = Request::create('/install/some-page');
        $route = new Route('GET', 'some-page', []);
        $route->name($name);
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        return $request;
    }

    public function test_skips_check_for_welcome_route()
    {
        $middleware = new DependencyMiddleware();
        $request = $this->requestForRoute('LaravelInstaller::welcome');

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return response('next-called');
        });

        $this->assertTrue($called);
        $this->assertSame('next-called', $response->getContent());
    }

    public function test_skips_check_for_dependency_api_routes()
    {
        $middleware = new DependencyMiddleware();

        foreach (['LaravelInstaller::api.dependencies.check', 'LaravelInstaller::api.dependencies.install'] as $routeName) {
            $request = $this->requestForRoute($routeName);

            $called = false;
            $middleware->handle($request, function ($req) use (&$called) {
                $called = true;

                return response('next-called');
            });

            $this->assertTrue($called, "Expected next() to be called for route {$routeName}");
        }
    }

    public function test_redirects_to_dependencies_page_when_critical_dependency_missing()
    {
        // TestCase::setUp() mirrors a real composer.lock into the sandbox so
        // DependencyMiddleware behaves realistically when wired into the app;
        // remove it here to exercise the "missing" branch specifically.
        unlink(base_path('composer.lock'));

        $middleware = new DependencyMiddleware();
        $request = $this->requestForRoute('LaravelInstaller::server-requirements');

        $response = $middleware->handle($request, function ($req) {
            return response('should-not-be-called');
        });

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertSame(route('LaravelInstaller::dependencies'), $response->getTargetUrl());
        $this->assertTrue($response->getSession()->has('error'));
    }

    public function test_calls_next_when_all_critical_dependencies_are_compatible()
    {
        file_put_contents(base_path('composer.lock'), json_encode([
            'packages' => [
                [
                    'name' => 'laravel/framework',
                    'version' => '11.9.0',
                ],
            ],
        ]));

        $middleware = new DependencyMiddleware();
        $request = $this->requestForRoute('LaravelInstaller::server-requirements');

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return response('next-called');
        });

        $this->assertTrue($called);
        $this->assertSame('next-called', $response->getContent());
    }
}
