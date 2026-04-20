@php
    // --- LOGIKA PENENTUAN ACTIVE STATE UNTUK DROPDOWN ---

    // Data Barang paths
    $dataBarangActive = request()->is('barangs*') ||
        request()->is('barangs-riwayat*') ||
        request()->is('barangs-audit-log*');

    // Data Master paths
    $dataMasterActive = $dataBarangActive ||
        request()->is('pelanggans*') ||
        request()->is('kategori_pelanggan*') ||
        request()->is('suppliers*') ||
        request()->is('kategori_barang*');

    // Inventori Gudang paths
    $inventoriGudangActive = request()->is('katalog_barang*') ||
        request()->is('stok-barang*') ||
        request()->is('stock_opname*') ||
        request()->is('tracking-kadaluarsa*') ||
        request()->is('reorder-point*');

    // Kelas untuk Active & Default Link
    $activeLinkClasses = 'bg-white/10 text-white shadow-md border-l-4 border-amber-400 font-medium';
    $defaultLinkClasses = 'text-red-200 hover:bg-white/5 hover:text-white font-medium border-l-4 border-transparent';

    // Kelas untuk Active & Default Sub-Link
    $activeSubLinkClasses = 'bg-white/10 text-amber-400 font-semibold shadow-sm';
    $defaultSubLinkClasses = 'text-red-300 hover:bg-white/5 hover:text-white';
@endphp

