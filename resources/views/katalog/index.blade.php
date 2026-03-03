@extends('layouts.app')

@section('page-title', 'Katalog Barang')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Katalog Barang Gudang</h1>
            <p class="text-sm text-gray-500 mt-1">Lihat daftar produk, ketersediaan stok, harga jual, dan informasi lainnya.</p>
        </div>
        <div class="flex items-center gap-2 text-sm text-gray-500 bg-white border border-gray-200 rounded-lg px-3 py-2 shadow-sm">
            <i class="fa-solid fa-box-open"></i>
            <span>{{ $barangs->total() }} Produk</span>
        </div>
    </div>

    {{-- FILTER SECTION --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <form method="GET" action="{{ route('katalog_barang.index') }}" class="flex flex-col md:flex-row gap-3 items-end">
            <div class="flex-1 w-full">
                <label for="search" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Cari Produk</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                    </div>
                    <input type="search" name="search" id="search" placeholder="Ketik nama barang..."
                           value="{{ request('search') }}"
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                </div>
            </div>

            <div class="w-full md:w-56">
                <label for="kategori" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Kategori</label>
                <select name="kategori" id="kategori"
                        class="w-full py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm appearance-none bg-white">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori->kategori_barang_id }}"
                                {{ request('kategori') == $kategori->kategori_barang_id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori_barang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2 w-full md:w-auto">
                <button type="submit"
                        class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-800 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-filter text-xs"></i> Filter
                </button>
                <a href="{{ route('katalog_barang.index') }}"
                   class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">
                    <i class="fa-solid fa-rotate-right text-xs"></i> Reset
                </a>
            </div>
        </form>
    </div>

    {{-- PRODUCT GRID --}}
    @if ($barangs->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @foreach ($barangs as $barang)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 group flex flex-col">

            {{-- Product Image --}}
            <div class="relative h-48 bg-gradient-to-br from-gray-50 to-gray-100 flex items-center justify-center p-4 overflow-hidden">
                @if ($barang->foto_produk)
                    <img src="{{ asset('storage/' . $barang->foto_produk) }}"
                         alt="{{ $barang->nama_barang }}"
                         class="object-contain h-full w-full rounded-lg group-hover:scale-105 transition-transform duration-500">
                @else
                    <div class="flex flex-col items-center gap-2 text-gray-300">
                        <i class="fa-solid fa-image text-4xl"></i>
                        <span class="text-xs">No Image</span>
                    </div>
                @endif

                {{-- Category Badge --}}
                <div class="absolute top-3 left-3">
                    <span class="px-2.5 py-1 bg-white/90 backdrop-blur-sm text-gray-700 text-xs font-semibold rounded-lg shadow-sm border border-gray-100">
                        {{ $barang->kategori->nama_kategori_barang ?? 'N/A' }}
                    </span>
                </div>

                {{-- Stock Status Badge --}}
                <div class="absolute top-3 right-3">
                    @if(($barang->total_stok ?? 0) > 10)
                        <span class="px-2 py-1 bg-emerald-500 text-white text-xs font-bold rounded-lg shadow-sm">
                            <i class="fa-solid fa-check text-[10px] mr-0.5"></i> Tersedia
                        </span>
                    @elseif(($barang->total_stok ?? 0) > 0)
                        <span class="px-2 py-1 bg-amber-500 text-white text-xs font-bold rounded-lg shadow-sm">
                            <i class="fa-solid fa-exclamation text-[10px] mr-0.5"></i> Terbatas
                        </span>
                    @else
                        <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-lg shadow-sm">
                            <i class="fa-solid fa-xmark text-[10px] mr-0.5"></i> Habis
                        </span>
                    @endif
                </div>
            </div>

            {{-- Product Info --}}
            <div class="p-4 flex-1 flex flex-col">
                {{-- Code + Name --}}
                <p class="text-xs font-mono text-gray-400 mb-1">{{ $barang->kode_barang }}</p>
                <h2 class="text-base font-bold text-gray-900 mb-3 truncate" title="{{ $barang->nama_barang }}">
                    {{ $barang->nama_barang }}
                </h2>

                {{-- Stats Grid --}}
                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div class="bg-emerald-50 rounded-xl p-2.5 text-center">
                        <p class="text-xs text-emerald-600 font-medium mb-0.5">Stok</p>
                        <p class="text-lg font-bold text-emerald-700">{{ number_format($barang->total_stok ?? 0, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-emerald-500">Karton</p>
                    </div>
                    <div class="bg-red-50 rounded-xl p-2.5 text-center">
                        <p class="text-xs text-red-600 font-medium mb-0.5">Rusak</p>
                        <p class="text-lg font-bold text-red-700">{{ number_format($barang->total_stok_rusak ?? 0, 0, ',', '.') }}</p>
                        <p class="text-[10px] text-red-500">Karton</p>
                    </div>
                </div>

                {{-- Details --}}
                <div class="space-y-1.5 text-sm mb-3">
                    <div class="flex items-center gap-2 text-gray-500">
                        <i class="fa-solid fa-truck-field text-xs text-gray-400 w-4"></i>
                        <span class="truncate">{{ $barang->supplier->namaSupplier ?? 'N/A' }}</span>
                    </div>
                    <div class="flex items-center gap-2 text-gray-500">
                        <i class="fa-solid fa-boxes-stacked text-xs text-gray-400 w-4"></i>
                        <span>{{ number_format(($barang->total_stok ?? 0) * ($barang->jml_barang_per_karton ?? 0), 0, ',', '.') }} {{ $barang->satuan_jual }}</span>
                    </div>
                </div>

                {{-- Price + Action --}}
                <div class="mt-auto pt-3 border-t border-gray-100">
                    <div class="flex items-end justify-between">
                        <div>
                            <p class="text-xl font-extrabold text-red-700">
                                Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}
                            </p>
                            <div class="flex items-center gap-1.5 mt-0.5">
                                <span class="text-xs text-gray-400">/ {{ $barang->satuan_jual }}</span>
                                <span class="px-1.5 py-0.5 bg-red-100 text-red-600 text-[10px] font-bold rounded uppercase">
                                    {{ $barang->tipe_harga_barang }}
                                </span>
                            </div>
                        </div>
                        <a href="{{ route('barangs.show', $barang->kode_barang) }}"
                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-red-800 text-white hover:bg-red-700 transition-all shadow-sm group-hover:scale-110">
                            <i class="fa-solid fa-arrow-right text-sm"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- PAGINATION --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4">
        {{ $barangs->withQueryString()->links() }}
    </div>

    @else
    {{-- EMPTY STATE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-box-open text-2xl text-gray-400"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-700 mb-1">Barang Tidak Ditemukan</h3>
        <p class="text-sm text-gray-500 mb-4">Coba ubah kriteria pencarian atau filter kategori.</p>
        <a href="{{ route('katalog_barang.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-red-800 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
            <i class="fa-solid fa-rotate-right text-xs"></i> Reset Filter
        </a>
    </div>
    @endif

</div>
@endsection