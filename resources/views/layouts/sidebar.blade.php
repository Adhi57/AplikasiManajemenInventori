@php
    // --- LOGIKA PENENTUAN ACTIVE STATE UNTUK DROPDOWN ---

    // Data Master paths
    $dataMasterActive = request()->is('barangs*') || 
                        request()->is('pelanggans*') || 
                        request()->is('kategori_pelanggan*') || 
                        request()->is('suppliers*') || 
                        request()->is('kategori_barang*');

    // Inventori Gudang paths
    $inventoriGudangActive = request()->is('katalog_barang*') || 
                             request()->is('stok-barang*') || 
                             request()->is('stock_opname*');
    
    // Fungsi untuk kelas link aktif
    $activeLinkClasses = 'bg-red-800 text-white shadow-xl shadow-red-950/70';
    $defaultLinkClasses = 'text-neutral-100';

    // Fungsi untuk kelas sub-link aktif
    $activeSubLinkClasses = 'bg-red-700/80 text-white font-semibold shadow-inner';
    $defaultSubLinkClasses = 'text-neutral-200';
@endphp

<div
    class="bg-gradient-to-b from-red-900 to-red-950 text-white w-68 space-y-6 py-7 px-4 fixed inset-y-0 left-0 z-40 h-full 
        transform transition duration-300 ease-in-out md:relative md:translate-x-0 overflow-y-scroll"
    :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
    @click.away="sidebarOpen = false"
    {{-- Initial state ditentukan oleh apakah ada sub-menu yang aktif --}}
    x-data="{ 
        dataMasterOpen: {{ $dataMasterActive ? 'true' : 'false' }}, 
        inventoriGudangOpen: {{ $inventoriGudangActive ? 'true' : 'false' }} 
    }"
