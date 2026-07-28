<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Log;
use Jmrashed\LaravelInstaller\Helpers\LogManager;

class LogManagerTest extends TestCase
{
    public function test_log_operation_logs_to_installer_channel()
    {
        Log::shouldReceive('channel')->with('installer')->once()->andReturnSelf();
        Log::shouldReceive('info')->once();

        LogManager::logOperation('environment_save', ['key' => 'value']);
    }

    public function test_log_operation_respects_custom_level()
    {
        Log::shouldReceive('channel')->with('installer')->once()->andReturnSelf();
        Log::shouldReceive('warning')->once();

        LogManager::logOperation('security_events', ['foo' => 'bar'], 'warning');
    }

    public function test_log_error_logs_to_installer_channel_with_exception_context()
    {
        Log::shouldReceive('channel')->with('installer')->once()->andReturnSelf();
        Log::shouldReceive('error')->once()->with('An error occurred', \Mockery::on(function ($context) {
            return $context['exception']['message'] === 'Something broke';
        }));

        $exception = new \RuntimeException('Something broke', 42);

        LogManager::logError('An error occurred', $exception, ['extra' => 'context']);
    }

    public function test_log_error_logs_without_exception()
    {
        Log::shouldReceive('channel')->with('installer')->once()->andReturnSelf();
        Log::shouldReceive('error')->once();

        LogManager::logError('An error occurred');
    }

    public function test_log_security_logs_to_security_channel()
    {
        Log::shouldReceive('channel')->with('security')->once()->andReturnSelf();
        Log::shouldReceive('warning')->once();

        LogManager::logSecurity('suspicious_login', ['ip' => '127.0.0.1']);
    }
}
