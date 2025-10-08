@extends('vendor.installer.layouts.imaster')

@section('template_title')
    Dependencies Check
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
                        Dependencies Check
                    </h4>
                    
                    @if(isset($error))
                        <div class="alert alert-danger bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            <strong>Error:</strong> {{ $error }}
                        </div>
                    @else
                        <div class="dependency-check">
                            <div class="card bg-white p-6 w-full rounded-md space-y-4">
                                <h5 class="text-md font-semibold text-primary mb-4">Composer Dependencies</h5>
                                <div id="dependency-results">
                                    <div class="loading text-center py-4">Checking dependencies...</div>
                                </div>
                                
                                <h5 class="text-md font-semibold text-primary mb-4 mt-6">Critical Dependencies</h5>
                                <div id="critical-results">
                                    <div class="loading text-center py-4">Checking critical dependencies...</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="flex gap-4 items-center justify-center">
                    <a href="{{ route('LaravelInstaller::permissions') }}" class="btn-primary-outline">
                        <i class="ri-arrow-left-line"></i>
                        Back
                    </a>
                    <button id="check-dependencies" class="btn-primary-fill">
                        Check Dependencies
                        <i class="ri-refresh-line"></i>
                    </button>
                    <a href="{{ route('LaravelInstaller::configuration-setting') }}" class="btn-primary-fill" id="next-step" style="display:none;">
                        Next
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-check dependencies on page load
        document.addEventListener('DOMContentLoaded', function() {
            checkDependencies();
        });

        document.getElementById('check-dependencies').addEventListener('click', function() {
            checkDependencies();
        });

        function checkDependencies() {
            const button = document.getElementById('check-dependencies');
            button.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Checking...';
            button.disabled = true;
            
            fetch('{{ route("LaravelInstaller::api.dependencies.check") }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('next-step').style.display = 'inline-flex';
                        button.style.display = 'none';
                    } else {
                        button.innerHTML = 'Check Dependencies <i class="ri-refresh-line"></i>';
                        button.disabled = false;
                    }
                    updateDependencyResults(data);
                })
                .catch(error => {
                    button.innerHTML = 'Check Dependencies <i class="ri-refresh-line"></i>';
                    button.disabled = false;
                    console.error('Error:', error);
                    
                    // Show error message
                    document.getElementById('dependency-results').innerHTML = 
                        '<div class="error text-red-500 p-4 border border-red-300 rounded">Failed to check dependencies. Please try again.</div>';
                    document.getElementById('critical-results').innerHTML = 
                        '<div class="error text-red-500 p-4 border border-red-300 rounded">Failed to check critical dependencies. Please try again.</div>';
                });
        }

        function updateDependencyResults(data) {
            const dependencyResults = document.getElementById('dependency-results');
            const criticalResults = document.getElementById('critical-results');
            
            dependencyResults.innerHTML = formatDependencies(data.dependencies);
            criticalResults.innerHTML = formatCritical(data.critical);
        }

        function formatDependencies(deps) {
            if (!deps || !deps.dependencies) {
                return '<div class="error text-red-500 p-4 border border-red-300 rounded">No dependency data available</div>';
            }
            
            let html = '<ul class="space-y-2">';
            deps.dependencies.forEach(dep => {
                const statusClass = (dep.status === 'installed' || dep.status === 'compatible') ? 'text-green-600' : 'text-red-600';
                const icon = (dep.status === 'installed' || dep.status === 'compatible') ? 'ri-check-line' : 'ri-close-line';
                html += `<li class="flex items-center justify-between p-2 border-b">
                    <div>
                        <span class="text-primary font-medium">${dep.name}</span>
                        <small class="block text-gray-500">Required: ${dep.required_version}</small>
                        ${dep.installed_version ? `<small class="block text-gray-500">Installed: ${dep.installed_version}</small>` : ''}
                    </div>
                    <span class="${statusClass} flex items-center gap-1">
                        <i class="${icon}"></i> ${dep.status}
                    </span>
                </li>`;
            });
            html += '</ul>';
            return html;
        }

        function formatCritical(critical) {
            if (!critical || !Array.isArray(critical)) {
                return '<div class="error text-red-500 p-4 border border-red-300 rounded">No critical dependency data available</div>';
            }
            
            let html = '<ul class="space-y-2">';
            critical.forEach(dep => {
                const statusClass = (dep.status === 'compatible' || dep.status === 'installed') ? 'text-green-600' : 'text-red-600';
                const icon = (dep.status === 'compatible' || dep.status === 'installed') ? 'ri-check-line' : 'ri-close-line';
                html += `<li class="flex items-center justify-between p-2 border-b">
                    <div>
                        <span class="text-primary font-medium">${dep.name}</span>
                        <small class="block text-gray-500">Required: ${dep.required_version}</small>
                        ${dep.installed_version ? `<small class="block text-gray-500">Installed: ${dep.installed_version}</small>` : ''}
                    </div>
                    <span class="${statusClass} flex items-center gap-1">
                        <i class="${icon}"></i> ${dep.status}
                    </span>
                </li>`;
            });
            html += '</ul>';
            return html;
        }
    </script>
@endsection