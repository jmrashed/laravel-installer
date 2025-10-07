@extends('vendor.installer.layouts.master')

@section('template_title')
    Resume Installation
@endsection

@section('title')
    <i class="fa fa-play fa-fw" aria-hidden="true"></i>
    Resume Installation
@endsection

@section('container')
    @include('vendor.installer.layouts.progress-bar')
    
    <div class="tabs tabs-full">
        <div class="tab-content">
            <div class="tab-pane active" role="tabpanel">
                <div class="resume-container">
                    <div class="resume-info">
                        <h3>Installation in Progress</h3>
                        <p>We detected that you have a previous installation in progress. You can continue from where you left off or start over.</p>
                        
                        <div class="installation-status" id="installation-status">
                            <div class="status-item">
                                <strong>Last Step:</strong> <span id="last-step">Loading...</span>
                            </div>
                            <div class="status-item">
                                <strong>Progress:</strong> <span id="current-progress">Loading...</span>
                            </div>
                            <div class="status-item">
                                <strong>Started:</strong> <span id="started-time">Loading...</span>
                            </div>
                        </div>
                    </div>

                    <div class="resume-actions">
                        <button type="button" class="button button-primary" id="continue-btn">
                            <i class="fa fa-play fa-fw"></i>
                            Continue Installation
                        </button>
                        
                        <button type="button" class="button button-secondary" id="restart-btn">
                            <i class="fa fa-refresh fa-fw"></i>
                            Start Over
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadInstallationStatus();
            
            document.getElementById('continue-btn').addEventListener('click', continueInstallation);
            document.getElementById('restart-btn').addEventListener('click', restartInstallation);
        });

        async function loadInstallationStatus() {
            try {
                const response = await fetch('/installer/progress');
                const progress = await response.json();
                
                document.getElementById('last-step').textContent = progress.current_step || 'Unknown';
                document.getElementById('current-progress').textContent = progress.percentage + '%';
                document.getElementById('started-time').textContent = new Date(progress.started_at).toLocaleString();
                
            } catch (error) {
                console.error('Failed to load installation status:', error);
            }
        }

        async function continueInstallation() {
            try {
                const response = await fetch('/installer/progress');
                const progress = await response.json();
                
                const routeMap = {
                    'welcome': '{{ route("LaravelInstaller::welcome") }}',
                    'requirements': '{{ route("LaravelInstaller::requirements") }}',
                    'permissions': '{{ route("LaravelInstaller::permissions") }}',
                    'environment': '{{ route("LaravelInstaller::environmentWizard") }}',
                    'database': '{{ route("LaravelInstaller::database") }}',
                    'migration': '{{ route("LaravelInstaller::final") }}',
                    'finished': '{{ route("LaravelInstaller::finished") }}'
                };
                
                const nextRoute = routeMap[progress.current_step] || routeMap['welcome'];
                window.location.href = nextRoute;
                
            } catch (error) {
                console.error('Failed to continue installation:', error);
                alert('Failed to continue installation. Please try again.');
            }
        }

        async function restartInstallation() {
            if (!confirm('Are you sure you want to start over? This will reset all progress.')) {
                return;
            }
            
            try {
                await fetch('/installer/progress/reset', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                window.location.href = '{{ route("LaravelInstaller::welcome") }}';
                
            } catch (error) {
                console.error('Failed to reset installation:', error);
                alert('Failed to reset installation. Please try again.');
            }
        }
    </script>

    <style>
        .resume-container {
            text-align: center;
            padding: 2rem;
        }
        
        .resume-info {
            margin-bottom: 2rem;
        }
        
        .installation-status {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            text-align: left;
        }
        
        .status-item {
            margin-bottom: 0.5rem;
        }
        
        .status-item:last-child {
            margin-bottom: 0;
        }
        
        .resume-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        .button-primary {
            background: #007bff;
            color: white;
        }
        
        .button-secondary {
            background: #6c757d;
            color: white;
        }
        
        @media (max-width: 768px) {
            .resume-actions {
                flex-direction: column;
            }
        }
    </style>
@endsection