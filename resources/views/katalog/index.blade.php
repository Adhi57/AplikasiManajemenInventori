@extends('layouts.app')

@section('page-title', 'Katalog Barang')

@section('content')
<!-- Container Utama (Diberi background abu-abu muda dan shadow-inner seperti barangs.index) -->
<div class="p-4 sm:p-6 lg:p-8 bg-gray-50 rounded-xl shadow-inner min-h-[80vh]">

    <!-- Kotak Filter & Pencarian -->
    <div class="mb-8 p-6 bg-white shadow-xl rounded-xl border border-gray-200">
        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Filter Katalog</h2>
        <form method="GET" action="{{ route('katalog_barang.index') }}" class="w-full flex flex-col md:flex-row gap-4 items-end">
            
            <div class="w-full md:w-3/5">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Cari Nama Barang</label>
                <input type="search" name="search" id="search" placeholder="Cari Nama Barang..." value="{{ request('search') }}" 
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-red-600 focus:border-red-600 shadow-sm transition duration-150">
            </div>
            
            <div class="w-full md:w-1/5">
                <label for="kategori" class="block text-sm font-medium text-gray-700 mb-1">Filter Kategori</label>
                <select name="kategori" id="kategori" class="w-full p-2 border border-gray-300 rounded-lg shadow-sm focus:ring-red-600 focus:border-red-600 transition duration-150">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori->kategori_barang_id }}" 
                                {{ request('kategori') == $kategori->kategori_barang_id ? 'selected' : '' }}>
                            {{ $kategori->nama_kategori_barang }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Filter button: Diubah ke Red-700 -->
            <button type="submit" class="w-full md:w-auto flex-shrink-0 p-2.5 bg-red-700 text-white rounded-lg hover:bg-red-800 transition duration-150 font-medium shadow-md">
                <i class="fa-solid fa-filter mr-2"></i> Filter
            </button>
            <!-- Reset button -->
            <a href="{{ route('katalog_barang.index') }}" class="w-full md:w-auto flex-shrink-0 p-2.5 bg-gray-400 text-white rounded-lg hover:bg-gray-500 transition duration-150 font-medium shadow-md text-center">
                <i class="fa-solid fa-rotate-right mr-2"></i> Reset
            </a>
        </form>
    </div>

    @if ($barangs->count())
    <!-- Grid Katalog Barang -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach ($barangs as $barang)
        <!-- Card Barang -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-100 hover:shadow-red-300 transition-shadow duration-300">
            
            <!-- Gambar Produk -->
            <div class="h-48 bg-gray-100 flex items-center justify-center p-4">
                @if ($barang->foto_produk)
                    <img src="{{ asset('storage/' . $barang->foto_produk) }}" alt="{{ $barang->nama_barang }}" class="object-contain h-full w-full rounded-md">
                @else
                    <!-- Placeholder SVG: Warna diubah menjadi abu-abu terang (gray-300) -->
                    <svg class="w-16 h-16 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M4 3h16a2 2 0 012 2v14a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2zM4 5v14h16V5H4zm8 2a3 3 0 100 6 3 3 0 000-6zm-5 8h10v2H7v-2z" /></svg>
                @endif
            </div>

            <div class="p-5">
                <!-- Kode Barang: Diubah ke Red-600, font-medium -->
                <p class="text-sm text-red-600 font-medium mb-1">{{ $barang->kode_barang }}</p>

                <!-- Nama Barang: font-semibold -->
                <h2 class="text-xl font-semibold text-gray-900 mb-2 truncate" title="{{ $barang->nama_barang }}">{{ $barang->nama_barang }}</h2>
                
                <div class="space-y-1 text-sm text-gray-600">
                    <p>
                        <span class="font-medium">Kategori:</span> {{ $barang->kategori->nama_kategori_barang ?? 'N/A' }}
                    </p>
                    <p>
                        <span class="font-medium">Supplier:</span> {{ $barang->supplier->namaSupplier ?? 'N/A' }}
                    </p>
                    
                    <p>
                        <span class="font-medium">Stok Tersedia:</span> 
                        <!-- Stok Karton: font-semibold -->
                        <span class="font-semibold {{ $barang->total_stok > 5 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($barang->total_stok ?? 0, 0, ',', '.') }} Karton
                        </span>
                    </p>
                    <!-- Total per Satuan Jual: font-semibold -->
                    <p>
                        <span class="font-medium">Total per {{ $barang->satuan_jual }}:</span> 
                        <span class="font-semibold text-gray-700">
                            {{ number_format( $barang->total_stok * $barang->jml_barang_per_karton  ?? 0, 0, ',', '.') }} {{ $barang->satuan_jual }}
                        </span>
                    </p>
                    <p>
                        <span class="font-medium">Stok Rusak:</span> 
                        <!-- Stok Rusak: font-semibold -->
                        <span class="font-semibold text-red-500">
                            {{ number_format($barang->total_stok_rusak ?? 0, 0, ',', '.') }} Karton
                        </span>
                    </p>

                    <!-- Harga Jual: text-xl font-bold, diubah ke Red-700 -->
                    <p class="pt-3 text-2xl font-bold text-red-700 border-t mt-4">
                        Rp{{ number_format($barang->harga_jual, 0, ',', '.') }} 
                        <span class="text-sm font-normal text-gray-500">/ {{ $barang->satuan_jual }}</span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-full text-white bg-red-500/80 uppercase tracking-wider ml-1">
                            {{ $barang->tipe_harga_barang }}
                        </span>
                    </p>
                </div>
            </div>
            
            <div class="p-5 bg-gray-50 border-t border-gray-100">
                <!-- Link Detail: Diubah ke Red-600, font-medium -->
                <a href={{ route('barangs.show', $barang->kode_barang) }} class="text-red-600 hover:text-red-800 text-sm font-medium transition duration-150 flex items-center justify-end">
                    Lihat Detail 
                    <i class="fa-solid fa-arrow-right ml-2 text-xs"></i>
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagiasi -->
    <div class="mt-8 p-4 bg-white rounded-xl shadow-lg border border-gray-200">
        {{ $barangs->links() }}
    </div>
    
    @else
    <div class="text-center p-10 bg-white rounded-xl shadow-lg border border-gray-200">
        <p class="text-xl text-gray-500 font-medium">Barang tidak ditemukan. Coba ubah kriteria pencarian.</p>
    </div>
    @endif

</div>

@endsection