<?php

namespace Tests\Feature;

use Tests\TestCase;

class FinalControllerTest extends TestCase
{
    protected function tearDown(): void
    {
        if (file_exists(storage_path('installed'))) {
            unlink(storage_path('installed'));
        }

        parent::tearDown();
    }

    public function test_finish_writes_installed_file_and_redirects_home()
    {
        try {
            $response = $this->get('/install/final');

            $response->assertRedirect('/');
            $this->assertFileExists(storage_path('installed'));
        } finally {
            if (file_exists(storage_path('installed'))) {
                unlink(storage_path('installed'));
            }
        }
    }

    public function test_finish_dispatches_installer_finished_event()
    {
        \Illuminate\Support\Facades\Event::fake();

        try {
            $this->get('/install/final');

            \Illuminate\Support\Facades\Event::assertDispatched(
                \Jmrashed\LaravelInstaller\Events\LaravelInstallerFinished::class
            );
        } finally {
            if (file_exists(storage_path('installed'))) {
                unlink(storage_path('installed'));
            }
        }
    }
}
