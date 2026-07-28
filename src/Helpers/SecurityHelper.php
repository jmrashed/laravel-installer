<?php

namespace Jmrashed\LaravelInstaller\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class SecurityHelper
{
    /**
     * Sanitize input data to prevent XSS and injection attacks
     */
    public static function sanitizeInput($input, $allowHtml = false)
    {
        if (is_array($input)) {
            return array_map(function($item) use ($allowHtml) {
                return self::sanitizeInput($item, $allowHtml);
            }, $input);
        }

        if (!$allowHtml) {
            $input = strip_tags($input);
        }

        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate database credentials format
     */
    public static function validateDatabaseCredentials($credentials)
    {
        $required = ['hostname', 'port', 'name', 'username'];
        
        foreach ($required as $field) {
            if (empty($credentials["database_$field"])) {
                return false;
            }
        }

        // Validate hostname format
        if (!filter_var($credentials['database_hostname'], FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            if (!filter_var($credentials['database_hostname'], FILTER_VALIDATE_IP)) {
                return false;
            }
        }

        // Validate port range
        $port = (int) $credentials['database_port'];
        if ($port < 1 || $port > 65535) {
            return false;
        }

        return true;
    }

    /**
     * Check whether a request is allowed to reach the installer/updater at
     * all, based on an optional IP allow-list and/or shared access token
     * (installer.security.allowed_ips / installer.security.access_token).
     *
     * Both are opt-in and empty/null by default, so existing deployments
     * keep working unchanged. An operator who wants to close the
     * unauthenticated window between "package installed" and "install
     * wizard completed" (the only gate otherwise is a storage/installed
     * lock file that doesn't exist yet at that point) can set either.
     */
    public static function isInstallerAccessAllowed($request)
    {
        $allowedIps = array_filter((array) config('installer.security.allowed_ips', []));
        if (!empty($allowedIps) && !in_array($request->ip(), $allowedIps, true)) {
            return false;
        }

        $requiredToken = config('installer.security.access_token');
        if ($requiredToken) {
            $providedToken = $request->query('installer_token') ?? $request->header('X-Installer-Token');
            if (!is_string($providedToken) || !hash_equals((string) $requiredToken, $providedToken)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if request is rate limited
     */
    public static function checkRateLimit($key, $maxAttempts = 5, $decayMinutes = 1)
    {
        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            Log::warning('Rate limit exceeded', [
                'key' => $key,
                'ip' => request()->ip(),
                'retry_after' => $seconds
            ]);
            return $seconds;
        }

        RateLimiter::hit($key, $decayMinutes * 60);
        return false;
    }

    /**
     * Validate environment configuration
     */
    public static function validateEnvironmentConfig($config)
    {
        $dangerous = ['<?php', '<?=', '<script', 'eval(', 'exec(', 'system('];
        
        foreach ($dangerous as $pattern) {
            if (stripos($config, $pattern) !== false) {
                Log::alert('Dangerous code detected in environment config', [
                    'pattern' => $pattern,
                    'ip' => request()->ip()
                ]);
                return false;
            }
        }

        return true;
    }
}