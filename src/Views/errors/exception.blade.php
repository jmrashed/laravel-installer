@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ $title ?? 'Installation Error' }}
@endsection

@section('title')
    <i class="fa fa-exclamation-triangle fa-fw" aria-hidden="true"></i>
    {{ $title ?? 'Installation Error' }}
@endsection

@section('container')
    <div class="tabs tabs-full">
        <div class="tab-content">
            <div class="tab-pane active" role="tabpanel">
                <div class="error-container">
                    <div class="error-icon">
                        <i class="fa fa-exclamation-triangle fa-5x text-danger"></i>
                    </div>
                    
                    <div class="error-content">
                        <h3>{{ $message }}</h3>
                        
                        @if($details && config('app.debug'))
                            <div class="error-details">
                                <h4>Technical Details:</h4>
                                <pre>{{ $details }}</pre>
                            </div>
                        @endif
                        
                        <div class="error-actions">
                            <a href="{{ url()->previous() }}" class="button">
                                <i class="fa fa-arrow-left fa-fw" aria-hidden="true"></i>
                                Go Back
                            </a>
                            <a href="{{ route('LaravelInstaller::welcome') }}" class="button">
                                <i class="fa fa-home fa-fw" aria-hidden="true"></i>
                                Start Over
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <style>
        .error-container {
            text-align: center;
            padding: 2rem;
        }
        .error-icon {
            margin-bottom: 2rem;
        }
        .error-content h3 {
            color: #d9534f;
            margin-bottom: 1.5rem;
        }
        .error-details {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 1rem;
            margin: 1rem 0;
            text-align: left;
        }
        .error-details pre {
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .error-actions {
            margin-top: 2rem;
        }
        .error-actions .button {
            margin: 0 0.5rem;
        }
    </style>
@endsection