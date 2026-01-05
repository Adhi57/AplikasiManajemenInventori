<header id="main-navbar" class="py-4 px-8 lg:px-12 bg-gradient-to-r  from-red-900 to-red-950 shadow-xl sticky top-0 z-10 rounded-b-3xl">
    <div class="flex justify-between items-center w-full max-w-7xl mx-auto">
        
        <h1 class="text-2xl font-bold text-white tracking-wide">
            @yield('page-title', 'Dashboard')
        </h1>

        <div class="flex items-center space-x-4">

            <div class="flex items-center p-2.5 bg-red-700/70 backdrop-blur-sm rounded-xl border border-red-600 shadow-lg">
                
                    <div class="h-10 w-10 bg-red-100 rounded-full flex items-center justify-center text-red-900 text-xl font-bold shadow-lg border-4 border-red-700/50 mr-4">
                        {{ strtoupper(substr(Auth::user()->nama_lengkap, 0, 1)) }}
                    </div>
                
                <div>
                    <h1 class="font-semibold text-white text-sm truncate max-w-[150px]">{{ Auth::user()->nama_lengkap }}</h1>
                    <h2 class="text-red-200 text-xs">{{ Auth::user()->role }}</h2>
                </div>
            </div>

            <a href="{{ route('profile.index') }}"
                class="flex items-center px-4 py-2 bg-white text-red-700 border border-red-300 rounded-xl text-sm font-medium hover:bg-red-50 hover:text-red-800 transition-colors shadow-md">
                <i class="fa-regular fa-user-circle text-xl w-6"></i>
                <span>Profil Saya</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex items-center px-4 py-2 bg-white text-red-700 border border-red-300 rounded-xl text-sm font-medium hover:bg-red-50 hover:text-red-800 transition-colors shadow-md">
                    <i class="fa-solid fa-right-from-bracket mr-2"></i>
                    Logout
                </button>
            </form>
        </div>
    </div>
</header>