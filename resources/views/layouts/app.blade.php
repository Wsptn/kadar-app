<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>KIPER</title>

    <link rel="shortcut icon" href="{{ asset('template-admin/img/logo-kadar.png') }}" />
    <link href="{{ asset('template-admin/css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('template-admin/css/custom.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('template-admin/img/logo-kadar.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    @include('layouts.style')
    @yield('this-page-style')
    @stack('styles')
</head>

<body>
    <div class="wrapper">
        @include('layouts.sidebar')

        <div class="main">
            @include('layouts.header')

            <main class="content">
                @yield('this-page-contain')
            </main>
        </div>
    </div>


    @include('layouts.script')
    @yield('this-page-scripts')
    @stack('page-scripts')
</body>

</html>
