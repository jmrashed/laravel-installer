@extends('vendor.installer.layouts.imaster')

@section('template_title')
    {{ trans('installer_messages.applicationSetting.templateTitle') }}
@endsection

@section('icontent')
    <div class="flex">
        @include('vendor.installer._inc.aside')

        <div class="body-content w-full h-screen ">
            <h1 class="capitalize text-primary border-b-[2px] border-[var(--primary)] pl-20 py-5 text-2xl font-semibold mb-4">
                {{ env('APP_NAME') }}
            </h1>
            <form method="post" action="{{ route('LaravelInstaller::environmentSaveWizard') }}">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="tab" value="application">

                <div class="h-[80vh] w-full flex flex-col justify-between items-center gap-10 pl-4">
                    <div class="content-wrapper w-full">

                        @include('vendor.installer._inc.environment-nav')

                        <div class="grid gap-x-3 gap-y-3 grid-cols-2">
                            <div class="card bg-white p-6 rounded-md space-y-4">
                                <h4 class="text-sm no-underline bg-primary text-white font-medium text-start px-4 py-3 mb-6 rounded-[4px] w-full">
                                    {{ trans('installer_messages.environment.wizard.form.app_tabs.broadcasting_title') }}
                                </h4>
                                <div class="contact-form">
                                    <label for="broadcast_driver" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.broadcasting_label') }}
                                        <sup>
                                            <a href="https://laravel.com/docs/broadcasting" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                                <i class="ri-information-line"></i>
                                            </a>
                                        </sup>
                                    </label>
                                    <input
                                        type="text"
                                        name="broadcast_driver"
                                        id="broadcast_driver"
                                        value="{{ env('BROADCAST_DRIVER', 'log') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.broadcasting_placeholder') }}"
                                    />
                                    @if ($errors->has('broadcast_driver'))
                                        <span class="text-red">
                                            {{ $errors->first('broadcast_driver') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="cache_driver" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.cache_label') }}
                                        <sup>
                                            <a href="https://laravel.com/docs/cache" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                                <i class="ri-information-line"></i>
                                            </a>
                                        </sup>
                                    </label>
                                    <input
                                        type="text"
                                        id="cache_driver"
                                        name="cache_driver"
                                        value="{{ env('CACHE_DRIVER', 'file') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.cache_placeholder') }}"
                                    />
                                    @if ($errors->has('cache_driver'))
                                        <span class="text-red">
                                            {{ $errors->first('cache_driver') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="session_driver" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.session_label') }}
                                        <sup>
                                            <a href="https://laravel.com/docs/session" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                                <i class="ri-information-line"></i>
                                            </a>
                                        </sup>
                                    </label>
                                    <input
                                        ype="text"
                                        id="session_driver"
                                        name="session_driver"
                                        value="{{ env('SESSION_DRIVER', 'file') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.session_placeholder') }}"
                                    />
                                    @if ($errors->has('session_driver'))
                                        <span class="text-red">
                                            {{ $errors->first('session_driver') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="queue_connection" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.queue_label') }}
                                        <sup>
                                            <a href="https://laravel.com/docs/queues" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                                <i class="ri-information-line"></i>
                                            </a>
                                        </sup>
                                    </label>
                                    <input
                                        type="text"
                                        name="queue_connection"
                                        id="queue_connection"
                                        value="{{ env('QUEUE_CONNECTION', 'sync') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.queue_placeholder') }}"
                                    />
                                    @if ($errors->has('queue_connection'))
                                        <span class="text-red">
                                            {{ $errors->first('queue_connection') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="card bg-white p-6 rounded-md space-y-4">
                                <h4 class="text-sm no-underline bg-primary text-white font-medium text-start px-4 py-3 mb-6 rounded-[4px] w-full">
                                    {{ trans('installer_messages.environment.wizard.form.app_tabs.redis_label') }}
                                </h4>
                                <div class="contact-form">
                                    <label for="redis_hostname" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.redis_host') }}
                                        <sup>
                                            <a href="https://laravel.com/docs/redis" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.redis_host') }}">
                                                <i class="ri-information-line"></i>
                                            </a>
                                        </sup>
                                    </label>
                                    <input
                                        type="text"
                                        id="redis_hostname"
                                        name="redis_hostname"
                                        value="{{ env('REDIS_HOST') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.redis_host') }}"
                                    />
                                    @if ($errors->has('redis_hostname'))
                                        <span class="text-red">
                                            {{ $errors->first('redis_hostname') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="redis_password" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.redis_password') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="redis_password"
                                        name="redis_password"
                                        value="{{ env('REDIS_PASSWORD') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="********"
                                    />
                                    @if ($errors->has('redis_password'))
                                        <span class="text-red">
                                            {{ $errors->first('redis_password') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="redis_port" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.redis_port') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="redis_port"
                                        name="redis_port"
                                        value="{{ env('REDIS_PORT') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.redis_port') }}"
                                    />
                                    @if ($errors->has('redis_port'))
                                        <span class="text-red">
                                            {{ $errors->first('redis_port') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="card bg-white p-6 rounded-md space-y-4">
                                <h4 class="text-sm no-underline bg-primary text-white font-medium text-start px-4 py-3 mb-6 rounded-[4px] w-full">
                                    {{ trans('installer_messages.environment.wizard.form.app_tabs.mail_label') }}
                                </h4>
                                <div class="contact-form">
                                    <label for="mail_mailer" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.mail_mailer_label') }}
                                        <sup>
                                            <a href="https://laravel.com/docs/mail" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                                <i class="ri-information-line"></i>
                                            </a>
                                        </sup>
                                    </label>
                                    <input
                                        type="text"
                                        id="mail_mailer"
                                        name="mail_mailer"
                                        value="{{ env('MAIL_MAILER', 'smtp') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_mailer_placeholder') }}"
                                    />
                                    @if ($errors->has('mail_mailer'))
                                        <span class="text-red">
                                            {{ $errors->first('mail_mailer') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="mail_host" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.mail_host_label') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="mail_host"
                                        name="mail_host"
                                        value="{{ env('MAIL_HOST', 'smtp.gmail.com') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_host_placeholder') }}"
                                    />
                                    @if ($errors->has('mail_host'))
                                        <span class="text-red">
                                            {{ $errors->first('mail_host') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="mail_port" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.mail_port_label') }}
                                    </label>
                                    <input
                                        type="number"
                                        id="mail_port"
                                        name="mail_port"
                                        value="{{ env('MAIL_PORT', '587') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_port_placeholder') }}"
                                    />
                                    @if ($errors->has('mail_port'))
                                        <span class="text-red">
                                            {{ $errors->first('mail_port') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="mail_username" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.mail_username_label') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="mail_username"
                                        name="mail_username"
                                        value="{{ env('MAIL_USERNAME') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_username_placeholder') }}"
                                    />
                                    @if ($errors->has('mail_username'))
                                        <span class="text-red">
                                            {{ $errors->first('mail_username') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="mail_password" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.mail_password_label') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="mail_password"
                                        name="mail_password"
                                        value="{{ env('MAIL_PASSWORD') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_password_placeholder') }}"
                                    />
                                    @if ($errors->has('mail_password'))
                                        <span class="text-red">
                                            {{ $errors->first('mail_password') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="mail_encryption" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.mail_encryption_label') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="mail_encryption"
                                        name="mail_encryption"
                                        value="{{ env('MAIL_ENCRYPTION', 'tls') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_encryption_placeholder') }}"
                                    />
                                    @if ($errors->has('mail_encryption'))
                                        <span class="text-red">
                                            {{ $errors->first('mail_encryption') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="mail_from_address" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.mail_from_address_label') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="mail_from_address"
                                        name="mail_from_address"
                                        value="{{ env('MAIL_FROM_ADDRESS') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_from_address_placeholder') }}"
                                    />
                                    @if ($errors->has('mail_from_address'))
                                        <span class="text-red">
                                            {{ $errors->first('mail_from_address') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="mail_from_name" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.mail_from_name_label') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="mail_from_name"
                                        name="mail_from_name"
                                        value="{{ env('MAIL_FROM_NAME') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.mail_from_name_placeholder') }}"
                                    />
                                    @if ($errors->has('mail_from_name'))
                                        <span class="text-red">
                                            {{ $errors->first('mail_from_name') }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="card bg-white p-6 rounded-md space-y-4">
                                <h4 class="text-sm no-underline bg-primary text-white font-medium text-start px-4 py-3 mb-6 rounded-[4px] w-full">
                                    {{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_label') }}
                                </h4>
                                <div class="contact-form">
                                    <label for="pusher_app_id" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_app_id_label') }}
                                        <sup>
                                            <a href="https://pusher.com/docs/server_api_guide" target="_blank" title="{{ trans('installer_messages.environment.wizard.form.app_tabs.more_info') }}">
                                                <i class="ri-information-line"></i>
                                            </a>
                                        </sup>
                                    </label>
                                    <input
                                        type="text"
                                        id="pusher_app_id"
                                        name="pusher_app_id"
                                        value="{{ env('PUSHER_APP_ID') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_app_id_palceholder') }}"
                                    />
                                    @if ($errors->has('pusher_app_id'))
                                        <span class="text-red">
                                            {{ $errors->first('pusher_app_id') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="pusher_app_key" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_app_key_label') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="pusher_app_key"
                                        name="pusher_app_key"
                                        value="{{ env('PUSHER_APP_KEY') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_app_key_palceholder') }}"
                                    />
                                    @if ($errors->has('pusher_app_key'))
                                        <span class="text-red">
                                            {{ $errors->first('pusher_app_key') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="contact-form">
                                    <label for="pusher_app_secret" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                        {{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_app_secret_label') }}
                                    </label>
                                    <input
                                        type="text"
                                        id="pusher_app_secret"
                                        name="pusher_app_secret"
                                        value="{{ env('PUSHER_APP_SECRET') }}"
                                        class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                        placeholder="{{ trans('installer_messages.environment.wizard.form.app_tabs.pusher_app_secret_palceholder') }}"
                                    />
                                    @if ($errors->has('pusher_app_secret'))
                                        <span class="text-red">
                                            {{ $errors->first('pusher_app_secret') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 items-center justify-center">
                        <div class="flex gap-4 items-center justify-center">
                            <a href="{{ route('LaravelInstaller::database-setting') }}" class="btn-primary-outline">
                                <i class="ri-arrow-left-line"></i>
                                {{ trans('installer_messages.applicationSetting.previous') }}
                            </a>
                            <button class="btn-primary-fill">
                                {{ trans('installer_messages.applicationSetting.next') }}
                                <i class="ri-arrow-right-s-line"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
