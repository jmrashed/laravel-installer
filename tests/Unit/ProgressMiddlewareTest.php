<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Jmrashed\LaravelInstaller\Middleware\ProgressMiddleware;
use Jmrashed\LaravelInstaller\Helpers\ProgressTracker;

class ProgressMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        ProgressTracker::reset();
    }

    protected function tearDown(): void
    {
        ProgressTracker::reset();
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

    public function test_calls_next_when_route_is_not_mapped_to_a_step()
    {
        $middleware = new ProgressMiddleware();
        $request = $this->requestForRoute('LaravelInstaller::some-unmapped-route');

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return response('next-called');
        });

        $this->assertTrue($called);
        $this->assertSame('next-called', $response->getContent());
    }

    public function test_calls_next_and_does_not_advance_step_when_on_current_step()
    {
        $middleware = new ProgressMiddleware();
        $request = $this->requestForRoute('LaravelInstaller::welcome');

        $called = false;
        $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return response('next-called');
        });

        $this->assertTrue($called);
        $this->assertSame('welcome', ProgressTracker::getProgress()['current_step']);
    }

    public function test_advances_progress_when_moving_to_the_next_forward_step()
    {
        $middleware = new ProgressMiddleware();
        $request = $this->requestForRoute('LaravelInstaller::dependencies');

        $called = false;
        $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return response('next-called');
        });

        $this->assertTrue($called);
        $progress = ProgressTracker::getProgress();
        $this->assertSame('dependencies', $progress['current_step']);
        $this->assertSame('in_progress', $progress['steps']['dependencies']['status']);
    }

    public function test_redirects_to_current_step_when_requested_step_cannot_be_resumed()
    {
        $middleware = new ProgressMiddleware();
        // 'database' is far ahead of the fresh default current_step ('welcome'),
        // so it cannot be resumed yet.
        $request = $this->requestForRoute('LaravelInstaller::database-setting');

        $response = $middleware->handle($request, function ($req) {
            return response('should-not-be-called');
        });

        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertSame(route('LaravelInstaller::welcome'), $response->getTargetUrl());
    }
}
