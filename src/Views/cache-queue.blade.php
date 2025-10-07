@extends('vendor.installer.layouts.master')

@section('template_title')
    Cache & Queue Setup
@endsection

@section('title')
    <i class="fa fa-cogs fa-fw" aria-hidden="true"></i>
    Cache & Queue Setup
@endsection

@section('container')
    @include('vendor.installer.layouts.progress-bar')
    
    <div class="tabs tabs-full">
        <div class="tab-content">
            <div class="tab-pane active" role="tabpanel">
                <div class="setup-sections">
                    <!-- Cache Management -->
                    <div class="setup-section">
                        <h4><i class="fa fa-database"></i> Cache Management</h4>
                        <p>Clear existing caches and optimize application performance.</p>
                        
                        <div class="cache-actions">
                            <button type="button" class="button" id="clear-cache-btn">
                                <i class="fa fa-trash"></i>
                                Clear All Caches
                            </button>
                            
                            <button type="button" class="button button-primary" id="optimize-btn">
                                <i class="fa fa-rocket"></i>
                                Optimize Application
                            </button>
                        </div>
                        
                        <div class="cache-results" id="cache-results" style="display: none;">
                            <h5>Cache Operations Results:</h5>
                            <div class="results-list" id="cache-results-list"></div>
                        </div>
                    </div>

                    <!-- Queue Configuration -->
                    <div class="setup-section">
                        <h4><i class="fa fa-tasks"></i> Queue Configuration</h4>
                        <p>Configure queue driver for background job processing.</p>
                        
                        <form id="queue-form">
                            <div class="form-group">
                                <label for="queue_driver">Queue Driver:</label>
                                <select id="queue_driver" name="queue_driver" class="form-control">
                                    <option value="sync">Sync (Default)</option>
                                    <option value="database">Database</option>
                                    <option value="redis">Redis</option>
                                </select>
                            </div>
                            
                            <div id="redis-config" style="display: none;">
                                <div class="form-group">
                                    <label for="redis_host">Redis Host:</label>
                                    <input type="text" id="redis_host" name="redis_host" class="form-control" value="127.0.0.1">
                                </div>
                                
                                <div class="form-group">
                                    <label for="redis_port">Redis Port:</label>
                                    <input type="number" id="redis_port" name="redis_port" class="form-control" value="6379">
                                </div>
                                
                                <div class="form-group">
                                    <label for="redis_password">Redis Password:</label>
                                    <input type="password" id="redis_password" name="redis_password" class="form-control">
                                </div>
                            </div>
                            
                            <button type="submit" class="button button-primary">
                                <i class="fa fa-save"></i>
                                Configure Queue
                            </button>
                        </form>
                    </div>

                    <!-- Task Scheduler -->
                    <div class="setup-section">
                        <h4><i class="fa fa-clock-o"></i> Task Scheduler</h4>
                        <p>Set up Laravel's task scheduler for automated jobs.</p>
                        
                        <div class="scheduler-info">
                            <div class="alert alert-info">
                                <strong>Note:</strong> You'll need to add the following cron entry to your server:
                                <code id="cron-entry">* * * * * cd {{ base_path() }} && php artisan schedule:run >> /dev/null 2>&1</code>
                            </div>
                        </div>
                        
                        <button type="button" class="button" id="setup-scheduler-btn">
                            <i class="fa fa-calendar"></i>
                            Setup Scheduler
                        </button>
                    </div>
                </div>

                <div class="setup-actions">
                    <a href="{{ route('LaravelInstaller::final') }}" class="button button-success" id="continue-btn">
                        <i class="fa fa-arrow-right"></i>
                        Continue to Final Step
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Queue driver change handler
            document.getElementById('queue_driver').addEventListener('change', function() {
                const redisConfig = document.getElementById('redis-config');
                redisConfig.style.display = this.value === 'redis' ? 'block' : 'none';
            });

            // Clear cache handler
            document.getElementById('clear-cache-btn').addEventListener('click', clearCaches);
            
            // Optimize handler
            document.getElementById('optimize-btn').addEventListener('click', optimizeApplication);
            
            // Queue form handler
            document.getElementById('queue-form').addEventListener('submit', setupQueues);
            
            // Scheduler handler
            document.getElementById('setup-scheduler-btn').addEventListener('click', setupScheduler);
        });

        async function clearCaches() {
            const btn = document.getElementById('clear-cache-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Clearing...';
            
            try {
                const response = await fetch('/installer/cache/clear', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showResults('cache-results', result.results);
                    showMessage('Caches cleared successfully!', 'success');
                } else {
                    showMessage('Failed to clear caches: ' + result.message, 'error');
                }
                
            } catch (error) {
                showMessage('Error: ' + error.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-trash"></i> Clear All Caches';
            }
        }

        async function optimizeApplication() {
            const btn = document.getElementById('optimize-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Optimizing...';
            
            try {
                const response = await fetch('/installer/cache/optimize', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showResults('cache-results', result.results);
                    showMessage('Application optimized successfully!', 'success');
                } else {
                    showMessage('Optimization failed: ' + result.message, 'error');
                }
                
            } catch (error) {
                showMessage('Error: ' + error.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-rocket"></i> Optimize Application';
            }
        }

        async function setupQueues(event) {
            event.preventDefault();
            
            const formData = new FormData(event.target);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch('/installer/queue/setup', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(data)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage('Queue configuration updated successfully!', 'success');
                } else {
                    showMessage('Queue setup failed: ' + result.message, 'error');
                }
                
            } catch (error) {
                showMessage('Error: ' + error.message, 'error');
            }
        }

        async function setupScheduler() {
            const btn = document.getElementById('setup-scheduler-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Setting up...';
            
            try {
                const response = await fetch('/installer/scheduler/setup', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage('Scheduler setup completed!', 'success');
                } else {
                    showMessage('Scheduler setup failed: ' + result.message, 'error');
                }
                
            } catch (error) {
                showMessage('Error: ' + error.message, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-calendar"></i> Setup Scheduler';
            }
        }

        function showResults(containerId, results) {
            const container = document.getElementById(containerId);
            const list = document.getElementById(containerId + '-list');
            
            list.innerHTML = '';
            
            Object.entries(results).forEach(([key, result]) => {
                const item = document.createElement('div');
                item.className = 'result-item ' + (result.success ? 'success' : 'error');
                item.innerHTML = `
                    <i class="fa fa-${result.success ? 'check' : 'times'}"></i>
                    ${result.description || key}: ${result.success ? 'Success' : result.error}
                `;
                list.appendChild(item);
            });
            
            container.style.display = 'block';
        }

        function showMessage(message, type) {
            // Remove existing messages
            const existing = document.querySelector('.setup-message');
            if (existing) existing.remove();
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `alert alert-${type} setup-message`;
            messageDiv.textContent = message;
            
            const container = document.querySelector('.setup-sections');
            container.parentNode.insertBefore(messageDiv, container);
            
            setTimeout(() => messageDiv.remove(), 5000);
        }
    </script>

    <style>
        .setup-sections { margin-bottom: 2rem; }
        
        .setup-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .setup-section h4 {
            margin-bottom: 0.5rem;
            color: #495057;
        }
        
        .cache-actions, .setup-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        
        .cache-results {
            margin-top: 1rem;
            padding: 1rem;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        
        .results-list {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .result-item {
            padding: 0.5rem;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .result-item.success {
            background: #d4edda;
            color: #155724;
        }
        
        .result-item.error {
            background: #f8d7da;
            color: #721c24;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.25rem;
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
        }
        
        .scheduler-info {
            margin: 1rem 0;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
        
        .alert-info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-error {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        code {
            background: #f8f9fa;
            padding: 0.25rem 0.5rem;
            border-radius: 3px;
            font-family: monospace;
        }
        
        .button-success {
            background: #28a745;
            color: white;
        }
    </style>
@endsection