@extends('vendor.installer.layouts.imaster')

@section('template_title')
    {{ trans('installer_messages.purchaseValidation.templateTitle') }}
@endsection

@section('icontent')
    <div class="flex">
        @include('vendor.installer._inc.aside')

        <div class="body-content w-full h-screen">
            <h1 class="capitalize text-primary border-b-[2px] border-[var(--primary)] pl-20 py-5 text-2xl font-semibold mb-4">
                {{ env('APP_NAME') }}
            </h1>
            <form action="{{ route('LaravelInstaller::validate-purchase') }}" id="" method="POST">
                @csrf
                <div class="h-[80vh] w-full flex flex-col justify-between items-center gap-10 pl-4"  style="background: #ffffffc4; padding: 15px;">
                    <div class="content-wrapper w-full">
                        <h4 class="text-lg no-underline bg-primary text-white font-medium text-start px-6 py-3 mb-6 rounded-[4px] w-full mt-2">
                            {{ trans('installer_messages.purchaseValidation.message') }}
                        </h4>

                        <div class="card bg-white p-6 w-full rounded-md space-y-4">
                            <div class="contact-form">
                                <label for="purchase_code" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.purchaseValidation.form.purchaseCodeLabel') }}
                                </label>
                                <input
                                    type="text"
                                    id="purchase_code"
                                    name="purchase_code"
                                    class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                    placeholder="{{ trans('installer_messages.purchaseValidation.form.purchaseCodePlaceholder') }}"
                                />
                                @if ($errors->has('purchase_code'))
                                    <span class="text-red">
                                        {{ $errors->first('purchase_code') }}
                                    </span>
                                @endif
                            </div>

                            <div class="contact-form">
                                <label for="domain" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.purchaseValidation.form.domainLabel') }}
                                </label>
                                <input
                                    type="text"
                                    id="domain"
                                    name="domain"
                                    class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                    placeholder="{{ trans('installer_messages.purchaseValidation.form.domainPlaceholder') }}"
                                />
                                @if ($errors->has('domain'))
                                    <span class="text-red">
                                        {{ $errors->first('domain') }}
                                    </span>
                                @endif
                            </div>

                            <div class="contact-form">
                                <label for="email" class="text-primary block text-sm font-semibold text-gray-700 capitalize">
                                    {{ trans('installer_messages.purchaseValidation.form.emailLabel') }}
                                </label>
                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    class="h-10 px-0 py-3 mt-1 block w-full border-b border-primary outline-none sm:text-sm"
                                    placeholder="{{ trans('installer_messages.purchaseValidation.form.emailPlaceholder') }}"
                                />
                                @if ($errors->has('email'))
                                    <span class="text-red">
                                        {{ $errors->first('email') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-4 items-center justify-center">
                        <a href="{{ route('LaravelInstaller::welcome') }}" class="btn-primary-outline">
                            <i class="ri-arrow-left-line"></i>
                            {{ trans('installer_messages.purchaseValidation.previous') }}
                        </a>
                        <a href="{{ route('LaravelInstaller::server-requirements') }}" class="btn-primary-fill">
                            {{ trans('installer_messages.purchaseValidation.next') }}
                            <i class="ri-arrow-right-s-line"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
