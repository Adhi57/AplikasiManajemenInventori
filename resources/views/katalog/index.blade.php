@extends('layouts.app')

@section('page-title', 'Katalog Barang')

@section('content')
<div class="space-y-6" x-data="{ viewMode: 'grid' }">

    {{-- ========================================= --}}
    {{-- HEADER --}}
    {{-- ========================================= --}}
    <x-page-header title="Katalog Barang Gudang" description="Lihat daftar produk, ketersediaan stok, harga jual, dan informasi lainnya." icon="fa-book-open">
    </x-page-header>

    {{-- ========================================= --}}
    {{-- STAT CARDS --}}
    {{-- ========================================= --}}
    @php
        $stats = [
            [
                'title' => 'Total Produk',
                'value' => $totalProduk,
                'suffix' => 'Varian',
                'icon' => 'fa-box-open',
                'gradient' => 'from-slate-600 to-slate-800',
            ],
            [
                'title' => 'Kategori',
                'value' => $totalKategori,
                'suffix' => 'Kategori Aktif',
                'icon' => 'fa-layer-group',
                'gradient' => 'from-violet-500 to-violet-700',
            ],
            [
                'title' => 'Stok Tersedia',
                'value' => $totalTersedia,
                'suffix' => 'Produk Ready',
                'icon' => 'fa-circle-check',
                'gradient' => 'from-emerald-500 to-emerald-700',
            ],
            [
                'title' => 'Ditampilkan',
                'value' => $barangs->total(),
                'suffix' => 'Hasil Filter',
                'icon' => 'fa-magnifying-glass',
                'gradient' => 'from-blue-500 to-blue-700',
            ],
        ];
    @endphp

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach ($stats as $stat)
            <div class="relative overflow-hidden bg-gradient-to-br {{ $stat['gradient'] }} text-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 group">
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-white/80">{{ $stat['title'] }}</p>
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20">
                            <i class="fa-solid {{ $stat['icon'] }} text-lg"></i>
                        </div>
                    </div>
                    <p class="text-3xl font-extrabold tracking-tight">{{ $stat['value'] }}</p>
                    <p class="text-xs text-white/60 mt-1">{{ $stat['suffix'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ========================================= --}}
    {{-- CATEGORY NAVIGATION --}}
    {{-- ========================================= --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-2 h-5 bg-violet-500 rounded-full"></div>
            <h2 class="font-semibold text-gray-800 text-sm">Kategori Produk</h2>
        </div>
        <div class="flex flex-wrap gap-2.5">
            {{-- Semua Kategori --}}
            <a href="{{ route('katalog_barang.index', array_merge(request()->except(['kategori', 'page']), [])) }}"
               class="group inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border
                      {{ !request('kategori') ? 'bg-red-700 text-white border-red-700 shadow-md' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-red-50 hover:text-red-700 hover:border-red-200' }}">
                <div class="w-7 h-7 flex items-center justify-center rounded-lg {{ !request('kategori') ? 'bg-white/20' : 'bg-white shadow-sm group-hover:bg-red-100' }} transition-colors">
                    <i class="fa-solid fa-border-all text-xs"></i>
                </div>
                <span>Semua</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ !request('kategori') ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-500 group-hover:bg-red-100 group-hover:text-red-600' }} transition-colors">
                    {{ $totalProduk }}
                </span>
            </a>

            @foreach ($kategoris as $kategori)
                @php
                    $isActive = request('kategori') == $kategori->kategori_barang_id;
                    $colorMap = [
                        0 => ['bg' => 'bg-blue-100', 'text' => 'text-blue-600', 'icon' => 'fa-cube'],
                        1 => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'icon' => 'fa-leaf'],
                        2 => ['bg' => 'bg-amber-100', 'text' => 'text-amber-600', 'icon' => 'fa-star'],
                        3 => ['bg' => 'bg-pink-100', 'text' => 'text-pink-600', 'icon' => 'fa-heart'],
                        4 => ['bg' => 'bg-cyan-100', 'text' => 'text-cyan-600', 'icon' => 'fa-droplet'],
                        5 => ['bg' => 'bg-orange-100', 'text' => 'text-orange-600', 'icon' => 'fa-fire'],
                        6 => ['bg' => 'bg-indigo-100', 'text' => 'text-indigo-600', 'icon' => 'fa-gem'],
                        7 => ['bg' => 'bg-teal-100', 'text' => 'text-teal-600', 'icon' => 'fa-seedling'],
                    ];
                    $color = $colorMap[$loop->index % 8];
                @endphp
                <a href="{{ route('katalog_barang.index', array_merge(request()->except(['kategori', 'page']), ['kategori' => $kategori->kategori_barang_id])) }}"
                   class="group inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 border
                          {{ $isActive ? 'bg-red-700 text-white border-red-700 shadow-md' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-red-50 hover:text-red-700 hover:border-red-200' }}">
                    <div class="w-7 h-7 flex items-center justify-center rounded-lg transition-colors
                                {{ $isActive ? 'bg-white/20' : $color['bg'] . ' ' . $color['text'] . ' group-hover:bg-red-100 group-hover:text-red-600' }}">
                        <i class="fa-solid {{ $color['icon'] }} text-xs"></i>
                    </div>
                    <span class="truncate max-w-[120px]">{{ $kategori->nama_kategori_barang }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold transition-colors
                               {{ $isActive ? 'bg-white/20 text-white' : 'bg-gray-200 text-gray-500 group-hover:bg-red-100 group-hover:text-red-600' }}">
                        {{ $kategori->barangs_count }}
                    </span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- SEARCH & SORT BAR --}}
    {{-- ========================================= --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <form method="GET" action="{{ route('katalog_barang.index') }}" class="flex flex-col md:flex-row gap-3 items-end">
            {{-- Preserve category filter --}}
            @if(request('kategori'))
                <input type="hidden" name="kategori" value="{{ request('kategori') }}">
            @endif

            <div class="flex-1 w-full">
                <label for="search" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Cari Produk</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                    </div>
                    <input type="search" name="search" id="search" placeholder="Ketik nama atau kode barang..."
                           value="{{ request('search') }}"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm bg-gray-50/50 hover:bg-white">
                </div>
            </div>

            <div class="w-full md:w-52">
                <label for="sort" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Urutkan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-arrow-down-short-wide text-gray-400 text-sm"></i>
                    </div>
                    <select name="sort" id="sort"
                            class="w-full py-2.5 pl-9 pr-8 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm appearance-none bg-gray-50/50 hover:bg-white cursor-pointer">
                        <option value="nama" {{ $sort == 'nama' ? 'selected' : '' }}>Nama (A-Z)</option>
                        <option value="harga_asc" {{ $sort == 'harga_asc' ? 'selected' : '' }}>Harga Terendah</option>
                        <option value="harga_desc" {{ $sort == 'harga_desc' ? 'selected' : '' }}>Harga Tertinggi</option>
                        <option value="terbaru" {{ $sort == 'terbaru' ? 'selected' : '' }}>Terbaru</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit"
                        class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-800 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md active:scale-95">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Cari
                </button>
                @if(request('search') || request('kategori') || request('sort'))
                <a href="{{ route('katalog_barang.index') }}"
                   class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">
                    <i class="fa-solid fa-xmark text-xs"></i> Reset
                </a>
                @endif
            </div>

            {{-- View Toggle --}}
            <div class="hidden md:flex items-center gap-1 bg-gray-100 rounded-xl p-1">
                <button type="button" @click="viewMode = 'grid'"
                        :class="viewMode === 'grid' ? 'bg-white shadow-sm text-red-700' : 'text-gray-400 hover:text-gray-600'"
                        class="w-9 h-9 flex items-center justify-center rounded-lg transition-all duration-200">
                    <i class="fa-solid fa-grip text-sm"></i>
                </button>
                <button type="button" @click="viewMode = 'list'"
                        :class="viewMode === 'list' ? 'bg-white shadow-sm text-red-700' : 'text-gray-400 hover:text-gray-600'"
                        class="w-9 h-9 flex items-center justify-center rounded-lg transition-all duration-200">
                    <i class="fa-solid fa-list text-sm"></i>
                </button>
            </div>
        </form>

        {{-- Active Filter Banner --}}
        @if(request('search') || $activeKategori)
        <div class="flex items-center gap-2 mt-4 pt-4 border-t border-gray-100">
            <span class="text-xs text-gray-400">Filter aktif:</span>
            @if(request('search'))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-50 text-blue-700 rounded-lg text-xs font-medium border border-blue-200">
                    <i class="fa-solid fa-magnifying-glass text-[9px]"></i>
                    "{{ request('search') }}"
                    <a href="{{ route('katalog_barang.index', array_merge(request()->except('search'), [])) }}" class="ml-1 hover:text-blue-900">
                        <i class="fa-solid fa-xmark text-[9px]"></i>
                    </a>
                </span>
            @endif
            @if($activeKategori)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-violet-50 text-violet-700 rounded-lg text-xs font-medium border border-violet-200">
                    <i class="fa-solid fa-layer-group text-[9px]"></i>
                    {{ $activeKategori->nama_kategori_barang }}
                    <a href="{{ route('katalog_barang.index', array_merge(request()->except('kategori'), [])) }}" class="ml-1 hover:text-violet-900">
                        <i class="fa-solid fa-xmark text-[9px]"></i>
                    </a>
                </span>
            @endif
        </div>
        @endif
    </div>

    {{-- ========================================= --}}
    {{-- PRODUCT GRID --}}
    {{-- ========================================= --}}
    @if ($barangs->count())

    {{-- Grid View --}}
    <div x-show="viewMode === 'grid'" x-transition class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach ($barangs as $barang)
        @php
            $stok = $barang->total_stok ?? 0;
            $rusak = $barang->total_stok_rusak ?? 0;
            $totalUnit = $stok * ($barang->jml_barang_per_karton ?? 0);
            $stokPct = ($stok + $rusak) > 0 ? round(($stok / ($stok + $rusak)) * 100) : 0;
        @endphp
        <a href="{{ route('barangs.show', $barang->kode_barang) }}"
           class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col cursor-pointer">

            {{-- Product Image --}}
            <div class="relative h-44 bg-gradient-to-br from-gray-50 via-gray-100 to-gray-50 flex items-center justify-center p-5 overflow-hidden">
                @if ($barang->foto_produk)
                    <img src="{{ asset('storage/' . $barang->foto_produk) }}"
                         alt="{{ $barang->nama_barang }}"
                         class="object-contain h-full w-full drop-shadow-md group-hover:scale-105 transition-transform duration-500 ease-out">
                @else
                    <div class="flex flex-col items-center gap-1.5 text-gray-300">
                        <div class="w-14 h-14 bg-gray-200/80 rounded-2xl flex items-center justify-center">
                            <i class="fa-solid fa-image text-2xl"></i>
                        </div>
                        <span class="text-[10px] font-medium">Belum ada foto</span>
                    </div>
                @endif

                {{-- Gradient overlay on hover --}}
                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

                {{-- Hover CTA --}}
                <div class="absolute bottom-3 left-3 right-3 opacity-0 group-hover:opacity-100 translate-y-2 group-hover:translate-y-0 transition-all duration-300">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/95 backdrop-blur-sm text-gray-800 text-xs font-semibold rounded-lg shadow-lg">
                        <i class="fa-solid fa-eye text-[10px] text-red-600"></i> Lihat Detail
                    </span>
                </div>

                {{-- Category Badge --}}
                <div class="absolute top-2.5 left-2.5">
                    <span class="px-2 py-1 bg-white/90 backdrop-blur-sm text-gray-700 text-[10px] font-bold rounded-lg shadow-sm border border-white/50 uppercase tracking-wider">
                        {{ $barang->kategori->nama_kategori_barang ?? 'N/A' }}
                    </span>
                </div>

                {{-- Stock Status Badge --}}
                <div class="absolute top-2.5 right-2.5">
                    @if($stok > 10)
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-emerald-500/90 backdrop-blur-sm text-white text-[10px] font-bold rounded-lg shadow-sm">
                            <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span> Tersedia
                        </span>
                    @elseif($stok > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-amber-500/90 backdrop-blur-sm text-white text-[10px] font-bold rounded-lg shadow-sm">
                            <i class="fa-solid fa-exclamation text-[8px]"></i> Terbatas
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2 py-1 bg-red-500/90 backdrop-blur-sm text-white text-[10px] font-bold rounded-lg shadow-sm">
                            <i class="fa-solid fa-xmark text-[8px]"></i> Habis
                        </span>
                    @endif
                </div>
            </div>

            {{-- Product Info --}}
            <div class="p-4 flex-1 flex flex-col">

                {{-- Name + Code --}}
                <h2 class="text-sm font-bold text-gray-900 mb-1 line-clamp-2 leading-snug group-hover:text-red-700 transition-colors" title="{{ $barang->nama_barang }}">
                    {{ $barang->nama_barang }}
                </h2>
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-[10px] font-mono text-gray-400">{{ $barang->kode_barang }}</span>
                    <span class="text-gray-200">·</span>
                    <span class="text-[10px] text-gray-400 truncate">
                        <i class="fa-solid fa-truck-field text-[8px] mr-0.5"></i>{{ $barang->supplier->namaSupplier ?? 'N/A' }}
                    </span>
                </div>

                {{-- Stock Bar --}}
                <div class="mb-3">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider">Stok</span>
                        <span class="text-[10px] text-gray-400">
                            <span class="font-bold text-gray-700">{{ number_format($stok, 0, ',', '.') }}</span> krt
                            @if($rusak > 0)
                                · <span class="text-red-500 font-bold">{{ number_format($rusak, 0, ',', '.') }}</span> rusak
                            @endif
                        </span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                        @if($stok + $rusak > 0)
                            <div class="h-1.5 rounded-full transition-all duration-700 ease-out flex">
                                <div class="{{ $stok > 10 ? 'bg-emerald-500' : ($stok > 0 ? 'bg-amber-500' : 'bg-gray-300') }}"
                                     style="width: {{ $stokPct }}%"></div>
                                @if($rusak > 0)
                                    <div class="bg-red-400" style="width: {{ 100 - $stokPct }}%"></div>
                                @endif
                            </div>
                        @else
                            <div class="h-1.5 bg-gray-200 rounded-full w-full"></div>
                        @endif
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-[9px] text-gray-400">{{ number_format($totalUnit, 0, ',', '.') }} {{ $barang->satuan_jual }}</span>
                        <span class="text-[9px] text-gray-400">{{ $barang->jml_barang_per_karton ?? 0 }}/krt</span>
                    </div>
                </div>

                {{-- Price Footer --}}
                <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
                    <div>
                        <p class="text-lg font-extrabold text-red-700 leading-none">
                            Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}
                        </p>
                        <div class="flex items-center gap-1 mt-0.5">
                            <span class="text-[10px] text-gray-400">/ {{ $barang->satuan_jual }}</span>
                            <span class="px-1.5 py-0.5 bg-red-50 text-red-600 text-[8px] font-bold rounded uppercase border border-red-100">
                                {{ $barang->tipe_harga_barang }}
                            </span>
                        </div>
                    </div>
                    <div class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-500 group-hover:bg-red-700 group-hover:text-white transition-all duration-200 group-hover:shadow-md group-hover:scale-110">
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- List View --}}
    <div x-show="viewMode === 'list'" x-transition class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="py-3.5 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Produk</th>
                        <th class="py-3.5 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="py-3.5 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Stok</th>
                        <th class="py-3.5 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="py-3.5 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Supplier</th>
                        <th class="py-3.5 px-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Harga Jual</th>
                        <th class="py-3.5 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($barangs as $barang)
                    <tr class="hover:bg-red-50/30 transition-colors duration-150 group">
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center overflow-hidden flex-shrink-0 border border-gray-200">
                                    @if ($barang->foto_produk)
                                        <img src="{{ asset('storage/' . $barang->foto_produk) }}" alt="{{ $barang->nama_barang }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="fa-solid fa-image text-gray-300"></i>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 text-sm truncate max-w-[200px]">{{ $barang->nama_barang }}</p>
                                    <p class="text-xs text-gray-400 font-mono">{{ $barang->kode_barang }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-violet-50 text-violet-700 text-xs font-semibold rounded-lg border border-violet-200">
                                <i class="fa-solid fa-tag text-[9px]"></i>
                                {{ $barang->kategori->nama_kategori_barang ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div>
                                <span class="font-bold text-gray-800">{{ number_format($barang->total_stok ?? 0, 0, ',', '.') }}</span>
                                <span class="text-[10px] text-gray-400 block">Karton</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if(($barang->total_stok ?? 0) > 10)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-xl border border-emerald-200">
                                    <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span> Tersedia
                                </span>
                            @elseif(($barang->total_stok ?? 0) > 0)
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-semibold rounded-xl border border-amber-200">
                                    <i class="fa-solid fa-exclamation text-[10px]"></i> Terbatas
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-700 text-xs font-semibold rounded-xl border border-red-200">
                                    <i class="fa-solid fa-xmark text-[10px]"></i> Habis
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex items-center gap-2 text-gray-500 text-xs">
                                <i class="fa-solid fa-truck-field text-[10px] text-gray-400"></i>
                                <span class="truncate max-w-[120px]">{{ $barang->supplier->namaSupplier ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-right">
                            <p class="font-bold text-red-700">Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}</p>
                            <p class="text-[10px] text-gray-400">/ {{ $barang->satuan_jual }}</p>
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <a href="{{ route('barangs.show', $barang->kode_barang) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-800 text-white rounded-xl text-xs font-semibold hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md active:scale-95">
                                <i class="fa-solid fa-eye text-[10px]"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- PAGINATION --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400">
                Menampilkan {{ $barangs->firstItem() ?? 0 }} - {{ $barangs->lastItem() ?? 0 }} dari {{ $barangs->total() }} produk
            </p>
            <div>
                {{ $barangs->withQueryString()->links() }}
            </div>
        </div>
    </div>

    @else
    {{-- EMPTY STATE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
        <div class="flex flex-col items-center">
            <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-5">
                <i class="fa-solid fa-box-open text-red-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2">Produk Tidak Ditemukan</h3>
            <p class="text-sm text-gray-500 mb-5 max-w-md">Tidak ada produk yang sesuai dengan kriteria pencarian Anda. Coba ubah kata kunci atau pilih kategori lain.</p>
            <a href="{{ route('katalog_barang.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-800 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md active:scale-95">
                <i class="fa-solid fa-rotate-right text-xs"></i> Reset Semua Filter
            </a>
        </div>
    </div>
    @endif

</div>
@endsection