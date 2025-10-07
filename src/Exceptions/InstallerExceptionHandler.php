<?php

namespace Jmrashed\LaravelInstaller\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class InstallerExceptionHandler
{
    public function handle(Throwable $exception, Request $request = null)
    {
        $this->logException($exception, $request);
        
        if ($request && $request->expectsJson()) {
            return $this->jsonResponse($exception);
        }
        
        return $this->webResponse($exception);
    }

    private function logException(Throwable $exception, Request $request = null)
    {
        $context = [
            'exception' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTraceAsString()
        ];

        if ($request) {
            $context['request'] = [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ];
        }

        Log::error('Installer Exception: ' . $exception->getMessage(), $context);
    }

    private function jsonResponse(Throwable $exception)
    {
        return response()->json([
            'error' => 'Installation Error',
            'message' => $this->getUserFriendlyMessage($exception),
            'code' => $exception->getCode() ?: 500
        ], 500);
    }

    private function webResponse(Throwable $exception)
    {
        return response()->view('vendor.installer.errors.exception', [
            'title' => 'Installation Error',
            'message' => $this->getUserFriendlyMessage($exception),
            'details' => config('app.debug') ? $exception->getMessage() : null
        ], 500);
    }

    private function getUserFriendlyMessage(Throwable $exception)
    {
        $friendlyMessages = [
            'PDOException' => 'Database connection failed. Please check your database credentials.',
            'FileNotFoundException' => 'Required file not found. Please ensure all files are uploaded correctly.',
            'PermissionDeniedException' => 'Permission denied. Please check file and folder permissions.',
            'ValidationException' => 'Invalid input provided. Please check your entries and try again.'
        ];

        $className = class_basename($exception);
        return $friendlyMessages[$className] ?? 'An unexpected error occurred during installation.';
    }
}