<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Jmrashed\LaravelInstaller\Middleware\PerformanceMiddleware;

class PerformanceMiddlewareTest extends TestCase
{
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

    public function test_adds_performance_headers_to_response()
    {
        $middleware = new PerformanceMiddleware();
        $request = $this->requestForRoute('LaravelInstaller::welcome');

        $response = $middleware->handle($request, function ($req) {
            usleep(1000);

            return response('ok');
        });

        $this->assertSame('ok', $response->getContent());
        $this->assertTrue($response->headers->has('X-Execution-Time'));
        $this->assertTrue($response->headers->has('X-Memory-Usage'));
        $this->assertTrue($response->headers->has('X-Peak-Memory'));
    }

    public function test_uses_unknown_operation_name_when_route_has_no_name()
    {
        $middleware = new PerformanceMiddleware();
        $request = Request::create('/install/some-page');
        $route = new Route('GET', 'some-page', []);
        $request->setRouteResolver(function () use ($route) {
            return $route;
        });

        $response = $middleware->handle($request, function ($req) {
            return response('ok');
        });

        $this->assertSame('ok', $response->getContent());
        $this->assertTrue($response->headers->has('X-Execution-Time'));
    }

    public function test_formats_memory_usage_in_bytes_when_small()
    {
        $middleware = new PerformanceMiddleware();
        $method = new \ReflectionMethod($middleware, 'formatBytes');
        $method->setAccessible(true);

        $this->assertSame('500B', $method->invoke($middleware, 500));
    }

    public function test_formats_memory_usage_in_kilobytes()
    {
        $middleware = new PerformanceMiddleware();
        $method = new \ReflectionMethod($middleware, 'formatBytes');
        $method->setAccessible(true);

        $this->assertSame('2KB', $method->invoke($middleware, 2048));
    }

    public function test_formats_memory_usage_in_megabytes()
    {
        $middleware = new PerformanceMiddleware();
        $method = new \ReflectionMethod($middleware, 'formatBytes');
        $method->setAccessible(true);

        $this->assertSame('2MB', $method->invoke($middleware, 2 * 1024 * 1024));
    }
}
