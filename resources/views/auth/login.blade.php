<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- CSS Kustom untuk Efek Latar Belakang Orbs dan Gradien Radial -->
    <style>
        .custom-bg {
            /* Gradien Radial Merah Ceria untuk Background */
            background: radial-gradient(circle at center, #7f1d1d 0%, #A00000 70%, #800000 100%);
            overflow: hidden;
            position: relative;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 0, 0, 0.2); /* Merah semi-transparan */
            box-shadow: 0 0 50px rgba(255, 0, 0, 0.4);
            animation: float 20s infinite ease-in-out alternate;
            z-index: 0;
            filter: blur(5px);
        }
        .orb-1 { width: 250px; height: 250px; top: 10%; left: 5%; }
        .orb-2 { width: 150px; height: 150px; bottom: 5%; right: 10%; animation-delay: 5s; }
        .orb-3 { width: 100px; height: 100px; top: 50%; right: 5%; animation-delay: 10s; }
        .orb-4 { width: 180px; height: 180px; bottom: 30%; left: 20%; animation-delay: 8s; }

        @keyframes float {
            0% { transform: translate(0, 0) scale(1); opacity: 1; }
            100% { transform: translate(20px, 30px) scale(1.05); opacity: 0.8; }
        }
        
        /* Mengatur input agar terlihat sangat minimalis */
        .input-minimal {
            border: 1px solid #e5e7eb; /* Border abu-abu sangat tipis */
            box-shadow: none;
            transition: all 0.2s;
        }
        .input-minimal:focus {
            border-color: #ef4444; /* Merah saat fokus */
            outline: none;
            box-shadow: 0 0 0 1px #f87171; /* Ring tipis saat fokus */
        }
    </style>
</head>

<!-- Mengganti kelas body dengan custom-bg -->
<body class="min-h-screen flex items-center justify-center custom-bg p-6 font-sans">
    
    <!-- Orbs (Bola Mengambang) -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2 hidden sm:block"></div>
    <div class="orb orb-3 hidden lg:block"></div>
    <div class="orb orb-4"></div>


    <!-- Card Login -->
    <div class="w-full max-w-md bg-white border border-gray-100 shadow-2xl rounded-2xl p-8 relative z-10"> {{-- bg-white untuk tampilan bersih --}}

        {{-- Logo/Icon Placeholder --}}
        <div class="text-center mb-4">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="w-36 mx-auto mb-6">
        </div>


        {{-- TITLE --}}
        <h2 class="text-3xl font-extrabold text-gray-800 text-center mb-8">
            LOGIN
        </h2>

        {{-- Messages --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded-lg mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded-lg mb-4 text-sm">
                {{ session('error') }}
            </div>
        @endif

        {{-- FORM --}}
        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf

            {{-- Login Field --}}
            <div>
                <label for="login" class="block text-gray-600 font-medium text-sm mb-1">
                    Email atau Username
                </label>
                <input id="login"
                        type="text"
                        name="login"
                        value="{{ old('login') }}"
                        required autofocus
                        {{-- Menggunakan kelas kustom untuk minimalis --}}
                        class="w-full px-4 py-2 input-minimal rounded-lg text-gray-800" />
                @error('login')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password --}}
            <div class="mt-4">
                <label for="password" class="block text-gray-600 font-medium text-sm mb-1">
                    Password
                </label>
                <input id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        {{-- Menggunakan kelas kustom untuk minimalis --}}
                        class="w-full px-4 py-2 input-minimal rounded-lg text-gray-800" />
                @error('password')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Remember Me & Link --}}
            <div class="flex items-center justify-between mt-4">
                <div class="flex items-center">
                    <input id="remember_me"
                            type="checkbox"
                            name="remember"
                            class="rounded border-gray-300 text-red-700 focus:ring-red-600">

                    <label for="remember_me" class="ml-2 text-sm text-gray-600">
                        Remember me
                    </label>
                </div>

            </div>

            {{-- Submit Button --}}
            <button type="submit"
                class="w-full bg-red-700 hover:bg-red-800 text-white py-3 rounded-lg
                       font-semibold text-lg shadow-xl shadow-red-500/50 transition duration-150 ease-in-out transform hover:-translate-y-0.5">
                Login
            </button>

        </form>

    </div>

</body>
</html>