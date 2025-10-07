<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Jmrashed\LaravelInstaller\Controllers\WelcomeController;
use Tests\TestCase;

class WelcomeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testWelcomePageReturnsView()
    {
        $controller = new WelcomeController();
        $response = $controller->welcome();

        $this->assertEquals('vendor.installer.welcome', $response->getName());
    }

    public function testWelcomeRoute()
    {
        $response = $this->get('/install');

        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.welcome');
    }

    public function testWelcomePageContainsExpectedContent()
    {
        $response = $this->get('/install');

        $response->assertStatus(200);
        $response->assertSee('Laravel Installer');
    }
}