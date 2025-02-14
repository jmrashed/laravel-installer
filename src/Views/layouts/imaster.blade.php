<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="description" content="...">
    <meta name="keywords" content="...">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@if (trim($__env->yieldContent('template_title')))@yield('template_title') | @endif {{ trans('installer_messages.title') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('installer/img/favicon/favicon-16x16.png') }}" sizes="16x16"/>
    <link rel="icon" type="image/png" href="{{ asset('installer/img/favicon/favicon-32x32.png') }}" sizes="32x32"/>
    <link rel="icon" type="image/png" href="{{ asset('installer/img/favicon/favicon-96x96.png') }}" sizes="96x96"/>
    <link rel="stylesheet" href="{{ asset('installer/css/fonts-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('installer/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('installer/css/custom.css') }}">
</head>

<body class="body-bg">
    <main>
        <div class="container">
            @yield('icontent')
        </div>
    </main>

    @yield('iscripts')
</body>
</html>
