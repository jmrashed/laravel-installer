<?php

namespace Jmrashed\LaravelInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Jmrashed\LaravelInstaller\Helpers\SecurityHelper;

class SecurityMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check for suspicious patterns in request
        if ($this->containsSuspiciousContent($request)) {
            Log::alert('Suspicious request detected during installation', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);
            
            abort(403, 'Suspicious request detected');
        }

        // Rate limiting check
        $rateLimitKey = 'installer:' . $request->ip();
        $retryAfter = SecurityHelper::checkRateLimit($rateLimitKey, 20, 5);
        
        if ($retryAfter) {
            return response()->json([
                'error' => 'Too many requests',
                'retry_after' => $retryAfter
            ], 429);
        }

        // Add security headers to response
        $response = $next($request);
        
        return $this->addSecurityHeaders($response);
    }

    /**
     * Check for suspicious content in request
     */
    private function containsSuspiciousContent(Request $request)
    {
        $suspicious = [
            '<script', 'javascript:', 'vbscript:', 'onload=', 'onerror=',
            'eval(', 'exec(', 'system(', 'shell_exec(', 'passthru(',
            '<?php', '<?=', '../', '..\\', 'file://', 'data://'
        ];

        $content = json_encode($request->all());
        
        foreach ($suspicious as $pattern) {
            if (stripos($content, $pattern) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add security headers to response
     */
    private function addSecurityHeaders($response)
    {
        $headers = [
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'X-XSS-Protection' => '1; mode=block',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Content-Security-Policy' => "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';"
        ];

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}