<div class="bg-gradient-to-b from-red-950 to-red-900 border-r border-red-800/50 text-white w-64 md:w-68 space-y-6 py-6 px-4 fixed inset-y-0 left-0 z-40 h-full 
        transform transition duration-300 ease-in-out md:relative md:translate-x-0 overflow-y-auto overflow-x-hidden custom-scrollbar"
    :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}" @click.away="sidebarOpen = false" x-data="{ 
        dataMasterOpen: {{ $dataMasterActive ? 'true' : 'false' }}, 
        inventoriGudangOpen: {{ $inventoriGudangActive ? 'true' : 'false' }} 
    }">

    {{-- Logo / Branding --}}
    <div class="px-2 mb-8">
        <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="w-32 mx-auto filter drop-shadow-md">
    </div>

    {{-- Main Navbar Links --}}
    <nav class="space-y-1">

        {{-- Dashboard - Link Utama --}}
        <h3 class="text-red-400/70 uppercase tracking-widest text-[10px] font-bold mt-2 mb-2 px-3">Overview</h3>
        <a href="/dashboard" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-200 
                {{ request()->is('dashboard') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i
                class="fa-solid fa-layer-group text-lg w-5 text-center {{ request()->is('dashboard') ? 'text-amber-400' : '' }}"></i>
            <span class="text-sm">Dashboard</span>
        </a>

        {{-- Group 1: Inventory --}}
        <h3 class="text-red-400/70 uppercase tracking-widest text-[10px] font-bold mt-8 mb-2 px-3">Inventory Core</h3>

        {{-- Inventori Gudang Dropdown --}}
        <div>
            <!-- Tombol Dropdown Utama -->
            <button @click="inventoriGudangOpen = !inventoriGudangOpen"
                class="w-full text-left flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 
                        focus:outline-none focus:ring-2 focus:ring-red-500/50
                        {{ $inventoriGudangActive ? 'bg-white/5 text-white' : 'text-red-200 hover:bg-white/5 hover:text-white' }}">
                <span class="flex items-center gap-3">
                    <i
                        class="fa-solid fa-boxes-stacked text-lg w-5 text-center {{ $inventoriGudangActive ? 'text-amber-400' : '' }}"></i>
                    <span class="text-sm font-medium">Inventori Gudang</span>
                </span>
                <i class="fa-solid fa-chevron-down transform transition-transform duration-200 text-xs"
                    :class="{'rotate-180': inventoriGudangOpen}"></i>
            </button>

            <!-- Isi Dropdown -->
            <div x-show="inventoriGudangOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="origin-top ml-5 border-l border-red-800/60 pl-2 mt-1 space-y-1" style="display: none;">
                <a href="/katalog_barang" class="block text-[13px] py-2 px-3 rounded-lg transition-all duration-150
                    {{ request()->is('katalog_barang*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Katalog
                    Barang</a>

                <a href="/stok-barang" class="block text-[13px] py-2 px-3 rounded-lg transition-all duration-150
                    {{ request()->is('stok-barang*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Stok
                    Barang</a>

                <a href="/reorder-point" class="block text-[13px] py-2 px-3 rounded-lg transition-all duration-150
                    {{ request()->is('reorder-point*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Reorder
                    Point</a>

                <a href="/stock_opname" class="block text-[13px] py-2 px-3 rounded-lg transition-all duration-150
                    {{ request()->is('stock_opname*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Stock
                    Opname</a>

                <a href="/tracking-kadaluarsa"
                    class="block text-[13px] py-2 px-3 rounded-lg transition-all duration-150
                    {{ request()->is('tracking-kadaluarsa*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Tracking Kadaluarsa</a>
            </div>
        </div>

        {{-- Permintaan & Transaksi --}}
        <a href="{{ route('purchase_orders.buat_permintaan') }}" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-200
            {{ request()->is('purchase_orders/buat_permintaan*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i
                class="fa-solid fa-money-check-dollar text-lg w-5 text-center {{ request()->is('purchase_orders/buat_permintaan*') ? 'text-amber-400' : '' }}"></i>
            <span class="text-sm">Permintaan Pembelian</span>
        </a>

        <a href="/verifBarang" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-200
            {{ request()->is('verifBarang*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i
                class="fa-solid fa-clipboard-check text-lg w-5 text-center {{ request()->is('verifBarang*') ? 'text-amber-400' : '' }}"></i>
            <span class="text-sm">Verifikasi Barang</span>
        </a>

        <a href="/returBarang" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-200
            {{ request()->is('returBarang*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i
                class="fa-solid fa-arrow-right-arrow-left text-lg w-5 text-center {{ request()->is('returBarang*') ? 'text-amber-400' : '' }}"></i>
            <span class="text-sm">Retur Barang</span>
        </a>


        {{-- Group 2: Surat Jalan & Pengiriman --}}
        <h3 class="text-red-400/70 uppercase tracking-widest text-[10px] font-bold mt-8 mb-2 px-3">Distribusi</h3>

        <a href="{{ route('surat_jalan.index') }}" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-200
            {{ request()->is('surat_jalan*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i
                class="fa-solid fa-file-invoice-dollar text-lg w-5 text-center {{ request()->is('surat_jalan*') ? 'text-amber-400' : '' }}"></i>
            <span class="text-sm">Surat Jalan</span>
        </a>

        <a href="/pengiriman" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-200
            {{ request()->is('pengiriman*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i
                class="fa-solid fa-truck-fast text-lg w-5 text-center {{ request()->is('pengiriman*') ? 'text-amber-400' : '' }}"></i>
            <span class="text-sm">Pengiriman Barang</span>
        </a>

        {{-- Group 3: Approval --}}
        <h3 class="text-red-400/70 uppercase tracking-widest text-[10px] font-bold mt-8 mb-2 px-3">Approval</h3>

        <a href="{{ route('approval.approval_po') }}" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-200
            {{ request()->is('approval/approval_po*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i
                class="fa-solid fa-file-signature text-lg w-5 text-center {{ request()->is('approval/approval_po*') ? 'text-amber-400' : '' }}"></i>
            <span class="text-sm">Approval PO</span>
        </a>

        <a href="{{ route('approval.approval_surat_jalan') }}" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-200
            {{ request()->is('approval/approval_surat_jalan*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i
                class="fa-solid fa-file-signature text-lg w-5 text-center {{ request()->is('approval/approval_surat_jalan*') ? 'text-amber-400' : '' }}"></i>
            <span class="text-sm">Approval SJ</span>
        </a>

        {{-- Group 4: Laporan --}}
        <h3 class="text-red-400/70 uppercase tracking-widest text-[10px] font-bold mt-8 mb-2 px-3">Analytics & Reports
        </h3>

        <a href="/laporan/barang-masuk" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-200
            {{ request()->is('laporan/barang-masuk*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i
                class="fa-solid fa-chart-pie text-lg w-5 text-center {{ request()->is('laporan/barang-masuk*') ? 'text-amber-400' : '' }}"></i>
            <span class="text-sm">Laporan Masuk</span>
        </a>

        <a href="/laporan/barang-keluar" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-200
            {{ request()->is('laporan/barang-keluar*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i
                class="fa-solid fa-chart-line text-lg w-5 text-center {{ request()->is('laporan/barang-keluar*') ? 'text-amber-400' : '' }}"></i>
            <span class="text-sm">Laporan Keluar</span>
        </a>

        {{-- Group 5: Settings & Management --}}
        <h3 class="text-red-400/70 uppercase tracking-widest text-[10px] font-bold mt-8 mb-2 px-3">System & Data</h3>

        {{-- Data Master Dropdown --}}
        <div>
            <!-- Tombol Dropdown Utama -->
            <button @click="dataMasterOpen = !dataMasterOpen"
                class="w-full text-left flex items-center justify-between px-3 py-2.5 rounded-xl transition-all duration-200 
                        focus:outline-none focus:ring-2 focus:ring-red-500/50 
                        {{ $dataMasterActive ? 'bg-white/5 text-white' : 'text-red-200 hover:bg-white/5 hover:text-white' }}">
                <span class="flex items-center gap-3">
                    <i
                        class="fa-solid fa-database text-lg w-5 text-center {{ $dataMasterActive ? 'text-amber-400' : '' }}"></i>
                    <span class="text-sm font-medium">Data Master</span>
                </span>
                <i class="fa-solid fa-chevron-down transform transition-transform duration-200 text-xs"
                    :class="{'rotate-180': dataMasterOpen}"></i>
            </button>

            <!-- Isi Dropdown -->
            <div x-show="dataMasterOpen" x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2"
                class="origin-top ml-5 border-l border-red-800/60 pl-2 mt-1 space-y-1" style="display: none;">
                {{-- Data Barang Sub-Dropdown --}}
                <div x-data="{ dataBarangOpen: {{ $dataBarangActive ? 'true' : 'false' }} }" class="mb-1">
                    <button @click="dataBarangOpen = !dataBarangOpen" type="button"
                        class="w-full text-left flex items-center justify-between py-2 px-3 rounded-lg transition-all duration-150
                            {{ $dataBarangActive ? 'bg-white/10 text-amber-400 font-semibold shadow-sm' : 'text-red-300 hover:bg-white/5 hover:text-white' }}">
                        <span class="flex items-center gap-2">
                            <span class="text-[13px]">Data Barang</span>
                        </span>
                        <i class="fa-solid fa-chevron-down transform transition-transform duration-200 text-[10px]"
                            :class="{'rotate-180': dataBarangOpen}"></i>
                    </button>

                    <div x-show="dataBarangOpen" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-1"
                        class="ml-4 border-l border-red-800/40 pl-2 mt-1 space-y-1" style="display: none;">
                        <a href="/barangs"
                            class="block text-[12px] py-1.5 px-3 rounded-md transition-all duration-150
                            {{ request()->is('barangs') || request()->is('barangs/create') || request()->is('barangs/*/edit') ? 'text-amber-300 font-semibold bg-white/5' : 'text-red-400 hover:bg-white/5 hover:text-red-200' }}">
                            <i class="fa-solid fa-table-list text-[9px] mr-1.5"></i>Daftar Barang</a>
                        <a href="{{ route('barangs.trashed') }}"
                            class="block text-[12px] py-1.5 px-3 rounded-md transition-all duration-150
                            {{ request()->is('barangs-riwayat*') ? 'text-amber-300 font-semibold bg-white/5' : 'text-red-400 hover:bg-white/5 hover:text-red-200' }}">
                            <i class="fa-solid fa-clock-rotate-left text-[9px] mr-1.5"></i>Riwayat Hapus</a>
                        <a href="{{ route('barangs.auditLogs') }}"
                            class="block text-[12px] py-1.5 px-3 rounded-md transition-all duration-150
                            {{ request()->is('barangs-audit-log*') ? 'text-amber-300 font-semibold bg-white/5' : 'text-red-400 hover:bg-white/5 hover:text-red-200' }}">
                            <i class="fa-solid fa-timeline text-[9px] mr-1.5"></i>Log Perubahan</a>
                    </div>
                </div>

                <a href="/pelanggans" class="block text-[13px] py-2 px-3 rounded-lg transition-all duration-150 
                    {{ request()->is('pelanggans*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Data
                    Pelanggan</a>

                <a href="/kategori_pelanggan"
                    class="block text-[13px] py-2 px-3 rounded-lg transition-all duration-150 
                    {{ request()->is('kategori_pelanggan*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Kategori Pelanggan</a>

                <a href="/suppliers" class="block text-[13px] py-2 px-3 rounded-lg transition-all duration-150 
                    {{ request()->is('suppliers*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Data
                    Supplier</a>

                <a href="/kategori_barang" class="block text-[13px] py-2 px-3 rounded-lg transition-all duration-150 
                    {{ request()->is('kategori_barang*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Kategori
                    Barang</a>
            </div>
        </div>

        <a href="{{ route('pengaturan.index') }}" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-200
                {{ request()->is('pengaturan*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i
                class="fa-solid fa-sliders text-lg w-5 text-center {{ request()->is('pengaturan*') ? 'text-amber-400' : '' }}"></i>
            <span class="text-sm">Pengaturan</span>
        </a>

        @if(auth()->user()->role === 'SuperAdmin')
            <a href="/users" class="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl transition-all duration-200
                            {{ request()->is('users*') ? $activeLinkClasses : $defaultLinkClasses }}">
                <i
                    class="fa-solid fa-users-gear text-lg w-5 text-center {{ request()->is('users*') ? 'text-amber-400' : '' }}"></i>
                <span class="text-sm">Users & Roles</span>
            </a>
        @endif

        <div class="h-10"></div> {{-- Bottom spacing padding --}}
    </nav>
</div>