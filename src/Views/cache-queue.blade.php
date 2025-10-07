@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.cache_queue.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-rocket fa-fw" aria-hidden="true"></i>
    {{ trans('installer_messages.cache_queue.title') }}
@endsection

@section('container')
    <div class="tabs tabs-full">
        <div class="tab-content">
            <div class="tab-pane active" id="cache-queue">
                <div class="cache-section">
                    <h4>Cache Management</h4>
                    <div class="form-group">
                        <button id="clear-cache" class="button">Clear All Caches</button>
                        <button id="optimize-cache" class="button">Optimize Application</button>
                    </div>
                    <div id="cache-results" class="results"></div>
                </div>

                <div class="queue-section">
                    <h4>Queue Configuration</h4>
                    <form id="queue-form">
                        <div class="form-group">
                            <label for="queue_driver">Queue Driver:</label>
                            <select name="queue_driver" id="queue_driver" required>
                                <option value="sync">Sync (Default)</option>
                                <option value="database">Database</option>
                                <option value="redis">Redis</option>
                            </select>
                        </div>
                        
                        <div id="redis-config" style="display:none;">
                            <div class="form-group">
                                <label for="redis_host">Redis Host:</label>
                                <input type="text" name="redis_host" id="redis_host" value="127.0.0.1">
                            </div>
                            <div class="form-group">
                                <label for="redis_port">Redis Port:</label>
                                <input type="number" name="redis_port" id="redis_port" value="6379">
                            </div>
                            <div class="form-group">
                                <label for="redis_password">Redis Password:</label>
                                <input type="password" name="redis_password" id="redis_password">
                            </div>
                        </div>
                        
                        <button type="submit" class="button">Setup Queue</button>
                    </form>
                    <div id="queue-results" class="results"></div>
                </div>

                <div class="scheduler-section">
                    <h4>Task Scheduler</h4>
                    <button id="setup-scheduler" class="button">Setup Scheduler</button>
                    <div id="scheduler-results" class="results"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="buttons">
        <a href="{{ route('LaravelInstaller::database-backup') }}" class="button">
            {{ trans('installer_messages.cache_queue.back') }}
        </a>
        <a href="{{ route('LaravelInstaller::performance-dashboard') }}" class="button button-next">
            {{ trans('installer_messages.cache_queue.next') }}
        </a>
    </div>

    <script>
        document.getElementById('queue_driver').addEventListener('change', function() {
            const redisConfig = document.getElementById('redis-config');
            redisConfig.style.display = this.value === 'redis' ? 'block' : 'none';
        });

        document.getElementById('clear-cache').addEventListener('click', function() {
            fetch('{{ route("LaravelInstaller::api.cache.clear") }}', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cache-results').innerHTML = 
                        `<div class="${data.success ? 'success' : 'error'}">${data.message}</div>`;
                });
        });

        document.getElementById('queue-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch('{{ route("LaravelInstaller::api.queue.setup") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('queue-results').innerHTML = 
                    `<div class="${data.success ? 'success' : 'error'}">${data.message}</div>`;
            });
        });

        document.getElementById('setup-scheduler').addEventListener('click', function() {
            fetch('{{ route("LaravelInstaller::scheduler.setup") }}', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('scheduler-results').innerHTML = 
                        `<div class="${data.success ? 'success' : 'error'}">${data.message}</div>`;
                });
        });
    </script>
@endsection