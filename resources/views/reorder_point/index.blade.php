@extends('layouts.app')

@section('page-title', 'Peringatan Pemesanan Ulang')

@section('content')
    <div class="space-y-6" x-data="reorderPointApp()">

        {{-- ========================================= --}}
        {{-- HEADER --}}
        {{-- ========================================= --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fa-solid fa-bell text-red-700 mr-2"></i>Peringatan Pemesanan Ulang
                </h1>
                <p class="text-sm text-gray-500 mt-1">Pantau kapan barang perlu dipesan kembali agar stok tidak habis.</p>
            </div>
            <div
                class="flex items-center gap-2 text-sm text-gray-500 bg-white border border-gray-200 rounded-lg px-3 py-2 shadow-sm">
                <i class="fa-regular fa-calendar"></i>
                <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- STAT CARDS --}}
        {{-- ========================================= --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            {{-- Total Barang --}}
            <div
                class="relative overflow-hidden bg-gradient-to-br from-slate-600 to-slate-800 text-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 group">
                <div
                    class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-white/80">Total Dipantau</p>
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20">
                            <i class="fa-solid fa-boxes-stacked text-lg"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight" x-text="items.length"></p>
                    <p class="text-xs text-white/60 mt-1">Produk</p>
                </div>
            </div>

            {{-- Perlu Pesan --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-red-500 to-red-700 text-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 group cursor-pointer"
                @click="activeTab = 'danger'">
                <div
                    class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-white/80">Segera Pesan</p>
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 animate-pulse">
                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight" x-text="needReorderCount"></p>
                    <p class="text-xs text-white/60 mt-1">Stok di bawah batas</p>
                </div>
            </div>

            {{-- Stok Aman --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-emerald-500 to-emerald-700 text-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 group cursor-pointer"
                @click="activeTab = 'safe'">
                <div
                    class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-white/80">Stok Aman</p>
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20">
                            <i class="fa-solid fa-circle-check text-lg"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight" x-text="safeCount"></p>
                    <p class="text-xs text-white/60 mt-1">Stok mencukupi</p>
                </div>
            </div>

            {{-- Belum Ada Data --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-gray-400 to-gray-600 text-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 group cursor-pointer"
                @click="activeTab = 'nodata'">
                <div
                    class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-white/80">Belum Ada Data</p>
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20">
                            <i class="fa-solid fa-circle-question text-lg"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight" x-text="noDataCount"></p>
                    <p class="text-xs text-white/60 mt-1">Belum pernah PO</p>
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
                    <div class="bg-gradient-to-r from-red-800 to-red-900 rounded-xl p-5 text-white">
                        <div class="space-y-1.5 text-sm text-red-100">
                            <p>Semua data dihitung <strong class="text-white">otomatis dari sistem:</strong></p>
                            <ul class="list-disc list-inside space-y-0.5 ml-1">
                                <li><strong class="text-white">Pemakaian/hari</strong> — dari data barang keluar 4 hari
                                    terakhir</li>
                                <li><strong class="text-white">Waktu tunggu pengiriman</strong> — dari riwayat Purchase
                                    Order yang sudah diterima</li>
                                <li><strong class="text-white">Stok Pengaman & Batas Minimum</strong> — dihitung otomatis
                                    berdasarkan rumus</li>
                            </ul>
                        </div>
                        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div class="bg-white/10 rounded-xl px-4 py-3">
                                <p class="text-[10px] uppercase tracking-wider text-red-300 font-semibold mb-1">Safety
                                    Stock</p>
                                <p class="text-sm text-white font-mono">SS = D<sub>avg</sub> × (L<sub>max</sub> −
                                    L<sub>avg</sub>)</p>
                            </div>
                            <div class="bg-white/10 rounded-xl px-4 py-3">
                                <p class="text-[10px] uppercase tracking-wider text-red-300 font-semibold mb-1">Reorder
                                    Point</p>
                                <p class="text-sm text-white font-mono">ROP = (D<sub>avg</sub> × L<sub>avg</sub>) + SS</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- FILTER & TAB BAR --}}
        {{-- ========================================= --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex flex-col gap-4">
                {{-- Status Tabs --}}
                <div class="flex flex-wrap gap-2">
                    <button @click="activeTab = 'all'"
                        :class="activeTab === 'all' ? 'bg-red-700 text-white border-red-700 shadow-md' :
                                'bg-gray-50 text-gray-600 border-gray-200 hover:bg-red-50 hover:text-red-700 hover:border-red-200'"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border">
                        <i class="fa-solid fa-border-all text-xs"></i> Semua
                        <span :class="activeTab === 'all' ? 'bg-white/20 text-white' :
                                    'bg-gray-200 text-gray-500'" class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                            x-text="items.length"></span>
                    </button>
                    <button @click="activeTab = 'danger'"
                        :class="activeTab === 'danger' ? 'bg-red-700 text-white border-red-700 shadow-md' :
                                'bg-gray-50 text-gray-600 border-gray-200 hover:bg-red-50 hover:text-red-700 hover:border-red-200'"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border">
                        <i class="fa-solid fa-triangle-exclamation text-xs"></i> Segera Pesan
                        <span :class="activeTab === 'danger' ? 'bg-white/20 text-white' :
                                    'bg-red-100 text-red-600'" class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                            x-text="needReorderCount"></span>
                    </button>
                    <button @click="activeTab = 'safe'"
                        :class="activeTab === 'safe' ? 'bg-red-700 text-white border-red-700 shadow-md' :
                                'bg-gray-50 text-gray-600 border-gray-200 hover:bg-red-50 hover:text-red-700 hover:border-red-200'"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border">
                        <i class="fa-solid fa-circle-check text-xs"></i> Stok Aman
                        <span :class="activeTab === 'safe' ? 'bg-white/20 text-white' :
                                    'bg-emerald-100 text-emerald-600'"
                            class="px-2 py-0.5 rounded-full text-[10px] font-bold" x-text="safeCount"></span>
                    </button>
                    <button @click="activeTab = 'nodata'"
                        :class="activeTab === 'nodata' ? 'bg-red-700 text-white border-red-700 shadow-md' :
                                'bg-gray-50 text-gray-600 border-gray-200 hover:bg-red-50 hover:text-red-700 hover:border-red-200'"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border">
                        <i class="fa-solid fa-circle-question text-xs"></i> Belum Ada Data
                        <span :class="activeTab === 'nodata' ? 'bg-white/20 text-white' :
                                    'bg-gray-200 text-gray-500'" class="px-2 py-0.5 rounded-full text-[10px] font-bold"
                            x-text="noDataCount"></span>
                    </button>
                </div>

                {{-- Search & Kategori --}}
                <div class="flex flex-col md:flex-row gap-3 items-end border-t border-gray-100 pt-4">
                    <div class="flex-1 w-full">
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Cari</label>
                        <form method="GET" action="{{ route('reorder_point.index') }}" class="relative">
                            <input type="hidden" name="kategori" value="{{ $kategori ?? '' }}">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                            </div>
                            <input type="text" name="search" value="{{ $search ?? '' }}"
                                placeholder="Ketik kode / nama barang..."
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm bg-gray-50/50 hover:bg-white">
                        </form>
                    </div>
                    <div class="w-full md:w-52">
                        <label
                            class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Kategori</label>
                        <form method="GET" action="{{ route('reorder_point.index') }}">
                            <input type="hidden" name="search" value="{{ $search ?? '' }}">
                            <select name="kategori" onchange="this.form.submit()"
                                class="w-full py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm bg-gray-50/50 hover:bg-white appearance-none cursor-pointer">
                                <option value="">Semua Kategori</option>
                                @foreach ($kategoris as $kat)
                                    <option value="{{ $kat->kategori_barang_id }}" {{ $kat->kategori_barang_id == ($kategori ?? null) ? 'selected' : '' }}>
                                        {{ $kat->nama_kategori_barang }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- TABLE --}}
        {{-- ========================================= --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-5 bg-red-600 rounded-full"></div>
                    <h2 class="font-semibold text-gray-800">Daftar Analisis Barang</h2>
                    <span class="px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-full"
                        x-text="filteredItems.length + ' data'"></span>
                </div>
                <div class="text-xs text-gray-400 flex items-center gap-1">
                    <i class="fa-solid fa-robot"></i>
                    Dihitung otomatis dari sistem
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Produk</th>
                            <th
                                class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Stok vs ROP
                            </th>
                            <th
                                class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Pemakaian
                                <span class="block text-[10px] text-gray-400 font-normal normal-case">/hari
                                    (krt)</span>
                            </th>
                            <th
                                class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Waktu Tunggu
                                <span class="block text-[10px] text-gray-400 font-normal normal-case">rata / max
                                    (hari)</span>
                            </th>
                            <th
                                class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Safety Stock
                                <span class="block text-[10px] text-gray-400 font-normal normal-case">(karton)</span>
                            </th>
                            <th
                                class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                ROP
                                <span class="block text-[10px] text-gray-400 font-normal normal-case">(karton)</span>
                            </th>
                            <th
                                class="px-4 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(item, idx) in filteredItems" :key="item.kode_barang">
                            <tr class="transition-colors duration-150 group" :class="{
                                        'bg-red-50/60 hover:bg-red-100/50': calcRop(item) > 0 && item.stok_karton <= calcRop(item),
                                        'hover:bg-blue-50/30': !(calcRop(item) > 0 && item.stok_karton <= calcRop(item))
                                    }">

                                {{-- Produk --}}
                                <td class="px-4 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                                            :class="calcRop(item) > 0 && item.stok_karton <= calcRop(item) ?
                                                    'bg-red-100 text-red-600' : (item.jumlah_po === 0 ?
                                                        'bg-gray-100 text-gray-400' : 'bg-emerald-100 text-emerald-600')">
                                            <i class="fa-solid text-sm" :class="calcRop(item) > 0 && item.stok_karton <= calcRop(item) ?
                                                        'fa-box-open' : (item.jumlah_po === 0 ? 'fa-box' : 'fa-cube')"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-gray-900 text-sm truncate max-w-[180px]"
                                                x-text="item.nama_barang"></p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="font-mono text-[10px] text-gray-400"
                                                    x-text="item.kode_barang"></span>
                                                <span class="text-gray-200">·</span>
                                                <span class="text-[10px] text-gray-400" x-text="item.kategori"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Visual Stok vs ROP --}}
                                <td class="px-4 py-3.5">
                                    <div class="min-w-[160px]">
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-xs font-bold" :class="calcRop(item) > 0 && item.stok_karton <= calcRop(item)
                                                        ? 'text-red-700' : 'text-gray-700'"
                                                x-text="formatNumber(item.stok_karton) + ' krt'"></span>
                                            <span class="text-[10px] text-gray-400"
                                                x-text="'ROP: ' + formatNumber(calcRop(item))"></span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden relative">
                                            {{-- ROP marker --}}
                                            <template x-if="calcRop(item) > 0">
                                                <div class="absolute top-0 bottom-0 w-0.5 bg-red-900/60 z-10" :style="'left: ' + Math.min(calcRop(item) / Math.max(item.stok_karton,
                                                            calcRop(item)) * 100, 95) + '%'">
                                                </div>
                                            </template>
                                            <div class="h-2 rounded-full transition-all duration-700 ease-out"
                                                :class="calcRop(item) > 0 && item.stok_karton <= calcRop(item)
                                                        ? 'bg-red-500' : (item.jumlah_po === 0 ? 'bg-gray-300' : 'bg-emerald-500')" :style="'width: ' + (calcRop(item) > 0 ? Math.min(item.stok_karton /
                                                        Math.max(calcRop(item), 1) * 100, 100) : (item.stok_karton >
                                                        0 ? 100 : 0)) + '%'">
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Pemakaian/Hari --}}
                                <td class="px-4 py-3.5 text-center">
                                    <span class="text-sm text-gray-700 font-medium"
                                        x-text="formatNumber(item.d_avg, 4)"></span>
                                </td>

                                {{-- Lead Time --}}
                                <td class="px-4 py-3.5 text-center">
                                    <template x-if="item.jumlah_po > 0">
                                        <div class="inline-flex items-center gap-1.5">
                                            <span
                                                class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs font-bold rounded-md border border-blue-100"
                                                x-text="item.l_avg + 'h'"></span>
                                            <span class="text-[10px] text-gray-300">/</span>
                                            <span
                                                class="px-2 py-0.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-md border border-amber-100"
                                                x-text="item.l_max + 'h'"></span>
                                        </div>
                                    </template>
                                    <template x-if="item.jumlah_po === 0">
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-50 text-gray-400 text-[10px] font-medium rounded-md border border-gray-200">
                                            <i class="fa-solid fa-minus text-[8px]"></i> Belum ada
                                        </span>
                                    </template>
                                </td>

                                {{-- Safety Stock --}}
                                <td class="px-4 py-3.5 text-center">
                                    <span class="font-semibold text-gray-700 text-sm"
                                        x-text="formatNumber(calcSafetyStock(item), 2)"></span>
                                </td>

                                {{-- ROP --}}
                                <td class="px-4 py-3.5 text-center">
                                    <span class="font-bold text-base"
                                        :class="calcRop(item) > 0 ? 'text-gray-900' : 'text-gray-400'"
                                        x-text="formatNumber(calcRop(item), 2)"></span>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3.5 text-center">
                                    <template x-if="calcRop(item) > 0 && item.stok_karton <= calcRop(item)">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 text-red-700 font-bold text-xs rounded-xl border border-red-200 shadow-sm">
                                            <span class="relative flex h-2 w-2">
                                                <span
                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                            </span>
                                            Segera Pesan
                                        </span>
                                    </template>
                                    <template x-if="item.jumlah_po === 0">
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-50 text-gray-500 font-medium text-xs rounded-xl border border-gray-200">
                                            <i class="fa-solid fa-circle-question text-[10px]"></i>
                                            Belum Ada Data
                                        </span>
                                    </template>
                                    <template
                                        x-if="item.jumlah_po > 0 && (calcRop(item) === 0 || item.stok_karton > calcRop(item))">
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-50 text-emerald-700 font-bold text-xs rounded-xl border border-emerald-200">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                                            Stok Aman
                                        </span>
                                    </template>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State --}}
                        <template x-if="filteredItems.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fa-solid fa-box-open text-2xl text-gray-400"></i>
                                        </div>
                                        <p class="text-sm text-gray-500 font-medium">Tidak ada data barang ditemukan.</p>
                                        <p class="text-xs text-gray-400 mt-1">Coba ubah tab filter atau pencarian Anda.
                                        </p>
                                        <button @click="activeTab = 'all'"
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
            <div class="mt-4 flex flex-wrap gap-5 text-xs text-gray-500 border-t border-gray-100 pt-4">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                    </span>
                    <span><strong>Segera Pesan</strong> — stok ≤ ROP</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    <span><strong>Stok Aman</strong> — stok > ROP</span>
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
    <script>
        function reorderPointApp() {
            return {
                items: @json($items),
                activeTab: 'all',

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
                    return 'safe';
                },

                get filteredItems() {
                    if (this.activeTab === 'all') return this.items;
                    return this.items.filter(item => this.getStatus(item) === this.activeTab);
                },

                get needReorderCount() {
                    return this.items.filter(item => this.getStatus(item) === 'danger').length;
                },

                get safeCount() {
                    return this.items.filter(item => this.getStatus(item) === 'safe').length;
                },

                get noDataCount() {
                    return this.items.filter(item => this.getStatus(item) === 'nodata').length;
                },

                formatNumber(val, decimals = 2) {
                    if (val === null || val === undefined) return '0';
                    return parseFloat(val).toLocaleString('id-ID', {
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals
                    });
                }
            };
        }
    </script>
@endpush