>

    {{-- Logo / Branding --}}
    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="w-36 mx-auto mb-6">

    {{-- Dashboard - Link Utama --}}
    <a href="/dashboard"
        class="text-sm font-semibold flex items-center gap-3 w-full p-2.5 rounded-xl transition-all duration-200 
                hover:bg-red-800/70 hover:shadow-lg hover:shadow-red-950/80
                {{ request()->is('dashboard') ? $activeLinkClasses : $defaultLinkClasses }}">
        <i class="fa-regular fa-chart-bar text-xl w-6"></i>
        <span>Dashboard</span>
    </a>

    <nav class="space-y-1">
        
        {{-- Group 1: Inventory --}}
        <h3 class="text-red-400 uppercase tracking-wider text-xs font-semibold pt-4 pb-2 border-t border-red-800/50">Inventory</h3>
        {{-- Inventori Gudang --}}
        <div>
            <!-- Tombol Dropdown Utama -->
            <button
                @click="inventoriGudangOpen = !inventoriGudangOpen"
                class="w-full text-left flex items-center justify-between text-sm font-medium p-2.5 rounded-xl transition-all duration-200 
                        hover:bg-red-800/70 focus:outline-none
                        {{ $inventoriGudangActive ? 'bg-red-800 text-white shadow-inner shadow-red-950/50' : 'text-neutral-100' }}"
            >
                <span class="flex items-center gap-3">
                    <i class="fa-solid fa-boxes-stacked text-xl w-6"></i>
                    <p>Inventori Gudang</p>
                </span>
                <i class="fa-solid fa-chevron-down transform transition-transform duration-200 text-xs"
                    :class="{'rotate-180': inventoriGudangOpen}"></i>
            </button>

            <!-- Isi Dropdown -->
            <div
                x-show="inventoriGudangOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 max-h-0"
                x-transition:enter-end="opacity-100 max-h-screen"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 max-h-screen"
                x-transition:leave-end="opacity-0 max-h-0"
                class="origin-top ml-4 border-l-2 border-red-500 pl-4 py-1 space-y-1 overflow-hidden"
                style="display: none;"
            >
                <a href="/katalog_barang" 
                    class="text-sm font-normal block py-1.5 rounded-md pl-2 transition-all duration-150
                    {{ request()->is('katalog_barang*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Katalog Barang</a>
                
                <a href="/stok-barang" 
                    class="text-sm font-normal block py-1.5 rounded-md pl-2 transition-all duration-150
                    {{ request()->is('stok-barang*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Stok Barang</a>
                
                <a href="/stock_opname" 
                    class="text-sm font-normal block py-1.5 rounded-md pl-2 transition-all duration-150
                    {{ request()->is('stock_opname*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Stock Opname</a>
                
                <a href="/tracking-kadaluarsa" 
                    class="text-sm font-normal block py-1.5 rounded-md pl-2 transition-all duration-150
                    {{ request()->is('batch*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Batch & Tracking Kadaluarsa</a>
            </div>
        </div>
        
        {{-- Link Navigasi Non-Dropdown --}}
        <a href="/verifBarang"
            class="flex items-center gap-3 text-sm font-medium w-full p-2.5 rounded-xl hover:bg-red-800/70 transition-all duration-200
            {{ request()->is('verifBarang*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i class="fa-solid fa-clipboard-check text-xl w-6"></i>
            <span>Verifikasi Barang Masuk</span>
        </a>

        <a href="/returBarang"
            class="flex items-center gap-3 text-sm font-medium w-full p-2.5 rounded-xl hover:bg-red-800/70 transition-all duration-200
            {{ request()->is('returBarang*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i class="fa-solid fa-truck-arrow-right text-xl w-6"></i>
            <span>Retur Barang</span>
        </a>

        <a href="{{ route('purchase_orders.buat_permintaan') }}"
            class="flex items-center gap-3 text-sm font-medium w-full p-2.5 rounded-xl hover:bg-red-800/70 transition-all duration-200
            {{ request()->is('purchase_orders/buat_permintaan*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i class="fa-solid fa-money-check-dollar text-xl w-6"></i>
            <span>Permintaan Pembelian</span>
        </a>


        {{-- Group 2: Surat Jalan dan Pengiriman --}}
        <h3 class="text-red-400 uppercase tracking-wider text-xs font-semibold mt-6 pt-4 pb-2 border-t border-red-800/50">Surat Jalan & Pengiriman</h3>
        
        <a href="{{ route('surat_jalan.index') }}"
            class="flex items-center gap-3 text-sm font-medium w-full p-2.5 rounded-xl hover:bg-red-800/70 transition-all duration-200
            {{ request()->is('surat_jalan*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i class="fa-solid fa-file-invoice-dollar text-xl w-6"></i>
            <span>Surat Jalan</span>
        </a>
        
        <a href="/pengiriman"
            class="flex items-center gap-3 text-sm font-medium w-full p-2.5 rounded-xl hover:bg-red-800/70 transition-all duration-200
            {{ request()->is('pengiriman*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i class="fa-solid fa-truck text-xl w-6"></i>
            <span>Pengiriman Barang</span>
        </a>

        {{-- Group 3: Approval --}}
        <h3 class="text-red-400 uppercase tracking-wider text-xs font-semibold mt-6 pt-4 pb-2 border-t border-red-800/50">Approval</h3>

        <a href="{{ route('approval.approval_po') }}" 
            class="flex items-center gap-3 text-sm font-medium w-full p-2.5 rounded-xl hover:bg-red-800/70 transition-all duration-200
            {{ request()->is('approval/approval_po*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i class="fa-solid fa-file text-xl w-6"></i>
            <span>Approval PO</span>
        </a>

        <a href="{{ route('approval.approval_surat_jalan') }}" 
            class="flex items-center gap-3 text-sm font-medium w-full p-2.5 rounded-xl hover:bg-red-800/70 transition-all duration-200
            {{ request()->is('approval/approval_surat_jalan*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i class="fa-solid fa-file text-xl w-6"></i>
            <span>Approval Surat Jalan</span>
        </a>

        {{-- Group 4: Laporan --}}
        <h3 class="text-red-400 uppercase tracking-wider text-xs font-semibold mt-6 pt-4 pb-2 border-t border-red-800/50">Laporan</h3>

        <a href="/laporan/barang-masuk" 
            class="flex items-center gap-3 text-sm font-medium w-full p-2.5 rounded-xl hover:bg-red-800/70 transition-all duration-200
            {{ request()->is('laporan/barang-masuk*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i class="fa-solid fa-file-export text-xl w-6"></i>
            <span>Laporan Barang Masuk</span>
        </a>

        <a href="/laporan/barang-keluar" 
            class="flex items-center gap-3 text-sm font-medium w-full p-2.5 rounded-xl hover:bg-red-800/70 transition-all duration-200
            {{ request()->is('laporan/barang-keluar*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i class="fa-solid fa-file-export text-xl w-6"></i>
            <span>Laporan Barang Keluar</span>
        </a>

        {{-- Group 5: Settings --}}
        <h3 class="text-red-400 uppercase tracking-wider text-xs font-semibold mt-6 pt-4 pb-2 border-t border-red-800/50">Settings</h3>
        {{-- Data Master --}}
        <div>
            <!-- Tombol Dropdown Utama -->
            <button
                @click="dataMasterOpen = !dataMasterOpen"
                class="w-full text-left flex items-center justify-between text-sm font-medium p-2.5 rounded-xl transition-all duration-200 
                        hover:bg-red-800/70 focus:outline-none 
                        {{ $dataMasterActive ? 'bg-red-800 text-white shadow-inner shadow-red-950/50' : 'text-neutral-100' }}"
            >
                <span class="flex items-center gap-3">
                    <i class="fa-solid fa-database text-xl w-6"></i>
                    <p>Data Master</p>
                </span>
                <i class="fa-solid fa-chevron-down transform transition-transform duration-200 text-xs"
                    :class="{'rotate-180': dataMasterOpen}"></i>
            </button>

            <!-- Isi Dropdown -->
            <div
                x-show="dataMasterOpen"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 max-h-0"
                x-transition:enter-end="opacity-100 max-h-screen"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 max-h-screen"
                x-transition:leave-end="opacity-0 max-h-0"
                class="origin-top ml-4 border-l-2 border-red-500 pl-4 py-1 space-y-1 overflow-hidden"
                style="display: none;"
            >
                <a href="/barangs" 
                    class="text-sm font-normal block py-1.5 rounded-md pl-2 transition-all duration-150 
                    {{ request()->is('barangs*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Data Barang</a>
                
                <a href="/pelanggans" 
                    class="text-sm font-normal block py-1.5 rounded-md pl-2 transition-all duration-150 
                    {{ request()->is('pelanggans*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Data Pelanggan</a>
                
                <a href="/kategori_pelanggan" 
                    class="text-sm font-normal block py-1.5 rounded-md pl-2 transition-all duration-150 
                    {{ request()->is('kategori_pelanggan*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Kategori Pelanggan</a>
                
                <a href="/suppliers" 
                    class="text-sm font-normal block py-1.5 rounded-md pl-2 transition-all duration-150 
                    {{ request()->is('suppliers*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Data Supplier</a>
                
                <a href="/kategori_barang" 
                    class="text-sm font-normal block py-1.5 rounded-md pl-2 transition-all duration-150 
                    {{ request()->is('kategori_barang*') ? $activeSubLinkClasses : $defaultSubLinkClasses }}">Kategori Barang</a>
            </div>
        </div>
        
        <a href="#" 
            class="flex items-center gap-3 text-sm font-medium w-full p-2.5 rounded-xl hover:bg-red-800/70 transition-all duration-200
            {{ request()->is('users-roles*') ? $activeLinkClasses : $defaultLinkClasses }}">
            <i class="fa-solid fa-users-gear text-xl w-6"></i>
            <span>Users & Roles</span>
        </a>
    </nav>
</div>