@extends('layouts.app')

@section('content')

<div class="container mx-auto p-4">

    <div class="bg-white rounded-xl shadow-lg p-8 max-w-4xl mx-auto">
        <div class="mb-8 border-b pb-4">
            <h1 class="text-3xl font-bold text-gray-800 text-center">Detail Barang: {{ $barang->nama_barang }}</h1>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
            {{-- Bagian Foto Produk --}}
            <div class="md:col-span-1">
                <h2 class="text-xl font-semibold mb-3 text-gray-700 border-b pb-2">Foto Produk</h2>
                @if ($barang->foto_produk)
                    <img src="{{ asset('storage/' . $barang->foto_produk) }}" alt="Foto Produk {{ $barang->nama_barang }}"
                        class="w-full h-auto object-cover rounded-lg shadow-md border border-gray-200">
                @else
                    <div class="w-full h-48 bg-gray-100 flex items-center justify-center rounded-lg border border-dashed border-gray-400">
                        <span class="text-gray-500">Tidak ada foto produk</span>
                    </div>
                @endif
            </div>

            {{-- Bagian Detail Utama --}}
            <div class="md:col-span-2 space-y-4">
                <h2 class="text-xl font-semibold mb-3 text-gray-700 border-b pb-2">Informasi Utama</h2>
                
                {{-- Nama Barang --}}
                <div class="detail-row">
                    <p class="text-sm font-medium text-gray-500">Nama Barang</p>
                    <p class="text-lg font-medium text-gray-900">{{ $barang->nama_barang }}</p>
                </div>

                {{-- Kategori & Supplier --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="detail-row">
                        <p class="text-sm font-medium text-gray-500">Kategori</p>
                        {{-- Menggunakan relasi 'kategori' --}}
                        <p class="text-lg font-medium text-gray-900">{{ $barang->kategori->nama_kategori_barang ?? '-' }}</p>
                    </div>
                    <div class="detail-row">
                        <p class="text-sm font-medium text-gray-500">Supplier</p>
                        <p class="text-lg font-medium text-gray-900">{{ $barang->supplier->namaSupplier ?? '-' }}</p>
                    </div>
                </div>

                {{-- Harga --}}
                <h2 class="text-xl font-semibold mt-6 mb-3 text-gray-700 border-b pb-2">Informasi Harga</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div class="detail-row">
                        <p class="text-sm font-medium text-gray-500">Harga Beli</p>
                        <p class="text-lg font-medium text-gray-900">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</p>
                    </div>
                    <div class="detail-row">
                        <p class="text-sm font-medium text-gray-500">Harga Jual</p>
                        <p class="text-lg font-medium text-gray-900">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</p>
                    </div>
                    <div class="detail-row">
                        <p class="text-sm font-medium text-gray-500">Tipe Harga</p>
                        <p class="text-lg font-medium text-gray-900">{{ $barang->tipe_harga_barang }}</p>
                    </div>
                </div>
                <div class="detail-row">
                    <p class="text-sm font-medium text-gray-500">Berlaku Mulai</p>
                    <p class="text-lg font-medium text-gray-900">{{ \Carbon\Carbon::parse($barang->berlaku_mulai)->format('d F Y') }}</p>
                </div>

                {{-- Stok dan Satuan --}}
                <h2 class="text-xl font-semibold mt-6 mb-3 text-gray-700 border-b pb-2">Informasi Stok</h2>
                <div class="grid grid-cols-3 gap-4">
                    <div class="detail-row">
                        <p class="text-sm font-medium text-gray-500">Stok Tersedia</p>
                        <p class="text-lg font-medium text-gray-900">{{ number_format($barang->stok->jumlah_stok ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="detail-row">
                        <p class="text-sm font-medium text-gray-500">Stok Rusak</p>
                        <p class="text-lg font-medium text-gray-900">{{ number_format($barang->stok->jumlah_stok_rusak ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="detail-row">
                        <p class="text-sm font-medium text-gray-500">Satuan Terkecil</p>
                        <p class="text-lg font-medium text-gray-900">{{ $barang->satuan_terkecil }}</p>
                    </div>
                </div>
                
                {{-- Jml Per Karton & Kadaluarsa --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="detail-row">
                        <p class="text-sm font-medium text-gray-500">Jumlah Per Karton</p>
                        <p class="text-lg font-medium text-gray-900">{{ number_format($barang->jml_barang_per_karton, 0, ',', '.') }}</p>
                    </div>
                    <div class="detail-row">
                        <p class="text-sm font-medium text-gray-500">Tanggal Kadaluarsa</p>
                        <p class="text-lg font-medium text-gray-900">
                            {{ optional($barang->stok)->tgl_kadaluarsa
                                ? \Carbon\Carbon::parse($barang->stok->tgl_kadaluarsa)->translatedFormat('d F Y')
                                : '-' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Aksi --}}
        <div class="flex justify-start space-x-3 mt-8 pt-4 border-t">
            <a href="{{ route('barangs.index') }}"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-150">
                Kembali
            </a>
            {{-- PERBAIKAN: Menggunakan kode_barang sebagai parameter --}}
            <a href="{{ route('barangs.edit', $barang->kode_barang) }}"
                class="px-4 py-2 text-sm font-medium text-white bg-yellow-600 rounded-lg shadow-md hover:bg-yellow-700 transition duration-150">
                Edit Barang
            </a>
        </div>
    </div>
</div>

{{-- Inline CSS for cleaner presentation --}}
<style>
    .detail-row p {
        margin-bottom: 0.25rem;
    }
</style>

@endsection
