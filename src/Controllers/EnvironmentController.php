<?php

namespace Jmrashed\LaravelInstaller\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Jmrashed\LaravelInstaller\Events\EnvironmentSaved;
use Jmrashed\LaravelInstaller\Helpers\EnvironmentManager;
use Jmrashed\LaravelInstaller\Helpers\LogManager;
use Jmrashed\LaravelInstaller\Helpers\DatabaseBackupManager;
use Jmrashed\LaravelInstaller\Helpers\ProgressTracker;
use Jmrashed\LaravelInstaller\Helpers\PerformanceMonitor;
use Jmrashed\LaravelInstaller\Exceptions\InstallerExceptionHandler;
use Validator;
use Carbon\Carbon;

class EnvironmentController extends Controller
{
    /**
     * @var EnvironmentManager
     */
    protected $EnvironmentManager;

    /**
     * @param  EnvironmentManager  $environmentManager
     */
    public function __construct(EnvironmentManager $environmentManager)
    {
        $this->EnvironmentManager = $environmentManager;
    }

    /**
     * Display the Environment menu page.
     *
     * @return \Illuminate\View\View
     */
    public function environmentMenu()
    {
        return view('vendor.installer.environment');
    }

    /**
     * Display the Environment page.
     *
     * @return \Illuminate\View\View
     */
    public function environmentWizard()
    {
        $envConfig = $this->EnvironmentManager->getEnvContent();

        return view('vendor.installer.environment-wizard', compact('envConfig'));
    }

    /**
     * Display the Environment page.
     *
     * @return \Illuminate\View\View
     */
    public function environmentClassic()
    {
        $envConfig = $this->EnvironmentManager->getEnvContent();

        return view('vendor.installer.environment-classic', compact('envConfig'));
    }

    /**
     * Processes the newly saved environment configuration (Classic).
     *
     * @param  Request  $input
     * @param  Redirector  $redirect
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveClassic(Request $input, Redirector $redirect)
    {
        PerformanceMonitor::startTimer('env_save_classic');
        
        try {
            // Rate limiting
            if (RateLimiter::tooManyAttempts('env-save:' . request()->ip(), 5)) {
                $this->logAudit('environment_save_rate_limited', ['ip' => request()->ip()]);
                return $redirect->back()->withErrors(['rate_limit' => 'Too many attempts. Please try again later.']);
            }
            RateLimiter::hit('env-save:' . request()->ip());

            // Input sanitization
            $sanitizedInput = $this->sanitizeInput($input->get('envConfig'));
            $input->merge(['envConfig' => $sanitizedInput]);

            // Create backup before saving
            $this->createEnvBackup();

            $message = $this->EnvironmentManager->saveFileClassic($input);

            LogManager::logOperation('environment_saved_classic', [
                'method' => 'classic',
                'success' => true
            ]);

            ProgressTracker::setStep('environment', 'completed');
            event(new EnvironmentSaved($input));

            $metrics = PerformanceMonitor::endTimer('env_save_classic');
            
            return $redirect->route('LaravelInstaller::installation-finished')
                            ->with(['message' => $message, 'performance' => $metrics]);
        } catch (Exception $e) {
            PerformanceMonitor::endTimer('env_save_classic');
            LogManager::logError('Environment save failed (classic)', $e, ['method' => 'classic']);
            $handler = new InstallerExceptionHandler();
            return $handler->handle($e, $input);
        }
    }

    /**
     * Processes the newly saved environment configuration (Form Wizard).
     *
     * @param  Request  $request
     * @param  Redirector  $redirect
     * @return \Illuminate\Http\RedirectResponse
     */
    public function saveWizard(Request $request, Redirector $redirect)
    {
        PerformanceMonitor::startTimer('env_save_wizard_' . $request->tab);
        
        try {
            // Rate limiting
            if (RateLimiter::tooManyAttempts('env-wizard:' . request()->ip(), 10)) {
                $this->logAudit('environment_wizard_rate_limited', ['ip' => request()->ip(), 'tab' => $request->tab]);
                return $redirect->back()->withErrors(['rate_limit' => 'Too many attempts. Please try again later.']);
            }
            RateLimiter::hit('env-wizard:' . request()->ip());

            // Get validation rules
            $rules = $this->getValidationRules($request->tab);
            if (!$rules) {
                throw new Exception('Invalid tab specified');
            }

            $messages = [
                'environment_custom.required_if' => trans('installer_messages.environment.wizard.form.name_required'),
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $this->logAudit('environment_validation_failed', [
                    'tab' => $request->tab,
                    'errors' => $validator->errors()->toArray()
                ]);
                return $this->redirectToTab($request->tab, $redirect)->withInput()->withErrors($validator->errors());
            }

            // Database connection test with timeout
            if ($request->tab == 'database' && !$this->checkDatabaseConnection($request)) {
                $this->logAudit('database_connection_failed', [
                    'host' => $request->input('database_hostname'),
                    'database' => $request->input('database_name')
                ]);
                return $redirect->route('LaravelInstaller::database-setting')->withInput()->withErrors([
                    'database_connection' => trans('installer_messages.environment.wizard.form.db_connection_failed'),
                ]);
            }

            // Create backup before saving
            if ($request->tab == 'database') {
                try {
                    $this->createDatabaseBackup($request);
                } catch (Exception $e) {
                    // Log but don't fail - backup is optional for env save
                    LogManager::logError('Database backup skipped', $e);
                }
            }
            $this->createEnvBackup();

            $results = $this->EnvironmentManager->saveFileWizard($request);

            LogManager::logOperation('environment_saved_wizard', [
                'tab' => $request->tab,
                'success' => true
            ]);

            // Update progress based on tab
            if ($request->tab === 'application') {
                ProgressTracker::setStep('environment', 'completed');
            }

            event(new EnvironmentSaved($request));

            $metrics = PerformanceMonitor::endTimer('env_save_wizard_' . $request->tab);
            
            return $this->getNextRoute($request->tab, $redirect, $results);
        } catch (Exception $e) {
            PerformanceMonitor::endTimer('env_save_wizard_' . $request->tab);
            LogManager::logError('Environment wizard failed', $e, ['tab' => $request->tab]);
            $handler = new InstallerExceptionHandler();
            return $handler->handle($e, $request);
        }
    }

