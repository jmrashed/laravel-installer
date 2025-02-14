@extends('vendor.installer.layouts.imaster')

@section('template_title')
    {{ trans('installer_messages.configurationSetting.templateTitle') }}
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
                <input type="hidden" name="tab" value="configuration">

                <div class="h-[80vh] w-full flex flex-col justify-between items-center gap-10 pl-4">
                    <div class="content-wrapper w-full">
                        @include('vendor.installer._inc.environment-nav')

                        <div class="card bg-white p-6 w-full rounded-md space-y-4">
                            <div class="contact-form">
                                <label for="app_name" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.environment.wizard.form.app_name_label') }}
                                </label>
                                <input
                                    name="app_name"
                                    id="app_name"
                                    value="{{ env('APP_NAME') }}"
                                    class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                    placeholder="{{ trans('installer_messages.environment.wizard.form.app_name_placeholder') }}"
                                />
                                @if ($errors->has('app_name'))
                                    <span class="text-red">
                                        {{ $errors->first('app_name') }}
                                    </span>
                                @endif
                            </div>

                            <div class="contact-form">
                                <label class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.environment.wizard.form.app_environment_label') }}
                                </label>
                                <select
                                    class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm bg-transparent"
                                    name="environment"
                                    id="environment"
                                >
                                    <option value="local" {{ env('APP_ENV') == 'local' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.app_environment_label_local') }}
                                    </option>
                                    <option value="development" {{ env('APP_ENV') == 'development' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.app_environment_label_developement') }}
                                    </option>
                                    <option value="qa" {{ env('APP_ENV') == 'qa' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.app_environment_label_qa') }}
                                    </option>
                                    <option value="production" {{ env('APP_ENV') == 'production' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.app_environment_label_production') }}
                                    </option>
                                </select>
                                @if ($errors->has('environment'))
                                    <span class="text-red">
                                        {{ $errors->first('environment') }}
                                    </span>
                                @endif
                            </div>

                            <div class="contact-form space-x-1 mb-6">
                                <label
                                    class="text-primary block text-sm font-semibold text-gray-700 capitalize mb-5"
                                >
                                    {{ trans('installer_messages.environment.wizard.form.app_debug_label') }}
                                </label>
                                <div class="flex items-center mt-2">
                                    <input
                                        type="radio"
                                        class="h-4 w-4 text-primary border-gray-300 focus:ring-primary cursor-pointer"
                                        name="app_debug"
                                        id="app_debug_true"
                                        value="true"
                                        {{ env('APP_DEBUG') ? 'checked' : '' }}
                                    />
                                    <label
                                        for="app_debug_true"
                                        class="ml-2 block text-sm text-gray-700 cursor-pointer"
                                    >
                                        {{ trans('installer_messages.environment.wizard.form.app_debug_label_true') }}
                                    </label>
                                </div>
                                <div class="flex items-center mt-2">
                                    <input
                                        type="radio"
                                        class="h-4 w-4 text-primary border-gray-300 focus:ring-primary cursor-pointer"
                                        name="app_debug"
                                        id="app_debug_false"
                                        value="false"
                                        {{ !env('APP_DEBUG') ? 'checked' : '' }}
                                    />
                                    <label
                                        for="app_debug_false"
                                        class="ml-2 block text-sm text-gray-700 cursor-pointer"
                                    >
                                        {{ trans('installer_messages.environment.wizard.form.app_debug_label_false') }}
                                    </label>
                                </div>
                            </div>

                            <div class="contact-form">
                                <label for="app_log_level" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.environment.wizard.form.app_log_level_label') }}
                                </label>
                                <select name="app_log_level" id="app_log_level" class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm bg-transparent">
                                    <option value="debug" {{ env('APP_LOG_LEVEL') == 'debug' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.app_log_level_label_debug') }}
                                    </option>
                                    <option value="info" {{ env('APP_LOG_LEVEL') == 'info' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.app_log_level_label_info') }}
                                    </option>
                                    <option value="notice" {{ env('APP_LOG_LEVEL') == 'notice' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.app_log_level_label_notice') }}
                                    </option>
                                    <option value="warning" {{ env('APP_LOG_LEVEL') == 'warning' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.app_log_level_label_warning') }}
                                    </option>
                                    <option value="error" {{ env('APP_LOG_LEVEL') == 'error' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.app_log_level_label_error') }}
                                    </option>
                                    <option value="critical" {{ env('APP_LOG_LEVEL') == 'critical' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.app_log_level_label_critical') }}
                                    </option>
                                    <option value="alert" {{ env('APP_LOG_LEVEL') == 'alert' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.app_log_level_label_alert') }}
                                    </option>
                                    <option value="emergency" {{ env('APP_LOG_LEVEL') == 'emergency' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.app_log_level_label_emergency') }}
                                    </option>
                                </select>
                                @if ($errors->has('app_log_level'))
                                    <span class="text-red">
                                        {{ $errors->first('app_log_level') }}
                                    </span>
                                @endif
                            </div>

                            <div class="contact-form">
                                <label for="app_url" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.environment.wizard.form.app_url_label') }}
                                </label>
                                <input
                                    type="url"
                                    id="app_url"
                                    name="app_url"
                                    class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                    value="{{ env('APP_URL') }}"
                                    placeholder="{{ trans('installer_messages.environment.wizard.form.app_url_placeholder') }}"
                                />
                                @if ($errors->has('app_url'))
                                    <span class="text-red">
                                        {{ $errors->first('app_url') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 items-center justify-center">
                        <a
                            href="{{ route('LaravelInstaller::environment-setting') }}"
                            class="btn-primary-outline"
                        >
                            <i class="ri-arrow-left-line"></i>
                            {{ trans('installer_messages.configurationSetting.previous') }}
                        </a>
                        <button class="btn-primary-fill">
                            {{ trans('installer_messages.configurationSetting.next') }}
                            <i class="ri-arrow-right-s-line"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
