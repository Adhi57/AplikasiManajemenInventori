<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Aplikasi Gudang</title>
    <script src="https://cdn.tailwindcss.com"></script> </head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md">
        <div class="bg-white shadow-xl rounded-lg px-8 py-10">
            <h2 class="text-3xl font-bold text-center text-gray-800 mb-8">
                Login Aplikasi Gudang
            </h2>

            <form method="POST" action="{{ url('/login') }}">
                @csrf

                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">
                            @foreach ($errors->all() as $error)
                                {{ $error }}
                            @endforeach
                        </span>
                    </div>
                @endif

                <div class="mb-6">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">
                        Username
                    </label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           value="{{ old('username') }}"
                           required 
                           autofocus 
                           class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('username') border-red-500 @enderror"
                           placeholder="Masukkan username Anda">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">
                        Password
                    </label>
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           class="shadow appearance-none border rounded w-full py-3 px-4 text-gray-700 leading-tight focus:outline-none focus:shadow-outline @error('password') border-red-500 @enderror"
                           placeholder="Masukkan password Anda">
                </div>

                <div class="mb-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="form-checkbox text-indigo-600 rounded">
                        <span class="ml-2 text-sm text-gray-700">Ingat Saya</span>
                    </label>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded focus:outline-none focus:shadow-outline transition duration-150">
                        Masuk
                    </button>
                </div>
            </form>
        </div>
        
        <p class="text-center text-gray-500 text-xs mt-6">
            &copy; {{ date('Y') }} Aplikasi Gudang. All rights reserved.
        </p>
    </div>

</body>
</html>