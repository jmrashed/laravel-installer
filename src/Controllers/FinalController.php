<?php

namespace Jmrashed\LaravelInstaller\Controllers;

use Illuminate\Routing\Controller;
use Jmrashed\LaravelInstaller\Events\LaravelInstallerFinished;
use Jmrashed\LaravelInstaller\Helpers\EnvironmentManager;
use Jmrashed\LaravelInstaller\Helpers\FinalInstallManager;
use Jmrashed\LaravelInstaller\Helpers\InstalledFileManager;

class FinalController extends Controller
{
    /**
     * Update installed file and display finished view.
     *
     * @param  \Jmrashed\LaravelInstaller\Helpers\InstalledFileManager  $fileManager
     * @param  \Jmrashed\LaravelInstaller\Helpers\FinalInstallManager  $finalInstall
     * @param  \Jmrashed\LaravelInstaller\Helpers\EnvironmentManager  $environment
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function finish(InstalledFileManager $fileManager, FinalInstallManager $finalInstall, EnvironmentManager $environment)
    {
        $finalMessages = $finalInstall->runFinal();
        $finalStatusMessage = $fileManager->update();
        $finalEnvFile = $environment->getEnvContent();

        event(new LaravelInstallerFinished);

        return view('vendor.installer.finished', compact('finalMessages', 'finalStatusMessage', 'finalEnvFile'));
    }
}
