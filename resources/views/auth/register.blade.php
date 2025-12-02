<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .custom-bg {
            background: radial-gradient(circle at center, #7f1d1d 0%, #A00000 70%, #800000 100%);
            overflow: hidden;
            position: relative;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 0, 0, 0.2);
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

        .input-minimal {
            border: 1px solid #e5e7eb;
            box-shadow: none;
            transition: all 0.2s;
        }
        .input-minimal:focus {
            border-color: #ef4444;
            outline: none;
            box-shadow: 0 0 0 1px #f87171;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center custom-bg p-6 font-sans">

    <!-- Orbs -->
    <div class="orb orb-1"></div>
    <div class="orb orb-2 hidden sm:block"></div>
    <div class="orb orb-3 hidden lg:block"></div>
    <div class="orb orb-4"></div>

    <!-- Card -->
    <div class="w-full max-w-md bg-white border border-gray-100 shadow-2xl rounded-2xl p-8 relative z-10">

        <div class="text-center mb-4">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="w-36 mx-auto mb-6">
        </div>

        <h2 class="text-3xl font-extrabold text-gray-800 text-center mb-8">
            REGISTER
        </h2>

        <!-- Alerts -->
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

        <!-- Form -->
        <form method="POST" action="{{ route('register') }}" class="space-y-6">
            @csrf

            <!-- Nama -->
            <div>
                <label for="nama_lengkap" class="block text-gray-600 font-medium text-sm mb-1">Nama Lengkap</label>
                <input id="nama_lengkap" name="nama_lengkap" type="text" required
                       class="w-full px-4 py-2 input-minimal rounded-lg text-gray-800"
                       value="{{ old('nama_lengkap') }}">
                @error('nama_lengkap')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Username -->
            <div>
                <label for="username" class="block text-gray-600 font-medium text-sm mb-1">Username</label>
                <input id="username" name="username" type="text" required
                       class="w-full px-4 py-2 input-minimal rounded-lg text-gray-800"
                       value="{{ old('username') }}">
                @error('username')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-gray-600 font-medium text-sm mb-1">Email</label>
                <input id="email" name="email" type="email" required
                       class="w-full px-4 py-2 input-minimal rounded-lg text-gray-800"
                       value="{{ old('email') }}">
                @error('email')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-gray-600 font-medium text-sm mb-1">Password</label>
                <input id="password" name="password" type="password" required
                       class="w-full px-4 py-2 input-minimal rounded-lg text-gray-800">
                @error('password')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-gray-600 font-medium text-sm mb-1">Konfirmasi Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                       class="w-full px-4 py-2 input-minimal rounded-lg text-gray-800">
                @error('password_confirmation')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
            </div>

            <!-- Submit -->
            <button type="submit"
                class="w-full bg-red-700 hover:bg-red-800 text-white py-3 rounded-lg
                       font-semibold text-lg shadow-xl shadow-red-500/50 transition duration-150 ease-in-out transform hover:-translate-y-0.5">
                Register
            </button>

        </form>

    </div>

</body>
</html>
