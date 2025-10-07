<?php

namespace Jmrashed\LaravelInstaller\Commands;

use Illuminate\Console\Command;
use Jmrashed\LaravelInstaller\Helpers\CacheQueueManager;

class ClearInstallerCaches extends Command
{
    protected $signature = 'installer:clear-caches';
    protected $description = 'Clear all Laravel caches during installation';

    public function handle()
    {
        $this->info('Clearing Laravel caches...');
        
        $results = CacheQueueManager::clearAllCaches();
        
        foreach ($results as $command => $result) {
            if ($result['success']) {
                $this->info("✓ {$result['description']} cleared");
            } else {
                $this->error("✗ Failed to clear {$result['description']}: {$result['error']}");
            }
        }
        
        $this->info('Cache clearing completed!');
    }
}