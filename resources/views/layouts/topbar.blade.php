@php
    // Notification queries
    $pendingPO = \App\Models\PurchaseOrder::where('status_po', 'Pending')->count();
    $pendingSJ = \App\Models\SuratJalan::where('status', 'Pending')->count();
    $pendingRetur = \App\Models\ReturBarang::where('status_retur', 'Pending')->count();
    $pengirimanMenunggu = \App\Models\Pengiriman::where('status_pengiriman', 'Menunggu')->count();
    $recentPO = \App\Models\PurchaseOrder::where('status_po', 'Pending')
        ->with('supplier')
        ->latest()
        ->take(3)
        ->get();

    $recentSJ = \App\Models\SuratJalan::where('status', 'Pending')
        ->with('pelanggan')
        ->latest()
        ->take(3)
        ->get();

    $recentPengiriman = \App\Models\Pengiriman::where('status_pengiriman', 'Menunggu')
        ->with('suratJalan.pelanggan')
        ->latest()
        ->take(3)
        ->get();

    $needReorderItems = Cache::remember('rop_notifications', 5, function () {
        return \App\Helpers\ReorderPointHelper::getItemsToReorder();
    });
    $needReorderCount = $needReorderItems->count();
    $recentReorder = $needReorderItems->take(3);

    $totalNotif = $pendingPO + $pendingSJ + $pendingRetur + $pengirimanMenunggu + $needReorderCount;
@endphp

