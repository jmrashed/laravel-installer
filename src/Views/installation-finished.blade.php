@extends('vendor.installer.layouts.imaster')

@section('template_title')
    {{ trans('installer_messages.installationFinished.templateTitle') }}
@endsection

@section('icontent')
<div class="flex">
    @include('vendor.installer._inc.aside')

    <div class="body-content w-full h-screen">
        <h1 class="capitalize text-primary border-b-[2px] border-[var(--primary)] pl-20 py-5 text-2xl font-semibold mb-4">
            {{ env('APP_NAME') }}
        </h1>
        <div class="h-[80vh] w-full flex flex-col justify-center items-center gap-10  pl-4">
            <img src="assets/images/complite.png" class="max-w-[120px]" alt="" />

            <h4 class="text-xl no-underline text-theme font-medium text-center">
                {{ trans('installer_messages.installationFinished.message') }}
            </h4>

            <div class="flex gap-4 items-center justify-center">
                <a href="{{ route('LaravelInstaller::final') }}" class="btn-primary-fill">
                    {{ trans('installer_messages.installationFinished.next') }}
                    <i class="ri-home-5-line"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
