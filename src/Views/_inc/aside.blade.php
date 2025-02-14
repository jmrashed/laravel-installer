<aside class="sidebar max-w-[500px] min-w-[420px] h-screen flex flex-col items-end justify-center border-r-[2px] border-[var(--primary)] pr-20">
    <h2 class="title text-5xl font-bold text-theme mb-14">{{ trans('installer_messages.menus.title') }}</h2>
    <ul class="flex flex-col gap-[3rem]">
        <li class="
            {{
                (request()->routeIs('LaravelInstaller::purchase-validation') ||
                request()->routeIs('LaravelInstaller::server-requirements') ||
                request()->routeIs('LaravelInstaller::permissions') ||
                request()->routeIs('LaravelInstaller::environment-setting') ||
                request()->routeIs('LaravelInstaller::configuration-setting') ||
                request()->routeIs('LaravelInstaller::database-setting') ||
                request()->routeIs('LaravelInstaller::application-setting') ||
                request()->routeIs('LaravelInstaller::classic-text-editor') ||
                request()->routeIs('LaravelInstaller::installation-finished'))
                ? 'before:bg-primary'
                : 'before:bg-[var(--primary-border)]'
            }} justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[100px] before:z-[-1]"
        >
            <a
                href="{{ route('LaravelInstaller::purchase-validation') }}"
                class="
                {{
                    (request()->routeIs('LaravelInstaller::purchase-validation') ||
                    request()->routeIs('LaravelInstaller::server-requirements') ||
                    request()->routeIs('LaravelInstaller::permissions') ||
                    request()->routeIs('LaravelInstaller::environment-setting') ||
                    request()->routeIs('LaravelInstaller::configuration-setting') ||
                    request()->routeIs('LaravelInstaller::database-setting') ||
                    request()->routeIs('LaravelInstaller::application-setting') ||
                    request()->routeIs('LaravelInstaller::classic-text-editor') ||
                    request()->routeIs('LaravelInstaller::installation-finished'))
                    ? 'text-theme'
                    : 'text-primary'
                }} text-lg capitalize p-2 no-underline font-medium"
            >
                {{ trans('installer_messages.menus.purchaseValidation') }}
            </a>
            <div class="
                {{
                    (request()->routeIs('LaravelInstaller::purchase-validation') ||
                    request()->routeIs('LaravelInstaller::server-requirements') ||
                    request()->routeIs('LaravelInstaller::permissions') ||
                    request()->routeIs('LaravelInstaller::environment-setting') ||
                    request()->routeIs('LaravelInstaller::configuration-setting') ||
                    request()->routeIs('LaravelInstaller::database-setting') ||
                    request()->routeIs('LaravelInstaller::application-setting') ||
                    request()->routeIs('LaravelInstaller::classic-text-editor') ||
                    request()->routeIs('LaravelInstaller::installation-finished'))
                    ? 'bg-primary text-white'
                    : 'bg-white text-theme'
                }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]"
            >
                <i class="ri-shield-check-line"></i>
            </div>
        </li>

        <li class="
            {{
                (request()->routeIs('LaravelInstaller::server-requirements') ||
                request()->routeIs('LaravelInstaller::permissions') ||
                request()->routeIs('LaravelInstaller::environment-setting') ||
                request()->routeIs('LaravelInstaller::configuration-setting') ||
                request()->routeIs('LaravelInstaller::database-setting') ||
                request()->routeIs('LaravelInstaller::application-setting') ||
                request()->routeIs('LaravelInstaller::classic-text-editor') ||
                request()->routeIs('LaravelInstaller::installation-finished'))
                ? 'before:bg-primary'
                : 'before:bg-[var(--primary-border)]'
            }} justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[100px] before:z-[-1]"
        >
            <a
                href="{{ route('LaravelInstaller::server-requirements') }}"
                class="
                {{
                    (request()->routeIs('LaravelInstaller::server-requirements') ||
                    request()->routeIs('LaravelInstaller::permissions') ||
                    request()->routeIs('LaravelInstaller::environment-setting') ||
                    request()->routeIs('LaravelInstaller::configuration-setting') ||
                    request()->routeIs('LaravelInstaller::database-setting') ||
                    request()->routeIs('LaravelInstaller::application-setting') ||
                    request()->routeIs('LaravelInstaller::classic-text-editor') ||
                    request()->routeIs('LaravelInstaller::installation-finished'))
                    ? 'text-theme'
                    : 'text-primary'
                }} text-lg capitalize p-2 no-underline font-medium"
            >
                {{ trans('installer_messages.menus.serverRequirements') }}
            </a>
            <div class="
                {{
                    (request()->routeIs('LaravelInstaller::server-requirements') ||
                    request()->routeIs('LaravelInstaller::permissions') ||
                    request()->routeIs('LaravelInstaller::environment-setting') ||
                    request()->routeIs('LaravelInstaller::configuration-setting') ||
                    request()->routeIs('LaravelInstaller::database-setting') ||
                    request()->routeIs('LaravelInstaller::application-setting') ||
                    request()->routeIs('LaravelInstaller::classic-text-editor') ||
                    request()->routeIs('LaravelInstaller::installation-finished'))
                    ? 'bg-primary text-white'
                    : 'bg-white text-theme'
                }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]"
            >
                <i class="ri-terminal-window-line"></i>
            </div>
        </li>

        <li class="
            {{
                (request()->routeIs('LaravelInstaller::permissions') ||
                request()->routeIs('LaravelInstaller::environment-setting') ||
                request()->routeIs('LaravelInstaller::configuration-setting') ||
                request()->routeIs('LaravelInstaller::database-setting') ||
                request()->routeIs('LaravelInstaller::application-setting') ||
                request()->routeIs('LaravelInstaller::classic-text-editor') ||
                request()->routeIs('LaravelInstaller::installation-finished'))
                ? 'before:bg-primary'
                : 'before:bg-[var(--primary-border)]'
            }} justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[100px] before:z-[-1]"
        >
            <a
                href="{{ route('LaravelInstaller::permissions') }}"
                class="
                {{
                    (request()->routeIs('LaravelInstaller::permissions') ||
                    request()->routeIs('LaravelInstaller::environment-setting') ||
                    request()->routeIs('LaravelInstaller::configuration-setting') ||
                    request()->routeIs('LaravelInstaller::database-setting') ||
                    request()->routeIs('LaravelInstaller::application-setting') ||
                    request()->routeIs('LaravelInstaller::classic-text-editor') ||
                    request()->routeIs('LaravelInstaller::installation-finished'))
                    ? 'text-theme'
                    : 'text-primary'
                }} text-lg capitalize p-2 no-underline font-medium"
            >
                {{ trans('installer_messages.menus.permissions') }}
            </a>
            <div class="
                {{
                    (request()->routeIs('LaravelInstaller::permissions') ||
                    request()->routeIs('LaravelInstaller::environment-setting') ||
                    request()->routeIs('LaravelInstaller::configuration-setting') ||
                    request()->routeIs('LaravelInstaller::database-setting') ||
                    request()->routeIs('LaravelInstaller::application-setting') ||
                    request()->routeIs('LaravelInstaller::classic-text-editor') ||
                    request()->routeIs('LaravelInstaller::installation-finished'))
                    ? 'bg-primary text-white'
                    : 'bg-white text-theme'
                }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]"
            >
                <i class="ri-key-2-line"></i>
            </div>
        </li>

        <li class="
            {{
                (request()->routeIs('LaravelInstaller::environment-setting') ||
                request()->routeIs('LaravelInstaller::configuration-setting') ||
                request()->routeIs('LaravelInstaller::database-setting') ||
                request()->routeIs('LaravelInstaller::application-setting') ||
                request()->routeIs('LaravelInstaller::classic-text-editor') ||
                request()->routeIs('LaravelInstaller::installation-finished'))
                ? 'before:bg-primary'
                : 'before:bg-[var(--primary-border)]'
            }} justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[100px] before:z-[-1]"
        >
            <a
                href="{{ route('LaravelInstaller::environment-setting') }}"
                class="
                {{
                    (request()->routeIs('LaravelInstaller::environment-setting') ||
                    request()->routeIs('LaravelInstaller::configuration-setting') ||
                    request()->routeIs('LaravelInstaller::database-setting') ||
                    request()->routeIs('LaravelInstaller::application-setting') ||
                    request()->routeIs('LaravelInstaller::classic-text-editor') ||
                    request()->routeIs('LaravelInstaller::installation-finished'))
                    ? 'text-theme'
                    : 'text-primary'
                }} text-lg capitalize p-2 no-underline font-medium"
            >
                {{ trans('installer_messages.menus.environmentSettings') }}
            </a>
            <div class="
                {{
                    (request()->routeIs('LaravelInstaller::environment-setting') ||
                    request()->routeIs('LaravelInstaller::configuration-setting') ||
                    request()->routeIs('LaravelInstaller::database-setting') ||
                    request()->routeIs('LaravelInstaller::application-setting') ||
                    request()->routeIs('LaravelInstaller::classic-text-editor') ||
                    request()->routeIs('LaravelInstaller::installation-finished'))
                    ? 'bg-primary text-white'
                    : 'bg-white text-theme'
                }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]"
            >
                <i class="ri-settings-2-line"></i>
            </div>
        </li>

        <li class="
            {{
                request()->routeIs('LaravelInstaller::installation-finished')
                ? 'before:bg-primary'
                : 'before:bg-[var(--primary-border)]'
            }} justify-end sidebar-item flex gap-10 items-center relative before:absolute before:right-[22px] before:top-0 before:w-[2px] before:h-[100px] before:z-[-1]"
        >
            <a
                href="{{ route('LaravelInstaller::installation-finished') }}"
                class="
                {{
                    request()->routeIs('LaravelInstaller::installation-finished')
                    ? 'text-theme'
                    : 'text-primary'
                }} text-lg capitalize p-2 no-underline font-medium"
            >
                {{ trans('installer_messages.menus.installationFinished') }}
            </a>
            <div class="
                {{
                    request()->routeIs('LaravelInstaller::installation-finished')
                    ? 'bg-primary text-white'
                    : 'bg-white text-theme'
                }} icon text-2xl rounded-full w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]"
            >
                <i class="ri-check-double-line"></i>
            </div>
        </li>
    </ul>
</aside>
