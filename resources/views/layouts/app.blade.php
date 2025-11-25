<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Aplikasi Gudang BJL') }}</title>

    {{-- VITE ASSET INCLUSION (WAJIB untuk JS/CSS utama) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- FONT POPPINS (Dibiarkan) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    
    {{-- CSS APEXCHARTS: PATH DIPERBAIKI --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/apexcharts/dist/apexcharts.css') }}">

    {{-- SWEET ALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="bg-neutral-100" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen">

        {{-- 2. Sidebar Component --}}
        @include('layouts.sidebar')

        {{-- 3. Main Content Area --}}
        <main class="flex-1 overflow-y-auto" id="app">
            <div class="px-4 md:px-8">
                @include('layouts.topbar')
                <div class="my-4">
                    @yield('content')
                    @stack('scripts')
                </div>
            </div>
        </main>
        
    </div>

    
    {{-- KELOMPOK SKRIP: PATH SEMUA DIPERBAIKI MENGGUNAKAN asset() DARI FOLDER public/assets/vendor --}}
    
    {{-- 1. LODASH --}}
    <script src="{{ asset('assets/vendor/lodash/lodash.min.js') }}"></script>
    
    {{-- 2. APEXCHARTS --}}
    <script src="{{ asset('assets/vendor/apexcharts/dist/apexcharts.min.js') }}"></script>
    
    {{-- 3. PRELINE.JS --}}
    <script src="{{ asset('assets/vendor/preline/preline.js') }}"></script> 
    
    {{-- 4. ALPINE.JS (Dibiarkan menggunakan CDN) --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    {{-- 5. Pustaka Lain (Dibiarkan) --}}
    <script src="https://kit.fontawesome.com/0063d6d309.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindplus/elements@1" type="module"></script>
    
</body>
</html>