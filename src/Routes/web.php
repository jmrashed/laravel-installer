<?php

Route::group(['prefix' => 'install', 'as' => 'LaravelInstaller::', 'namespace' => 'Jmrashed\LaravelInstaller\Controllers', 'middleware' => ['web', 'install', 'security', 'performance', 'progress', 'dependency']], function () {

    Route::get('/', [
        'as' => 'welcome',
        'uses' => 'InstallerController@welcome',
    ]);

    Route::get('/dependencies', [
        'as' => 'dependencies',
        'uses' => 'InstallerController@dependencies',
    ]);

    Route::get('/performance-dashboard', [
        'as' => 'performance-dashboard',
        'uses' => 'InstallerController@performanceDashboard',
    ]);

    Route::get('/cache-queue', [
        'as' => 'cache-queue',
        'uses' => 'InstallerController@cacheQueue',
    ]);

    Route::get('/database-backup', [
        'as' => 'database-backup',
        'uses' => 'InstallerController@databaseBackup',
    ]);

    Route::get('/resume-installation', [
        'as' => 'resume-installation',
        'uses' => 'InstallerController@resumeInstallation',
    ]);

    Route::post('environment/saveWizard', [
        'as' => 'environmentSaveWizard',
        'uses' => 'EnvironmentController@saveWizard',
    ]);

    Route::post('environment/saveClassic', [
        'as' => 'environmentSaveClassic',
        'uses' => 'EnvironmentController@saveClassic',
    ]);

    Route::get('final', [
        'as' => 'final',
        'uses' => 'FinalController@finish',
    ]);

    Route::get('/purchase-validation', [
        'as' => 'purchase-validation',
        'uses' => 'InstallerController@purchaseValidation',
    ]);

    Route::get('/server-requirements', [
        'as' => 'server-requirements',
        'uses' => 'InstallerController@serverRequirements',
    ]);

    Route::get('/permissions', [
        'as' => 'permissions',
        'uses' => 'InstallerController@permissions',
    ]);

    Route::get('/environment-setting', [
        'as' => 'environment-setting',
        'uses' => 'InstallerController@environmentSetting',
    ]);

    Route::get('/configuration-setting', [
        'as' => 'configuration-setting',
        'uses' => 'InstallerController@configurationSetting',
    ]);

    Route::get('/database-setting', [
        'as' => 'database-setting',
        'uses' => 'InstallerController@databaseSetting',
    ]);

    Route::get('/application-setting', [
        'as' => 'application-setting',
        'uses' => 'InstallerController@applicationSetting',
    ]);

    Route::get('/classic-text-editor', [
        'as' => 'classic-text-editor',
        'uses' => 'InstallerController@classicTextEditor',
    ]);

    Route::get('/installation-finished', [
        'as' => 'installation-finished',
        'uses' => 'InstallerController@installationFinished',
    ]);
    // validatePurchase
    Route::post('/validate-purchase', [
        'as' => 'validate-purchase',
        'uses' => 'PurchaseController@validatePurchase',
    ]);

    // API routes for AJAX calls
    Route::get('/api/progress', [
        'as' => 'api.progress',
        'uses' => 'ProgressController@getProgress',
    ]);

    Route::post('/api/progress/update', [
        'as' => 'api.progress.update',
        'uses' => 'ProgressController@updateProgress',
    ]);

    Route::get('/api/dependencies/check', [
        'as' => 'api.dependencies.check',
        'uses' => 'DependencyController@check',
    ]);

    Route::post('/api/dependencies/install', [
        'as' => 'api.dependencies.install',
        'uses' => 'DependencyController@install',
    ]);

    Route::get('/api/performance/metrics', [
        'as' => 'api.performance.metrics',
        'uses' => 'PerformanceController@getMetrics',
    ]);

    Route::post('/api/cache/clear', [
        'as' => 'api.cache.clear',
        'uses' => 'CacheQueueController@clearCaches',
    ]);

    Route::post('/api/queue/setup', [
        'as' => 'api.queue.setup',
        'uses' => 'CacheQueueController@setupQueues',
    ]);

    Route::post('/api/database/migrate', [
        'as' => 'api.database.migrate',
        'uses' => 'DatabaseController@migrate',
    ]);

    Route::post('/api/database/rollback', [
        'as' => 'api.database.rollback',
        'uses' => 'DatabaseController@rollback',
    ]);

    Route::post('/api/scheduler/setup', [
        'as' => 'scheduler.setup',
        'uses' => 'CacheQueueController@setupScheduler',
    ]);

    Route::post('/api/performance/optimize', [
        'as' => 'performance.optimize',
        'uses' => 'PerformanceController@optimize',
    ]);
});

Route::group(['prefix' => 'update', 'as' => 'LaravelUpdater::', 'namespace' => 'Jmrashed\LaravelInstaller\Controllers', 'middleware' => 'web'], function () {
    Route::group(['middleware' => 'update'], function () {
        Route::get('/', [
            'as' => 'welcome',
            'uses' => 'UpdateController@welcome',
        ]);

        Route::get('overview', [
            'as' => 'overview',
            'uses' => 'UpdateController@overview',
        ]);

        Route::get('database', [
            'as' => 'database',
            'uses' => 'UpdateController@database',
        ]);
    });

    // This needs to be out of the middleware because right after the migration has been
    // run, the middleware sends a 404.
    Route::get('final', [
        'as' => 'final',
        'uses' => 'UpdateController@finish',
    ]);
});
