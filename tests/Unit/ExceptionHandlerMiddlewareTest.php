<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jmrashed\LaravelInstaller\Middleware\ExceptionHandlerMiddleware;

class ExceptionHandlerMiddlewareTest extends TestCase
{
    public function test_returns_response_from_next_when_no_exception_is_thrown()
    {
        Log::shouldReceive('channel')->with('installer')->andReturnSelf();
        Log::shouldReceive('info');

        $middleware = new ExceptionHandlerMiddleware();
        $request = Request::create('/install');

        $response = $middleware->handle($request, function ($req) {
            return response('all-good', 200);
        });

        $this->assertSame('all-good', $response->getContent());
        $this->assertSame(200, $response->getStatusCode());
    }

    public function test_catches_exception_and_delegates_to_exception_handler_json_response()
    {
        Log::shouldReceive('channel')->with('installer')->andReturnSelf();
        Log::shouldReceive('info');
        Log::shouldReceive('error');

        $middleware = new ExceptionHandlerMiddleware();
        $request = Request::create('/install', 'GET', [], [], [], ['HTTP_ACCEPT' => 'application/json']);

        $response = $middleware->handle($request, function ($req) {
            throw new \RuntimeException('boom');
        });

        $this->assertSame(500, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Installation Error', $data['error']);
        $this->assertSame('An unexpected error occurred during installation.', $data['message']);
    }

    public function test_catches_exception_and_delegates_to_exception_handler_web_response()
    {
        Log::shouldReceive('channel')->with('installer')->andReturnSelf();
        Log::shouldReceive('info');
        Log::shouldReceive('error');

        $middleware = new ExceptionHandlerMiddleware();
        $request = Request::create('/install');

        $response = $middleware->handle($request, function ($req) {
            throw new \RuntimeException('boom');
        });

        $this->assertSame(500, $response->getStatusCode());
    }
}
