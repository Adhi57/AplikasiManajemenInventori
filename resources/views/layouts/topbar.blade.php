@php
    // Notification queries
    $pendingPO = \App\Models\PurchaseOrder::where('status_po', 'Pending')->count();
    $pendingSJ = \App\Models\SuratJalan::where('status', 'Pending')->count();
    $pendingRetur = \App\Models\ReturBarang::where('status_retur', 'Pending')->count();
    $pengirimanMenunggu = \App\Models\Pengiriman::where('status_pengiriman', 'Menunggu')
        ->whereHas('suratJalan', fn($q) => $q->where('status', 'Disetujui'))
        ->count();
        
    $recentPO = \App\Models\PurchaseOrder::where('status_po', 'Pending')
        ->with('supplier')->latest()->take(3)->get();
    $recentSJ = \App\Models\SuratJalan::where('status', 'Pending')
        ->with('pelanggan')->latest()->take(3)->get();
    $recentPengiriman = \App\Models\Pengiriman::where('status_pengiriman', 'Menunggu')
        ->whereHas('suratJalan', fn($q) => $q->where('status', 'Disetujui'))
        ->with('suratJalan.pelanggan')->latest()->take(3)->get();

    $needReorderItems = Cache::remember('rop_notifications', 5, function () {
        return \App\Helpers\ReorderPointHelper::getItemsToReorder();
    });
    $needReorderCount = $needReorderItems->count();
    $recentReorder = $needReorderItems->take(3);

    $totalNotif = $pendingPO + $pendingSJ + $pendingRetur + $pengirimanMenunggu + $needReorderCount;

    $segments = request()->segments();
    $breadcrumbLabels = [
        'dashboard' => 'Dashboard',
        'katalog_barang' => 'Katalog Barang',
        'stok-barang' => 'Stok Barang',
        'stock_opname' => 'Stock Opname',
        'tracking-kadaluarsa' => 'Tracking Kadaluarsa',
        'purchase_orders' => 'Purchase Order',
        'buat_permintaan' => 'Buat Permintaan',
        'verifBarang' => 'Verifikasi Barang',
        'returBarang' => 'Retur Barang',
        'surat_jalan' => 'Surat Jalan',
        'pengiriman' => 'Pengiriman',
        'approval' => 'Approval',
        'approval_po' => 'Approval PO',
        'approval_surat_jalan' => 'Approval SJ',
        'laporan' => 'Laporan',
        'barang-masuk' => 'Barang Masuk',
        'barang-keluar' => 'Barang Keluar',
        'barangs' => 'Data Barang',
        'pelanggans' => 'Data Pelanggan',
        'suppliers' => 'Data Supplier',
        'kategori_barang' => 'Kategori Barang',
        'kategori_pelanggan' => 'Kategori Pelanggan',
        'users' => 'Users & Roles',
        'profile' => 'Profil',
        'create' => 'Tambah',
        'edit' => 'Edit',
        'show' => 'Detail',
    ];
@endphp

