@extends('vendor.installer.layouts.master')

@section('template_title')
    Performance Dashboard
@endsection

@section('title')
    <i class="fa fa-tachometer fa-fw" aria-hidden="true"></i>
    Performance Dashboard
@endsection

@section('container')
    <div class="performance-dashboard">
        <div class="metrics-grid">
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fa fa-clock-o"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value" id="execution-time">--</div>
                    <div class="metric-label">Execution Time (s)</div>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fa fa-memory"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value" id="memory-usage">--</div>
                    <div class="metric-label">Memory Usage</div>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fa fa-line-chart"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value" id="memory-percentage">--</div>
                    <div class="metric-label">Memory %</div>
                </div>
            </div>
            
            <div class="metric-card">
                <div class="metric-icon">
                    <i class="fa fa-database"></i>
                </div>
                <div class="metric-content">
                    <div class="metric-value" id="peak-memory">--</div>
                    <div class="metric-label">Peak Memory</div>
                </div>
            </div>
        </div>
        
        <div class="performance-actions">
            <button type="button" class="button" id="optimize-btn">
                <i class="fa fa-rocket"></i>
                Optimize Performance
            </button>
            <button type="button" class="button" id="refresh-btn">
                <i class="fa fa-refresh"></i>
                Refresh Metrics
            </button>
        </div>
        
        <div class="performance-chart">
            <h4>Performance History</h4>
            <canvas id="performance-chart" width="400" height="200"></canvas>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        class PerformanceDashboard {
            constructor() {
                this.init();
            }

            init() {
                this.loadMetrics();
                this.loadHistory();
                
                document.getElementById('optimize-btn').addEventListener('click', () => this.optimize());
                document.getElementById('refresh-btn').addEventListener('click', () => this.loadMetrics());
                
                setInterval(() => this.loadMetrics(), 10000); // Update every 10 seconds
            }

            async loadMetrics() {
                try {
                    const response = await fetch('/installer/performance/metrics');
                    const metrics = await response.json();
                    this.updateMetrics(metrics);
                } catch (error) {
                    console.error('Failed to load metrics:', error);
                }
            }

            async loadHistory() {
                try {
                    const response = await fetch('/installer/performance/history?hours=1');
                    const data = await response.json();
                    this.updateChart(data.metrics);
                } catch (error) {
                    console.error('Failed to load history:', error);
                }
            }

            updateMetrics(metrics) {
                document.getElementById('execution-time').textContent = 
                    metrics.execution_time ? metrics.execution_time.toFixed(3) : '--';
                document.getElementById('memory-usage').textContent = 
                    this.formatBytes(metrics.current_memory);
                document.getElementById('memory-percentage').textContent = 
                    metrics.memory_percentage + '%';
                document.getElementById('peak-memory').textContent = 
                    this.formatBytes(metrics.peak_memory);
            }

            updateChart(metrics) {
                const canvas = document.getElementById('performance-chart');
                const ctx = canvas.getContext('2d');
                
                // Clear canvas
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                if (metrics.length === 0) return;
                
                // Draw execution time chart
                const times = metrics.map(m => m.execution_time);
                const maxTime = Math.max(...times);
                const width = canvas.width - 40;
                const height = canvas.height - 40;
                
                ctx.strokeStyle = '#007bff';
                ctx.lineWidth = 2;
                ctx.beginPath();
                
                times.forEach((time, index) => {
                    const x = 20 + (index / (times.length - 1)) * width;
                    const y = height - (time / maxTime) * height + 20;
                    
                    if (index === 0) {
                        ctx.moveTo(x, y);
                    } else {
                        ctx.lineTo(x, y);
                    }
                });
                
                ctx.stroke();
            }

            async optimize() {
                const btn = document.getElementById('optimize-btn');
                btn.disabled = true;
                btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Optimizing...';
                
                try {
                    const response = await fetch('/installer/performance/optimize', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        alert('Performance optimization completed successfully!');
                        this.loadMetrics();
                    } else {
                        alert('Optimization failed: ' + result.message);
                    }
                } catch (error) {
                    alert('Optimization failed: ' + error.message);
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-rocket"></i> Optimize Performance';
                }
            }

            formatBytes(bytes) {
                if (bytes >= 1024 * 1024 * 1024) {
                    return (bytes / (1024 * 1024 * 1024)).toFixed(2) + ' GB';
                } else if (bytes >= 1024 * 1024) {
                    return (bytes / (1024 * 1024)).toFixed(2) + ' MB';
                } else if (bytes >= 1024) {
                    return (bytes / 1024).toFixed(2) + ' KB';
                }
                return bytes + ' B';
            }
        }

        // Initialize dashboard
        new PerformanceDashboard();
    </script>

    <style>
        .performance-dashboard { padding: 2rem; }
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .metric-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            display: flex;
            align-items: center;
        }
        
        .metric-icon {
            font-size: 2rem;
            color: #007bff;
            margin-right: 1rem;
        }
        
        .metric-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #495057;
        }
        
        .metric-label {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .performance-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .performance-chart {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
        }
        
        #performance-chart {
            width: 100%;
            max-width: 600px;
            border: 1px solid #dee2e6;
        }
    </style>
@endsection