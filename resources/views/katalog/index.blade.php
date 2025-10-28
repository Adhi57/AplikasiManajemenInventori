@extends('layouts.app')

@section('page-title', 'Katalog Barang')

@section('content')
<div class="container mx-auto p-4 sm:p-6 lg:p-8">

    <div class="mb-8 p-4 bg-white shadow-sm rounded-lg flex flex-col md:flex-row gap-4 items-center">
        <form method="GET" action="{{ route('katalog_barang.index') }}" class="w-full md:w-2/3 flex gap-4">
            <input type="search" name="search" placeholder="Cari Nama Barang..." value="{{ request('search') }}" 
                   class="flex-grow p-2 border border-gray-300 rounded-sm focus:ring-indigo-500 focus:border-indigo-500 shadow-sm">
            
            <select name="kategori" class="p-2 border border-gray-300 rounded-md shadow-sm w-1/3">
                <option value="">Semua Kategori</option>
                @foreach ($kategoris as $kategori)
                    <option value="{{ $kategori->kategori_barang_id }}" 
                            {{ request('kategori') == $kategori->kategori_barang_id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori_barang }}
                    </option>
                @endforeach
            </select>
            
            <button type="submit" class="p-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition duration-150">
                Filter
            </button>
            <a href="{{ route('katalog_barang.index') }}" class="p-2 bg-gray-400 text-white rounded-md hover:bg-gray-500 transition duration-150">Reset</a>
        </form>
    </div>

    @if ($barangs->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach ($barangs as $barang)
        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-indigo-300 transition-shadow duration-300">
            <div class="h-48 bg-gray-100 flex items-center justify-center">
                @if ($barang->foto_produk)
                    <img src="{{ asset('storage/' . $barang->foto_produk) }}" alt="{{ $barang->nama_barang }}" class="object-contain h-full w-full">
                @else
                    <svg class="w-16 h-16 text-gray-300" fill="currentColor" viewBox="0 0 24 24"><path d="M4 3h16a2 2 0 012 2v14a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2zM4 5v14h16V5H4zm8 2a3 3 0 100 6 3 3 0 000-6zm-5 8h10v2H7v-2z" /></svg>
                @endif
            </div>

            <div class="p-5">
                <p class="text-sm text-indigo-600 font-semibold mb-1">{{ $barang->kode_barang }}</p>

                <h2 class="text-xl font-bold text-gray-900 mb-2 truncate" title="{{ $barang->nama_barang }}">{{ $barang->nama_barang }}</h2>
                
                <div class="space-y-1 text-sm text-gray-600">
                    <p>
                        <span class="font-medium">Kategori:</span> {{ $barang->kategori->nama_kategori_barang ?? 'N/A' }}
                    </p>
                    <p>
                        <span class="font-medium">Supplier:</span> {{ $barang->supplier->namaSupplier ?? 'N/A' }}
                    </p>
                    
                    <p>
                        <span class="font-medium">Stok Tersedia:</span> 
                        <span class="font-bold {{ $barang->total_stok > 5 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($barang->total_stok ?? 0, 0, ',', '.') }} Karton
                        </span>
                    </p>
                        <span class="font-medium">Total per {{ $barang->satuan_terkecil }}:</span> 
                        <span class="font-bold">
                            {{ number_format( $barang->total_stok * $barang->jml_barang_per_karton  ?? 0, 0, ',', '.') }} {{ $barang->satuan_terkecil }}
                        </span>
                    </p>
                    <p>
                        <span class="font-medium">Stok Rusak:</span> 
                        <span class="font-bold text-red-500">
                            {{ number_format($barang->total_stok_rusak ?? 0, 0, ',', '.') }} Karton
                        </span>
                    </p>

                    <p class="pt-2 text-2xl font-extrabold text-indigo-700">
                        Rp{{ number_format($barang->harga_jual, 0, ',', '.') }} 
                        <span class="text-xs font-normal text-gray-500">/ {{ $barang->satuan_terkecil }} ({{ $barang->tipe_harga_barang }})</span>
                    </p>
                </div>
            </div>
            
            <div class="p-5 bg-gray-50 border-t border-gray-100">
                <a href={{ route('barangs.show', $barang->kode_barang) }} class="text-indigo-600 hover:text-indigo-800 text-sm font-semibold">
                    Lihat Detail &rarr;
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $barangs->links() }}
    </div>
    
    @else
    <div class="text-center p-10 bg-white rounded-lg shadow-lg">
        <p class="text-xl text-gray-500">Barang tidak ditemukan. Coba ubah kriteria pencarian.</p>
    </div>
    @endif

</div>

@endsection