<div class="sticky top-0 z-30 pt-4 pb-4 bg-neutral-100/80 backdrop-blur-md">
    <header id="main-navbar"
        class="py-3 px-5 lg:px-6 bg-gradient-to-r from-red-950 via-red-900 to-red-950 shadow-md border border-red-800/80 rounded-2xl flex items-center justify-between transition-all w-full">
        
        {{-- Left: Mobile Toggle & Typography --}}
        <div class="flex items-center gap-3">
            {{-- Mobile hamburger --}}
            <button @click="sidebarOpen = !sidebarOpen"
                class="md:hidden text-red-200 p-2 rounded-xl hover:bg-white/10 hover:text-white transition outline-none border border-transparent hover:border-white/20">
                <i class="fa-solid fa-bars-staggered text-lg"></i>
            </button>

            <div class="hidden sm:block">
                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-1.5 text-[11px] font-semibold text-red-300 tracking-wide uppercase">
                    <a href="/dashboard" class="hover:text-white transition flex items-center gap-1">
                        <i class="fa-solid fa-house text-[10px]"></i>
                    </a>
                    @foreach($segments as $i => $segment)
                        <span class="text-red-500/50">/</span>
                        @if($i < count($segments) - 1)
                            <a href="/{{ implode('/', array_slice($segments, 0, $i + 1)) }}"
                                class="hover:text-white transition">
                                {{ $breadcrumbLabels[$segment] ?? ucwords(str_replace(['-', '_'], ' ', $segment)) }}
                            </a>
                        @else
                            <span class="text-amber-400">
                                {{ $breadcrumbLabels[$segment] ?? ucwords(str_replace(['-', '_'], ' ', $segment)) }}
                            </span>
                        @endif
                    @endforeach
                </nav>
                <h1 class="text-xl font-extrabold text-white tracking-tight leading-tight mt-0.5">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>
            
            {{-- Mobile Title --}}
            <div class="sm:hidden">
                <h1 class="text-lg font-bold text-white truncate max-w-[150px]">
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>
        </div>

        {{-- Center: Global Search --}}
        <form action="{{ route('barangs.index') }}" method="GET" class="hidden lg:flex flex-1 max-w-md mx-8 relative group">
            <button type="submit" class="absolute left-3.5 top-1/2 -translate-y-1/2 outline-none">
                <i class="fa-solid fa-magnifying-glass text-red-300 text-sm group-focus-within:text-white transition-colors"></i>
            </button>
            <input type="text" name="search" placeholder="Cari data barang global..." 
                class="w-full bg-black/20 hover:bg-black/30 border border-white/10 text-white placeholder-red-300/60 text-sm rounded-xl pl-10 pr-4 py-2 focus:bg-black/40 focus:ring-4 focus:ring-red-500/50 focus:border-red-400/50 transition-all outline-none shadow-inner"
                value="{{ request()->is('barangs*') ? request('search') : '' }}">
            <div class="absolute right-3 top-1/2 -translate-y-1/2 hidden xl:flex gap-1 pointer-events-none">
                <kbd class="px-1.5 py-0.5 text-[10px] font-mono text-red-300 bg-black/30 border border-white/10 rounded shadow-sm">Enter</kbd>
            </div>
        </form>

        {{-- Right: Actions & User Menu --}}
        <div class="flex items-center gap-2 sm:gap-3">

            {{-- Notification Bell --}}
            <div x-data="{ notifOpen: false }" class="relative">
                <button @click="notifOpen = !notifOpen"
                    class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 text-red-100 hover:text-white transition-all border border-white/10 hover:border-white/30 backdrop-blur-sm">
                    <i class="fa-regular fa-bell text-lg"></i>
                    @if($totalNotif > 0)
                        <span
                            class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-amber-500 text-[10px] font-bold text-white rounded-full flex items-center justify-center shadow animate-pulse border-2 border-red-900">
                            {{ $totalNotif > 9 ? '9+' : $totalNotif }}
                        </span>
                    @endif
                </button>

                {{-- Dropdown Notifikasi --}}
                <div x-show="notifOpen" @click.away="notifOpen = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                    class="absolute right-0 mt-3 w-80 sm:w-96 bg-gradient-to-b from-red-950 to-red-900 rounded-2xl shadow-2xl border border-red-800 overflow-hidden z-50 origin-top-right"
                    style="display: none;">

                    <div class="px-5 py-3 bg-red-900/30 border-b border-red-800 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-white">Notifikasi</h3>
                        @if($totalNotif > 0)
                            <span class="px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-extrabold rounded-full uppercase tracking-wider">{{ $totalNotif }} Baru</span>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-2 p-3">
                        @if($pendingPO > 0)
                            <a href="{{ route('approval.approval_po') }}"
                                class="flex items-center gap-2 p-2 bg-white/5 rounded-xl hover:bg-white/10 transition group border border-white/5 hover:border-white/20">
                                <div class="w-8 h-8 bg-amber-500 text-white rounded-lg flex items-center justify-center text-xs font-bold shadow-sm">
                                    {{ $pendingPO }}</div>
                                <div>
                                    <p class="text-[11px] font-bold text-white group-hover:text-amber-300">PO Pending</p>
                                    <p class="text-[9px] text-red-200">Menunggu approval</p>
                                </div>
                            </a>
                        @endif

                        @if($pendingSJ > 0)
                            <a href="{{ route('approval.approval_surat_jalan') }}"
                                class="flex items-center gap-2 p-2 bg-white/5 rounded-xl hover:bg-white/10 transition group border border-white/5 hover:border-white/20">
                                <div class="w-8 h-8 bg-blue-500 text-white rounded-lg flex items-center justify-center text-xs font-bold shadow-sm">
                                    {{ $pendingSJ }}</div>
                                <div>
                                    <p class="text-[11px] font-bold text-white group-hover:text-blue-300">SJ Pending</p>
                                    <p class="text-[9px] text-red-200">Menunggu approval</p>
                                </div>
                            </a>
                        @endif

                        @if($pendingRetur > 0)
                            <a href="{{ route('returBarang.index') }}"
                                class="flex items-center gap-2 p-2 bg-white/5 rounded-xl hover:bg-white/10 transition group border border-white/5 hover:border-white/20">
                                <div class="w-8 h-8 bg-orange-500 text-white rounded-lg flex items-center justify-center text-xs font-bold shadow-sm">
                                    {{ $pendingRetur }}</div>
                                <div>
                                    <p class="text-[11px] font-bold text-white group-hover:text-orange-300">Retur Pending</p>
                                    <p class="text-[9px] text-red-200">Perlu konfirmasi</p>
                                </div>
                            </a>
                        @endif

                        @if($pengirimanMenunggu > 0)
                            <a href="{{ route('pengiriman.index') }}"
                                class="flex items-center gap-2 p-2 bg-white/5 rounded-xl hover:bg-white/10 transition group border border-white/5 hover:border-white/20">
                                <div class="w-8 h-8 bg-emerald-500 text-white rounded-lg flex items-center justify-center text-xs font-bold shadow-sm">
                                    {{ $pengirimanMenunggu }}</div>
                                <div>
                                    <p class="text-[11px] font-bold text-white group-hover:text-emerald-300">Pengiriman</p>
                                    <p class="text-[9px] text-red-200">Menunggu dikirim</p>
                                </div>
                            </a>
                        @endif
                    </div>

                    @if($totalNotif === 0)
                        <div class="py-10 text-center">
                            <i class="fa-solid fa-check-double text-3xl text-red-800 mb-2"></i>
                            <p class="text-xs font-medium text-red-300">Wah, semua tugas sudah beres!</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- User Dropdown Menu --}}
            <div x-data="{ userMenu: false }" class="relative">
                <button @click="userMenu = !userMenu" 
                    class="flex items-center gap-2.5 p-1 pr-3 bg-white/10 hover:bg-white/20 border border-white/10 hover:border-white/30 backdrop-blur-sm rounded-xl transition-all focus:outline-none focus:ring-2 focus:ring-white/30">
                    <div class="h-8 w-8 bg-white rounded-lg flex items-center justify-center text-red-900 text-xs font-extrabold shadow-sm">
                        {{ strtoupper(substr(Auth::user()->nama_lengkap, 0, 1)) }}
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="font-bold text-white text-xs truncate max-w-[100px] leading-none">{{ Auth::user()->nama_lengkap }}</p>
                        <p class="text-red-200 text-[10px] font-medium tracking-wide leading-none mt-1">{{ Auth::user()->role }}</p>
                    </div>
                    <i class="fa-solid fa-chevron-down text-[10px] text-red-300 ml-1 hidden sm:block"></i>
                </button>

                {{-- Dropdown Profil --}}
                <div x-show="userMenu" @click.away="userMenu = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-2 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-2 scale-95"
                    class="absolute right-0 mt-3 w-56 bg-gradient-to-b from-red-950 to-red-900 rounded-2xl shadow-xl border border-red-800 overflow-hidden z-50 origin-top-right"
                    style="display: none;">
                    
                    <div class="px-4 py-3 bg-red-900/40 border-b border-red-800 sm:hidden">
                        <p class="font-bold text-white text-sm">{{ Auth::user()->nama_lengkap }}</p>
                        <p class="text-red-300 text-xs font-medium">{{ Auth::user()->role }}</p>
                    </div>

                    <div class="p-2 space-y-1">
                        <a href="{{ route('profile.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-100 hover:text-white hover:bg-white/10 transition-colors">
                            <i class="fa-regular fa-id-badge w-4 text-center"></i>
                            Profil Saya
                        </a>
                        <a href="{{ route('pengaturan.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-red-100 hover:text-white hover:bg-white/10 transition-colors">
                            <i class="fa-solid fa-sliders w-4 text-center"></i>
                            Pengaturan
                        </a>
                        
                        <div class="border-t border-red-800/50 my-1"></div>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 px-3 py-2.5 w-full rounded-xl text-sm font-bold text-red-300 hover:text-white hover:bg-red-800/50 transition-colors text-left">
                                <i class="fa-solid fa-right-from-bracket w-4 text-center"></i>
                                Keluar Aplikasi
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </header>
</div>