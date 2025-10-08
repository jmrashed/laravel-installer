@extends('vendor.installer.layouts.imaster')

@section('template_title')
    Database Migration & Backup
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
                        Database Migration & Backup
                    </h4>
                    
                    <div class="card bg-white p-6 w-full rounded-md space-y-4">
                        <div class="backup-info">
                            <p class="text-gray-600 mb-4">This step will create a backup of your database before running migrations to ensure data safety.</p>
                        </div>

                        <div class="backup-options space-y-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="create_backup" checked class="h-4 w-4 text-primary border-gray-300 focus:ring-primary cursor-pointer">
                                <label for="create_backup" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                                    Create database backup before migration
                                </label>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="run_seeders" class="h-4 w-4 text-primary border-gray-300 focus:ring-primary cursor-pointer">
                                <label for="run_seeders" class="ml-2 block text-sm text-gray-700 cursor-pointer">
                                    Run database seeders after migration
                                </label>
                            </div>
                            
                            <div class="contact-form">
                                <label for="batch_size" class="text-primary block text-sm font-semibold text-gray-700">Migration batch size (for large databases):</label>
                                <input type="number" id="batch_size" value="10" min="1" max="100" class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm">
                            </div>
                        </div>

                        <div class="migration-controls mt-6">
                            <button id="start-migration" class="btn-primary-fill w-full">
                                Start Database Migration
                                <i class="ri-database-2-line"></i>
                            </button>
                            <button id="rollback-migration" class="btn-primary-outline w-full mt-3" style="display:none;">
                                Rollback Migration
                                <i class="ri-arrow-go-back-line"></i>
                            </button>
                        </div>

                        <div id="migration-progress" class="progress-section mt-6" style="display:none;">
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-primary h-2.5 rounded-full transition-all duration-300" id="progress-fill" style="width: 0%"></div>
                            </div>
                            <div id="migration-status" class="text-center mt-2 text-sm text-gray-600"></div>
                        </div>

                        <div id="migration-results" class="results mt-4"></div>
                    </div>
                </div>
                
                <div class="flex gap-4 items-center justify-center">
                    <a href="{{ route('LaravelInstaller::database-setting') }}" class="btn-primary-outline">
                        <i class="ri-arrow-left-line"></i>
                        Back
                    </a>
                    <a href="{{ route('LaravelInstaller::cache-queue') }}" class="btn-primary-fill" id="next-step" style="display:none;">
                        Next
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let migrationInProgress = false;

        document.getElementById('start-migration').addEventListener('click', function() {
            if (migrationInProgress) return;
            
            migrationInProgress = true;
            this.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Starting Migration...';
            this.disabled = true;
            
            const progressSection = document.getElementById('migration-progress');
            const statusText = document.getElementById('migration-status');
            const resultsDiv = document.getElementById('migration-results');
            
            progressSection.style.display = 'block';
            statusText.textContent = 'Starting migration...';
            
            const formData = new FormData();
            formData.append('create_backup', document.getElementById('create_backup').checked);
            formData.append('seed', document.getElementById('run_seeders').checked);
            formData.append('batch_size', document.getElementById('batch_size').value);
            formData.append('_token', '{{ csrf_token() }}');
            
            fetch('{{ route("LaravelInstaller::api.database.migrate") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                migrationInProgress = false;
                this.disabled = false;
                
                if (data.success) {
                    statusText.textContent = 'Migration completed successfully!';
                    document.getElementById('progress-fill').style.width = '100%';
                    document.getElementById('next-step').style.display = 'inline-flex';
                    this.style.display = 'none';
                    resultsDiv.innerHTML = `<div class="p-3 rounded bg-green-100 text-green-700">${data.message}</div>`;
                } else {
                    statusText.textContent = 'Migration failed';
                    resultsDiv.innerHTML = `<div class="p-3 rounded bg-red-100 text-red-700">${data.message}</div>`;
                    document.getElementById('rollback-migration').style.display = 'block';
                    this.innerHTML = 'Start Database Migration <i class="ri-database-2-line"></i>';
                }
            })
            .catch(error => {
                migrationInProgress = false;
                this.disabled = false;
                statusText.textContent = 'Migration failed';
                resultsDiv.innerHTML = `<div class="p-3 rounded bg-red-100 text-red-700">Network error: ${error.message}</div>`;
                this.innerHTML = 'Start Database Migration <i class="ri-database-2-line"></i>';
            });
        });

        document.getElementById('rollback-migration').addEventListener('click', function() {
            if (confirm('Are you sure you want to rollback the migration?')) {
                this.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Rolling back...';
                this.disabled = true;
                
                fetch('{{ route("LaravelInstaller::api.database.rollback") }}', { 
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const resultsDiv = document.getElementById('migration-results');
                    resultsDiv.innerHTML = `<div class="p-3 rounded ${data.success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${data.message}</div>`;
                    
                    if (data.success) {
                        this.style.display = 'none';
                        document.getElementById('start-migration').style.display = 'block';
                    }
                    this.innerHTML = 'Rollback Migration <i class="ri-arrow-go-back-line"></i>';
                    this.disabled = false;
                });
            }
        });
    </script>
@endsection