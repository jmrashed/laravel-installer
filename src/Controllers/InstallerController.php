<?php

namespace Jmrashed\LaravelInstaller\Controllers;

use Illuminate\Routing\Controller;
use Jmrashed\LaravelInstaller\Helpers\PermissionsChecker;
use Jmrashed\LaravelInstaller\Helpers\RequirementsChecker;

class InstallerController extends Controller
{
    protected $requirements;
    protected $permissions;

    /**
     * @param  RequirementsChecker  $checker
     */
    public function __construct(RequirementsChecker $requirements, PermissionsChecker $permissions)
    {
        $this->requirements = $requirements;
        $this->permissions = $permissions;
    }

    public function welcome()
    {
        return view('vendor.installer.welcome');
    }

    public function startInstalling()
    {
        return view('vendor.installer.start-installing');
    }

    public function purchaseValidation()
    {
        return view('vendor.installer.purchase-validation');
    }

    public function serverRequirements()
    {
        $phpSupportInfo = $this->requirements->checkPHPversion(
            config('installer.core.minPhpVersion')
        );
        $requirements = $this->requirements->check(
            config('installer.requirements')
        );

        return view('vendor.installer.server-requirements', compact('requirements', 'phpSupportInfo'));
    }

    public function permissions()
    {
        $permissions = $this->permissions->check(
            config('installer.permissions')
        );

        return view('vendor.installer.permissions', compact('permissions'));
    }

    public function environmentSetting()
    {
        return view('vendor.installer.environment-setting');
    }

    public function configurationSetting()
    {
        return view('vendor.installer.configuration-setting');
    }

    public function databaseSetting()
    {
        return view('vendor.installer.database-setting');
    }

    public function applicationSetting()
    {
        return view('vendor.installer.application-setting');
    }

    public function classicTextEditor()
    {
        $envPath = base_path('.env');
        $envContent = file_exists($envPath) ? file_get_contents($envPath) : '';

        return view('vendor.installer.classic-text-editor', compact('envContent'));
    }

    public function installationFinished()
    {
        return view('vendor.installer.installation-finished');
    }

    public function dependencies()
    {
        return view('vendor.installer.dependencies');
    }

    public function performanceDashboard()
    {
        return view('vendor.installer.performance-dashboard');
    }

    public function cacheQueue()
    {
        return view('vendor.installer.cache-queue');
    }

    public function databaseBackup()
    {
        return view('vendor.installer.database-backup');
    }

    public function resumeInstallation()
    {
        return view('vendor.installer.resume-installation');
    }
}
