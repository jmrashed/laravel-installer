<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;

class ProgressControllerTest extends TestCase
{
    public function test_get_progress_returns_current_state()
    {
        $response = $this->getJson('/installer/progress');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'steps',
            'current_step',
            'percentage',
            'started_at'
        ]);
    }

    public function test_update_progress_sets_step_status()
    {
        $response = $this->postJson('/installer/progress/update', [
            'step' => 'environment',
            'status' => 'completed',
            'data' => ['test' => 'value']
        ]);
        
        $response->assertStatus(200);
        $response->assertJsonPath('steps.environment.status', 'completed');
    }

    public function test_can_resume_validates_step_access()
    {
        $response = $this->getJson('/installer/progress/can-resume?step=database');
        
        $response->assertStatus(200);
        $response->assertJsonStructure(['can_resume']);
    }

    public function test_reset_clears_progress()
    {
        $this->postJson('/installer/progress/update', [
            'step' => 'environment',
            'status' => 'completed'
        ]);
        
        $response = $this->postJson('/installer/progress/reset');
        
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }
}