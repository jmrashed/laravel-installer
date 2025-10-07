@extends('vendor.installer.layouts.imaster')

@section('template_title')
    Performance Dashboard
@endsection

@section('icontent')
    <div class="flex">
        @include('vendor.installer._inc.aside')

        <div class="body-content w-full h-screen">
            <h1 class="capitalize text-primary border-b-[2px] border-[var(--primary)] pl-20 py-5 text-2xl font-semibold mb-4">
                {{ env('APP_NAME') }}
            </h1>
            <div class="h-[80vh] w-full flex flex-col justify-between items-center gap-10 pl-4">
                <div class="content-wrapper w-full">
                    <h4 class="text-lg no-underline bg-primary text-white font-medium text-start px-6 py-3 mb-6 rounded-[4px] w-full">
                        Performance Dashboard
                    </h4>
                    
                    <div class="grid gap-6 grid-cols-1 md:grid-cols-2 lg:grid-cols-4 mb-6">
                        <div class="card bg-white p-6 rounded-md text-center">
                            <h5 class="text-sm font-semibold text-gray-600 mb-2">Execution Time</h5>
                            <div id="execution-time" class="text-2xl font-bold text-primary">--</div>
                        </div>
                        <div class="card bg-white p-6 rounded-md text-center">
                            <h5 class="text-sm font-semibold text-gray-600 mb-2">Memory Usage</h5>
                            <div id="memory-usage" class="text-2xl font-bold text-primary">--</div>
                        </div>
                        <div class="card bg-white p-6 rounded-md text-center">
                            <h5 class="text-sm font-semibold text-gray-600 mb-2">Peak Memory</h5>
                            <div id="peak-memory" class="text-2xl font-bold text-primary">--</div>
                        </div>
                        <div class="card bg-white p-6 rounded-md text-center">
                            <h5 class="text-sm font-semibold text-gray-600 mb-2">Database Queries</h5>
                            <div id="db-queries" class="text-2xl font-bold text-primary">--</div>
                        </div>
                    </div>

                    <div class="card bg-white p-6 rounded-md mb-6">
                        <h5 class="text-md font-semibold text-primary mb-4">Performance History</h5>
                        <div class="chart-container bg-gray-50 p-4 rounded">
                            <canvas id="metricsChart" width="400" height="200"></canvas>
                        </div>
                    </div>

                    <div class="card bg-white p-6 rounded-md">
                        <h5 class="text-md font-semibold text-primary mb-4">Optimization Tools</h5>
                        <div class="grid gap-4 grid-cols-1 md:grid-cols-3">
                            <button id="optimize-performance" class="btn-primary-fill">
                                <i class="ri-speed-line"></i>
                                Optimize Performance
                            </button>
                            <button id="clear-opcache" class="btn-primary-outline">
                                <i class="ri-refresh-line"></i>
                                Clear OPCache
                            </button>
                            <button id="garbage-collect" class="btn-primary-outline">
                                <i class="ri-delete-bin-line"></i>
                                Run Garbage Collection
                            </button>
                        </div>
                        <div id="optimization-results" class="results mt-4"></div>
                    </div>
                </div>
                
                <div class="flex gap-4 items-center justify-center">
                    <a href="{{ route('LaravelInstaller::cache-queue') }}" class="btn-primary-outline">
                        <i class="ri-arrow-left-line"></i>
                        Back
                    </a>
                    <a href="{{ route('LaravelInstaller::installation-finished') }}" class="btn-primary-fill">
                        Finish Installation
                        <i class="ri-check-double-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        let metricsInterval;
        let chartData = [];

        function startMetricsMonitoring() {
            metricsInterval = setInterval(fetchMetrics, 3000);
            fetchMetrics();
        }

        function fetchMetrics() {
            fetch('{{ route("LaravelInstaller::api.performance.metrics") }}')
                .then(response => response.json())
                .then(data => {
                    updateMetricsDisplay(data);
                    updateChart(data);
                })
                .catch(error => console.error('Error fetching metrics:', error));
        }

        function updateMetricsDisplay(metrics) {
            document.getElementById('execution-time').textContent = 
                metrics.execution_time ? metrics.execution_time.toFixed(4) + 's' : '--';
            document.getElementById('memory-usage').textContent = 
                metrics.memory_used ? formatBytes(metrics.memory_used) : '--';
            document.getElementById('peak-memory').textContent = 
                metrics.peak_memory ? formatBytes(metrics.peak_memory) : '--';
            document.getElementById('db-queries').textContent = 
                metrics.db_queries || '--';
        }

        function updateChart(metrics) {
            if (metrics.execution_time) {
                chartData.push({
                    time: new Date().toLocaleTimeString(),
                    execution_time: metrics.execution_time,
                    memory: metrics.memory_used
                });
                
                if (chartData.length > 20) {
                    chartData.shift();
                }
                
                drawChart();
            }
        }

        function drawChart() {
            const canvas = document.getElementById('metricsChart');
            const ctx = canvas.getContext('2d');
            
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            if (chartData.length < 2) return;
            
            const maxTime = Math.max(...chartData.map(d => d.execution_time));
            const width = canvas.width;
            const height = canvas.height;
            
            ctx.strokeStyle = '#007cba';
            ctx.lineWidth = 2;
            ctx.beginPath();
            
            chartData.forEach((point, index) => {
                const x = (index / (chartData.length - 1)) * width;
                const y = height - (point.execution_time / maxTime) * height;
                
                if (index === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            });
            
            ctx.stroke();
        }

        function formatBytes(bytes) {
            if (bytes >= 1024 * 1024) {
                return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
            } else if (bytes >= 1024) {
                return (bytes / 1024).toFixed(2) + ' KB';
            }
            return bytes + ' B';
        }

        document.getElementById('optimize-performance').addEventListener('click', function() {
            this.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> Optimizing...';
            this.disabled = true;
            
            fetch('{{ route("LaravelInstaller::performance.optimize") }}', { 
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('optimization-results').innerHTML = 
                    `<div class="p-3 rounded ${data.success ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'}">${data.message}</div>`;
                this.innerHTML = '<i class="ri-speed-line"></i> Optimize Performance';
                this.disabled = false;
            });
        });

        // Start monitoring when page loads
        document.addEventListener('DOMContentLoaded', startMetricsMonitoring);
        
        // Stop monitoring when leaving page
        window.addEventListener('beforeunload', function() {
            if (metricsInterval) {
                clearInterval(metricsInterval);
            }
        });
    </script>
@endsection