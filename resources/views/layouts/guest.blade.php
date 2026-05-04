<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        {{-- FAVICON --}}
        <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>

    <body class="font-sans text-gray-900 antialiased">

        {{-- BACKGROUND FULL PAGE --}}
        <div class="min-h-screen flex flex-col justify-center items-center 
                    bg-gradient-to-b from-red-900 to-red-950 p-6">

            {{-- LOGO --}}
            <div class="mb-6">
                <a href="/">
                    <x-application-logo class="w-24 h-24 text-white opacity-90" />
                </a>
            </div>

            {{-- LOGIN CARD --}}
            <div class="w-full sm:max-w-md bg-white shadow-xl rounded-xl border border-red-200/40 px-6 py-8">
                {{ $slot }}
            </div>
        </div>

    </body>
</html>
