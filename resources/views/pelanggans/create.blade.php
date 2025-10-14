@extends('layouts.app')

@section('content')

<div class="container mx-auto p-4">

    <div class="bg-white rounded-xl shadow-xs p-6 max-w-2xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl text-center font-bold text-gray-800">Tambah Pelanggan Baru</h1>
        </div>

        <form action="{{ route('pelanggans.store') }}" method="POST">
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

            {{-- Nama Pelanggan --}}
            <div class="mb-4">
                <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                <input type="text" name="nama_pelanggan" id="nama_pelanggan"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required>
            </div>

            {{-- Alamat --}}
            <div class="mb-4">
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required></textarea>
            </div>

            {{-- NPWP --}}
            <div class="mb-4">
                <label for="NPWP" class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
                <input type="text" name="NPWP" id="NPWP"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- PIC --}}
            <div class="mb-4">
                <label for="PIC" class="block text-sm font-medium text-gray-700 mb-1">PIC</label>
                <input type="text" name="PIC" id="PIC"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            {{-- Kategori Pelanggan --}}
            <div class="mb-4">
                <label for="kategori_pelanggan_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori Pelanggan</label>
                <select name="kategori_pelanggan_id" id="kategori_pelanggan_id"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500"
                    required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategori_pelanggans as $kategori)
                        <option value="{{ $kategori->kategori_pelanggan_id }}">{{ $kategori->kategori_pelanggan }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Tipe Harga --}}
            <div class="mb-4">
                <label for="tipe_harga" class="block text-sm font-medium text-gray-700 mb-1">Tipe Harga</label>
                <select name="tipe_harga" id="tipe_harga"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="Eceran">Eceran</option>
                    <option value="Grosir">Grosir</option>
                    <option value="Diskon">Diskon</option>
                </select>
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end space-x-3">
                <a href="{{ route('pelanggans.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700">
                    Simpan Pelanggan
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
