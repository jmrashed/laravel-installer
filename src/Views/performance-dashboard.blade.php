@extends('vendor.installer.layouts.master')

@section('template_title')
    {{ trans('installer_messages.performance.templateTitle') }}
@endsection

@section('title')
    <i class="fa fa-tachometer fa-fw" aria-hidden="true"></i>
    {{ trans('installer_messages.performance.title') }}
@endsection

@section('container')
    <div class="tabs tabs-full">
        <div class="tab-content">
            <div class="tab-pane active" id="performance">
                <div class="performance-metrics">
                    <h4>Real-time Performance Metrics</h4>
                    <div class="metrics-grid">
                        <div class="metric-card">
                            <h5>Execution Time</h5>
                            <div id="execution-time" class="metric-value">--</div>
                        </div>
                        <div class="metric-card">
                            <h5>Memory Usage</h5>
                            <div id="memory-usage" class="metric-value">--</div>
                        </div>
                        <div class="metric-card">
                            <h5>Peak Memory</h5>
                            <div id="peak-memory" class="metric-value">--</div>
                        </div>
                        <div class="metric-card">
                            <h5>Database Queries</h5>
                            <div id="db-queries" class="metric-value">--</div>
                        </div>
                    </div>
                </div>

                <div class="performance-history">
                    <h4>Performance History</h4>
                    <div id="performance-chart" class="chart-container">
                        <canvas id="metricsChart" width="400" height="200"></canvas>
                    </div>
                </div>

                <div class="optimization-tools">
                    <h4>Optimization Tools</h4>
                    <div class="optimization-buttons">
                        <button id="optimize-performance" class="button">
                            Optimize Performance
                        </button>
                        <button id="clear-opcache" class="button">
                            Clear OPCache
                        </button>
                        <button id="garbage-collect" class="button">
                            Run Garbage Collection
                        </button>
                    </div>
                    <div id="optimization-results" class="results"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="buttons">
        <a href="{{ route('LaravelInstaller::cache-queue') }}" class="button">
            {{ trans('installer_messages.performance.back') }}
        </a>
        <a href="{{ route('LaravelInstaller::installation-finished') }}" class="button button-next">
            {{ trans('installer_messages.performance.next') }}
        </a>
    </div>

    <script>
        let metricsInterval;
        let chartData = [];

        function startMetricsMonitoring() {
            metricsInterval = setInterval(fetchMetrics, 2000);
            fetchMetrics();
        }

        function fetchMetrics() {
            fetch('{{ route("LaravelInstaller::api.performance.metrics") }}')
                .then(response => response.json())
                .then(data => {
                    updateMetricsDisplay(data);
                    updateChart(data);
                });
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
            fetch('{{ route("LaravelInstaller::performance.optimize") }}', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    document.getElementById('optimization-results').innerHTML = 
                        `<div class="${data.success ? 'success' : 'error'}">${data.message}</div>`;
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

    <style>
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .metric-card {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            text-align: center;
        }
        
        .metric-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #007cba;
        }
        
        .chart-container {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .optimization-buttons {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
        }
    </style>
@endsection