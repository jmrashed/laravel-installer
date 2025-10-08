@extends('vendor.installer.layouts.imaster')

@section('template_title')
    {{ trans('installer_messages.databaseSetting.templateTitle') }}
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
                <input type="hidden" name="tab" value="database">

                <div class="h-[80vh] w-full flex flex-col justify-between items-center gap-10 pl-4"  style="background: #ffffffc4; padding: 15px;">
                    <div class="content-wrapper w-full">

                        @include('vendor.installer._inc.environment-nav')

                        <div class="card bg-white p-6 w-full rounded-md space-y-4">
                            <div class="contact-form">
                                <label for="database_connection" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.environment.wizard.form.db_connection_label') }}
                                </label>
                                <select name="database_connection" id="database_connection" class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm bg-transparent">
                                    <option value="mysql" {{ env('DB_CONNECTION') == 'mysql' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.db_connection_label_mysql') }}
                                    </option>
                                    <option value="sqlite" {{ env('DB_CONNECTION') == 'sqlite' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.db_connection_label_sqlite') }}
                                    </option>
                                    <option value="pgsql" {{ env('DB_CONNECTION') == 'pgsql' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.db_connection_label_pgsql') }}
                                    </option>
                                    <option value="sqlsrv" {{ env('DB_CONNECTION') == 'sqlsrv' ? 'selected' : '' }}>
                                        {{ trans('installer_messages.environment.wizard.form.db_connection_label_sqlsrv') }}
                                    </option>
                                </select>
                                @if ($errors->has('database_connection'))
                                    <span class="text-red">
                                        {{ $errors->first('database_connection') }}
                                    </span>
                                @endif
                            </div>

                            <div class="contact-form">
                                <label for="database_hostname" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.environment.wizard.form.db_host_label') }}
                                </label>
                                <input
                                    type="text"
                                    class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                    name="database_hostname"
                                    id="database_hostname"
                                    value="{{ env('DB_HOST') }}"
                                    placeholder="{{ trans('installer_messages.environment.wizard.form.db_host_placeholder') }}"
                                />
                                @if ($errors->has('database_hostname'))
                                    <span class="text-red">
                                        {{ $errors->first('database_hostname') }}
                                    </span>
                                @endif
                            </div>
                            <div class="contact-form">
                                <label for="database_port" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.environment.wizard.form.db_port_label') }}
                                </label>
                                <input
                                    type="number"
                                    name="database_port"
                                    id="database_port"
                                    value="{{ env('DB_PORT') }}"
                                    placeholder="{{ trans('installer_messages.environment.wizard.form.db_port_placeholder') }}"
                                    class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                />
                                @if ($errors->has('database_port'))
                                    <span class="text-red">
                                        {{ $errors->first('database_port') }}
                                    </span>
                                @endif
                            </div>
                            <div class="contact-form">
                                <label for="database_name" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.environment.wizard.form.db_name_label') }}
                                </label>
                                <input
                                    type="text"
                                    class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                    name="database_name"
                                    id="database_name"
                                    value="{{ env('DB_DATABASE') }}"
                                    placeholder="{{ trans('installer_messages.environment.wizard.form.db_name_placeholder') }}"
                                />
                                @if ($errors->has('database_name'))
                                    <span class="text-red">
                                        {{ $errors->first('database_name') }}
                                    </span>
                                @endif
                            </div>
                            <div class="contact-form">
                                <label for="database_username" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.environment.wizard.form.db_username_label') }}
                                </label>
                                <input
                                    type="text"
                                    class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                    name="database_username"
                                    id="database_username"
                                    value="{{ env('DB_USERNAME') }}"
                                    placeholder="{{ trans('installer_messages.environment.wizard.form.db_username_placeholder') }}"
                                />
                                @if ($errors->has('database_username'))
                                    <span class="text-red">
                                        {{ $errors->first('database_username') }}
                                    </span>
                                @endif
                            </div>
                            <div class="contact-form">
                                <label for="database_password" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.environment.wizard.form.db_password_label') }}
                                </label>
                                <input
                                    type="text"
                                    class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                    name="database_password"
                                    id="database_password"
                                    value="{{ env('DB_PASSWORD') }}"
                                    placeholder="{{ trans('installer_messages.environment.wizard.form.db_password_placeholder') }}"
                                />
                                @if ($errors->has('database_password'))
                                    <span class="text-red">
                                        {{ $errors->first('database_password') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 items-center justify-center">
                        <a href="{{ route('LaravelInstaller::configuration-setting') }}" class="btn-primary-outline">
                            <i class="ri-arrow-left-line"></i>
                            {{ trans('installer_messages.databaseSetting.previous') }}
                        </a>
                        <button class="btn-primary-fill">
                            {{ trans('installer_messages.databaseSetting.next') }}
                            <i class="ri-arrow-right-s-line"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
