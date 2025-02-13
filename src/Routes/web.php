<?php

Route::group(['prefix' => 'install', 'as' => 'LaravelInstaller::', 'namespace' => 'Jmrashed\LaravelInstaller\Controllers', 'middleware' => ['web', 'install']], function () {

    Route::get('/', [
        'as' => 'welcome',
        'uses' => 'InstallerController@welcome',
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
