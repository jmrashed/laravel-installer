<?php

return [

    /*
     *
     * Shared translations.
     *
     */
    'title' => 'Laravel Installer',
    'next' => 'Next Step',
    'back' => 'Previous',
    'finish' => 'Install',
    'installation' => 'Installation',
    'version' => 'version',
    'required' => 'required',
    'forms' => [
        'errorTitle' => 'The Following errors occurred:',
    ],

    /*
     *
     * menus translations.
     *
     */
    'menus' => [
        'title' => 'INSTALLER',
        'purchaseValidation' => 'Purchase Validation',
        'serverRequirements' => 'Server Requirements',
        'permissions' => 'Permissions',
        'environmentSettings' => 'Environment Settings',
        'installationFinished' => 'Installation Finished',
    ],

    /*
     *
     * Home page translations.
     *
     */
    'welcome' => [
        'templateTitle' => 'Welcome',
        'title'   => 'Laravel Installer',
        'message' => 'Easy Installation and Setup Wizard.',
        'next'    => 'Start Installing',
    ],

    /*
     *
     * Start & Installing translations.
     *
     */
    'startInstalling' => [
        'templateTitle' => 'Start & Installing',
        'title'   => 'Start & Installing | Laravel Installer',
        'message' => 'Easy Installation and Setup Wizard.',
        'next'    => 'Next & Install',
    ],

    /*
     *
     * Purchase Validation translations.
     *
     */
    'purchaseValidation' => [
        'templateTitle' => 'Purchase Validation',
        'title'   => 'Purchase Validation | Laravel Installer',
        'message' => 'Validate Purchase Key',
        'previous'=> 'Welcome',
        'next'    => 'Server Requirements',
        'form' => [
            'purchaseCodeLabel' => 'Purchase Code',
            'domainLabel' => 'Domain',
            'emailLabel' => 'Email',
            'purchaseCodePlaceholder' => 'xxxxxx-xxxx-xxxx-xxxx-xxxxxx',
            'domainPlaceholder' => 'example.com',
            'emailPlaceholder' => 'test@example.com',
        ]
    ],

    /*
     *
     * Server Requirements translations.
     *
     */
    'serverRequirements' => [
        'templateTitle' => 'Server Requirements',
        'title'   => 'Server Requirements | Laravel Installer',
        'message' => 'Server Requirements',
        'previous'=> 'Purchase Validation',
        'next'    => 'Permissions'
    ],

    /*
     *
     * Permissions page translations.
     *
     */
    'permissions' => [
        'templateTitle' => 'Permissions',
        'title'   => 'Permissions | Laravel Installer',
        'message' => 'Permissions',
        'previous'=> 'Server Requirements',
        'next'    => 'Environment Settings'
    ],

    /*
     *
     * Environment Settings page translations.
     *
     */
    'environmentSettings' => [
        'templateTitle'     => 'Environment Settings',
        'title'             => 'Environment Settings | Laravel Installer',
        'message'           => 'Environment Settings',
        'formWizardSetup'   => 'Form Wizard Setup',
        'classicTextEditor' => 'Classic Text Editor'
    ],

    /*
     *
     * Configuration Setting page translations.
     *
     */
    'configurationSetting' => [
        'templateTitle' => 'Configuration Setting',
        'title'         => 'Configuration Setting | Laravel Installer',
        'message'       => 'Configuration Setting',
        'previous'      => 'Environment Settings',
        'next'          => 'Database Setting'
    ],

    /*
     *
     * Database Setting page translations.
     *
     */
    'databaseSetting' => [
        'templateTitle' => 'Database Setting',
        'title'         => 'Database Setting | Laravel Installer',
        'message'       => 'Database Setting',
        'previous'      => 'Configuration Setting',
        'next'          => 'Application Setting'
    ],

    /*
     *
     * Application Setting page translations.
     *
     */
    'applicationSetting' => [
        'templateTitle' => 'Application Setting',
        'title'         => 'Application Setting | Laravel Installer',
        'message'       => 'Application Setting',
        'previous'      => 'Database Setting',
        'next'          => 'Finish'
    ],

    /*
     *
     * Classic Text Editor page translations.
     *
     */
    'classicTextEditor' => [
        'templateTitle' => 'Classic Text Editor',
        'title'         => 'Classic Text Editor | Laravel Installer',
        'message'       => 'Classic Text Editor',
        'previous'      => 'Environment Settings',
        'next'          => 'Finish'
    ],

    /*
     *
     * Installation Finished page translations.
     *
     */
    'installationFinished' => [
        'templateTitle' => 'Installation Finished',
        'title'         => 'Installation Finished | Laravel Installer',
        'message'       => 'Installation Process Finished Successfully',
        'next'          => 'Go to Home'
    ],

    /*
     *
     * Environment page translations.
     *
     */
    'environment' => [
        'menu' => [
            'templateTitle' => 'Step 3 | Environment Settings',
            'title' => 'Environment Settings',
            'desc' => 'Please select how you want to configure the apps <code>.env</code> file.',
            'wizard-button' => 'Form Wizard Setup',
            'classic-button' => 'Classic Text Editor',
        ],
        'wizard' => [
            'templateTitle' => 'Step 3 | Environment Settings | Guided Wizard',
            'title' => 'Guided <code>.env</code> Wizard',
            'tabs' => [
                'environment' => 'Environment',
                'database' => 'Database',
                'application' => 'Application',
            ],
            'form' => [
                'name_required' => 'An environment name is required.',
                'app_name_label' => 'App Name',
                'app_name_placeholder' => 'App Name',
                'app_environment_label' => 'App Environment',
                'app_environment_label_local' => 'Local',
                'app_environment_label_developement' => 'Development',
                'app_environment_label_qa' => 'Qa',
                'app_environment_label_production' => 'Production',
                'app_environment_label_other' => 'Other',
                'app_environment_placeholder_other' => 'Enter your environment...',
                'app_debug_label' => 'App Debug',
                'app_debug_label_true' => 'True',
                'app_debug_label_false' => 'False',
                'app_log_level_label' => 'App Log Level',
                'app_log_level_label_debug' => 'debug',
                'app_log_level_label_info' => 'info',
                'app_log_level_label_notice' => 'notice',
                'app_log_level_label_warning' => 'warning',
                'app_log_level_label_error' => 'error',
                'app_log_level_label_critical' => 'critical',
                'app_log_level_label_alert' => 'alert',
                'app_log_level_label_emergency' => 'emergency',
                'app_url_label' => 'App Url',
                'app_url_placeholder' => 'App Url',
                'db_connection_failed' => 'Could not connect to the database.',
                'db_connection_label' => 'Database Connection',
                'db_connection_label_mysql' => 'mysql',
                'db_connection_label_sqlite' => 'sqlite',
                'db_connection_label_pgsql' => 'pgsql',
                'db_connection_label_sqlsrv' => 'sqlsrv',
                'db_host_label' => 'Database Host',
                'db_host_placeholder' => 'Database Host',
                'db_port_label' => 'Database Port',
                'db_port_placeholder' => 'Database Port',
                'db_name_label' => 'Database Name',
                'db_name_placeholder' => 'Database Name',
                'db_username_label' => 'Database User Name',
                'db_username_placeholder' => 'Database User Name',
                'db_password_label' => 'Database Password',
                'db_password_placeholder' => 'Database Password',

                'app_tabs' => [
                    'more_info' => 'More Info',
                    'broadcasting_title' => 'Broadcasting, Caching, Session, & Queue',
                    'broadcasting_label' => 'Broadcast Driver',
                    'broadcasting_placeholder' => 'Broadcast Driver',
                    'cache_label' => 'Cache Driver',
                    'cache_placeholder' => 'Cache Driver',
                    'session_label' => 'Session Driver',
                    'session_placeholder' => 'Session Driver',
                    'queue_label' => 'Queue Connection',
                    'queue_placeholder' => 'Queue Connection',
                    'redis_label' => 'Redis Driver',
                    'redis_host' => 'Redis Host',
                    'redis_password' => 'Redis Password',
                    'redis_port' => 'Redis Port',

                    'mail_label' => 'Mail',
                    'mail_mailer_label' => 'Mail Mailer',
                    'mail_mailer_placeholder' => 'Mail Mailer',
                    'mail_host_label' => 'Mail Host',
                    'mail_host_placeholder' => 'Mail Host',
                    'mail_port_label' => 'Mail Port',
                    'mail_port_placeholder' => 'Mail Port',
                    'mail_username_label' => 'Mail Username',
                    'mail_username_placeholder' => 'Mail Username',
                    'mail_password_label' => 'Mail Password',
                    'mail_password_placeholder' => 'Mail Password',
                    'mail_encryption_label' => 'Mail Encryption',
                    'mail_encryption_placeholder' => 'Mail Encryption',
                    'mail_from_address_label' => 'Mail From Address',
                    'mail_from_address_placeholder' => 'Mail From Address',
                    'mail_from_name_label' => 'Mail From Name',
                    'mail_from_name_placeholder' => 'Mail From Name',

                    'pusher_label' => 'Pusher',
                    'pusher_app_id_label' => 'Pusher App Id',
                    'pusher_app_id_palceholder' => 'Pusher App Id',
                    'pusher_app_key_label' => 'Pusher App Key',
                    'pusher_app_key_palceholder' => 'Pusher App Key',
                    'pusher_app_secret_label' => 'Pusher App Secret',
                    'pusher_app_secret_palceholder' => 'Pusher App Secret',
                ],
                'buttons' => [
                    'setup_database' => 'Setup Database',
                    'setup_application' => 'Setup Application',
                    'install' => 'Install',
                ],
            ],
        ],
        'classic' => [
            'templateTitle' => 'Step 3 | Environment Settings | Classic Editor',
            'title' => 'Classic Environment Editor',
            'save' => 'Save .env',
            'back' => 'Use Form Wizard',
            'install' => 'Save and Install',
        ],
        'success' => 'Your .env file settings have been saved.',
        'errors' => 'Unable to save the .env file, Please create it manually.',
    ],

    'install' => 'Install',

    /*
     *
     * Installed Log translations.
     *
     */
    'installed' => [
        'success_log_message' => 'Laravel Installer successfully INSTALLED on ',
    ],

    /*
     *
     * Final page translations.
     *
     */
    'final' => [
        'title' => 'Installation Finished',
        'templateTitle' => 'Installation Finished',
        'finished' => 'Application has been successfully installed.',
        'migration' => 'Migration &amp; Seed Console Output:',
        'console' => 'Application Console Output:',
        'log' => 'Installation Log Entry:',
        'env' => 'Final .env File:',
        'exit' => 'Click here to exit',
    ],

    /*
     *
     * Update specific translations
     *
     */
    'updater' => [
        /*
         *
         * Shared translations.
         *
         */
        'title' => 'Laravel Updater',

        /*
         *
         * Welcome page translations for update feature.
         *
         */
        'welcome' => [
            'title'   => 'Welcome To The Updater',
            'message' => 'Welcome to the update wizard.',
        ],

        /*
         *
         * Welcome page translations for update feature.
         *
         */
        'overview' => [
            'title'   => 'Overview',
            'message' => 'There is 1 update.|There are :number updates.',
            'install_updates' => 'Install Updates',
        ],

        /*
         *
         * Final page translations.
         *
         */
        'final' => [
            'title' => 'Finished',
            'finished' => 'Application\'s database has been successfully updated.',
            'exit' => 'Click here to exit',
        ],

        'log' => [
            'success_message' => 'Laravel Installer successfully UPDATED on ',
        ],
    ],
];
