@extends('layouts.app')

@section('content')

<div class="container mx-auto p-4">
@php
    // Variabel yang dilewatkan dari Controller adalah $barang (tunggal)
    $isEdit = $barang->exists; 
    
    // Perbarui rute aksi: Gunakan kode_barang sebagai parameter untuk update
    $actionRoute = $isEdit 
        ? route('barangs.update', $barang->kode_barang) 
        : route('barangs.store');

    $header = $isEdit 
        ? 'Edit Barang: ' . $barang->nama_barang
        : 'Tambah Barang Baru';
@endphp
    <div class="bg-white rounded-xl shadow-lg p-6 max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl text-center font-bold text-gray-800">{{ $header }}</h1>
        </div>

        <!-- FORM: Tambahkan enctype untuk file upload -->
        <form action="{{ $actionRoute }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($isEdit)
                @method('PUT')
            @endif

            <!-- ALERT -->
            @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
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
                    value="{{ old('nama_barang', $barang->nama_barang) }}"
                    class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required>
            </div>

            {{-- Foto Produk --}}
            <div class="mb-4">
                <label for="foto_produk" class="block text-sm font-medium text-gray-700 mb-1">Foto Produk (Opsional)</label>
                <!-- Tampilkan foto produk yang sudah ada jika dalam mode edit -->
                @if ($isEdit && $barang->foto_produk)
                    <div class="mb-2">
                        <p class="text-xs text-gray-500">Foto saat ini:</p>
                        <!-- Menggunakan asset() atau url() tergantung konfigurasi storage Anda -->
                        <img src="{{ asset('storage/' . $barang->foto_produk) }}" alt="Foto Produk" class="h-20 w-20 object-cover rounded-lg border border-gray-200">
                    </div>
                @endif
                <input type="file" name="foto_produk" id="foto_produk"
                    class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-lg file:border-0
                    file:text-sm file:font-semibold
                    file:bg-indigo-50 file:text-indigo-700
                    hover:file:bg-indigo-100
                    focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                @error('foto_produk')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                {{-- Kategori --}}
                <div>
                    <label for="kategori_barang_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="kategori_barang_id" id="kategori_barang_id"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($kategoriBarangs as $kategoriBarang)
                            <option value="{{ $kategoriBarang->kategori_barang_id }}" 
                                {{ old('kategori_barang_id', $barang->kategori_barang_id) == $kategoriBarang->kategori_barang_id ? 'selected' : '' }}>
                                {{ $kategoriBarang->nama_kategori_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- Supplier --}}
                <div>
                    <label for="id_supplier" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <select name="id_supplier" id="id_supplier"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id_supplier }}" 
                                {{ old('id_supplier', $barang->id_supplier) == $supplier->id_supplier ? 'selected' : '' }}>
                                {{ $supplier->namaSupplier }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Harga Beli & Harga Jual --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="harga_beli" class="block text-sm font-medium text-gray-700 mb-1">Harga Beli</label>
                    <input type="number" step="0.01" name="harga_beli" id="harga_beli" 
                        value="{{ old('harga_beli', $barang->harga_beli) }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>
                <div>
                    <label for="harga_jual" class="block text-sm font-medium text-gray-700 mb-1">Harga Jual</label>
                    <input type="number" step="0.01" name="harga_jual" id="harga_jual" 
                        value="{{ old('harga_jual', $barang->harga_jual) }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>
            </div>

            {{-- Tipe Harga --}}
            <div class="mb-4">
                <label for="tipe_harga_barang" class="block text-sm font-medium text-gray-700 mb-1">Tipe Harga</label>
                <select name="tipe_harga_barang" id="tipe_harga_barang"
                    class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    @php
                        $selectedTipe = old('tipe_harga_barang', $barang->tipe_harga_barang);
                    @endphp
                    <option value="Eceran" {{ $selectedTipe == 'Eceran' ? 'selected' : '' }}>Eceran</option>
                    <option value="Grosir" {{ $selectedTipe == 'Grosir' ? 'selected' : '' }}>Grosir</option>
                    <option value="Diskon" {{ $selectedTipe == 'Diskon' ? 'selected' : '' }}>Diskon</option>
                </select>
            </div>

            {{-- Stok --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="jumlah_stok" class="block text-sm font-medium text-gray-700 mb-1">
                        Jumlah Stok Awal 
                        @if ($isEdit) <span class="text-xs text-indigo-500">(Tidak dapat diubah)</span> @endif
                    </label>
                    {{-- Input Stok Awal hanya bisa diisi saat mode CREATE --}}
                    <input type="number" name="jumlah_stok" id="jumlah_stok" 
                        value="{{ old('jumlah_stok', $barang->stok->jumlah_stok ?? 0) }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 
                        {{ $isEdit ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                        {{ $isEdit ? 'disabled' : 'required' }}>
                    @if ($isEdit)
                        <p class="text-xs text-gray-500 mt-1">Stok diperbarui melalui transaksi (Masuk/Keluar) bukan form ini.</p>
                    @endif
                </div>
                <div>
                    <label for="jumlah_stok_rusak" class="block text-sm font-medium text-gray-700 mb-1">Stok Rusak (Jika Ada)</label>
                    <input type="number" name="jumlah_stok_rusak" id="jumlah_stok_rusak" 
                        value="{{ old('jumlah_stok_rusak', $barang->stok->jumlah_stok_rusak ?? 0) }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            {{-- Satuan & Jml/Karton --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="satuan_terkecil" class="block text-sm font-medium text-gray-700 mb-1">Satuan Terkecil</label>
                    <select name="satuan_terkecil" id="satuan_terkecil"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                        @php
                            $selectedSatuan = old('satuan_terkecil', $barang->satuan_terkecil);
                        @endphp
                        <option value="pcs" {{ $selectedSatuan == 'pcs' ? 'selected' : '' }}>pcs</option>
                        <option value="renteng" {{ $selectedSatuan == 'renteng' ? 'selected' : '' }}>renteng</option>
                        <option value="pack" {{ $selectedSatuan == 'pack' ? 'selected' : '' }}>pack</option>
                        <option value="karton" {{ $selectedSatuan == 'karton' ? 'selected' : '' }}>karton</option>
                    </select>
                </div>
                <div>
                    <label for="jml_barang_per_karton" class="block text-sm font-medium text-gray-700 mb-1">Jumlah barang per Karton</label>
                    <input type="number" name="jml_barang_per_karton" id="jml_barang_per_karton" 
                        value="{{ old('jml_barang_per_karton', $barang->jml_barang_per_karton) }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>
            </div>


            {{-- Berlaku Mulai & Tgl Kadaluarsa --}}
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="berlaku_mulai" class="block text-sm font-medium text-gray-700 mb-1">Harga Berlaku Mulai</label>
                    <input type="date" name="berlaku_mulai" id="berlaku_mulai" 
                        value="{{ old('berlaku_mulai', $barang->berlaku_mulai ? \Carbon\Carbon::parse($barang->berlaku_mulai)->format('Y-m-d') : date('Y-m-d')) }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>
                <div>
                    <label for="tgl_kadaluarsa" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Kadaluarsa (Stok)</label>
                    <input type="date" name="tgl_kadaluarsa" id="tgl_kadaluarsa" 
                        value="{{ old('tgl_kadaluarsa', $barang->stok->tgl_kadaluarsa ?? '') }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-xs text-gray-500 mt-1">Opsional, jika barang memiliki kadaluarsa.</p>
                </div>
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end space-x-3">
                <a href="{{ route('barangs.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition duration-150">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700 transition duration-150">
                    {{ $isEdit ? 'Perbarui Barang' : 'Simpan Barang' }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
