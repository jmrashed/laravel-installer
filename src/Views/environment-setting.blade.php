@extends('vendor.installer.layouts.imaster')

@section('template_title')
    {{ trans('installer_messages.environmentSettings.templateTitle') }}
@endsection

@section('icontent')
    <div class="flex">
        @include('vendor.installer._inc.aside')

        <div class="body-content w-full h-screen">
            <h1 class="capitalize text-primary border-b-[2px] border-[var(--primary)] pl-20 py-5 text-2xl font-semibold mb-4">
                {{ env('APP_NAME') }}
            </h1>
            <div class="h-[80vh] w-full flex flex-col justify-center items-center gap-10  pl-4">
                <h4 class="text-xl no-underline text-primary font-medium text-center">
                    {{ trans('installer_messages.environmentSettings.message') }}
                </h4>
                <div class="flex gap-4 items-center justify-center">
                    <a href="{{ route('LaravelInstaller::configuration-setting') }}" class="btn-primary-outline">
                        <i class="ri-equalizer-line"></i>
                        {{ trans('installer_messages.environmentSettings.formWizardSetup') }}
                    </a>
                    <a href="{{ route('LaravelInstaller::classic-text-editor') }}" class="btn-primary-fill">
                        <i class="ri-code-s-slash-line"></i>
                        {{ trans('installer_messages.environmentSettings.classicTextEditor') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
