<div class="progress-container">
    <div class="progress-header">
        <h4>Installation Progress</h4>
        <span class="progress-percentage" id="progress-percentage">0%</span>
    </div>
    
    <div class="progress-bar-wrapper">
        <div class="progress-bar" id="main-progress-bar">
            <div class="progress-fill" id="progress-fill" style="width: 0%"></div>
        </div>
    </div>
    
    <div class="progress-steps" id="progress-steps">
        <div class="step" data-step="welcome">
            <div class="step-icon"><i class="fa fa-home"></i></div>
            <div class="step-label">Welcome</div>
        </div>
        <div class="step" data-step="dependencies">
            <div class="step-icon"><i class="fa fa-cube"></i></div>
            <div class="step-label">Dependencies</div>
        </div>
        <div class="step" data-step="requirements">
            <div class="step-icon"><i class="fa fa-check-circle"></i></div>
            <div class="step-label">Requirements</div>
        </div>
        <div class="step" data-step="permissions">
            <div class="step-icon"><i class="fa fa-lock"></i></div>
            <div class="step-label">Permissions</div>
        </div>
        <div class="step" data-step="environment">
            <div class="step-icon"><i class="fa fa-cog"></i></div>
            <div class="step-label">Environment</div>
        </div>
        <div class="step" data-step="database">
            <div class="step-icon"><i class="fa fa-database"></i></div>
            <div class="step-label">Database</div>
        </div>
        <div class="step" data-step="migration">
            <div class="step-icon"><i class="fa fa-play"></i></div>
            <div class="step-label">Migration</div>
        </div>
        <div class="step" data-step="optimization">
            <div class="step-icon"><i class="fa fa-cogs"></i></div>
            <div class="step-label">Optimization</div>
        </div>
        <div class="step" data-step="finished">
            <div class="step-icon"><i class="fa fa-flag-checkered"></i></div>
            <div class="step-label">Complete</div>
        </div>
    </div>
</div>

<script>
class ProgressManager {
    constructor() {
        this.init();
    }

    init() {
        this.loadProgress();
        setInterval(() => this.loadProgress(), 5000);
    }

    async loadProgress() {
        try {
            const response = await fetch('/installer/progress');
            const progress = await response.json();
            this.updateUI(progress);
        } catch (error) {
            console.error('Failed to load progress:', error);
        }
    }

    updateUI(progress) {
        // Update percentage
        document.getElementById('progress-percentage').textContent = progress.percentage + '%';
        document.getElementById('progress-fill').style.width = progress.percentage + '%';

        // Update steps
        const steps = document.querySelectorAll('.step');
        steps.forEach(step => {
            const stepName = step.dataset.step;
            const stepData = progress.steps[stepName];
            
            step.classList.remove('completed', 'current', 'pending');
            
            if (stepData && stepData.status === 'completed') {
                step.classList.add('completed');
            } else if (stepName === progress.current_step) {
                step.classList.add('current');
            } else {
                step.classList.add('pending');
            }
        });
    }

    async updateStep(step, status = 'completed', data = {}) {
        try {
            const response = await fetch('/installer/progress/update', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ step, status, data })
            });
            
            const progress = await response.json();
            this.updateUI(progress);
            return progress;
        } catch (error) {
            console.error('Failed to update progress:', error);
        }
    }
}

// Initialize progress manager
const progressManager = new ProgressManager();
</script>

<style>
.progress-container {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.progress-header h4 {
    margin: 0;
    color: #495057;
}

.progress-percentage {
    font-weight: bold;
    color: #28a745;
    font-size: 1.1em;
}

.progress-bar-wrapper {
    margin-bottom: 1.5rem;
}

.progress-bar {
    width: 100%;
    height: 8px;
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #28a745, #20c997);
    transition: width 0.5s ease;
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.step {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    flex: 1;
    position: relative;
}

.step:not(:last-child)::after {
    content: '';
    position: absolute;
    top: 15px;
    right: -50%;
    width: 100%;
    height: 2px;
    background: #dee2e6;
    z-index: 1;
}

.step.completed:not(:last-child)::after {
    background: #28a745;
}

.step-icon {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #dee2e6;
    color: #6c757d;
    margin-bottom: 0.5rem;
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
}

.step.completed .step-icon {
    background: #28a745;
    color: white;
}

.step.current .step-icon {
    background: #007bff;
    color: white;
    animation: pulse 2s infinite;
}

.step-label {
    font-size: 0.8em;
    color: #6c757d;
    margin-top: 0.25rem;
}

.step.completed .step-label {
    color: #28a745;
    font-weight: 500;
}

.step.current .step-label {
    color: #007bff;
    font-weight: 500;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
    100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
}

@media (max-width: 768px) {
    .progress-steps {
        flex-wrap: wrap;
    }
    
    .step {
        flex-basis: 33.333%;
        margin-bottom: 1rem;
    }
    
    .step:not(:last-child)::after {
        display: none;
    }
}
</style>