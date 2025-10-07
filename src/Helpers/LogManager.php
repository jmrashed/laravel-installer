<?php

namespace Jmrashed\LaravelInstaller\Helpers;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class LogManager
{
    public static function logOperation($operation, $data = [], $level = 'info')
    {
        $context = [
            'timestamp' => Carbon::now()->toISOString(),
            'operation' => $operation,
            'ip' => request()->ip(),
            'session_id' => session()->getId(),
            'data' => $data
        ];

        Log::channel('installer')->{$level}($operation, $context);
    }

    public static function logError($message, $exception = null, $context = [])
    {
        $errorContext = array_merge([
            'timestamp' => Carbon::now()->toISOString(),
            'ip' => request()->ip(),
            'url' => request()->fullUrl(),
            'method' => request()->method()
        ], $context);

        if ($exception) {
            $errorContext['exception'] = [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine()
            ];
        }

        Log::channel('installer')->error($message, $errorContext);
    }

    public static function logSecurity($event, $data = [])
    {
        $securityContext = [
            'timestamp' => Carbon::now()->toISOString(),
            'event' => $event,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'data' => $data
        ];

        Log::channel('security')->warning($event, $securityContext);
    }
}