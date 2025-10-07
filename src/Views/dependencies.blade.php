@extends('vendor.installer.layouts.master')

@section('template_title')
    Dependency Check
@endsection

@section('title')
    <i class="fa fa-cube fa-fw" aria-hidden="true"></i>
    Dependency Check
@endsection

@section('container')
    @include('vendor.installer.layouts.progress-bar')
    
    <div class="tabs tabs-full">
        <div class="tab-content">
            <div class="tab-pane active" role="tabpanel">
                @if(isset($error))
                    <div class="alert alert-danger">
                        <strong>Error:</strong> {{ $error }}
                    </div>
                @else
                    <div class="dependency-sections">
                        <!-- Critical Dependencies -->
                        <div class="dependency-section">
                            <h4><i class="fa fa-exclamation-triangle"></i> Critical Dependencies</h4>
                            <div class="dependency-list">
                                @foreach($critical as $dep)
                                    <div class="dependency-item {{ $dep['status'] }}">
                                        <div class="dependency-info">
                                            <strong>{{ $dep['name'] }}</strong>
                                            <span class="version-info">
                                                Required: {{ $dep['required_version'] ?? 'N/A' }}
                                                @if($dep['installed_version'])
                                                    | Installed: {{ $dep['installed_version'] }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="dependency-status">
                                            @if($dep['status'] === 'compatible')
                                                <i class="fa fa-check-circle text-success"></i>
                                            @elseif($dep['status'] === 'incompatible')
                                                <i class="fa fa-times-circle text-danger"></i>
                                            @else
                                                <i class="fa fa-exclamation-circle text-warning"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Required Dependencies -->
                        <div class="dependency-section">
                            <h4><i class="fa fa-cubes"></i> Required Dependencies</h4>
                            <div class="dependency-list">
                                @foreach($dependencies['dependencies'] as $dep)
                                    <div class="dependency-item {{ $dep['status'] }}">
                                        <div class="dependency-info">
                                            <strong>{{ $dep['name'] }}</strong>
                                            <span class="version-info">
                                                Required: {{ $dep['required_version'] }}
                                                @if($dep['installed_version'])
                                                    | Installed: {{ $dep['installed_version'] }}
                                                @endif
                                            </span>
                                        </div>
                                        <div class="dependency-status">
                                            @if($dep['status'] === 'installed' && $dep['compatible'])
                                                <i class="fa fa-check-circle text-success"></i>
                                            @elseif($dep['status'] === 'incompatible')
                                                <i class="fa fa-times-circle text-danger"></i>
                                            @elseif($dep['status'] === 'missing')
                                                <i class="fa fa-exclamation-circle text-warning"></i>
                                            @else
                                                <i class="fa fa-question-circle text-muted"></i>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- System Info -->
                        <div class="dependency-section">
                            <h4><i class="fa fa-info-circle"></i> System Information</h4>
                            <div class="system-info">
                                <div class="info-item">
                                    <strong>PHP Version:</strong> {{ $dependencies['php_version'] }}
                                </div>
                                <div class="info-item">
                                    <strong>Composer Version:</strong> {{ $dependencies['composer_version'] }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="dependency-actions">
                        <button type="button" class="button" id="recheck-btn">
                            <i class="fa fa-refresh"></i>
                            Recheck Dependencies
                        </button>
                        
                        <button type="button" class="button button-primary" id="continue-btn" disabled>
                            <i class="fa fa-arrow-right"></i>
                            Continue Installation
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            checkDependencies();
            
            document.getElementById('recheck-btn').addEventListener('click', checkDependencies);
            document.getElementById('continue-btn').addEventListener('click', continueInstallation);
        });

        async function checkDependencies() {
            const recheckBtn = document.getElementById('recheck-btn');
            const continueBtn = document.getElementById('continue-btn');
            
            recheckBtn.disabled = true;
            recheckBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Checking...';
            
            try {
                const response = await fetch('/installer/dependencies/check');
                const result = await response.json();
                
                if (result.success && result.can_proceed) {
                    continueBtn.disabled = false;
                    continueBtn.classList.add('button-success');
                    showMessage('All dependencies are satisfied!', 'success');
                } else {
                    continueBtn.disabled = true;
                    continueBtn.classList.remove('button-success');
                    showMessage('Some dependencies need attention before continuing.', 'warning');
                }
                
            } catch (error) {
                showMessage('Failed to check dependencies: ' + error.message, 'error');
            } finally {
                recheckBtn.disabled = false;
                recheckBtn.innerHTML = '<i class="fa fa-refresh"></i> Recheck Dependencies';
            }
        }

        function continueInstallation() {
            window.location.href = '{{ route("LaravelInstaller::permissions") }}';
        }

        function showMessage(message, type) {
            // Remove existing messages
            const existing = document.querySelector('.dependency-message');
            if (existing) {
                existing.remove();
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `alert alert-${type} dependency-message`;
            messageDiv.textContent = message;
            
            const container = document.querySelector('.dependency-sections');
            container.parentNode.insertBefore(messageDiv, container);
        }
    </script>

    <style>
        .dependency-sections { margin-bottom: 2rem; }
        
        .dependency-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .dependency-section h4 {
            margin-bottom: 1rem;
            color: #495057;
        }
        
        .dependency-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        
        .dependency-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        
        .dependency-item.compatible {
            border-left: 4px solid #28a745;
        }
        
        .dependency-item.incompatible {
            border-left: 4px solid #dc3545;
        }
        
        .dependency-item.missing {
            border-left: 4px solid #ffc107;
        }
        
        .dependency-info strong {
            display: block;
            margin-bottom: 0.25rem;
        }
        
        .version-info {
            font-size: 0.9em;
            color: #6c757d;
        }
        
        .dependency-status {
            font-size: 1.2em;
        }
        
        .system-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .info-item {
            padding: 0.75rem;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        
        .dependency-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        .button-success {
            background: #28a745;
            color: white;
        }
        
        .text-success { color: #28a745; }
        .text-danger { color: #dc3545; }
        .text-warning { color: #ffc107; }
        .text-muted { color: #6c757d; }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
        }
        
        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
    </style>
@endsection