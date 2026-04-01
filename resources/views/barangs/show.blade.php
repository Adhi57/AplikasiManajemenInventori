@extends('layouts.app')
@section('page-title', 'Detail Barang')
@section('content')

    @php
        $totalStok = $barang->total_stok ?? 0;
        $totalStokRusak = $barang->total_stok_rusak ?? 0;
        $stokAman = $totalStok > 10; // Simple threshold example
    @endphp

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Page Header --}}
        <x-page-header title="Detail Barang" description="Katalog / Data Master / {{ $barang->kode_barang }}" icon="fa-box">
            <x-slot name="actions">
                <a href="{{ route('barangs.edit', $barang->kode_barang) }}"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-amber-500 hover:bg-amber-600 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 text-white duration-200">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <span>Edit</span>
                </a>
                <a href="{{ route('barangs.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                    <i class="fa-solid fa-arrow-left text-amber-400"></i>
                    <span>Kembali</span>
                </a>
            </x-slot>
        </x-page-header>

        {{-- Main Profile Card --}}
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-4">

                {{-- Product Image Sidebar --}}
                <div
                    class="lg:col-span-1 p-6 flex flex-col items-center justify-center border-b lg:border-b-0 lg:border-r border-gray-100 bg-gray-50/50">
                    <div class="relative group cursor-pointer w-full max-w-[200px] aspect-square mb-4">
                        @if ($barang->foto_produk)
                            <img src="{{ asset('storage/' . $barang->foto_produk) }}" alt="Foto {{ $barang->nama_barang }}"
                                class="w-full h-full object-cover rounded-2xl shadow-md border-4 border-white transition-transform duration-300 group-hover:scale-105">
                        @else
                            <div
                                class="w-full h-full bg-white flex flex-col items-center justify-center rounded-2xl shadow-sm border-2 border-dashed border-gray-300 text-gray-400">
                                <i class="fa-solid fa-image text-4xl mb-2 text-gray-300"></i>
                                <span class="text-xs font-medium">No Image</span>
                            </div>
                        @endif
                        <div class="absolute top-2 right-2">
                            @if($stokAman)
                                <span class="px-2 py-1 bg-emerald-500 text-white text-[10px] font-bold rounded-lg shadow-sm">
                                    <i class="fa-solid fa-check-circle mr-1"></i>Aman
                                </span>
                            @else
                                <span class="px-2 py-1 bg-red-500 text-white text-[10px] font-bold rounded-lg shadow-sm">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>Low Stock
                                </span>
                            @endif
                        </div>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900 text-center leading-tight mb-1">
                        {{ $barang->nama_barang }}
                    </h2>
                    <p class="text-xs text-red-600 font-mono bg-red-50 px-2 py-0.5 rounded border border-red-100">
                        {{ $barang->kode_barang }}
                    </p>
                </div>

                {{-- Product Details with Alpine Tabs --}}
                <div class="lg:col-span-3" x-data="{ activeTab: 'umum' }">

                    {{-- Tabs Header --}}
                    <div class="flex overflow-x-auto border-b border-gray-100 bg-white px-2 pt-2 hide-scrollbar">
                        <button @click="activeTab = 'umum'"
                            class="px-5 py-3.5 text-sm font-bold border-b-2 transition-colors whitespace-nowrap flex items-center gap-2"
                            :class="activeTab === 'umum' ? 'border-red-600 text-red-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200'">
                            <i class="fa-solid fa-circle-info"></i> Informasi Umum
                        </button>
                        <button @click="activeTab = 'harga'"
                            class="px-5 py-3.5 text-sm font-bold border-b-2 transition-colors whitespace-nowrap flex items-center gap-2"
                            :class="activeTab === 'harga' ? 'border-red-600 text-red-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200'">
                            <i class="fa-solid fa-tags"></i> Data Harga
                        </button>
                        <button @click="activeTab = 'inventori'"
                            class="px-5 py-3.5 text-sm font-bold border-b-2 transition-colors whitespace-nowrap flex items-center gap-2"
                            :class="activeTab === 'inventori' ? 'border-red-600 text-red-700' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-200'">
                            <i class="fa-solid fa-boxes-stacked"></i> Stok & Inventori
                        </button>
                    </div>

                    {{-- Tab Contents --}}
                    <div class="p-6 md:p-8 bg-white min-h-[300px]">

                        {{-- TAB UMUM --}}
                        <div x-show="activeTab === 'umum'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                            <h3 class="text-base font-bold text-gray-800 mb-5 pb-2 border-b flex items-center gap-2"><i
                                    class="fa-solid fa-tag text-red-500"></i> Klasifikasi Produk</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div
                                    class="flex gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-red-200 transition">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-red-600 border border-gray-100 flex-shrink-0">
                                        <i class="fa-solid fa-layer-group text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Kategori
                                        </p>
                                        <p class="text-sm font-bold text-gray-900">
                                            {{ $barang->kategori->nama_kategori_barang ?? '-' }}</p>
                                    </div>
                                </div>

                                <div
                                    class="flex gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-blue-200 transition">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-blue-600 border border-gray-100 flex-shrink-0">
                                        <i class="fa-solid fa-building-user text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Supplier
                                            Utama</p>
                                        <p class="text-sm font-bold text-gray-900">
                                            {{ $barang->supplier->namaSupplier ?? '-' }}</p>
                                    </div>
                                </div>

                                <div
                                    class="flex gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-amber-200 transition">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-amber-500 border border-gray-100 flex-shrink-0">
                                        <i class="fa-solid fa-ruler-combined text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Satuan
                                            Dasar</p>
                                        <p class="text-sm font-bold text-gray-900">{{ $barang->satuan_jual }}</p>
                                    </div>
                                </div>

                                <div
                                    class="flex gap-4 p-4 rounded-2xl bg-gray-50 border border-gray-100 hover:border-emerald-200 transition">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-emerald-600 border border-gray-100 flex-shrink-0">
                                        <i class="fa-solid fa-box-open text-xl"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Isi Per
                                            Karton</p>
                                        <p class="text-sm font-bold text-gray-900">
                                            {{ number_format($barang->jml_barang_per_karton, 0, ',', '.') }}
                                            {{ $barang->satuan_jual }}</p>
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- TAB HARGA --}}
                        <div x-show="activeTab === 'harga'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                            <div class="flex items-center justify-between mb-5 pb-2 border-b">
                                <h3 class="text-base font-bold text-gray-800 flex items-center gap-2"><i
                                        class="fa-solid fa-money-bill-wave text-emerald-500"></i> Penetapan Harga</h3>
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">Tipe:
                                    {{ $barang->tipe_harga_barang }}</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                                {{-- Harga Beli --}}
                                <div
                                    class="p-5 bg-gradient-to-br from-red-50 to-white rounded-2xl border border-red-100 shadow-sm relative overflow-hidden">
                                    <div class="absolute -right-4 -bottom-4 opacity-5 text-red-900"><i
                                            class="fa-solid fa-arrow-down-wide-short text-7xl"></i></div>
                                    <p class="text-xs font-bold text-red-500 uppercase mb-2">Harga Beli Dasar</p>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-sm font-bold text-gray-500">Rp</span>
                                        <span
                                            class="text-3xl font-black text-gray-800">{{ number_format($barang->harga_beli, 0, ',', '.') }}</span>
                                    </div>
                                </div>

                                {{-- Harga Jual --}}
                                <div
                                    class="p-5 bg-gradient-to-br from-emerald-50 to-white rounded-2xl border border-emerald-100 shadow-sm relative overflow-hidden">
                                    <div class="absolute -right-4 -bottom-4 opacity-5 text-emerald-900"><i
                                            class="fa-solid fa-hand-holding-dollar text-7xl"></i></div>
                                    <p class="text-xs font-bold text-emerald-600 uppercase mb-2">Harga Jual Konsumen</p>
                                    <div class="flex items-baseline gap-1">
                                        <span class="text-sm font-bold text-gray-500">Rp</span>
                                        <span
                                            class="text-3xl font-black text-gray-800">{{ number_format($barang->harga_jual, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-gray-400 shadow-sm">
                                        <i class="fa-regular fa-calendar-check text-lg"></i></div>
                                    <div>
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Periode Berlaku Mulai</p>
                                        <p class="text-sm font-bold text-gray-800">
                                            {{ \Carbon\Carbon::parse($barang->berlaku_mulai)->translatedFormat('d F Y, H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="h-8 w-px bg-gray-200 hidden md:block"></div>
                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <p class="text-[10px] font-bold text-gray-400 uppercase">Berlaku Sampai</p>
                                        <p class="text-sm font-bold text-gray-800">
                                            {{ $barang->berlaku_sampai ? \Carbon\Carbon::parse($barang->berlaku_sampai)->translatedFormat('d F Y, H:i') : 'Selamanya (Aktif)' }}
                                        </p>
                                    </div>
                                    <div
                                        class="w-10 h-10 bg-white rounded-lg flex items-center justify-center text-gray-400 shadow-sm">
                                        <i class="fa-solid fa-timeline text-lg"></i></div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB INVENTORI --}}
                        <div x-show="activeTab === 'inventori'" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0" style="display: none;">
                            <h3 class="text-base font-bold text-gray-800 mb-5 pb-2 border-b flex items-center gap-2"><i
                                    class="fa-solid fa-cubes text-blue-500"></i> Pantauan Stok Gudang</h3>

                            <div class="space-y-4">
                                {{-- Stok Utama --}}
                                <div
                                    class="p-5 rounded-2xl border {{ $stokAman ? 'bg-blue-50/50 border-blue-100' : 'bg-red-50/50 border-red-200' }}">
                                    <div class="flex items-center justify-between mb-2">
                                        <p
                                            class="text-xs font-bold text-gray-500 uppercase tracking-widest flex items-center gap-2">
                                            <i class="fa-solid fa-box text-gray-400"></i> Total Stok Tersedia
                                        </p>
                                        <span
                                            class="px-2 py-0.5 rounded text-[10px] font-bold {{ $stokAman ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $stokAman ? 'Stock Cukup' : 'Perlu Reorder' }}
                                        </span>
                                    </div>
                                    <div class="flex items-end gap-3 mb-2">
                                        <p class="text-3xl font-black text-gray-900">
                                            {{ number_format($totalStok, 2, ',', '.') }} <span
                                                class="text-lg font-bold text-gray-500">Karton</span></p>
                                    </div>
                                    <p
                                        class="text-xs font-semibold text-gray-500 bg-white inline-block px-3 py-1 rounded-lg border border-gray-100">
                                        <i class="fa-solid fa-equals mx-1 text-gray-300"></i> Setara dengan <strong
                                            class="text-gray-800">{{ number_format($totalStok * $barang->jml_barang_per_karton, 0, ',', '.') }}
                                            {{ $barang->satuan_jual }}</strong>
                                    </p>
                                </div>

                                {{-- Stok Rusak & Kadaluarsa Grid --}}
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    {{-- Stok Rusak --}}
                                    <div
                                        class="p-4 rounded-xl border border-gray-100 bg-white shadow-sm flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Stok Rusak (Retur/Cacat)</p>
                                            <p class="text-xl font-bold text-red-600">
                                                {{ number_format($totalStokRusak, 2, ',', '.') }} <span
                                                    class="text-sm text-gray-400">Krt</span></p>
                                            <p class="text-xs text-gray-500 mt-0.5">
                                                ({{ number_format($totalStokRusak * $barang->jml_barang_per_karton, 0, ',', '.') }}
                                                {{ $barang->satuan_jual }})</p>
                                        </div>
                                        <div
                                            class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500">
                                            <i class="fa-solid fa-heart-crack text-xl"></i>
                                        </div>
                                    </div>

                                    {{-- Kadaluarsa Terdekat --}}
                                    <div
                                        class="p-4 rounded-xl border border-gray-100 bg-white shadow-sm flex items-center justify-between">
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">
                                                Tanggal Expired</p>
                                            <p class="text-base font-bold text-gray-800">
                                                @if(optional($barang->stok)->tgl_kadaluarsa)
                                                    {{ \Carbon\Carbon::parse($barang->stok->tgl_kadaluarsa)->translatedFormat('d M Y') }}
                                                @else
                                                    <span class="text-gray-400 italic">Tidak ada data</span>
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-500 mt-0.5">Pantau dari menu Tracking</p>
                                        </div>
                                        <div
                                            class="w-12 h-12 rounded-full bg-amber-50 flex items-center justify-center text-amber-500">
                                            <i class="fa-solid fa-hourglass-half text-xl"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hide-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .hide-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
    </style>

@endsection