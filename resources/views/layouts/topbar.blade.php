<header id="main-navbar" class="h-16 flex items-center justify-between px-4">
<div class="grid grid-cols-7 items-center mx-auto">
    <!-- Judul halaman -->
    <h1 class="col-span-2 text-2xl font-bold text-gray-800">
        @yield('page-title', 'Dashboard')
    </h1>

    <!-- User Info Card -->
    <div class="bg-red-700 rounded-2xl border-gray-200 col-span-3 col-end-8 p-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fa-solid fa-face-laugh text-5xl text-white mx-6"></i>
                <div>
                    <!-- Nama lengkap dan role -->
                    <h1 class="font-semibold text-gray-200">{{ Auth::user()->nama_lengkap }}</h1>
                    <h2 class="text-gray-300 text-sm">{{ Auth::user()->role }}</h2>
                </div>
            </div>

            <!-- Tombol Logout -->
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center bg-red-700 text-white px-4 py-2 rounded-lg shadow hover:bg-red-800 transition-colors">
                    <i class="fa-solid fa-right-from-bracket mr-2"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>
</header>
