@extends('vendor.installer.layouts.master')

@section('template_title')
    Database Migration
@endsection

@section('title')
    <i class="fa fa-database fa-fw" aria-hidden="true"></i>
    Database Migration & Backup
@endsection

@section('container')
    <div class="tabs tabs-full">
        <div class="tab-content">
            <div class="tab-pane active" role="tabpanel">
                <div class="migration-container">
                    <div class="backup-status" id="backup-status" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fa fa-shield fa-fw"></i>
                            Database backup created successfully. Your data is safe.
                        </div>
                    </div>

                    <div class="migration-options">
                        <h4>Migration Options</h4>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="run-seeders" checked>
                                Run database seeders
                            </label>
                        </div>
                        <div class="form-group">
                            <label>
                                <input type="checkbox" id="create-backup" checked>
                                Create backup before migration
                            </label>
                        </div>
                    </div>

                    <div class="migration-progress" id="migration-progress" style="display: none;">
                        <div class="progress-bar">
                            <div class="progress-fill" id="progress-fill"></div>
                        </div>
                        <div class="progress-text" id="progress-text">Preparing migration...</div>
                    </div>

                    <div class="migration-actions">
                        <button type="button" class="button" id="start-migration">
                            <i class="fa fa-play fa-fw"></i>
                            Start Migration
                        </button>
                        
                        <button type="button" class="button button-danger" id="rollback-btn" style="display: none;">
                            <i class="fa fa-undo fa-fw"></i>
                            Rollback Database
                        </button>
                    </div>

                    <div class="migration-log" id="migration-log" style="display: none;">
                        <h4>Migration Log</h4>
                        <div class="log-content" id="log-content"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('start-migration').addEventListener('click', function() {
            startMigration();
        });

        document.getElementById('rollback-btn').addEventListener('click', function() {
            rollbackDatabase();
        });

        function startMigration() {
            const progressDiv = document.getElementById('migration-progress');
            const logDiv = document.getElementById('migration-log');
            const startBtn = document.getElementById('start-migration');
            
            progressDiv.style.display = 'block';
            logDiv.style.display = 'block';
            startBtn.disabled = true;
            
            updateProgress(25, 'Creating database backup...');
            
            fetch('/installer/database/migrate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    seed: document.getElementById('run-seeders').checked,
                    backup: document.getElementById('create-backup').checked
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateProgress(100, 'Migration completed successfully!');
                    addLog('✓ Migration completed successfully', 'success');
                    if (data.backup_id) {
                        document.getElementById('backup-status').style.display = 'block';
                        document.getElementById('rollback-btn').style.display = 'inline-block';
                    }
                } else {
                    updateProgress(0, 'Migration failed');
                    addLog('✗ Migration failed: ' + data.message, 'error');
                    document.getElementById('rollback-btn').style.display = 'inline-block';
                }
            })
            .catch(error => {
                updateProgress(0, 'Migration failed');
                addLog('✗ Network error: ' + error.message, 'error');
            })
            .finally(() => {
                startBtn.disabled = false;
            });
        }

        function rollbackDatabase() {
            if (!confirm('Are you sure you want to rollback the database? This will restore the previous state.')) {
                return;
            }

            fetch('/installer/database/rollback', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    addLog('✓ Database rollback completed', 'success');
                } else {
                    addLog('✗ Rollback failed: ' + data.message, 'error');
                }
            });
        }

        function updateProgress(percent, text) {
            document.getElementById('progress-fill').style.width = percent + '%';
            document.getElementById('progress-text').textContent = text;
        }

        function addLog(message, type = 'info') {
            const logContent = document.getElementById('log-content');
            const logEntry = document.createElement('div');
            logEntry.className = 'log-entry log-' + type;
            logEntry.textContent = new Date().toLocaleTimeString() + ' - ' + message;
            logContent.appendChild(logEntry);
        }
    </script>

    <style>
        .migration-container { padding: 2rem; }
        .migration-options { margin: 1rem 0; }
        .progress-bar {
            width: 100%;
            height: 20px;
            background: #f0f0f0;
            border-radius: 10px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: #28a745;
            transition: width 0.3s ease;
        }
        .progress-text { margin-top: 0.5rem; text-align: center; }
        .log-content {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 1rem;
            max-height: 200px;
            overflow-y: auto;
        }
        .log-entry { margin: 0.25rem 0; }
        .log-success { color: #28a745; }
        .log-error { color: #dc3545; }
        .button-danger { background: #dc3545; }
    </style>
@endsection