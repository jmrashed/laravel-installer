<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Jmrashed\LaravelInstaller\Middleware\SecurityMiddleware;

class SecurityMiddlewareTest extends TestCase
{
    public function test_clean_request_passes_through_and_adds_security_headers()
    {
        $middleware = new SecurityMiddleware();
        $request = Request::create('/install/environment-setting', 'GET', ['app_name' => 'My App']);
        $request->server->set('REMOTE_ADDR', '10.0.0.1');

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return response('ok');
        });

        $this->assertTrue($called);
        $this->assertSame('ok', $response->getContent());
        $this->assertSame('nosniff', $response->headers->get('X-Content-Type-Options'));
        $this->assertSame('DENY', $response->headers->get('X-Frame-Options'));
        $this->assertSame('1; mode=block', $response->headers->get('X-XSS-Protection'));
        $this->assertSame('strict-origin-when-cross-origin', $response->headers->get('Referrer-Policy'));
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
    }

    public function test_request_with_suspicious_content_is_aborted_with_403()
    {
        $middleware = new SecurityMiddleware();
        $request = Request::create('/install/environment-setting', 'GET', [
            'comment' => '<script>alert(1)</script>',
        ]);
        $request->server->set('REMOTE_ADDR', '10.0.0.2');

        try {
            $middleware->handle($request, function ($req) {
                return response('should-not-be-called');
            });
            $this->fail('Expected HttpException was not thrown.');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertSame(403, $e->getStatusCode());
        }
    }

    public function test_rate_limited_request_returns_429_json_response()
    {
        RateLimiter::shouldReceive('tooManyAttempts')
            ->with('installer:10.0.0.3', 20)
            ->andReturn(true);
        RateLimiter::shouldReceive('availableIn')
            ->with('installer:10.0.0.3')
            ->andReturn(45);

        $middleware = new SecurityMiddleware();
        $request = Request::create('/install/environment-setting', 'GET');
        $request->server->set('REMOTE_ADDR', '10.0.0.3');

        $called = false;
        $response = $middleware->handle($request, function ($req) use (&$called) {
            $called = true;

            return response('should-not-be-called');
        });

        $this->assertFalse($called);
        $this->assertSame(429, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertSame('Too many requests', $data['error']);
        $this->assertSame(45, $data['retry_after']);
    }
}
