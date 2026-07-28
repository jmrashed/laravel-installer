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
            'data' => self::redact($data)
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
        ], self::redact($context));

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
            'data' => self::redact($data)
        ];

        Log::channel('security')->warning($event, $securityContext);
    }

    /**
     * Recursively redact sensitive_fields (config('audit.sensitive_fields'))
     * from a log data array so passwords/secrets never reach the log files,
     * regardless of which helper happened to pass them through.
     */
    private static function redact($data)
    {
        if (!is_array($data)) {
            return $data;
        }

        $sensitiveFields = array_map('strtolower', config('audit.sensitive_fields', []));

        $redacted = [];
        foreach ($data as $key => $value) {
            if (is_string($key) && in_array(strtolower($key), $sensitiveFields, true)) {
                $redacted[$key] = '***REDACTED***';
            } elseif (is_array($value)) {
                $redacted[$key] = self::redact($value);
            } else {
                $redacted[$key] = $value;
            }
        }

        return $redacted;
    }
}