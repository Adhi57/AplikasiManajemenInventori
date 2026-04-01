@extends('layouts.app')

@section('page-title', 'Dashboard Reorder Point')

@section('content')
    <div class="space-y-6" x-data="reorderDashboard()">

        {{-- ========================================= --}}
        {{-- HERO HEADER --}}
        {{-- ========================================= --}}
        <div class="bg-gradient-to-br from-red-950 via-red-900 to-red-950 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-72 h-72 bg-red-500/10 rounded-full blur-3xl -mr-24 -mt-24"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl -ml-16 -mb-16"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-11 h-11 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <i class="fa-solid fa-bell text-xl text-amber-400"></i>
                        </div>
                        <div>
                            <p class="text-sm text-red-200/80 font-medium">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                            <h1 class="text-2xl font-bold">Dashboard Reorder Point</h1>
                        </div>
                    </div>
                    <p class="text-sm text-red-200/60 max-w-lg mt-1">Pantau kapan barang perlu dipesan kembali agar stok gudang tidak habis. Sistem menghitung otomatis berdasarkan pemakaian dan waktu tunggu.</p>
                </div>

                {{-- Quick Actions --}}
                <div class="flex flex-wrap gap-2 lg:justify-end">
                    <a href="{{ route('purchase_orders.buat_permintaan') }}"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-red-950 shadow-md transition-all text-sm font-bold hover:scale-105 duration-200">
                        <i class="fa-solid fa-file-circle-plus"></i>
                        <span>Buat PO Baru</span>
                    </a>
                    <a href="{{ route('stok.index') }}"
                        class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                        <i class="fa-solid fa-warehouse text-amber-400"></i>
                        <span class="hidden sm:inline">Cek Stok</span>
                    </a>
                    <a href="/tracking-kadaluarsa"
                        class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                        <i class="fa-solid fa-clock text-amber-400"></i>
                        <span class="hidden sm:inline">Kadaluarsa</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- KPI SUMMARY CARDS --}}
        {{-- ========================================= --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Total Dipantau --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all group cursor-pointer"
                 @click="activeTab = 'all'">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-slate-100 text-slate-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-boxes-stacked text-lg"></i>
                    </div>
                    <div class="flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">
                        <i class="fa-solid fa-layer-group text-[8px]"></i> ALL
                    </div>
                </div>
                <p class="text-xs font-semibold text-gray-500 mb-1 tracking-wide">Total Dipantau</p>
                <p class="text-2xl font-black text-gray-900" x-text="items.length"></p>
                <p class="text-[10px] text-gray-400 mt-0.5">Produk terdaftar</p>
            </div>

            {{-- Segera Pesan (DANGER) --}}
            <div class="bg-white rounded-2xl border border-red-100 p-5 shadow-sm hover:shadow-md transition-all group cursor-pointer relative overflow-hidden"
                 @click="activeTab = 'danger'">
                <div class="absolute top-0 right-0 w-20 h-20 bg-red-500/5 rounded-full -mr-6 -mt-6"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-red-50 text-red-600 group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        </div>
                        <template x-if="needReorderCount > 0">
                            <span class="flex items-center gap-1 text-[10px] font-bold text-red-700 bg-red-100 px-2 py-1 rounded-lg animate-pulse">
                                <i class="fa-solid fa-bell text-[8px]"></i> URGENT
                            </span>
                        </template>
                    </div>
                    <p class="text-xs font-semibold text-gray-500 mb-1 tracking-wide">Segera Pesan</p>
                    <p class="text-2xl font-black text-red-600" x-text="needReorderCount"></p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Stok ≤ Reorder Point</p>
                </div>
            </div>

            {{-- Mendekati ROP (WARNING) --}}
            <div class="bg-white rounded-2xl border border-amber-100 p-5 shadow-sm hover:shadow-md transition-all group cursor-pointer relative overflow-hidden"
                 @click="activeTab = 'warning'">
                <div class="absolute top-0 right-0 w-20 h-20 bg-amber-500/5 rounded-full -mr-6 -mt-6"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-gauge-high text-lg"></i>
                        </div>
                        <template x-if="warningCount > 0">
                            <span class="flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-1 rounded-lg">
                                <i class="fa-solid fa-eye text-[8px]"></i> PANTAU
                            </span>
                        </template>
                    </div>
                    <p class="text-xs font-semibold text-gray-500 mb-1 tracking-wide">Mendekati ROP</p>
                    <p class="text-2xl font-black text-amber-600" x-text="warningCount"></p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Stok mendekati batas</p>
                </div>
            </div>

            {{-- Stok Aman --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all group cursor-pointer"
                 @click="activeTab = 'safe'">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-shield-check text-lg"></i>
                    </div>
                    <div class="flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">
                        <i class="fa-solid fa-circle text-[5px]"></i> AMAN
                    </div>
                </div>
                <p class="text-xs font-semibold text-gray-500 mb-1 tracking-wide">Stok Aman</p>
                <p class="text-2xl font-black text-emerald-600" x-text="safeCount"></p>
                <p class="text-[10px] text-gray-400 mt-0.5">Stok di atas ROP</p>
            </div>

            {{-- Belum Ada Data --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all group cursor-pointer"
                 @click="activeTab = 'nodata'">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-gray-100 text-gray-400 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-circle-question text-lg"></i>
                    </div>
                </div>
                <p class="text-xs font-semibold text-gray-500 mb-1 tracking-wide">Belum Ada Data</p>
                <p class="text-2xl font-black text-gray-400" x-text="noDataCount"></p>
                <p class="text-[10px] text-gray-400 mt-0.5">Belum pernah PO</p>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- CHARTS ROW + ALERT PANEL --}}
        {{-- ========================================= --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Alert Panel: Segera Pesan --}}
            <div class="xl:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-red-50 to-amber-50 px-5 py-4 border-b border-red-100/50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-6 bg-red-600 rounded-full"></div>
                        <h2 class="font-bold text-gray-800 text-sm">Alert Restok</h2>
                    </div>
                    <span class="bg-red-600 text-white text-[9px] px-2.5 py-1 rounded-full font-bold" x-text="needReorderCount + ' item'"></span>
                </div>

                <div class="p-5 space-y-2.5 max-h-[380px] overflow-y-auto custom-scrollbar">
                    <template x-for="item in dangerItems.slice(0, 8)" :key="'alert-' + item.kode_barang">
                        <div class="flex items-center justify-between p-3 rounded-xl border border-red-100 bg-red-50/50 hover:bg-red-50 hover:shadow-sm transition-all group">
                            <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                <template x-if="item.foto_produk">
                                    <img :src="'/storage/' + item.foto_produk" class="w-9 h-9 rounded-lg border border-red-200 object-cover flex-shrink-0">
                                </template>
                                <template x-if="!item.foto_produk">
                                    <div class="w-9 h-9 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-box-open text-red-400 text-xs"></i>
                                    </div>
                                </template>
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-gray-800 truncate" x-text="item.nama_barang"></p>
                                    <p class="text-[9px] text-gray-400">Stok: <span class="font-bold text-red-600" x-text="formatNumber(item.stok_karton)"></span> / ROP: <span class="font-bold" x-text="formatNumber(calcRop(item))"></span></p>
                                </div>
                            </div>
                            <a :href="'{{ route('purchase_orders.buat_permintaan') }}'"
                                class="flex-shrink-0 px-2.5 py-1.5 bg-red-600 text-white text-[9px] font-bold rounded-lg hover:bg-red-700 transition-colors opacity-0 group-hover:opacity-100 whitespace-nowrap">
                                <i class="fa-solid fa-cart-plus mr-0.5"></i> Pesan
                            </a>
                        </div>
                    </template>
                    <template x-if="needReorderCount === 0">
                        <div class="border border-dashed border-emerald-200 bg-emerald-50/50 rounded-xl p-6 flex flex-col items-center justify-center text-center gap-2">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                                <i class="fa-solid fa-check text-emerald-600 text-xl"></i>
                            </div>
                            <span class="text-xs text-emerald-600 font-bold">SEMUA STOK AMAN</span>
                            <span class="text-[10px] text-gray-400">Tidak ada item perlu restok saat ini</span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Tren Pemakaian Chart --}}
            <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                            <i class="fa-solid fa-chart-line text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm">Tren Pergerakan Barang</h3>
                            <p class="text-[10px] text-gray-400">Masuk vs Keluar — 6 bulan terakhir</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-2 rounded-sm bg-emerald-500"></span> Masuk
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-3 h-2 rounded-sm bg-red-400"></span> Keluar
                        </div>
                    </div>
                </div>
                <div class="h-56">
                    <canvas id="globalTrendChart"></canvas>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- CARA KERJA (Collapsible) --}}
        {{-- ========================================= --}}
        <div x-data="{ open: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <button @click="open = !open"
                class="w-full flex items-center justify-between p-5 hover:bg-gray-50/50 transition-colors">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-circle-info text-red-600"></i>
                    </div>
                    <div class="text-left">
                        <h3 class="font-semibold text-gray-800 text-sm">Cara Kerja Reorder Point</h3>
                        <p class="text-xs text-gray-400">Klik untuk melihat penjelasan rumus perhitungan</p>
                    </div>
                </div>
                <i class="fa-solid fa-chevron-down text-gray-400 text-sm transition-transform duration-300"
                    :class="open ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="open" x-collapse>
                <div class="px-5 pb-5 border-t border-gray-100 pt-4">
                    <div class="bg-gradient-to-r from-red-900 to-red-950 rounded-xl p-5 text-white">
                        <div class="space-y-1.5 text-sm text-red-100">
                            <p>Semua data dihitung <strong class="text-white">otomatis dari sistem:</strong></p>
                            <ul class="list-disc list-inside space-y-0.5 ml-1">
                                <li><strong class="text-white">Pemakaian/hari</strong> — dari data barang keluar 4 hari terakhir</li>
                                <li><strong class="text-white">Waktu tunggu pengiriman</strong> — dari riwayat Purchase Order yang sudah diterima</li>
                                <li><strong class="text-white">Stok Pengaman & Batas Minimum</strong> — dihitung otomatis berdasarkan rumus</li>
                            </ul>
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="bg-white/10 rounded-xl px-4 py-3 backdrop-blur-sm">
                                <p class="text-[10px] uppercase tracking-wider text-red-300 font-semibold mb-1">Safety Stock</p>
                                <p class="text-sm text-white font-mono">SS = D<sub>avg</sub> × (L<sub>max</sub> − L<sub>avg</sub>)</p>
                            </div>
                            <div class="bg-white/10 rounded-xl px-4 py-3 backdrop-blur-sm">
                                <p class="text-[10px] uppercase tracking-wider text-red-300 font-semibold mb-1">Reorder Point</p>
                                <p class="text-sm text-white font-mono">ROP = (D<sub>avg</sub> × L<sub>avg</sub>) + SS</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- FILTER & SEARCH BAR --}}
        {{-- ========================================= --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex flex-col lg:flex-row gap-4 items-end">
                {{-- Search --}}
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Pencarian</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" x-model="searchQuery" @input.debounce.300ms=""
                            placeholder="Cari kode / nama barang..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                    </div>
                </div>

                {{-- Status Filter Pills --}}
                <div class="flex gap-1.5 flex-wrap">
                    <button @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-red-800 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200">
                        <i class="fa-solid fa-border-all mr-1 text-[10px]"></i>Semua
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[9px]" :class="activeTab === 'all' ? 'bg-white/20' : 'bg-gray-200'" x-text="items.length"></span>
                    </button>
                    <button @click="activeTab = 'danger'" :class="activeTab === 'danger' ? 'bg-red-600 text-white shadow-sm' : 'bg-red-50 text-red-700 hover:bg-red-100'"
                        class="px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200">
                        <i class="fa-solid fa-triangle-exclamation mr-1 text-[10px]"></i>Segera Pesan
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[9px]" :class="activeTab === 'danger' ? 'bg-white/20' : 'bg-red-100'" x-text="needReorderCount"></span>
                    </button>
                    <button @click="activeTab = 'warning'" :class="activeTab === 'warning' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'"
                        class="px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200">
                        <i class="fa-solid fa-gauge-high mr-1 text-[10px]"></i>Mendekati
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[9px]" :class="activeTab === 'warning' ? 'bg-white/20' : 'bg-amber-100'" x-text="warningCount"></span>
                    </button>
                    <button @click="activeTab = 'safe'" :class="activeTab === 'safe' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'"
                        class="px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200">
                        <i class="fa-solid fa-circle-check mr-1 text-[10px]"></i>Aman
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[9px]" :class="activeTab === 'safe' ? 'bg-white/20' : 'bg-emerald-100'" x-text="safeCount"></span>
                    </button>
                    <button @click="activeTab = 'nodata'" :class="activeTab === 'nodata' ? 'bg-gray-600 text-white shadow-sm' : 'bg-gray-100 text-gray-500 hover:bg-gray-200'"
                        class="px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200">
                        <i class="fa-solid fa-circle-question mr-1 text-[10px]"></i>No Data
                        <span class="ml-1 px-1.5 py-0.5 rounded-full text-[9px]" :class="activeTab === 'nodata' ? 'bg-white/20' : 'bg-gray-200'" x-text="noDataCount"></span>
                    </button>
                </div>

                {{-- Kategori (server-side) --}}
                <form method="GET" action="{{ route('reorder_point.index') }}" class="flex gap-2 items-end">
                    <input type="hidden" name="search" value="{{ $search ?? '' }}">
                    <div class="w-44">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Kategori</label>
                        <select name="kategori" onchange="this.form.submit()"
                            class="w-full py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm bg-white">
                            <option value="">Semua</option>
                            @foreach ($kategoris as $kat)
                                <option value="{{ $kat->kategori_barang_id }}" {{ $kat->kategori_barang_id == ($kategori ?? null) ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            {{-- Active filter indicator --}}
            <div class="mt-3 flex items-center gap-2 text-xs text-gray-400" x-show="activeTab !== 'all' || searchQuery" x-transition>
                <i class="fa-solid fa-filter text-[10px]"></i>
                <span>Filter aktif:</span>
                <span x-show="searchQuery" class="bg-slate-100 text-slate-600 font-bold px-2 py-0.5 rounded-md">"<span x-text="searchQuery"></span>"</span>
                <span x-show="activeTab !== 'all'" class="font-bold px-2 py-0.5 rounded-md"
                    :class="{
                        'bg-red-100 text-red-700': activeTab === 'danger',
                        'bg-amber-100 text-amber-700': activeTab === 'warning',
                        'bg-emerald-100 text-emerald-700': activeTab === 'safe',
                        'bg-gray-100 text-gray-600': activeTab === 'nodata'
                    }" x-text="activeTab === 'danger' ? 'Segera Pesan' : (activeTab === 'warning' ? 'Mendekati ROP' : (activeTab === 'safe' ? 'Stok Aman' : 'Belum Ada Data'))"></span>
                <button @click="activeTab = 'all'; searchQuery = ''" class="text-gray-400 hover:text-red-500 transition ml-1">
                    <i class="fa-solid fa-times-circle"></i> Reset
                </button>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- INTERACTIVE TABLE --}}
        {{-- ========================================= --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-5 bg-red-700 rounded-full"></div>
                    <h2 class="font-bold text-gray-800">Analisis Reorder Point</h2>
                    <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md" x-text="displayedItems.length + ' item ditampilkan'"></span>
                </div>
                <div class="flex items-center gap-3 text-xs text-gray-400">
                    <span class="flex items-center gap-1"><i class="fa-solid fa-robot text-[10px]"></i> Dihitung otomatis</span>
                    <span class="flex items-center gap-1"><i class="fa-solid fa-arrow-down-up text-[10px]"></i> Klik header untuk sorting</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50/80">
                            <th @click="sortBy('nama_barang')" class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition select-none">
                                <div class="flex items-center gap-1.5">
                                    Produk
                                    <i class="fa-solid fa-sort text-gray-300" :class="{'text-red-600': sortColumn === 'nama_barang'}"></i>
                                </div>
                            </th>
                            <th @click="sortBy('stok_karton')" class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition select-none">
                                <div class="flex items-center justify-center gap-1.5">
                                    Stok vs ROP
                                    <i class="fa-solid fa-sort text-gray-300" :class="{'text-red-600': sortColumn === 'stok_karton'}"></i>
                                </div>
                            </th>
                            <th @click="sortBy('d_avg')" class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition select-none whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    Pemakaian
                                    <i class="fa-solid fa-sort text-gray-300" :class="{'text-red-600': sortColumn === 'd_avg'}"></i>
                                </div>
                                <span class="block text-[9px] text-gray-400 font-normal normal-case">/hari (karton)</span>
                            </th>
                            <th class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Lead Time
                                <span class="block text-[9px] text-gray-400 font-normal normal-case">rata / max (hari)</span>
                            </th>
                            <th class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Safety Stock
                                <span class="block text-[9px] text-gray-400 font-normal normal-case">(karton)</span>
                            </th>
                            <th @click="sortBy('rop')" class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition select-none">
                                <div class="flex items-center justify-center gap-1.5">
                                    ROP
                                    <i class="fa-solid fa-sort text-gray-300" :class="{'text-red-600': sortColumn === 'rop'}"></i>
                                </div>
                                <span class="block text-[9px] text-gray-400 font-normal normal-case">(karton)</span>
                            </th>
                            <th class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                Tren Pemakaian
                            </th>
                            <th class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                Status & Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(item, idx) in displayedItems" :key="item.kode_barang">
                            <tr class="transition-colors duration-150 group" :class="{
                                        'bg-red-50/60 hover:bg-red-100/50': getStatus(item) === 'danger',
                                        'bg-amber-50/30 hover:bg-amber-100/30': getStatus(item) === 'warning',
                                        'hover:bg-blue-50/30': getStatus(item) === 'safe' || getStatus(item) === 'nodata'
                                    }">

                                {{-- Produk --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <template x-if="item.foto_produk">
                                            <img :src="'/storage/' + item.foto_produk" class="w-9 h-9 rounded-lg border border-gray-100 object-cover flex-shrink-0">
                                        </template>
                                        <template x-if="!item.foto_produk">
                                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                                :class="getStatus(item) === 'danger' ? 'bg-red-100 text-red-600' : (getStatus(item) === 'warning' ? 'bg-amber-100 text-amber-600' : (getStatus(item) === 'nodata' ? 'bg-gray-100 text-gray-400' : 'bg-emerald-100 text-emerald-600'))">
                                                <i class="fa-solid text-sm" :class="getStatus(item) === 'danger' ? 'fa-box-open' : (getStatus(item) === 'nodata' ? 'fa-box' : 'fa-cube')"></i>
                                            </div>
                                        </template>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm truncate max-w-[180px]" x-text="item.nama_barang"></p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="font-mono text-[10px] text-gray-400" x-text="item.kode_barang"></span>
                                                <span class="text-gray-200">·</span>
                                                <span class="text-[10px] text-gray-400" x-text="item.kategori"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Stok vs ROP Bar --}}
                                <td class="px-5 py-3.5">
                                    <div class="min-w-[160px]">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-bold" :class="getStatus(item) === 'danger' ? 'text-red-700' : (getStatus(item) === 'warning' ? 'text-amber-700' : 'text-gray-700')"
                                                x-text="formatNumber(item.stok_karton) + ' krt'"></span>
                                            <span class="text-[10px] text-gray-400" x-text="'ROP: ' + formatNumber(calcRop(item))"></span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden relative">
                                            <template x-if="calcRop(item) > 0">
                                                <div class="absolute top-0 bottom-0 w-0.5 bg-red-900/60 z-10" :style="'left: ' + Math.min(calcRop(item) / Math.max(item.stok_karton, calcRop(item)) * 100, 95) + '%'"></div>
                                            </template>
                                            <div class="h-2 rounded-full transition-all duration-700 ease-out"
                                                :class="getStatus(item) === 'danger' ? 'bg-red-500' : (getStatus(item) === 'warning' ? 'bg-amber-500' : (item.jumlah_po === 0 ? 'bg-gray-300' : 'bg-emerald-500'))"
                                                :style="'width: ' + (calcRop(item) > 0 ? Math.min(item.stok_karton / Math.max(calcRop(item), 1) * 100, 100) : (item.stok_karton > 0 ? 100 : 0)) + '%'">
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Pemakaian/Hari --}}
                                <td class="px-5 py-3.5 text-center">
                                    <span class="text-sm text-gray-700 font-medium" x-text="formatNumber(item.d_avg, 4)"></span>
                                </td>

                                {{-- Lead Time --}}
                                <td class="px-5 py-3.5 text-center">
                                    <template x-if="item.jumlah_po > 0">
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-bold rounded-md border border-blue-100" x-text="item.l_avg + 'h'"></span>
                                            <span class="text-[10px] text-gray-300">/</span>
                                            <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-md border border-amber-100" x-text="item.l_max + 'h'"></span>
                                        </div>
                                    </template>
                                    <template x-if="item.jumlah_po === 0">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-50 text-gray-400 text-[10px] font-medium rounded-md border border-gray-200">
                                            <i class="fa-solid fa-minus text-[8px]"></i> Belum ada
                                        </span>
                                    </template>
                                </td>

                                {{-- Safety Stock --}}
                                <td class="px-5 py-3.5 text-center">
                                    <span class="font-semibold text-gray-700 text-sm" x-text="formatNumber(calcSafetyStock(item), 2)"></span>
                                </td>

                                {{-- ROP --}}
                                <td class="px-5 py-3.5 text-center">
                                    <span class="font-bold text-base"
                                        :class="calcRop(item) > 0 ? 'text-gray-900' : 'text-gray-400'"
                                        x-text="formatNumber(calcRop(item), 2)"></span>
                                </td>

                                {{-- Tren Sparkline --}}
                                <td class="px-5 py-3.5 text-center">
                                    <template x-if="item.trend && item.trend.some(v => v > 0)">
                                        <div class="flex items-end gap-0.5 justify-center h-6">
                                            <template x-for="(val, i) in item.trend" :key="'spark-' + item.kode_barang + '-' + i">
                                                <div class="w-3 rounded-sm transition-all duration-300"
                                                    :class="getStatus(item) === 'danger' ? 'bg-red-400' : (getStatus(item) === 'warning' ? 'bg-amber-400' : 'bg-emerald-400')"
                                                    :style="'height: ' + (Math.max(val / (Math.max(...item.trend) || 1) * 24, 2)) + 'px'"
                                                    :title="val + ' unit'">
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                    <template x-if="!item.trend || !item.trend.some(v => v > 0)">
                                        <span class="text-[10px] text-gray-300">—</span>
                                    </template>
                                </td>

                                {{-- Status & Quick Actions --}}
                                <td class="px-5 py-3.5 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        {{-- Status Badge --}}
                                        <template x-if="getStatus(item) === 'danger'">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 text-red-700 font-bold text-[10px] rounded-xl border border-red-200 shadow-sm uppercase tracking-wide">
                                                <span class="relative flex h-2 w-2">
                                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                                </span>
                                                Segera Pesan
                                            </span>
                                        </template>
                                        <template x-if="getStatus(item) === 'warning'">
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-100 text-amber-700 font-bold text-[10px] rounded-xl border border-amber-200 uppercase tracking-wide">
                                                <i class="fa-solid fa-gauge-high text-[8px]"></i>
                                                Mendekati
                                            </span>
                                        </template>
                                        <template x-if="getStatus(item) === 'nodata'">
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-50 text-gray-500 font-medium text-[10px] rounded-xl border border-gray-200 uppercase tracking-wide">
                                                <i class="fa-solid fa-circle-question text-[8px]"></i>
                                                Belum Ada Data
                                            </span>
                                        </template>
                                        <template x-if="getStatus(item) === 'safe'">
                                            <span class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 text-emerald-700 font-bold text-[10px] rounded-xl border border-emerald-200 uppercase tracking-wide">
                                                <i class="fa-solid fa-circle-check text-[8px]"></i>
                                                Stok Aman
                                            </span>
                                        </template>

                                        {{-- Quick Action --}}
                                        <template x-if="getStatus(item) === 'danger'">
                                            <a :href="'{{ route('purchase_orders.buat_permintaan') }}'"
                                                class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-600 text-white text-[10px] font-bold rounded-lg hover:bg-red-700 transition-colors shadow-sm opacity-0 group-hover:opacity-100">
                                                <i class="fa-solid fa-cart-plus text-[8px]"></i> Buat PO
                                            </a>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State --}}
                        <template x-if="displayedItems.length === 0">
                            <tr>
                                <td colspan="8" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-4">
                                            <i class="fa-solid fa-filter-circle-xmark text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm text-gray-500 font-medium">Tidak ada data barang ditemukan</p>
                                        <p class="text-xs text-gray-400 mt-1">Coba ubah tab filter atau pencarian Anda</p>
                                        <button @click="activeTab = 'all'; searchQuery = ''"
                                            class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-red-700 text-white rounded-xl text-xs font-semibold hover:bg-red-800 transition-colors shadow-sm">
                                            <i class="fa-solid fa-rotate-left text-[10px]"></i> Tampilkan Semua
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Legend --}}
            <div class="px-6 py-4 flex flex-wrap gap-5 text-xs text-gray-500 border-t border-gray-100">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                    </span>
                    <span><strong>Segera Pesan</strong> — stok ≤ ROP</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                    <span><strong>Mendekati</strong> — stok ≤ ROP × 1.5</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    <span><strong>Stok Aman</strong> — stok > ROP × 1.5</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex rounded-full h-2.5 w-2.5 bg-gray-300"></span>
                    <span><strong>Belum Ada Data</strong> — belum ada riwayat PO</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 bg-red-900/50 rounded-sm"></div>
                    <span><strong>Garis</strong> — batas ROP pada progress bar</span>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function reorderDashboard() {
            return {
                items: @json($items),
                activeTab: 'all',
                searchQuery: '{{ $search ?? '' }}',
                sortColumn: '',
                sortDirection: 'asc',

                calcSafetyStock(item) {
                    const dAvg = item.d_avg || 0;
                    const lMax = item.l_max || 0;
                    const lAvg = item.l_avg || 0;
                    return dAvg * (lMax - lAvg);
                },

                calcRop(item) {
                    const dAvg = item.d_avg || 0;
                    const lAvg = item.l_avg || 0;
                    const ss = this.calcSafetyStock(item);
                    return (dAvg * lAvg) + ss;
                },

                getStatus(item) {
                    const rop = this.calcRop(item);
                    if (item.jumlah_po === 0) return 'nodata';
                    if (rop > 0 && item.stok_karton <= rop) return 'danger';
                    if (rop > 0 && item.stok_karton <= rop * 1.5) return 'warning';
                    return 'safe';
                },

                get filteredByTab() {
                    if (this.activeTab === 'all') return this.items;
                    return this.items.filter(item => this.getStatus(item) === this.activeTab);
                },

                get filteredBySearch() {
                    if (!this.searchQuery) return this.filteredByTab;
                    const q = this.searchQuery.toLowerCase();
                    return this.filteredByTab.filter(item =>
                        item.kode_barang.toLowerCase().includes(q) ||
                        item.nama_barang.toLowerCase().includes(q) ||
                        (item.kategori && item.kategori.toLowerCase().includes(q))
                    );
                },

                get displayedItems() {
                    let result = [...this.filteredBySearch];
                    if (this.sortColumn) {
                        const dir = this.sortDirection === 'asc' ? 1 : -1;
                        result.sort((a, b) => {
                            let valA, valB;
                            if (this.sortColumn === 'rop') {
                                valA = this.calcRop(a);
                                valB = this.calcRop(b);
                            } else if (this.sortColumn === 'nama_barang') {
                                return dir * (a.nama_barang || '').localeCompare(b.nama_barang || '');
                            } else {
                                valA = a[this.sortColumn] || 0;
                                valB = b[this.sortColumn] || 0;
                            }
                            return dir * (valA - valB);
                        });
                    }
                    return result;
                },

                get dangerItems() {
                    return this.items.filter(item => this.getStatus(item) === 'danger');
                },

                get needReorderCount() {
                    return this.items.filter(item => this.getStatus(item) === 'danger').length;
                },

                get warningCount() {
                    return this.items.filter(item => this.getStatus(item) === 'warning').length;
                },

                get safeCount() {
                    return this.items.filter(item => this.getStatus(item) === 'safe').length;
                },

                get noDataCount() {
                    return this.items.filter(item => this.getStatus(item) === 'nodata').length;
                },

                sortBy(column) {
                    if (this.sortColumn === column) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortColumn = column;
                        this.sortDirection = 'asc';
                    }
                },

                formatNumber(val, decimals = 2) {
                    if (val === null || val === undefined) return '0';
                    return parseFloat(val).toLocaleString('id-ID', {
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals
                    });
                },

                init() {
                    this.$nextTick(() => {
                        this.initGlobalChart();
                    });
                },

                initGlobalChart() {
                    const el = document.getElementById('globalTrendChart');
                    if (!el) return;
                    new Chart(el, {
                        type: 'line',
                        data: {
                            labels: @json($trendLabelNames),
                            datasets: [
                                {
                                    label: 'Masuk',
                                    data: @json($globalMasukData),
                                    borderColor: '#10b981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.08)',
                                    borderWidth: 2.5,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#10b981',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                },
                                {
                                    label: 'Keluar',
                                    data: @json($globalKeluarData),
                                    borderColor: '#f87171',
                                    backgroundColor: 'rgba(248, 113, 113, 0.08)',
                                    borderWidth: 2.5,
                                    pointRadius: 4,
                                    pointBackgroundColor: '#f87171',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false },
                                tooltip: {
                                    cornerRadius: 8, padding: 10,
                                    bodyFont: { family: 'Poppins', size: 11 },
                                    mode: 'index', intersect: false
                                }
                            },
                            scales: {
                                x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 10 }, color: '#94a3b8' } },
                                y: { grid: { color: '#f1f5f9', borderDash: [2, 4] }, ticks: { font: { family: 'Poppins', size: 10 }, color: '#94a3b8' }, border: { display: false } }
                            },
                            interaction: { mode: 'nearest', axis: 'x', intersect: false }
                        }
                    });
                }
            };
        }
    </script>
@endpush

{{-- CUSTOM STYLES --}}
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #d1d5db; }
</style>