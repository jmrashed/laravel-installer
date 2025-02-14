@extends('vendor.installer.layouts.imaster')

@section('template_title')
    {{ trans('installer_messages.permissions.templateTitle') }}
@endsection

@section('icontent')
    <div class="flex">
        @include('vendor.installer._inc.aside')

        <div class="body-content w-full h-screen">
            <h1 class="capitalize text-primary border-b-[2px] border-[var(--primary)] pl-20 py-5 text-2xl font-semibold mb-4">
                {{ env('APP_NAME') }}
            </h1>
            <div class="h-[80vh] w-full flex flex-col justify-between items-center gap-10 pl-4">
                <div class="content-wrapper w-full">
                    <h4 class="text-lg no-underline bg-primary text-white font-medium text-start px-6 py-3 mb-6 rounded-[4px] w-full">
                        {{ trans('installer_messages.permissions.message') }}
                    </h4>
                    <ul class="listing flex flex-col gap-2 mt-4 px-2">
                        @foreach($permissions['permissions'] as $permission)
                            <li class="flex items-center justify-between gap-4 border-b border-[rgba(var(--primary-rgb),0.4)] last:border-b-0 pb-2">
                                <span class="text-md capitalize text-primary">{{ $permission['folder'] }}</span>

                                <div class="right flex items-center gap-2">
                                    <div class="icon {{ $permission['isSet'] ? 'text-theme' : 'text-red-400' }} text-xl">
                                        @if ($permission['isSet'])
                                            <i class="ri-check-double-line"></i>
                                        @else
                                            <i class="ri-close-line"></i>
                                        @endif
                                    </div>
                                    <span class="text-md font-semibold capitalize text-primary"> {{ $permission['permission'] }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="flex gap-4 items-center justify-center">
                    <a href="{{ route('LaravelInstaller::server-requirements') }}" class="btn-primary-fill">
                        <i class="ri-arrow-left-line"></i>
                        {{ trans('installer_messages.permissions.previous') }}
                    </a>
                    @if (!isset($permissions['errors']))
                        <a href="{{ route('LaravelInstaller::environment-setting') }}" class="btn-primary-fill">
                            {{ trans('installer_messages.permissions.next') }}
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
