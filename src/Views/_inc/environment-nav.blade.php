<ul class="flex justify-center gap-[2rem] mb-10 mt-10">
    <a
        href="{{ route('LaravelInstaller::configuration-setting') }}"
        class="
            {{
                (request()->routeIs('LaravelInstaller::database-setting') ||
                request()->routeIs('LaravelInstaller::application-setting'))
                ? 'before:bg-primary'
                : 'before:bg-[var(--primary-border)]'
            }} sidebar-item w-[150px] flex flex-col gap-2 items-center justify-end relative before:absolute before:top-5 before:left-[100px] before:w-[150px] before:h-[2px] before:z-[-1]
        "
    >
        <div class="bg-primary text-white icon text-2xl rounded-md w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]">
            <i class="ri-settings-2-line"></i>
        </div>
        <span class="text-lg capitalize no-underline text-theme font-medium">
            Configuration
        </span>
    </a>
    <a
        href="{{ route('LaravelInstaller::database-setting') }}"
        class="
            {{
                request()->routeIs('LaravelInstaller::application-setting')
                ? 'before:bg-primary'
                : 'before:bg-[var(--primary-border)]'
            }} sidebar-item w-[150px] flex flex-col gap-2 items-center justify-end relative before:absolute before:top-5 before:left-[100px] before:w-[150px] before:h-[2px] before:z-[-1]
        "
    >
        <div
            class="
                {{
                    (request()->routeIs('LaravelInstaller::database-setting') ||
                    request()->routeIs('LaravelInstaller::application-setting'))
                    ? 'bg-primary text-white'
                    : 'bg-white text-theme'
                }} icon text-2xl rounded-md w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]
            "
        >
            <i class="ri-database-2-line"></i>
        </div>
        <span
            class="
                {{
                    (request()->routeIs('LaravelInstaller::database-setting') ||
                    request()->routeIs('LaravelInstaller::application-setting'))
                    ? 'text-theme'
                    : 'text-primary'
                }} text-lg capitalize no-underline font-medium
            "
        >
            Database
        </span>
    </a>
    <a href="{{ route('LaravelInstaller::application-setting') }}" class="sidebar-item w-[150px] flex flex-col gap-2 items-center justify-end relative before:absolute before:top-5 before:left-[100px]  before:bg-[var(--primary-border)] before:w-[150px] before:h-[2px] before:z-[-1]">
        <div
            class="
                {{
                    request()->routeIs('LaravelInstaller::application-setting')
                    ? 'bg-primary text-white'
                    : 'bg-white text-theme'
                }} icon text-2xl rounded-md w-[50px] h-[50px] flex items-center justify-center shadow-[0px_0px_16px_rgba(17,_17,_26,_0.1)]
            "
        >
            <i class="ri-computer-line"></i>
        </div>
        <span
            class="
                {{
                    request()->routeIs('LaravelInstaller::application-setting')
                    ? 'text-theme'
                    : 'text-primary'
                }} text-lg capitalize no-underline font-medium
            "
        >
            Application
        </span>
    </a>
</ul>
