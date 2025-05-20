<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ mighty_language_direction() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    @include('partials._head')

    <!-- Vite Assets -->
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Fix for oversized SVG icons -->
    <style>
        svg {
            max-width: 24px !important;
            max-height: 24px !important;
        }

        /* Exception for logos and other specific SVGs */
        .logo svg,
        .brand-logo svg,
        .chart svg,
        .map svg {
            max-width: none !important;
            max-height: none !important;
        }
    </style>

</head>

<body class="" id="app">

    @include('partials._body')

</body>

</html>