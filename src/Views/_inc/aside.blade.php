<aside class="sidebar max-w-[500px] min-w-[420px] h-screen flex flex-col items-end justify-center border-r-[2px] border-[var(--primary)] pr-20">
    <h2 class="title text-5xl font-bold text-theme mb-14">{{ trans('installer_messages.menus.title') }}</h2>
    <ul class="flex flex-col gap-[2rem]">
        <!-- Step 1: Purchase Validation -->
        <li class="justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[80px] before:z-[-1] before:bg-[var(--primary-border)]">
            @if(Route::has('LaravelInstaller::purchase-validation'))
                <a href="{{ route('LaravelInstaller::purchase-validation') }}" class="{{ request()->routeIs('LaravelInstaller::purchase-validation') ? 'text-theme' : 'text-primary' }} text-lg capitalize p-2 no-underline font-medium">
                    Purchase Validation
                </a>
            @else
                <span class="text-primary text-lg capitalize p-2 font-medium">Purchase Validation</span>
            @endif
            <div class="{{ request()->routeIs('LaravelInstaller::purchase-validation') ? 'bg-primary text-white' : 'bg-white text-theme' }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]">
                <i class="ri-shield-check-line"></i>
            </div>
        </li>

        <!-- Step 2: Server Requirements -->
        <li class="justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[80px] before:z-[-1] before:bg-[var(--primary-border)]">
            @if(Route::has('LaravelInstaller::server-requirements'))
                <a href="{{ route('LaravelInstaller::server-requirements') }}" class="{{ request()->routeIs('LaravelInstaller::server-requirements') ? 'text-theme' : 'text-primary' }} text-lg capitalize p-2 no-underline font-medium">
                    Server Requirements
                </a>
            @else
                <span class="text-primary text-lg capitalize p-2 font-medium">Server Requirements</span>
            @endif
            <div class="{{ request()->routeIs('LaravelInstaller::server-requirements') ? 'bg-primary text-white' : 'bg-white text-theme' }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]">
                <i class="ri-terminal-window-line"></i>
            </div>
        </li>

        <!-- Step 3: Permissions -->
        <li class="justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[80px] before:z-[-1] before:bg-[var(--primary-border)]">
            @if(Route::has('LaravelInstaller::permissions'))
                <a href="{{ route('LaravelInstaller::permissions') }}" class="{{ request()->routeIs('LaravelInstaller::permissions') ? 'text-theme' : 'text-primary' }} text-lg capitalize p-2 no-underline font-medium">
                    Permissions
                </a>
            @else
                <span class="text-primary text-lg capitalize p-2 font-medium">Permissions</span>
            @endif
            <div class="{{ request()->routeIs('LaravelInstaller::permissions') ? 'bg-primary text-white' : 'bg-white text-theme' }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]">
                <i class="ri-key-2-line"></i>
            </div>
        </li>

        <!-- Step 4: Dependencies Check (NEW) -->
        <li class="justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[80px] before:z-[-1] before:bg-[var(--primary-border)]">
            @if(Route::has('LaravelInstaller::dependencies'))
                <a href="{{ route('LaravelInstaller::dependencies') }}" class="{{ request()->routeIs('LaravelInstaller::dependencies') ? 'text-theme' : 'text-primary' }} text-lg capitalize p-2 no-underline font-medium">
                    Dependencies
                </a>
            @else
                <span class="text-primary text-lg capitalize p-2 font-medium">Dependencies</span>
            @endif
            <div class="{{ request()->routeIs('LaravelInstaller::dependencies') ? 'bg-primary text-white' : 'bg-white text-theme' }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]">
                <i class="ri-package-line"></i>
            </div>
        </li>

        <!-- Step 5: Environment Settings -->
        <li class="justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[80px] before:z-[-1] before:bg-[var(--primary-border)]">
            @if(Route::has('LaravelInstaller::environment-setting'))
                <a href="{{ route('LaravelInstaller::environment-setting') }}" class="{{ request()->routeIs('LaravelInstaller::environment-setting') ? 'text-theme' : 'text-primary' }} text-lg capitalize p-2 no-underline font-medium">
                    Environment
                </a>
            @else
                <span class="text-primary text-lg capitalize p-2 font-medium">Environment</span>
            @endif
            <div class="{{ request()->routeIs('LaravelInstaller::environment-setting') ? 'bg-primary text-white' : 'bg-white text-theme' }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]">
                <i class="ri-settings-2-line"></i>
            </div>
        </li>

        <!-- Step 6: Database Settings -->
        <li class="justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[80px] before:z-[-1] before:bg-[var(--primary-border)]">
            @if(Route::has('LaravelInstaller::database-setting'))
                <a href="{{ route('LaravelInstaller::database-setting') }}" class="{{ request()->routeIs('LaravelInstaller::database-setting') ? 'text-theme' : 'text-primary' }} text-lg capitalize p-2 no-underline font-medium">
                    Database
                </a>
            @else
                <span class="text-primary text-lg capitalize p-2 font-medium">Database</span>
            @endif
            <div class="{{ request()->routeIs('LaravelInstaller::database-setting') ? 'bg-primary text-white' : 'bg-white text-theme' }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]">
                <i class="ri-database-2-line"></i>
            </div>
        </li>

        <!-- Step 7: Database Backup & Migration (NEW) -->
        <li class="justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[80px] before:z-[-1] before:bg-[var(--primary-border)]">
            @if(Route::has('LaravelInstaller::database-backup'))
                <a href="{{ route('LaravelInstaller::database-backup') }}" class="{{ request()->routeIs('LaravelInstaller::database-backup') ? 'text-theme' : 'text-primary' }} text-lg capitalize p-2 no-underline font-medium">
                    Migration
                </a>
            @else
                <span class="text-primary text-lg capitalize p-2 font-medium">Migration</span>
            @endif
            <div class="{{ request()->routeIs('LaravelInstaller::database-backup') ? 'bg-primary text-white' : 'bg-white text-theme' }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]">
                <i class="ri-hard-drive-2-line"></i>
            </div>
        </li>

        <!-- Step 8: Cache & Queue Setup (NEW) -->
        <li class="justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[80px] before:z-[-1] before:bg-[var(--primary-border)]">
            @if(Route::has('LaravelInstaller::cache-queue'))
                <a href="{{ route('LaravelInstaller::cache-queue') }}" class="{{ request()->routeIs('LaravelInstaller::cache-queue') ? 'text-theme' : 'text-primary' }} text-lg capitalize p-2 no-underline font-medium">
                    Cache & Queue
                </a>
            @else
                <span class="text-primary text-lg capitalize p-2 font-medium">Cache & Queue</span>
            @endif
            <div class="{{ request()->routeIs('LaravelInstaller::cache-queue') ? 'bg-primary text-white' : 'bg-white text-theme' }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]">
                <i class="ri-rocket-line"></i>
            </div>
        </li>

        <!-- Step 9: Performance Dashboard (NEW) -->
        <li class="justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[80px] before:z-[-1] before:bg-[var(--primary-border)]">
            @if(Route::has('LaravelInstaller::performance-dashboard'))
                <a href="{{ route('LaravelInstaller::performance-dashboard') }}" class="{{ request()->routeIs('LaravelInstaller::performance-dashboard') ? 'text-theme' : 'text-primary' }} text-lg capitalize p-2 no-underline font-medium">
                    Performance
                </a>
            @else
                <span class="text-primary text-lg capitalize p-2 font-medium">Performance</span>
            @endif
            <div class="{{ request()->routeIs('LaravelInstaller::performance-dashboard') ? 'bg-primary text-white' : 'bg-white text-theme' }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]">
                <i class="ri-speed-line"></i>
            </div>
        </li>

        <!-- Step 10: Installation Finished -->
        <li class="justify-end sidebar-item flex gap-10 items-center">
            @if(Route::has('LaravelInstaller::installation-finished'))
                <a href="{{ route('LaravelInstaller::installation-finished') }}" class="{{ request()->routeIs('LaravelInstaller::installation-finished') ? 'text-theme' : 'text-primary' }} text-lg capitalize p-2 no-underline font-medium">
                    Finished
                </a>
            @else
                <span class="text-primary text-lg capitalize p-2 font-medium">Finished</span>
            @endif
            <div class="{{ request()->routeIs('LaravelInstaller::installation-finished') ? 'bg-primary text-white' : 'bg-white text-theme' }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]">
                <i class="ri-check-double-line"></i>
            </div>
        </li>
    </ul>
</aside>de>