    /**
     * Validate database connection with user credentials (Form Wizard).
     *
     * @param  Request  $request
     * @return bool
     */
    private function checkDatabaseConnection(Request $request)
    {
        if ($request->tab != 'database') {
            return false;
        }

        $connection = $request->input('database_connection');
        $settings = config("database.connections.$connection");

        if (!$settings) {
            Log::warning('Invalid database connection type', ['connection' => $connection]);
            return false;
        }

        config([
            'database' => [
                'default' => $connection,
                'connections' => [
                    $connection => array_merge($settings, [
                        'driver' => $connection,
                        'host' => $request->input('database_hostname'),
                        'port' => $request->input('database_port'),
                        'database' => $request->input('database_name'),
                        'username' => $request->input('database_username'),
                        'password' => $request->input('database_password'),
                        'options' => [
                            \PDO::ATTR_TIMEOUT => 5, // 5 second timeout
                        ],
                    ]),
                ],
            ],
        ]);

        DB::purge();

        try {
            $pdo = DB::connection()->getPdo();
            // Test with a simple query
            $pdo->query('SELECT 1');
            return true;
        } catch (Exception $e) {
            LogManager::logError('Database connection test failed', $e, [
                'host' => $request->input('database_hostname'),
                'database' => $request->input('database_name')
            ]);
            return false;
        }
    }

    /**
     * Get validation rules for specific tab
     */
    private function getValidationRules($tab)
    {
        $rules = [
            'configuration' => config('installer.environment.form.configuration_rules'),
            'database' => config('installer.environment.form.database_rules'),
            'application' => config('installer.environment.form.application_rules')
        ];

        return $rules[$tab] ?? null;
    }

    /**
     * Redirect to appropriate tab route
     */
    private function redirectToTab($tab, $redirect)
    {
        $routes = [
            'configuration' => 'LaravelInstaller::configuration-setting',
            'database' => 'LaravelInstaller::database-setting',
            'application' => 'LaravelInstaller::application-setting'
        ];

        return $redirect->route($routes[$tab] ?? 'LaravelInstaller::environment-setting');
    }

    /**
     * Get next route based on current tab
     */
    private function getNextRoute($tab, $redirect, $results)
    {
        $routes = [
            'configuration' => 'LaravelInstaller::database-setting',
            'database' => 'LaravelInstaller::database-backup',
            'application' => 'LaravelInstaller::cache-queue'
        ];

        return $redirect->route($routes[$tab])->with(['results' => $results]);
    }

    /**
     * Sanitize input to prevent XSS and injection attacks
     */
    private function sanitizeInput($input)
    {
        return htmlspecialchars(strip_tags($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Create backup of .env file
     */
    private function createEnvBackup()
    {
        try {
            $envPath = base_path('.env');
            if (file_exists($envPath)) {
                $backupPath = base_path('.env.backup.' . time());
                copy($envPath, $backupPath);
                Log::info('Environment backup created', ['backup_path' => $backupPath]);
            }
        } catch (Exception $e) {
            Log::warning('Failed to create env backup: ' . $e->getMessage());
        }
    }

    /**
     * Create database backup before migration
     */
    private function createDatabaseBackup(Request $request)
    {
        try {
            // Test connection first
            if (!$this->checkDatabaseConnection($request)) {
                throw new Exception('Cannot backup - database connection failed');
            }

            $backupId = DatabaseBackupManager::createBackup();
            Cache::put('installer_backup_id', $backupId, 3600);
            
            LogManager::logOperation('database_backup_created', [
                'backup_id' => $backupId,
                'database' => $request->input('database_name')
            ]);
            
            return $backupId;
        } catch (Exception $e) {
            LogManager::logError('Database backup failed', $e, [
                'database' => $request->input('database_name')
            ]);
            throw $e;
        }
    }

    /**
     * Log audit trail for security and compliance
     */
    private function logAudit($action, $data = [])
    {
        Log::channel('audit')->info($action, array_merge([
            'timestamp' => Carbon::now()->toISOString(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId()
        ], $data));
    }
}
