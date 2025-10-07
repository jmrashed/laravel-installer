@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.resume.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-play fa-fw" aria-hidden="true"></i>
    {{ trans('installer_messages.resume.title') }}
@endsection

@section('container')
    <div class="tabs tabs-full">
        <div class="tab-content">
            <div class="tab-pane active" id="resume">
                <div class="resume-info">
                    <h4>Installation Progress</h4>
                    <p>{{ trans('installer_messages.resume.description') }}</p>
                </div>

                <div class="progress-overview">
                    <div id="progress-steps" class="steps-container">
                        <!-- Progress steps will be loaded here -->
                    </div>
                </div>

                <div class="resume-actions">
                    <button id="resume-installation" class="button button-next">
                        Resume Installation
                    </button>
                    <button id="restart-installation" class="button">
                        Restart from Beginning
                    </button>
                </div>

                <div id="resume-results" class="results"></div>
            </div>
        </div>
    </div>

    <div class="buttons">
        <a href="{{ route('LaravelInstaller::welcome') }}" class="button">
            {{ trans('installer_messages.resume.back') }}
        </a>
    </div>

    <script>
        function loadProgress() {
            fetch('{{ route("LaravelInstaller::api.progress") }}')
                .then(response => response.json())
                .then(data => {
                    displayProgress(data);
                });
        }

        function displayProgress(progress) {
            const stepsContainer = document.getElementById('progress-steps');
            const steps = [
                { key: 'welcome', name: 'Welcome', route: '{{ route("LaravelInstaller::welcome") }}' },
                { key: 'requirements', name: 'Requirements', route: '{{ route("LaravelInstaller::server-requirements") }}' },
                { key: 'permissions', name: 'Permissions', route: '{{ route("LaravelInstaller::permissions") }}' },
                { key: 'dependencies', name: 'Dependencies', route: '{{ route("LaravelInstaller::dependencies") }}' },
                { key: 'environment', name: 'Environment', route: '{{ route("LaravelInstaller::environment-setting") }}' },
                { key: 'database', name: 'Database', route: '{{ route("LaravelInstaller::database-setting") }}' },
                { key: 'backup', name: 'Database Backup', route: '{{ route("LaravelInstaller::database-backup") }}' },
                { key: 'cache_queue', name: 'Cache & Queue', route: '{{ route("LaravelInstaller::cache-queue") }}' },
                { key: 'performance', name: 'Performance', route: '{{ route("LaravelInstaller::performance-dashboard") }}' },
                { key: 'finished', name: 'Finished', route: '{{ route("LaravelInstaller::installation-finished") }}' }
            ];

            let html = '<div class="progress-steps">';
            
            steps.forEach((step, index) => {
                const stepProgress = progress.steps[step.key] || { status: 'pending' };
                const statusClass = getStatusClass(stepProgress.status);
                const isCurrentStep = progress.current_step === step.key;
                
                html += `
                    <div class="step ${statusClass} ${isCurrentStep ? 'current' : ''}">
                        <div class="step-number">${index + 1}</div>
                        <div class="step-info">
                            <div class="step-name">${step.name}</div>
                            <div class="step-status">${stepProgress.status}</div>
                        </div>
                        ${stepProgress.status === 'completed' ? 
                            `<a href="${step.route}" class="step-link">View</a>` : ''}
                    </div>
                `;
            });
            
            html += '</div>';
            stepsContainer.innerHTML = html;

            // Update resume button
            const resumeBtn = document.getElementById('resume-installation');
            const currentStepRoute = steps.find(s => s.key === progress.current_step)?.route;
            
            if (currentStepRoute) {
                resumeBtn.onclick = () => window.location.href = currentStepRoute;
            }
        }

        function getStatusClass(status) {
            switch (status) {
                case 'completed': return 'step-completed';
                case 'in_progress': return 'step-in-progress';
                case 'failed': return 'step-failed';
                default: return 'step-pending';
            }
        }

        document.getElementById('restart-installation').addEventListener('click', function() {
            if (confirm('Are you sure you want to restart the installation? This will reset all progress.')) {
                fetch('{{ route("LaravelInstaller::api.progress.reset") }}', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.href = '{{ route("LaravelInstaller::welcome") }}';
                        }
                    });
            }
        });

        // Load progress on page load
        document.addEventListener('DOMContentLoaded', loadProgress);
    </script>

    <style>
        .progress-steps {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin: 2rem 0;
        }
        
        .step {
            display: flex;
            align-items: center;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #f8f9fa;
        }
        
        .step.current {
            border-color: #007cba;
            background: #e3f2fd;
        }
        
        .step-completed {
            background: #d4edda;
            border-color: #28a745;
        }
        
        .step-in-progress {
            background: #fff3cd;
            border-color: #ffc107;
        }
        
        .step-failed {
            background: #f8d7da;
            border-color: #dc3545;
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #007cba;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 1rem;
        }
        
        .step-info {
            flex: 1;
        }
        
        .step-name {
            font-weight: bold;
            margin-bottom: 0.25rem;
        }
        
        .step-status {
            font-size: 0.9rem;
            color: #666;
            text-transform: capitalize;
        }
        
        .step-link {
            padding: 0.5rem 1rem;
            background: #007cba;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.9rem;
        }
    </style>
@endsection