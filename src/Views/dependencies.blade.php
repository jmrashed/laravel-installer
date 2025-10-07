@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.dependencies.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-cogs fa-fw" aria-hidden="true"></i>
    {{ trans('installer_messages.dependencies.title') }}
@endsection

@section('container')
    <div class="tabs tabs-full">
        <div class="tab-content">
            <div class="tab-pane active" id="dependencies">
                @if(isset($error))
                    <div class="alert alert-danger">
                        <strong>Error:</strong> {{ $error }}
                    </div>
                @else
                    <div class="dependency-check">
                        <h4>Composer Dependencies</h4>
                        <div id="dependency-results">
                            <div class="loading">Checking dependencies...</div>
                        </div>
                        
                        <h4>Critical Dependencies</h4>
                        <div id="critical-results">
                            <div class="loading">Checking critical dependencies...</div>
                        </div>
                    </div>
                @endif
                
                <div class="buttons">
                    <a href="{{ route('LaravelInstaller::permissions') }}" class="button">
                        {{ trans('installer_messages.dependencies.back') }}
                    </a>
                    <button id="check-dependencies" class="button button-next">
                        {{ trans('installer_messages.dependencies.check') }}
                    </button>
                    <a href="{{ route('LaravelInstaller::environment-setting') }}" class="button button-next" id="next-step" style="display:none;">
                        {{ trans('installer_messages.dependencies.next') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('check-dependencies').addEventListener('click', function() {
            fetch('{{ route("LaravelInstaller::api.dependencies.check") }}')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('next-step').style.display = 'inline-block';
                    }
                    updateDependencyResults(data);
                });
        });

        function updateDependencyResults(data) {
            const dependencyResults = document.getElementById('dependency-results');
            const criticalResults = document.getElementById('critical-results');
            
            dependencyResults.innerHTML = formatDependencies(data.dependencies);
            criticalResults.innerHTML = formatCritical(data.critical);
        }

        function formatDependencies(deps) {
            if (!deps) return '<div class="error">Failed to check dependencies</div>';
            
            let html = '<ul class="dependency-list">';
            deps.dependencies.forEach(dep => {
                const status = dep.status === 'compatible' ? 'success' : 'error';
                html += `<li class="${status}">${dep.name} - ${dep.status}</li>`;
            });
            html += '</ul>';
            return html;
        }

        function formatCritical(critical) {
            if (!critical) return '<div class="error">Failed to check critical dependencies</div>';
            
            let html = '<ul class="dependency-list">';
            critical.forEach(dep => {
                const status = dep.status === 'compatible' ? 'success' : 'error';
                html += `<li class="${status}">${dep.name} - ${dep.status}</li>`;
            });
            html += '</ul>';
            return html;
        }
    </script>
@endsection