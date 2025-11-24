<header id="main-navbar" class="py-4 px-8 lg:px-12 bg-gradient-to-r  from-red-900 to-red-950 shadow-xl sticky top-0 z-10 rounded-b-3xl">
    <div class="flex justify-between items-center w-full max-w-7xl mx-auto">
        
        <h1 class="text-3xl font-extrabold text-white tracking-wide">
            @yield('page-title', 'Dashboard')
        </h1>

        <div class="flex items-center space-x-4">

            <div class="flex items-center p-2.5 bg-red-700/70 backdrop-blur-sm rounded-xl border border-red-600 shadow-lg">
                
                <div class="w-10 h-10 bg-white text-red-700 flex items-center justify-center rounded-full text-lg font-semibold shadow-inner mr-3">
                    <i class="fa-solid fa-user-circle"></i>
                </div>
                
                <div>
                    <h1 class="font-semibold text-white text-sm truncate max-w-[150px]">{{ Auth::user()->nama_lengkap }}</h1>
                    <h2 class="text-red-200 text-xs">{{ Auth::user()->role }}</h2>
                </div>
            </div>

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