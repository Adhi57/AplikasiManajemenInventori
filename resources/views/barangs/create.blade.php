@extends('layouts.app')

@section('content')

<div class="container mx-auto p-4">

    <div class="bg-white rounded-xl shadow-xs p-6 max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl text-center font-bold text-gray-800">Tambah Barang Baru</h1>
        </div>

        <form action="{{ route('barangs.store') }}" method="POST">
            @csrf

            {{-- Success Message --}}
            @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            {{-- Error Messages --}}
            @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">Ada masalah dengan input Anda.</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Nama Barang --}}
            <div class="mb-4">
                <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                <input type="text" name="nama_barang" id="nama_barang"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required>
            </div>

            {{-- Kategori & Supplier --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="kategori_barang_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="kategori_barang_id" id="kategori_barang_id"
                        class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriBarangs as $kategoriBarang)
                            <option value="{{ $kategoriBarang->kategori_barang_id }}">{{ $kategoriBarang->nama_kategori_barang }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="id_supplier" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <select name="id_supplier" id="id_supplier"
                        class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id_supplier }}">{{ $supplier->namaSupplier }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Harga Beli & Harga Jual --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="harga_beli" class="block text-sm font-medium text-gray-700 mb-1">Harga Beli</label>
                    <input type="number" step="0.01" name="harga_beli" id="harga_beli"
                        class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>
                <div>
                    <label for="harga_jual" class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                    <input type="number" step="0.01" name="harga_jual" id="harga_jual"
                        class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>
            </div>

            {{-- Tipe Harga --}}
            <div class="mb-4">
                <label for="tipe_harga_barang" class="block text-sm font-medium text-gray-700 mb-1">Tipe Harga</label>
                <select name="tipe_harga_barang" id="tipe_harga_barang"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="Eceran">Eceran</option>
                    <option value="Grosir">Grosir</option>
                    <option value="Diskon">Diskon</option>
                </select>
            </div>

            {{-- Stok --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="jumlah_stok" class="block text-sm font-medium text-gray-700 mb-1">Jumlah Stok Awal</label>
                    <input type="number" name="jumlah_stok" id="jumlah_stok"
                        class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label for="jumlah_stok_rusak" class="block text-sm font-medium text-gray-700 mb-1">Stok Rusak (Jika Ada)</label>
                    <input type="number" name="jumlah_stok_rusak" id="jumlah_stok_rusak"
                        class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            {{-- Satuan --}}
            <div class="mb-4">
                <label for="nama_satuan" class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                <select name="nama_satuan" id="nama_satuan"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="pcs">pcs</option>
                    <option value="renteng">renteng</option>
                    <option value="pack">pack</option>
                    <option value="karton">karton</option>
                </select>
            </div>

            {{-- Berlaku Mulai --}}
            <div class="mb-6">
                <label for="berlaku_mulai" class="block text-sm font-medium text-gray-700 mb-1">Berlaku Mulai</label>
                <input type="date" name="berlaku_mulai" id="berlaku_mulai"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required>
            </div>

            {{-- Tgl Kadaluarsa --}}
            <div class="mb-6">
                <label for="berlaku_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kadaluarsa</label>
                <input type="date" name="tgl_kadaluarsa" id="berlaku_mulai"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required>
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end space-x-3">
                <a href="{{ route('barangs.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700">
                    Simpan Barang
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
