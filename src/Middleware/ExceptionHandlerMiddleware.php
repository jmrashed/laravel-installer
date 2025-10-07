<?php

namespace Jmrashed\LaravelInstaller\Middleware;

use Closure;
use Illuminate\Http\Request;
use Jmrashed\LaravelInstaller\Exceptions\InstallerExceptionHandler;
use Jmrashed\LaravelInstaller\Helpers\LogManager;
use Throwable;

class ExceptionHandlerMiddleware
{
    protected $exceptionHandler;

    public function __construct()
    {
        $this->exceptionHandler = new InstallerExceptionHandler();
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            LogManager::logOperation('request_started', [
                'url' => $request->fullUrl(),
                'method' => $request->method()
            ]);

            $response = $next($request);

            LogManager::logOperation('request_completed', [
                'status' => $response->getStatusCode()
            ]);

            return $response;
        } catch (Throwable $exception) {
            LogManager::logError('Request failed with exception', $exception);
            return $this->exceptionHandler->handle($exception, $request);
        }
    }
}