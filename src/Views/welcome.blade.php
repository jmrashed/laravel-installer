@extends('vendor.installer.layouts.imaster')

@section('template_title')
    {{ trans('installer_messages.welcome.templateTitle') }}
@endsection

@section('icontent')
    <div class="h-screen w-full flex flex-col justify-center items-start gap-10  pl-4">
        <div class="welcome-caption flex gap-6 flex-col">
            <img src="{{ asset('installer/img/company-logo.png') }}" alt="logo" class="max-w-[150px]">
            <h1 class="text-[40px] text-primary font-bold">{{ env('APP_NAME') }} {{ trans('installer_messages.installation') }}...</h1>
            <div class="flex gap-4 items-center justify-start">
                <img src="{{ asset('installer/img/laravel.png') }}" alt="laravel" class="max-w-[150px] max-h-[40px]">
                <img src="{{ asset('installer/img/bootstrap.png') }}" alt="bootstrap" class="max-w-[150px] max-h-[40px]">
                <img src="{{ asset('installer/img/jquery.png') }}" alt="jquery" class="max-w-[150px] max-h-[40px]">
                <img src="{{ asset('installer/img/mySQL.png') }}" alt="mySQL" class="max-w-[150px] max-h-[40px]">
            </div>
            <div class="flex gap-4 items-center justify-start mt-6">
                <a href="{{ route('LaravelInstaller::purchase-validation') }}" class="btn-primary-fill">
                    {{ trans('installer_messages.welcome.next') }}
                    <i class="ri-arrow-right-s-line"></i>
                </a>
            </div>
        </div>
    </div>
@endsection
