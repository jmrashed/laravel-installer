@extends('vendor.installer.layouts.imaster')

@section('template_title')
    {{ trans('installer_messages.startInstalling.templateTitle') }}
@endsection

@section('icontent')
<div class="flex">
    @include('vendor.installer._inc.aside')

    <div class="body-content w-full h-screen ">
        <h1 class="capitalize text-primary border-b-[2px] border-[var(--primary)] pl-20 py-5 text-2xl font-semibold mb-4">
            HRM application
        </h1>
        <form action="" id="" method="POST">
            <div class="h-[80vh] w-full flex flex-col justify-center items-center gap-10  pl-4">
                <h4 class="text-xl no-underline text-primary font-medium text-center">
                    {{ trans('installer_messages.startInstalling.templateTitle') }}
                </h4>
                <div class="flex gap-4 items-center justify-center">
                    <a href="{{ route('LaravelInstaller::welcome') }}" class="btn-primary-outline">
                        <i class="ri-arrow-left-line"></i>
                        {{ trans('installer_messages.back') }}
                    </a>
                    <a href="{{ route('LaravelInstaller::purchase-validation') }}" class="btn-primary-fill">
                        {{ trans('installer_messages.startInstalling.next') }}
                        <i class="ri-arrow-right-s-line"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
