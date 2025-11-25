@extends('layouts.app')

@section('page-title', 'Data Master / Barang')

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
    <div class="bg-white rounded-xl shadow-lg p-6 max-w-4xl mx-auto"> {{-- Diperluas ke max-w-4xl untuk menampung grid 2 kolom --}}
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">{{ $header }}</h1>
            <a href="{{ route('barangs.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"></path></svg>
                Kembali ke Daftar
            </a>
        </div>

        <form action="{{ $actionRoute }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if ($isEdit)
            @method('PUT')
            @endif

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
            
            <h2 class="text-xl font-semibold text-gray-700 mb-4 mt-6">Informasi Dasar Barang</h2>
            <hr class="mb-6 border-gray-200">

            {{-- Grid 2 Kolom untuk Info Dasar --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                {{-- Nama Barang (Full Width di Mobile) --}}
                <div class="md:col-span-2">
                    <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-1">Nama Barang</label>
                    <input type="text" name="nama_barang" id="nama_barang"
                        value="{{ old('nama_barang', $barang->nama_barang) }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>

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
            
            <h2 class="text-xl font-semibold text-gray-700 mb-4 mt-6">Informasi Harga & Satuan</h2>
            <hr class="mb-6 border-gray-200">
            
            {{-- Harga Beli & Harga Jual --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="harga_beli" class="block text-sm font-medium text-gray-700 mb-1">Harga Beli per Karton</label>
                    <input type="number" step="0.01" name="harga_beli" id="harga_beli"
                        value="{{ old('harga_beli', $barang->harga_beli) }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>
                <div>
                    <label for="harga_jual" class="block text-sm font-medium text-gray-700 mb-1">Harga Jual per Satuan</label>
                    <input type="number" step="0.01" name="harga_jual" id="harga_jual"
                        value="{{ old('harga_jual', $barang->harga_jual) }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>
            </div>

            {{-- Tipe Harga --}}
            <div class="mb-4">
                <label for="tipe_harga_barang" class="block text-sm font-medium text-gray-700 mb-1">Tipe Harga Jual</label>
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

            {{-- Satuan & Jml/Karton --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="satuan_jual" class="block text-sm font-medium text-gray-700 mb-1">Satuan Jual</label>
                    <select name="satuan_jual" id="satuan_jual"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                        @php
                        $selectedSatuan = old('satuan_jual', $barang->satuan_jual);
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


            <h2 class="text-xl font-semibold text-gray-700 mb-4 mt-6">Periode Harga</h2>
            <hr class="mb-6 border-gray-200">

            {{-- Berlaku Mulai & Berlaku Sampai --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label for="berlaku_mulai" class="block text-sm font-medium text-gray-700 mb-1">Harga Berlaku Mulai</label>
                    <input type="date" name="berlaku_mulai" id="berlaku_mulai"
                        value="{{ old('berlaku_mulai', $barang->berlaku_mulai ? \Carbon\Carbon::parse($barang->berlaku_mulai)->format('Y-m-d') : date('Y-m-d')) }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>
                <div>
                    <label for="berlaku_sampai" class="block text-sm font-medium text-gray-700 mb-1">Harga Berlaku Sampai</label>
                    <input type="date" name="berlaku_sampai" id="berlaku_sampai" {{-- Perbaikan: ubah name dari 'berlaku_mulai' ke 'berlaku_sampai' --}}
                        value="{{ old('berlaku_sampai', $barang->berlaku_sampai ? \Carbon\Carbon::parse($barang->berlaku_sampai)->format('Y-m-d') : date('Y-m-d')) }}" {{-- Perbaikan: ubah old dan $barang->field dari 'berlaku_mulai' ke 'berlaku_sampai' --}}
                        class="py-2.5 sm:py-3 px-4 block w-full border border-gray-300 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                        required>
                </div>
            </div>

            <h2 class="text-xl font-semibold text-gray-700 mb-4 mt-6">Foto Produk</h2>
            <hr class="mb-6 border-gray-200">

            {{-- Foto Produk (Full Width) --}}
            <div class="mb-6">
                <label for="foto_produk" class="block text-sm font-medium text-gray-700 mb-1">Foto Produk (Opsional)</label>
                @if ($isEdit && $barang->foto_produk)
                <div class="mb-2">
                    <p class="text-xs text-gray-500">Foto saat ini:</p>
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

            {{-- Tombol --}}
            <div class="flex justify-end space-x-3 mt-8">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    const satuanSelect = document.getElementById('satuan_jual');
    const jumlahInput = document.getElementById('jml_barang_per_karton');

    function updateJumlahKarton() {
        if (satuanSelect.value === 'karton') {
            jumlahInput.value = 1;
            jumlahInput.readOnly = true; // Tidak bisa diubah manual
            jumlahInput.classList.add('bg-gray-100', 'cursor-not-allowed');
        } else {
            jumlahInput.readOnly = false;
            jumlahInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
            // Opsional: Atur ulang nilai ke kosong/null jika sebelumnya adalah 1 dan sekarang bukan 'karton'
            // if (jumlahInput.value == 1 && !'{{ old('jml_barang_per_karton', $barang->jml_barang_per_karton) }}') {
            //     jumlahInput.value = '';
            // }
        }
    }

    // Jalankan saat halaman pertama kali dimuat
    updateJumlahKarton();

    // Jalankan setiap kali dropdown berubah
    satuanSelect.addEventListener('change', updateJumlahKarton);
});
</script>

@endsection