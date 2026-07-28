<?php

namespace Tests\Unit;

use Tests\TestCase;
use Jmrashed\LaravelInstaller\Helpers\InstalledFileManager;

class InstalledFileManagerTest extends TestCase
{
    protected function tearDown(): void
    {
        $installedFile = storage_path('installed');
        if (file_exists($installedFile)) {
            unlink($installedFile);
        }

        parent::tearDown();
    }

    public function test_create_writes_initial_installed_file()
    {
        $installedFile = storage_path('installed');
        if (file_exists($installedFile)) {
            unlink($installedFile);
        }

        $manager = new InstalledFileManager();
        $message = $manager->create();

        $this->assertFileExists($installedFile);
        $this->assertStringContainsString(trans('installer_messages.installed.success_log_message'), $message);
        $this->assertStringEqualsFile($installedFile, $message);
    }

    public function test_create_appends_to_existing_installed_file()
    {
        $installedFile = storage_path('installed');
        file_put_contents($installedFile, 'existing content' . PHP_EOL);

        $manager = new InstalledFileManager();
        $message = $manager->create();

        $this->assertStringContainsString(trans('installer_messages.updater.log.success_message'), $message);
        $contents = file_get_contents($installedFile);
        $this->assertStringContainsString('existing content', $contents);
        $this->assertStringContainsString($message, $contents);
    }

    public function test_update_delegates_to_create()
    {
        $installedFile = storage_path('installed');
        if (file_exists($installedFile)) {
            unlink($installedFile);
        }

        $manager = new InstalledFileManager();
        $message = $manager->update();

        $this->assertFileExists($installedFile);
        $this->assertStringContainsString(trans('installer_messages.installed.success_log_message'), $message);
    }
}
