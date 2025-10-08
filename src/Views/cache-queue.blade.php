@extends('vendor.installer.layouts.imaster')

@section('template_title')
    Cache & Queue Setup
@endsection

@section('icontent')
    <div class="flex">
        @include('vendor.installer._inc.aside')

        <div class="body-content w-full h-screen">
            <h1 class="capitalize text-primary border-b-[2px] border-[var(--primary)] pl-20 py-5 text-2xl font-semibold mb-4">
                {{ env('APP_NAME') }}
            </h1>
            <div class="h-[80vh] w-full flex flex-col justify-between items-center gap-10 pl-4"  style="background: #ffffffc4; padding: 15px;">
                <div class="content-wrapper w-full">
                    <h4 class="text-lg no-underline bg-primary text-white font-medium text-start px-6 py-3 mb-6 rounded-[4px] w-full">
                        Cache & Queue Setup
                    </h4>
                    
                    <div class="grid gap-6 grid-cols-1 md:grid-cols-2">
                        <div class="card bg-white p-6 rounded-md space-y-4">
                            <h5 class="text-md font-semibold text-primary mb-4">Cache Management</h5>
                            <div class="space-y-3">
                                <button id="clear-cache" class="btn-primary-outline w-full">Clear All Caches</button>
                                <button id="optimize-cache" class="btn-primary-outline w-full">Optimize Application</button>
                            </div>
                            <div id="cache-results" class="results mt-4"></div>
                        </div>

                        <div class="card bg-white p-6 rounded-md space-y-4">
                            <h5 class="text-md font-semibold text-primary mb-4">Queue Configuration</h5>
                            <form id="queue-form" class="space-y-4">
                                <div class="contact-form">
                                    <label for="queue_driver" class="text-primary block text-sm font-semibold text-gray-700">Queue Driver:</label>
                                    <select name="queue_driver" id="queue_driver" class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm bg-transparent" required>
                                        <option value="sync">Sync (Default)</option>
                                        <option value="database">Database</option>
                                        <option value="redis">Redis</option>
                                    </select>
                                </div>
                                
                                <div id="redis-config" style="display:none;" class="space-y-4">
                                    <div class="contact-form">
                                        <label for="redis_host" class="text-primary block text-sm font-semibold text-gray-700">Redis Host:</label>
                                        <input type="text" name="redis_host" id="redis_host" value="127.0.0.1" class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm">
                                    </div>
                                    <div class="contact-form">
                                        <label for="redis_port" class="text-primary block text-sm font-semibold text-gray-700">Redis Port:</label>
                                        <input type="number" name="redis_port" id="redis_port" value="6379" class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm">
                                    </div>
                                    <div class="contact-form">
                                        <label for="redis_password" class="text-primary block text-sm font-semibold text-gray-700">Redis Password:</label>
                                        <input type="password" name="redis_password" id="redis_password" class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm">
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn-primary-fill w-full">Setup Queue</button>
                            </form>
                            <div id="queue-results" class="results mt-4"></div>
                        </div>
                    </div>

                    <div class="card bg-white p-6 rounded-md space-y-4 mt-6">
                        <h5 class="text-md font-semibold text-primary mb-4">Task Scheduler</h5>
                        <button id="setup-scheduler" class="btn-primary-fill">Setup Scheduler</button>
                        <div id="scheduler-results" class="results mt-4"></div>
                    </div>
                </div>
                
                <div class="flex gap-4 items-center justify-center">
                    <a href="{{ route('LaravelInstaller::database-backup') }}" class="btn-primary-outline">
                        <i class="ri-arrow-left-line"></i>
                        Back
                    </a>
                    <a href="{{ route('LaravelInstaller::performance-dashboard') }}" class="btn-primary-fill">
                        Next
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('queue_driver').addEventListener('change', function() {
            const redisConfig = document.getElementById('redis-config');
            redisConfig.style.display = this.value === 'redis' ? 'block' : 'none';
        });

        document.getElementById('clear-cache').addEventListener('click', function() {
            this.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Clearing...';
            this.disabled = true;
            
            fetch('{{ route("LaravelInstaller::api.cache.clear") }}', { 
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('cache-results').innerHTML = 
                    `<div class="p-3 rounded ${data.success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${data.message}</div>`;
                this.innerHTML = 'Clear All Caches';
                this.disabled = false;
            });
        });

        document.getElementById('queue-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route("LaravelInstaller::api.queue.setup") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('queue-results').innerHTML = 
                    `<div class="p-3 rounded ${data.success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${data.message}</div>`;
            });
        });

        document.getElementById('setup-scheduler').addEventListener('click', function() {
            this.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Setting up...';
            this.disabled = true;
            
            fetch('{{ route("LaravelInstaller::scheduler.setup") }}', { 
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('scheduler-results').innerHTML = 
                    `<div class="p-3 rounded ${data.success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${data.message}</div>`;
                this.innerHTML = 'Setup Scheduler';
                this.disabled = false;
            });
        });
    </script>
@endsection