<header id="main-navbar"
    class="py-3 px-6 lg:px-10 bg-gradient-to-r from-red-900 to-red-950 shadow-xl sticky top-0 z-30 rounded-b-3xl">
    <div class="flex justify-between items-center w-full max-w-7xl mx-auto">

        {{-- Left: Page Title + Breadcrumb --}}
        <div class="flex items-center gap-3">
            {{-- Mobile hamburger --}}
            <button @click="sidebarOpen = !sidebarOpen"
                class="md:hidden text-white p-2 rounded-lg hover:bg-red-800 transition">
                <i class="fa-solid fa-bars text-lg"></i>
            </button>
            <div>
                <h1 class="text-xl font-bold text-white tracking-wide leading-tight">
                    @yield('page-title', 'Dashboard')
                </h1>
                {{-- Breadcrumb --}}
                @php
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
                <nav class="hidden sm:flex items-center gap-1.5 mt-0.5 text-sm">
                    <a href="/dashboard" class="text-red-300 hover:text-white transition">
                        <i class="fa-solid fa-house text-xs"></i>
                    </a>
                    @foreach($segments as $i => $segment)
                        <span class="text-red-500/60">/</span>
                        @if($i < count($segments) - 1)
                            <a href="/{{ implode('/', array_slice($segments, 0, $i + 1)) }}"
                                class="text-red-300 hover:text-white transition">
                                {{ $breadcrumbLabels[$segment] ?? ucwords(str_replace(['-', '_'], ' ', $segment)) }}
                            </a>
                        @else
                            <span class="text-white/80 font-medium">
                                {{ $breadcrumbLabels[$segment] ?? ucwords(str_replace(['-', '_'], ' ', $segment)) }}
                            </span>
                        @endif
                    @endforeach
                </nav>
            </div>
        </div>

        {{-- Right: Actions --}}
        <div class="flex items-center gap-2 sm:gap-3">

            {{-- Notification Bell --}}
            <div x-data="{ notifOpen: false }" class="relative">
                <button @click="notifOpen = !notifOpen"
                    class="relative w-10 h-10 flex items-center justify-center rounded-xl bg-red-800/50 hover:bg-red-800 text-white transition-all shadow-sm border border-red-700/30">
                    <i class="fa-regular fa-bell text-lg"></i>
                    @if($totalNotif > 0)
                        <span
                            class="absolute -top-1 -right-1 w-5 h-5 bg-amber-500 text-[10px] font-bold text-white rounded-full flex items-center justify-center shadow-lg animate-pulse">
                            {{ $totalNotif > 9 ? '9+' : $totalNotif }}
                        </span>
                    @endif
                </button>

                {{-- Dropdown --}}
                <div x-show="notifOpen" @click.away="notifOpen = false"
                    x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                    x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                    class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50"
                    style="display: none;">

                    {{-- Header --}}
                    <div class="px-5 py-3 bg-gradient-to-r from-red-50 to-white border-b border-gray-100">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-bold text-gray-900">Notifikasi</h3>
                            @if($totalNotif > 0)
                                <span
                                    class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-bold rounded-full">{{ $totalNotif }}
                                    baru</span>
                            @endif
                        </div>
                    </div>

                    {{-- Summary Cards --}}
                    <div class="grid grid-cols-2 gap-2 p-3">
                        @if($pendingPO > 0)
                            <a href="{{ route('approval.approval_po') }}"
                                class="flex items-center gap-2 p-2.5 bg-amber-50 rounded-xl hover:bg-amber-100 transition group">
                                <div
                                    class="w-8 h-8 bg-amber-500 text-white rounded-lg flex items-center justify-center text-xs font-bold">
                                    {{ $pendingPO }}</div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800 group-hover:text-amber-800">PO Pending</p>
                                    <p class="text-[10px] text-gray-400">Menunggu approval</p>
                                </div>
                            </a>
                        @endif

                        @if($pendingSJ > 0)
                            <a href="{{ route('approval.approval_surat_jalan') }}"
                                class="flex items-center gap-2 p-2.5 bg-blue-50 rounded-xl hover:bg-blue-100 transition group">
                                <div
                                    class="w-8 h-8 bg-blue-500 text-white rounded-lg flex items-center justify-center text-xs font-bold">
                                    {{ $pendingSJ }}</div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800 group-hover:text-blue-800">SJ Pending</p>
                                    <p class="text-[10px] text-gray-400">Menunggu approval</p>
                                </div>
                            </a>
                        @endif

                        @if($pendingRetur > 0)
                            <a href="{{ route('retur.index') }}"
                                class="flex items-center gap-2 p-2.5 bg-red-50 rounded-xl hover:bg-red-100 transition group">
                                <div
                                    class="w-8 h-8 bg-red-500 text-white rounded-lg flex items-center justify-center text-xs font-bold">
                                    {{ $pendingRetur }}</div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800 group-hover:text-red-800">Retur Pending
                                    </p>
                                    <p class="text-[10px] text-gray-400">Perlu konfirmasi</p>
                                </div>
                            </a>
                        @endif

                        @if($pengirimanMenunggu > 0)
                            <a href="{{ route('pengiriman.index') }}"
                                class="flex items-center gap-2 p-2.5 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition group">
                                <div
                                    class="w-8 h-8 bg-emerald-500 text-white rounded-lg flex items-center justify-center text-xs font-bold">
                                    {{ $pengirimanMenunggu }}</div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800 group-hover:text-emerald-800">Pengiriman
                                    </p>
                                    <p class="text-[10px] text-gray-400">Menunggu dikirim</p>
                                </div>
                            </a>
                        @endif

                        @if($needReorderCount > 0)
                            <a href="{{ route('reorder_point.index') }}"
                                class="flex items-center gap-2 p-2.5 bg-rose-50 rounded-xl hover:bg-rose-100 transition group">
                                <div
                                    class="w-8 h-8 bg-rose-500 text-white rounded-lg flex items-center justify-center text-xs font-bold">
                                    {{ $needReorderCount }}</div>
                                <div>
                                    <p class="text-xs font-semibold text-gray-800 group-hover:text-rose-800">Perlu Dipesan</p>
                                    <p class="text-[10px] text-gray-400">Stok mencapai batas</p>
                                </div>
                            </a>
                        @endif
                    </div>

                    {{-- Recent PO Items --}}
                    @if($recentPO->count() > 0)
                        <div class="border-t border-gray-100">
                            <p class="px-4 pt-2 pb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">PO
                                Terbaru</p>
                            @foreach($recentPO as $po)
                                <a href="{{ route('approval.show_po', $po->po_id) }}"
                                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                                    <div
                                        class="w-8 h-8 bg-amber-100 text-amber-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-file-invoice text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $po->po_id }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">{{ $po->supplier->namaSupplier ?? '-' }}
                                        </p>
                                    </div>
                                    <span
                                        class="px-1.5 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold rounded flex-shrink-0">Pending</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Recent SJ Items --}}
                    @if($recentSJ->count() > 0)
                        <div class="border-t border-gray-100">
                            <p class="px-4 pt-2 pb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">SJ
                                Terbaru</p>
                            @foreach($recentSJ as $sj)
                                <a href="{{ route('approval.show_surat_jalan', $sj->sj_id) }}"
                                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                                    <div
                                        class="w-8 h-8 bg-blue-100 text-blue-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-truck text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $sj->sj_id }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">
                                            {{ $sj->pelanggan->nama_pelanggan ?? '-' }}</p>
                                    </div>
                                    <span
                                        class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-[10px] font-bold rounded flex-shrink-0">Pending</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Recent Pengiriman Items --}}
                    @if($recentPengiriman->count() > 0)
                        <div class="border-t border-gray-100">
                            <p class="px-4 pt-2 pb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Pengiriman Terbaru</p>
                            @foreach($recentPengiriman as $kiriman)
                                <a href="{{ route('pengiriman.show', $kiriman->pengiriman_id) }}"
                                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                                    <div class="w-8 h-8 bg-emerald-100 text-emerald-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-truck-fast text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $kiriman->sj_id }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">{{ $kiriman->suratJalan->pelanggan->nama_pelanggan ?? $kiriman->sj_id }}</p>
                                    </div>
                                    <span class="px-1.5 py-0.5 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded flex-shrink-0">Menunggu</span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Recent Reorder Items --}}
                    @if($recentReorder->count() > 0)
                        <div class="border-t border-gray-100">
                            <p class="px-4 pt-2 pb-1 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Perlu Segera Dipesan</p>
                            @foreach($recentReorder as $item)
                                <a href="{{ route('reorder_point.index') }}"
                                    class="flex items-center gap-3 px-4 py-2.5 hover:bg-gray-50 transition">
                                    <div class="w-8 h-8 bg-rose-100 text-rose-700 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-box-open text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold text-gray-800 truncate">{{ $item->nama_barang }}</p>
                                        <p class="text-[10px] text-gray-400 truncate">Sisa: {{ number_format($item->stok_saat_ini, 1, ',', '.') }} / Min: {{ number_format($item->rop_value, 1, ',', '.') }}</p>
                                    </div>
                                    <span class="px-1.5 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-bold rounded flex-shrink-0"><i class="fa-solid fa-triangle-exclamation"></i></span>
                                </a>
                            @endforeach
                        </div>
                    @endif

                    {{-- Empty state --}}
                    @if($totalNotif === 0)
                        <div class="py-8 text-center">
                            <i class="fa-regular fa-bell-slash text-3xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-400">Tidak ada notifikasi baru</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- User Info Card --}}
            <div
                class="hidden sm:flex items-center p-2 bg-red-800/40 backdrop-blur-sm rounded-xl border border-red-700/30 shadow-sm">
                <div
                    class="h-9 w-9 bg-red-100 rounded-lg flex items-center justify-center text-red-900 text-sm font-bold shadow border-2 border-red-700/30 mr-2.5">
                    {{ strtoupper(substr(Auth::user()->nama_lengkap, 0, 1)) }}
                </div>
                <div class="mr-2">
                    <p class="font-semibold text-white text-xs truncate max-w-[120px] leading-tight">
                        {{ Auth::user()->nama_lengkap }}</p>
                    <p class="text-red-300 text-[10px] leading-tight">{{ Auth::user()->role }}</p>
                </div>
            </div>

            {{-- Profile --}}
            <a href="{{ route('profile.index') }}"
                class="w-10 h-10 flex items-center justify-center bg-white/10 hover:bg-white/20 text-white rounded-xl transition-all border border-red-700/30 shadow-sm"
                title="Profil Saya">
                <i class="fa-regular fa-user text-sm"></i>
            </a>

            {{-- Logout --}}
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-10 h-10 flex items-center justify-center bg-white/10 hover:bg-red-700 text-white rounded-xl transition-all border border-red-700/30 shadow-sm"
                    title="Logout">
                    <i class="fa-solid fa-right-from-bracket text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</header>