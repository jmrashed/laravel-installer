@extends('vendor.installer.layouts.imaster')

@section('template_title')
    {{ trans('installer_messages.serverRequirements.templateTitle') }}
@endsection

@section('icontent')
    <div class="flex">
        @include('vendor.installer._inc.aside')

        <div class="body-content w-full h-screen">
            <h1 class="capitalize text-primary border-b-[2px] border-[var(--primary)] pl-20 py-5 text-2xl font-semibold mb-4">
                {{ env('APP_NAME') }}
            </h1>
            <div class="h-[80vh] w-full flex flex-col justify-between items-center gap-10 pl-4"  style="background: #ffffffc4; padding: 15px;">
                <div class="content-wrapper w-full">
                    @foreach($requirements['requirements'] as $type => $requirement)
                        <ul class="listing flex flex-col gap-2 mt-4 px-2">
                            <li class="flex items-center justify-between gap-4 border-b border-[rgba(var(--primary-rgb),0.4)] last:border-b-0 pb-2 {{ $phpSupportInfo['supported'] ? 'success' : 'error' }}">
                                <strong class="flex items-center justify-between text-lg no-underline bg-primary text-white font-medium text-start px-6 py-3 mb-6 rounded-[4px] w-full">
                                    <span>
                                        {{ ucfirst($type) }}
                                        @if($type == 'php')
                                            <small>
                                                ({{ trans('installer_messages.version') }} {{ $phpSupportInfo['minimum'] }} {{ trans('installer_messages.required') }})
                                            </small>
                                        @endif
                                    </span>
                                    @if($type == 'php')
                                        <span class="flex items-center gap-2">
                                            <strong>
                                                {{ $phpSupportInfo['current'] }}
                                            </strong>
                                            @if ($phpSupportInfo['supported'])
                                                <i class="ri-check-double-line"></i>
                                            @else
                                                <i class="ri-close-line"></i>
                                            @endif
                                        </span>
                                    @endif
                                </strong>
                            </li>
                            @foreach($requirements['requirements'][$type] as $extention => $enabled)
                                <li class="flex items-center justify-between gap-4 border-b border-[rgba(var(--primary-rgb),0.4)] last:border-b-0 pb-2 {{ $enabled ? 'success' : 'error' }}">
                                    <span class="text-md capitalize text-primary">{{ $extention }}</span>
                                    <div class="icon text-lg {{ $enabled ? 'text-theme' : 'text-red-400' }}">
                                        @if ($enabled)
                                            <i class="ri-check-double-line"></i>
                                        @else
                                            <i class="ri-close-line"></i>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @endforeach
                </div>
                <div class="flex gap-4 items-center justify-center">
                    <a href="{{ route('LaravelInstaller::purchase-validation') }}" class="btn-primary-outline">
                        <i class="ri-arrow-left-line"></i>
                        {{ trans('installer_messages.serverRequirements.previous') }}
                    </a>
                    @if (!isset($requirements['errors']) && $phpSupportInfo['supported'] )
                        <a href="{{ route('LaravelInstaller::permissions') }}" class="btn-primary-fill">
                            {{ trans('installer_messages.serverRequirements.next') }}
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
