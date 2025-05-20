<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ mighty_language_direction() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - {{ config('app.name', 'Laravel') }}</title>

    @include('partials._head')
    @yield('styles')
</head>

<body class="" id="app">
    @include('partials._body')

    <div class="content-page">
        <div class="content">
            @yield('content')
        </div>
    </div>

    @yield('scripts')
</body>

</html>