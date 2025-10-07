<?php

namespace Jmrashed\LaravelInstaller\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Jmrashed\LaravelInstaller\Helpers\ProgressTracker;

class ProgressController extends Controller
{
    public function getProgress()
    {
        return response()->json(ProgressTracker::getProgress());
    }

    public function updateProgress(Request $request)
    {
        $step = $request->input('step');
        $status = $request->input('status', 'completed');
        $data = $request->input('data', []);
        
        $progress = ProgressTracker::setStep($step, $status, $data);
        
        return response()->json($progress);
    }

    public function canResume(Request $request)
    {
        $step = $request->input('step');
        $canResume = ProgressTracker::canResume($step);
        
        return response()->json(['can_resume' => $canResume]);
    }

    public function reset()
    {
        ProgressTracker::reset();
        return response()->json(['success' => true]);
    }
}