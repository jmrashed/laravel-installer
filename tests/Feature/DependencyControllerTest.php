<?php

namespace Tests\Feature;

use Tests\TestCase;

class DependencyControllerTest extends TestCase
{
    public function test_dependency_check_returns_status()
    {
        $response = $this->getJson('/install/api/dependencies/check');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'dependencies',
            'critical',
            'can_proceed'
        ]);
    }

    public function test_dependency_page_loads()
    {
        $response = $this->get('/install/dependencies');
        
        $response->assertStatus(200);
        $response->assertViewIs('vendor.installer.dependencies');
    }

    public function test_install_packages_with_valid_input()
    {
        $response = $this->postJson('/install/api/dependencies/install', [
            'packages' => ['laravel/framework']
        ]);
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'results',
            'message'
        ]);
    }

    public function test_install_fails_with_empty_packages()
    {
        $response = $this->postJson('/install/api/dependencies/install', [
            'packages' => []
        ]);
        
        $response->assertStatus(500);
        $response->assertJson(['success' => false]);
    }
}