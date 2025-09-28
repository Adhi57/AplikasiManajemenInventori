{{--
    Sidebar Container: 
    - Fixed position on mobile (z-40)
    - Full height (h-screen)
    - Hidden by default on mobile (-translate-x-full)
    - Visible and part of flow on desktop (md:translate-x-0)
--}}
<div
    class=" bg-linear-to-t from-red-500 to-red-700 border-gray-400 text-white w-68 space-y-6 py-7 px-4 fixed inset-y-0 left-0 z-40 h-full 
  transform transition duration-300 ease-in-out md:relative md:translate-x-0 overflow-y-scroll"
    :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}"
    @click.away="sidebarOpen = false"
    x-data="{ dataMasterOpen: false }">

    {{-- Logo / Branding --}}
    <img src="{{asset('assets/images/logo.png')}}">

    {{-- Navigation --}}

    <a href="/dashboard" class="  text-sm font-medium text-neutral-100 block my-8 py-2.5 px-4 rounded transition duration-200 hover:bg-red-700 hover:text-neutral-100 @if(request()->is('dashboard')) bg-red-800 text-neutral-100 @endif">
        <i class="fa-regular fa-chart-bar my-1 mx-2 text-xl text-center"></i>Dashboard
    </a>

    <nav>
        <h3 class="text-gray-200 uppercase tracking-wider text-sm mb-2">Inventory</h3>

        {{-- Data Master Dropdown Toggle --}}
        <button
            @click="dataMasterOpen = !dataMasterOpen"
            class="w-full text-left flex items-center justify-between text-sm font-medium text-gray-200 block my-1 py-2.5 px-4 rounded transition duration-200 hover:bg-red-700 hover:text-neutral-100 focus:outline-none"
            :class="{'bg-red-700': dataMasterOpen}">
            <span>
                <i class="fa-solid fa-database my-1 mx-2 text-xl text-center"></i>Data Master
            </span>
            {{-- Dropdown Arrow Icon --}}
            <i class="fa-solid fa-chevron-down transform transition-transform duration-200 text-xs"
                :class="{'rotate-180': dataMasterOpen, 'rotate-0': !dataMasterOpen}"></i>
        </button>

        {{-- Data Master Dropdown Content (using x-show to toggle) --}}
        <div
            x-show="dataMasterOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-y-0"
            x-transition:enter-end="opacity-100 transform scale-y-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-y-100"
            x-transition:leave-end="opacity-0 transform scale-y-0"
            class="origin-top ml-4 border-l border-red-400" {{-- Slight indent and separator --}}
            style="display: none;" {{-- Initial hide for Alpine --}}>
            <a href="/master_dataBarang" class=" text-sm font-medium text-gray-200 block my-1 py-1.5 pl-8 pr-4 rounded transition duration-200 hover:bg-red-600 hover:text-neutral-100">
                Data Barang
            </a>
            <a href="#" class=" text-sm font-medium text-gray-200 block my-1 py-1.5 pl-8 pr-4 rounded transition duration-200 hover:bg-red-600 hover:text-neutral-100">
                Data Pelanggan
            </a>
            <a href="/master_dataSupplier" class=" text-sm font-medium text-gray-200 block my-1 py-1.5 pl-8 pr-4 rounded transition duration-200 hover:bg-red-600 hover:text-neutral-100">
                Data Supplier
            </a>
            <a href="#" class=" text-sm font-medium text-gray-200 block my-1 py-1.5 pl-8 pr-4 rounded transition duration-200 hover:bg-red-600 hover:text-neutral-100">
                Kategori Barang
            </a>
        </div>
        {{-- End Data Master Dropdown --}}

        <a href="#" class=" text-sm font-medium text-gray-200 block my-1 py-2.5 px-4 rounded transition duration-200 hover:bg-red-700 hover:text-neutral-100 @if(request()->is('dashboard')) @endif">
            <i class="fa-solid fa-warehouse my-1 mx-2 text-xl text-center"></i>Inventory Gudang
        </a>

        <a href="#" class=" text-sm font-medium text-gray-200 block my-1 py-2.5 px-4 rounded transition duration-200 hover:bg-red-700 hover:text-neutral-100 @if(request()->is('dashboard')) @endif">
            <i class="fa-solid fa-money-check-dollar my-1 mx-2 text-xl text-center"></i>Pembelian Barang
        </a>

        <a href="#" class=" text-sm font-medium text-gray-200 block my-1 py-2.5 px-4 rounded transition duration-200 hover:bg-red-700 hover:text-neutral-100 @if(request()->is('dashboard')) @endif">
            <i class="fa-solid fa-file-invoice-dollar my-1 mx-2 text-xl text-center"></i>Surat Jalan
        </a>

        <h3 class=" uppercase tracking-wider text-sm mt-6 mb-2">Manajerial</h3>

        <a href="#" class=" text-sm font-medium text-gray-200 block my-1 py-2.5 px-4 rounded transition duration-200 hover:bg-red-700 hover:text-neutral-100 @if(request()->is('dashboard')) @endif">
            <i class="fa-solid fa-file my-1 mx-2 text-xl text-center"></i>Laporan
        </a>

        <h3 class=" uppercase tracking-wider text-sm mt-6 mb-2">Settings</h3>
        <a href="#" class="block py-2.5 px-4 rounded transition duration-200 hover:bg-gray-700 text-gray-100">
            Users & Roles
        </a>
        
    </nav>
</div>