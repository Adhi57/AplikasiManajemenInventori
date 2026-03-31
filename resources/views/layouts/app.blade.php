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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- CSS APEXCHARTS: PATH DIPERBAIKI --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/apexcharts/dist/apexcharts.css') }}">

    {{-- SWEET ALERT --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- HTML5 QRCODE (Barcode Scanner) --}}
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    {{-- SWEETALERT2 CUSTOM THEME --}}
    <style>
        /* Popup container */
        .swal2-popup.swal-custom-popup {
            font-family: 'Poppins', sans-serif;
            border-radius: 1rem;
            padding: 2rem 1.5rem 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Title */
        .swal2-popup.swal-custom-popup .swal2-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #111827;
            padding: 0;
            margin-bottom: 0.25rem;
        }

        /* Text content */
        .swal2-popup.swal-custom-popup .swal2-html-container {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0.5rem 0 0;
            line-height: 1.5;
        }

        /* Icon sizing */
        .swal2-popup.swal-custom-popup .swal2-icon {
            margin: 0 auto 1rem;
        }

        /* Success icon colors */
        .swal2-popup.swal-custom-popup .swal2-icon.swal2-success {
            border-color: #d1fae5;
            color: #059669;
        }

        .swal2-popup.swal-custom-popup .swal2-icon.swal2-success [class^='swal2-success-line'] {
            background-color: #059669;
        }

        .swal2-popup.swal-custom-popup .swal2-icon.swal2-success .swal2-success-ring {
            border-color: #d1fae5;
        }

        /* Error icon colors */
        .swal2-popup.swal-custom-popup .swal2-icon.swal2-error {
            border-color: #fee2e2;
            color: #dc2626;
        }

        .swal2-popup.swal-custom-popup .swal2-icon.swal2-error [class^='swal2-x-mark-line'] {
            background-color: #dc2626;
        }

        /* Warning icon colors */
        .swal2-popup.swal-custom-popup .swal2-icon.swal2-warning {
            border-color: #fef3c7;
            color: #d97706;
        }

        /* Question icon colors */
        .swal2-popup.swal-custom-popup .swal2-icon.swal2-question {
            border-color: #dbeafe;
            color: #2563eb;
        }

        /* Action buttons */
        .swal2-popup.swal-custom-popup .swal2-actions {
            margin-top: 1.25rem;
            gap: 0.5rem;
        }

        .swal2-popup.swal-custom-popup .swal2-styled {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.8125rem;
            border-radius: 0.75rem;
            padding: 0.625rem 1.5rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            transition: all 0.15s ease;
        }

        .swal2-popup.swal-custom-popup .swal2-styled.swal2-confirm {
            background: #991b1b;
        }

        .swal2-popup.swal-custom-popup .swal2-styled.swal2-confirm:hover {
            background: #7f1d1d;
        }

        .swal2-popup.swal-custom-popup .swal2-styled.swal2-confirm:focus {
            box-shadow: 0 0 0 3px rgba(153, 27, 27, 0.25);
        }

        .swal2-popup.swal-custom-popup .swal2-styled.swal2-cancel {
            background: #f3f4f6;
            color: #374151;
        }

        .swal2-popup.swal-custom-popup .swal2-styled.swal2-cancel:hover {
            background: #e5e7eb;
        }

        /* Timer progress bar */
        .swal2-popup.swal-custom-popup .swal2-timer-progress-bar {
            background: #991b1b;
        }

        /* Success-specific timer bar */
        .swal2-popup.swal-custom-popup.swal-success-popup .swal2-timer-progress-bar {
            background: #059669;
        }

        /* Backdrop */
        .swal-custom-backdrop {
            background: rgba(0, 0, 0, 0.4) !important;
            backdrop-filter: blur(4px) !important;
            -webkit-backdrop-filter: blur(4px) !important;
        }

        /* Toast styles */
        .swal2-popup.swal-toast-popup {
            font-family: 'Poppins', sans-serif;
            border-radius: 1rem;
            padding: 1rem 1.25rem;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .swal2-popup.swal-toast-popup .swal2-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: #111827;
        }

        .swal2-popup.swal-toast-popup .swal2-timer-progress-bar {
            background: #059669;
        }
    </style>
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

    {{-- GLOBAL SWEETALERT NOTIFICATIONS (satu-satunya handler, jangan duplikasi di view lain) --}}
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success')),
                toast: true,
                position: 'top-end',
                timer: 3000,
                showConfirmButton: false,
                timerProgressBar: true,
                customClass: {
                    popup: 'swal-toast-popup',
                },
                showClass: { popup: 'animate__animated animate__slideInRight' },
                hideClass: { popup: 'animate__animated animate__slideOutRight' },
            });
        </script>
    @endif

    @if (session('warning'))
        <script>
            Swal.fire({
                icon: 'warning',
                title: 'Perhatian',
                text: @json(session('warning')),
                confirmButtonText: 'Mengerti',
                customClass: {
                    popup: 'swal-custom-popup',
                },
                backdrop: true,
                allowOutsideClick: false,
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: @json(session('error')),
                confirmButtonText: 'Tutup',
                customClass: {
                    popup: 'swal-custom-popup',
                },
                backdrop: true,
            });
        </script>
    @endif


</body>

</html>