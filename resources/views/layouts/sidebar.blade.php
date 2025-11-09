<div
    class="bg-gradient-to-t from-red-600 to-red-700 border-gray-400 text-white w-68 space-y-6 py-7 px-4 fixed inset-y-0 left-0 z-40 h-full 
        transform transition duration-300 ease-in-out md:relative md:translate-x-0 overflow-y-scroll"
    :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
    @click.away="sidebarOpen = false"
    x-data="{ dataMasterOpen: false, inventoriGudangOpen: false }"
>

    {{-- Logo / Branding --}}
    <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" class="w-36 mx-auto mb-6">

    {{-- Dashboard --}}
    <a href="/dashboard"
        class="text-sm font-medium text-neutral-100 block my-8 pb-1 px-2 pt-1 rounded transition duration-200 hover:bg-red-700 hover:text-neutral-100 {{ request()->is('dashboard') ? 'bg-red-800 text-neutral-100' : '' }}">
        <i class="fa-regular fa-chart-bar my-1 mx-2 text-xl"></i>Dashboard
    </a>

    <nav>
        <h3 class="text-gray-200 uppercase tracking-wider text-sm mb-2">Inventory</h3>

        {{-- Data Master --}}
        <div class="flex">
            <button
                @click="dataMasterOpen = !dataMasterOpen"
                class="w-full text-left flex items-center justify-between text-sm font-medium text-gray-100 block my-1 pb-1 px-2 pt-1 rounded transition duration-200 hover:bg-red-700 focus:outline-none"
                :class="{'bg-red-700': dataMasterOpen}">
                <span class="flex ml-3 items-center gap-2">
                    <i class="fa-solid fa-database text-xl w-6"></i>
                    <p>Data Master</p>
                </span>
                <i class="fa-solid fa-chevron-down transform transition-transform duration-200 text-xs"
                    :class="{'rotate-180': dataMasterOpen}"></i>
            </button>
        </div>

        <div
            x-show="dataMasterOpen"
            x-transition
            class="origin-top ml-4 border-l border-red-400 pl-2 space-y-1"
            style="display: none;"
        >
            <a href="/barangs" class="text-sm font-medium block py-1.5 pl-6 rounded hover:bg-red-600">Data Barang</a>
            <a href="/pelanggans" class="text-sm font-medium block py-1.5 pl-6 rounded hover:bg-red-600">Data Pelanggan</a>
            <a href="/kategori_pelanggan" class="text-sm font-medium block py-1.5 pl-6 rounded hover:bg-red-600">Kategori Pelanggan</a>
            <a href="/suppliers" class="text-sm font-medium block py-1.5 pl-6 rounded hover:bg-red-600">Data Supplier</a>
            <a href="/kategori_barang" class="text-sm font-medium block py-1.5 pl-6 rounded hover:bg-red-600">Kategori Barang</a>
        </div>

        {{-- Inventori Gudang --}}
        <div class="flex">
            <button
                @click="inventoriGudangOpen = !inventoriGudangOpen"
                class="w-full text-left flex items-center justify-between text-sm font-medium text-gray-100 block my-1 pb-1 px-2 pt-1 rounded transition duration-200 hover:bg-red-700 focus:outline-none"
                :class="{'bg-red-700': inventoriGudangOpen}">
                <span class="flex ml-3 items-center gap-2">
                    <i class="fa-solid fa-boxes-stacked text-xl w-6 "></i>
                    <p>Inventori Gudang</p>
                </span>
                <i class="fa-solid fa-chevron-down transform transition-transform duration-200 text-xs"
                    :class="{'rotate-180': inventoriGudangOpen}"></i>
            </button>
        </div>

        <div
            x-show="inventoriGudangOpen"
            x-transition
            class="origin-top ml-4 border-l border-red-400 pl-2 space-y-1"
            style="display: none;"
        >
            <a href="/katalog_barang" class="text-sm font-medium block py-1.5 pl-6 rounded hover:bg-red-600">
                Katalog Barang
            </a>
            <a href="/stok-barang" class="text-sm font-medium block py-1.5 pl-6 rounded hover:bg-red-600">
                Stok Barang
            </a>
            <a href="/stock_opname" class="text-sm font-medium block py-1.5 pl-6 rounded hover:bg-red-600">
                Stock Opname
            </a>
            <a href="/stock_opname" class="text-sm font-medium block py-1.5 pl-6 rounded hover:bg-red-600">
                Batch & Tracking Kadaluarsa
            </a>
        </div>

        {{-- Verifikasi Barang --}}
        <a href="/verifBarang"
            class="text-sm font-medium block my-1 pb-1 px-2 pt-1 rounded hover:bg-red-700 transition duration-200">
            <span class="flex ml-3 items-center gap-2">
                <i class="fa-solid fa-clipboard-check text-xl w-6"></i>
                <p>Verifikasi Barang Masuk</p>
            </span>
        </a>

        {{-- Retur Barang --}}
        <a href="/returBarang"
            class="text-sm font-medium block my-1 pb-1 px-2 pt-1 rounded hover:bg-red-700 transition duration-200">
            <span class="flex ml-3 items-center gap-2">
                <i class="fa-solid fa-clipboard-check text-xl w-6"></i>
                <p>Retur Barang</p>
            </span>
        </a>

        {{-- Permintaan Pembelian --}}
        <a href="{{ route('purchase_orders.buat_permintaan') }}"
            class="text-sm font-medium block my-1 pb-1 px-2 pt-1 rounded hover:bg-red-700 transition duration-200">
            <span class="flex ml-3 items-center gap-2">
                <i class="fa-solid fa-money-check-dollar text-xl w-6"></i>
                <p>Permintaan Pembelian</p>
            </span>
        </a>

        <h3 class="uppercase tracking-wider text-sm text-gray-200 mt-6 mb-2">Surat Jalan dan Pengiriman</h3>

        {{-- Surat Jalan --}}
        
        <a href="{{ route('surat_jalan.index') }}"
        class="text-sm font-medium block my-1 pb-1 px-2 pt-1 rounded hover:bg-red-700 transition duration-200">
        <span class="flex ml-3 items-center gap-2">
            <i class="fa-solid fa-file-invoice-dollar text-xl w-6"></i>
            <p>Surat Jalan</p>
        </span>
    </a>
    
    <a href="/pengiriman"
        class="text-sm font-medium block my-1 pb-1 px-2 pt-1 rounded hover:bg-red-700 transition duration-200">
        <span class="flex ml-3 items-center gap-2">
            <i class="fa-solid fa-file-invoice-dollar text-xl w-6"></i>
            <p>Pengiriman Barang</p>
        </span>
    </a>
        {{-- Approval Section --}}
        <h3 class="uppercase tracking-wider text-sm text-gray-200 mt-6 mb-2">Approval</h3>
        <a href="{{ route('approval.approval_po') }}" class="text-sm font-medium block my-1 py-1.5 px-2 rounded hover:bg-red-700 transition duration-200">
            <span class="flex ml-3 items-center gap-2">
                <i class="fa-solid fa-file text-xl w-6"></i>
                <p>Approval PO</p>
            </span>
        </a>
        <a href="{{ route('approval.approval_surat_jalan') }}" class="text-sm font-medium block my-1 py-1.5 px-2 rounded hover:bg-red-700 transition duration-200">
            <span class="flex ml-3 items-center gap-2">
                <i class="fa-solid fa-file text-xl w-6"></i>
                <p>Approval Surat Jalan</p>
            </span>
        </a>

        {{-- Laporan Section --}}
        <h3 class="uppercase tracking-wider text-sm text-gray-200 mt-6 mb-2">Laporan</h3>
        <a href="/laporan/barang-masuk" class="text-sm font-medium block my-1 py-1.5 px-2 rounded hover:bg-red-700 transition duration-200">
            <span class="flex ml-3 items-center gap-2">
                <i class="fa-solid fa-file-export text-xl w-6"></i>
                <p>Laporan Barang Masuk</p>
            </span>
        </a>
        <a href="/laporan/barang-keluar" class="text-sm font-medium block my-1 py-1.5 px-2 rounded hover:bg-red-700 transition duration-200">
            <span class="flex ml-3 items-center gap-2">
                <i class="fa-solid fa-file-export text-xl w-6"></i>
                <p>Laporan Barang Keluar</p>
            </span>
        </a>

        {{-- Settings --}}
        <h3 class="uppercase tracking-wider text-sm text-gray-200 mt-6 mb-2">Settings</h3>
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 text-gray-100">
            Users & Roles
        </a>
    </nav>
</div>
