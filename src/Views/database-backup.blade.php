@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.database_backup.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-database fa-fw" aria-hidden="true"></i>
    {{ trans('installer_messages.database_backup.title') }}
@endsection

@section('container')
    <div class="tabs tabs-full">
        <div class="tab-content">
            <div class="tab-pane active" id="database-backup">
                <div class="backup-info">
                    <p>{{ trans('installer_messages.database_backup.description') }}</p>
                </div>

                <div class="backup-options">
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="create_backup" checked>
                            Create database backup before migration
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="run_seeders">
                            Run database seeders after migration
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label for="batch_size">Migration batch size (for large databases):</label>
                        <input type="number" id="batch_size" value="10" min="1" max="100">
                    </div>
                </div>

                <div class="migration-controls">
                    <button id="start-migration" class="button button-next">
                        Start Database Migration
                    </button>
                    <button id="rollback-migration" class="button" style="display:none;">
                        Rollback Migration
                    </button>
                </div>

                <div id="migration-progress" class="progress-section" style="display:none;">
                    <div class="progress-bar">
                        <div class="progress-fill" id="progress-fill"></div>
                    </div>
                    <div id="migration-status" class="status-text"></div>
                </div>

                <div id="migration-results" class="results"></div>
            </div>
        </div>
    </div>

    <div class="buttons">
        <a href="{{ route('LaravelInstaller::database-setting') }}" class="button">
            {{ trans('installer_messages.database_backup.back') }}
        </a>
        <a href="{{ route('LaravelInstaller::cache-queue') }}" class="button button-next" id="next-step" style="display:none;">
            {{ trans('installer_messages.database_backup.next') }}
        </a>
    </div>

    <script>
        let migrationInProgress = false;

        document.getElementById('start-migration').addEventListener('click', function() {
            if (migrationInProgress) return;
            
            migrationInProgress = true;
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
            
            fetch('{{ route("LaravelInstaller::api.database.migrate") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                migrationInProgress = false;
                document.getElementById('start-migration').disabled = false;
                
                if (data.success) {
                    statusText.textContent = 'Migration completed successfully!';
                    document.getElementById('progress-fill').style.width = '100%';
                    document.getElementById('next-step').style.display = 'inline-block';
                    resultsDiv.innerHTML = `<div class="success">${data.message}</div>`;
                } else {
                    statusText.textContent = 'Migration failed';
                    resultsDiv.innerHTML = `<div class="error">${data.message}</div>`;
                    document.getElementById('rollback-migration').style.display = 'inline-block';
                }
            })
            .catch(error => {
                migrationInProgress = false;
                document.getElementById('start-migration').disabled = false;
                statusText.textContent = 'Migration failed';
                resultsDiv.innerHTML = `<div class="error">Network error: ${error.message}</div>`;
            });
        });

        document.getElementById('rollback-migration').addEventListener('click', function() {
            if (confirm('Are you sure you want to rollback the migration?')) {
                fetch('{{ route("LaravelInstaller::api.database.rollback") }}', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        const resultsDiv = document.getElementById('migration-results');
                        resultsDiv.innerHTML = `<div class="${data.success ? 'success' : 'error'}">${data.message}</div>`;
                        
                        if (data.success) {
                            this.style.display = 'none';
                        }
                    });
            }
        });
    </script>
@endsection