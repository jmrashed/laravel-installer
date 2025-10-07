<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;

class EnvironmentControllerTest extends TestCase
{
    public function test_save_classic_with_valid_input()
    {
        $response = $this->post('/installer/environment/classic', [
            'envConfig' => 'APP_NAME="Test App"'
        ]);
        
        $response->assertRedirect();
    }

    public function test_save_wizard_configuration_tab()
    {
        $data = [
            'tab' => 'configuration',
            'app_name' => 'Test App',
            'environment' => 'testing',
            'app_debug' => 'true',
            'app_log_level' => 'debug',
            'app_url' => 'http://localhost'
        ];
        
        $response = $this->post('/installer/environment/wizard', $data);
        
        $response->assertRedirect();
    }

    public function test_rate_limiting_works()
    {
        RateLimiter::clear('env-save:127.0.0.1');
        
        for ($i = 0; $i < 6; $i++) {
            $this->post('/installer/environment/classic', ['envConfig' => 'APP_NAME=Test']);
        }
        
        $response = $this->post('/installer/environment/classic', ['envConfig' => 'APP_NAME=Test']);
        $response->assertSessionHasErrors(['rate_limit']);
    }
}