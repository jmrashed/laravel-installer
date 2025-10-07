@extends('vendor.installer.layouts.master')

@section('template_title')
    Server Error - Installation Failed
@endsection

@section('title')
    <i class="fa fa-server fa-fw" aria-hidden="true"></i>
    Server Error
@endsection

@section('container')
    <div class="tabs tabs-full">
        <div class="tab-content">
            <div class="tab-pane active" role="tabpanel">
                <div class="error-container">
                    <div class="error-icon">
                        <i class="fa fa-server fa-5x text-danger"></i>
                    </div>
                    
                    <div class="error-content">
                        <h3>Internal Server Error</h3>
                        <p>The installation process encountered an unexpected server error. This could be due to:</p>
                        
                        <ul class="error-suggestions">
                            <li>Insufficient server resources</li>
                            <li>PHP configuration issues</li>
                            <li>File permission problems</li>
                            <li>Database connectivity issues</li>
                        </ul>
                        
                        <div class="error-actions">
                            <a href="{{ url()->previous() }}" class="button">
                                <i class="fa fa-arrow-left fa-fw" aria-hidden="true"></i>
                                Try Again
                            </a>
                            <a href="{{ route('LaravelInstaller::requirements') }}" class="button">
                                <i class="fa fa-check fa-fw" aria-hidden="true"></i>
                                Check Requirements
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection