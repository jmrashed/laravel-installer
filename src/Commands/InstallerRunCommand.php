<?php

namespace Jmrashed\LaravelInstaller\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class InstallerRunCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'installer:run {--force : Force run even if already installed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the Laravel web installer';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 Starting Laravel Web Installer...');

        // Check if already installed
        if (!$this->option('force') && file_exists(base_path('.env')) && file_exists(storage_path('installed'))) {
            $this->error('❌ Laravel appears to be already installed.');
            $this->line('Use --force flag to run anyway.');
            return Command::FAILURE;
        }

        $this->info('📋 Opening installer in your default browser...');
        $this->line('If browser doesn\'t open automatically, visit: ' . url('/install'));

        // Try to open browser
        $url = url('/install');
        if (PHP_OS_FAMILY === 'Windows') {
            exec("start $url");
        } elseif (PHP_OS_FAMILY === 'Linux') {
            exec("xdg-open $url");
        } elseif (PHP_OS_FAMILY === 'Darwin') {
            exec("open $url");
        }

        $this->info('✅ Installer is now accessible at: ' . $url);
        $this->line('Follow the on-screen instructions to complete the installation.');

        return Command::SUCCESS;
    }
}