<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Jmrashed\LaravelInstaller\Helpers\ProgressTracker;

class ProgressTrackerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Session::start();
        ProgressTracker::reset();
    }

    public function test_set_step_updates_progress()
    {
        $progress = ProgressTracker::setStep('environment', 'completed');
        
        $this->assertEquals('environment', $progress['current_step']);
        $this->assertEquals('completed', $progress['steps']['environment']['status']);
        $this->assertGreaterThan(0, $progress['percentage']);
    }

    public function test_get_progress_returns_default_state()
    {
        $progress = ProgressTracker::getProgress();
        
        $this->assertEquals('welcome', $progress['current_step']);
        $this->assertEquals(0, $progress['percentage']);
        $this->assertIsArray($progress['steps']);
    }

    public function test_can_resume_validates_step_sequence()
    {
        ProgressTracker::setStep('environment', 'completed');
        
        $this->assertTrue(ProgressTracker::canResume('environment'));
        $this->assertTrue(ProgressTracker::canResume('database'));
        $this->assertFalse(ProgressTracker::canResume('finished'));
    }

    public function test_get_next_step_returns_correct_step()
    {
        $nextStep = ProgressTracker::getNextStep('environment');
        $this->assertEquals('database', $nextStep);
        
        $lastStep = ProgressTracker::getNextStep('finished');
        $this->assertNull($lastStep);
    }

    public function test_calculate_percentage_works_correctly()
    {
        ProgressTracker::setStep('welcome', 'completed');
        ProgressTracker::setStep('dependencies', 'completed');
        
        $progress = ProgressTracker::getProgress();
        $this->assertGreaterThan(10, $progress['percentage']);
        $this->assertLessThan(100, $progress['percentage']);
    }
}