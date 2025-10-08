@extends('vendor.installer.layouts.imaster')

@section('template_title')
    {{ trans('installer_messages.classicTextEditor.templateTitle') }}
@endsection

@section('icontent')
    <div class="flex">
        @include('vendor.installer._inc.aside')

        <div class="body-content w-full h-screen">
            <h1 class="capitalize text-primary border-b-[2px] border-[var(--primary)] pl-20 py-5 text-2xl font-semibold mb-4">
                {{ env('APP_NAME') }}
            </h1>
            <form method="post" action="{{ route('LaravelInstaller::environmentSaveClassic') }}">
                {!! csrf_field() !!}
                <div class="h-[80vh] w-full flex flex-col justify-between items-center gap-10 pl-4"  style="background: #ffffffc4; padding: 15px;">
                    <div class="content-wrapper w-full">
                        <div class="card w-full rounded-md space-y-4 mb-4">
                            <textarea
                                id="envContent"
                                name="envConfig"
                                class="textarea rounded-6 p-6 mt-1 block w-full border-b border-primary outline-none sm:text-sm bg-black text-white"
                            >{{ $envContent }}</textarea>
                        </div>
                    </div>
                    <div class="flex gap-4 items-center justify-center">
                        <a href="{{ route('LaravelInstaller::environment-setting') }}" class="btn-primary-outline">
                            <i class="ri-arrow-left-line"></i>
                            {{ trans('installer_messages.classicTextEditor.previous') }}
                        </a>
                        <button class="btn-primary-fill">
                            {{ trans('installer_messages.classicTextEditor.next') }}
                            <i class="ri-arrow-right-s-line"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
