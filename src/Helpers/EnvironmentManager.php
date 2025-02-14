<?php

namespace Jmrashed\LaravelInstaller\Helpers;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EnvironmentManager
{
    /**
     * @var string
     */
    private $envPath;

    /**
     * @var string
     */
    private $envExamplePath;

    /**
     * Set the .env and .env.example paths.
     */
    public function __construct()
    {
        $this->envPath = base_path('.env');
        $this->envExamplePath = base_path('.env.example');
    }

    /**
     * Get the content of the .env file.
     *
     * @return string
     */
    public function getEnvContent()
    {
        if (! file_exists($this->envPath)) {
            if (file_exists($this->envExamplePath)) {
                copy($this->envExamplePath, $this->envPath);
            } else {
                touch($this->envPath);
            }
        }

        return file_get_contents($this->envPath);
    }

    /**
     * Get the the .env file path.
     *
     * @return string
     */
    public function getEnvPath()
    {
        return $this->envPath;
    }

    /**
     * Get the the .env.example file path.
     *
     * @return string
     */
    public function getEnvExamplePath()
    {
        return $this->envExamplePath;
    }

    /**
     * Save the edited content to the .env file.
     *
     * @param  Request  $input
     * @return string
     */
    public function saveFileClassic(Request $input)
    {
        $message = trans('installer_messages.environment.success');

        try {
            file_put_contents($this->envPath, $input->get('envConfig'));
        } catch (Exception $e) {
            $message = trans('installer_messages.environment.errors');
        }

        return $message;
    }

    /**
     * Save the form content to the .env file.
     *
     * @param  Request  $request
     * @return string
     */
    public function saveFileWizard(Request $request)
    {
        $results = trans('installer_messages.environment.success');
        $envPath = $this->envPath;
        $envData = file_exists($envPath) ? file_get_contents($envPath) : '';

        $updates = [];

        if ($request->tab == 'configuration') {
            $updates = [
                'APP_NAME' => "\"".$request->app_name."\"",
                'APP_ENV' => "\"".$request->environment."\"",
                'APP_KEY' => "\"base64:".base64_encode(Str::random(32))."\"",
                'APP_DEBUG' => "\"".$request->app_debug."\"",
                'APP_LOG_LEVEL' => "\"".$request->app_log_level."\"",
                'APP_URL' => "\"" . $request->app_url . "\"",
                'APP_HTTPS' => $this->setAppHttps($request->app_url),
                'APP_DOMAIN' => "\"" . $this->extractDomain($request->app_url) . "\"",
            ];
        } elseif ($request->tab == 'database') {
            $updates = [
                'DB_CONNECTION' => "\"".$request->database_connection."\"",
                'DB_HOST' => "\"".$request->database_hostname."\"",
                'DB_PORT' => "\"".$request->database_port."\"",
                'DB_DATABASE' => "\"".$request->database_name."\"",
                'DB_USERNAME' => "\"".$request->database_username."\"",
                'DB_PASSWORD' => "\"".$request->database_password."\"",
            ];
        } elseif ($request->tab == 'application') {
            $updates = [
                'BROADCAST_DRIVER' => "\"".$request->broadcast_driver."\"",
                'CACHE_DRIVER' => "\"".$request->cache_driver."\"",
                'SESSION_DRIVER' => "\"".$request->session_driver."\"",
                'QUEUE_CONNECTION' => "\"".$request->queue_connection."\"",

                'REDIS_HOST' => "\"".$request->redis_hostname."\"",
                'REDIS_PASSWORD' => "\"".$request->redis_password."\"",
                'REDIS_PORT' => "\"".$request->redis_port."\"",

                'MAIL_MAILER' => "\"".$request->mail_mailer."\"",
                'MAIL_HOST' => "\"".$request->mail_host."\"",
                'MAIL_PORT' => "\"".$request->mail_port."\"",
                'MAIL_USERNAME' => "\"".$request->mail_username."\"",
                'MAIL_PASSWORD' => "\"".$request->mail_password."\"",
                'MAIL_ENCRYPTION' => "\"".$request->mail_encryption."\"",
                'MAIL_FROM_ADDRESS' => "\"".$request->mail_from_address."\"",
                'MAIL_FROM_NAME' => "\"".$request->mail_from_name."\"",

                'PUSHER_APP_ID' => "\"".$request->pusher_app_id."\"",
                'PUSHER_APP_KEY' => "\"".$request->pusher_app_key."\"",
                'PUSHER_APP_SECRET' => "\"".$request->pusher_app_secret."\"",
            ];
        }

        try {

            foreach ($updates as $key => $value) {
                $pattern = "/^" . preg_quote($key, '/') . "=(.*)/m";

                if (preg_match($pattern, $envData)) {
                    $envData = preg_replace_callback($pattern, function($matches) use ($key, $value) {
                        return "{$key}={$value}";
                    }, $envData);
                } else {
                    $envData .= "\n{$key}={$value}";
                }
            }

            file_put_contents($envPath, rtrim($envData) . "\n");
        } catch (Exception $e) {
            $results = trans('installer_messages.environment.errors');
        }

        return $results;
    }

    // Helper method to set APP_HTTPS
    private function setAppHttps($url)
    {
        return strpos($url, 'https://') === 0 ? 'true' : 'false';
    }

    // Helper method to extract domain from APP_URL
    private function extractDomain($url)
    {
        $parsedUrl = parse_url($url);
        return $parsedUrl['host'] ?? '';
    }
}
