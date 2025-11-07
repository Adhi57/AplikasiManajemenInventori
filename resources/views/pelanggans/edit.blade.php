@extends('layouts.app')
@section('page-title',  'Edit Pelanggan')
@section('content')

<div class="container mx-auto p-4">
    <div class="bg-white rounded-xl shadow-xs p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl text-center font-bold text-gray-800 mb-6">Edit Data Pelanggan</h1>

        @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

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

        <form action="{{ route('pelanggans.update', $pelanggan->pelanggan_id) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Nama Pelanggan --}}
            <div class="mb-4">
                <label for="nama_pelanggan" class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                <input type="text" name="nama_pelanggan" id="nama_pelanggan"
                    value="{{ old('nama_pelanggan', $pelanggan->nama_pelanggan) }}"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('nama_pelanggan') border-red-500 @enderror"
                    required>
                @error('nama_pelanggan')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Alamat --}}
            <div class="mb-4">
                <label for="alamat" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="alamat" id="alamat" rows="3"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('alamat') border-red-500 @enderror"
                    required>{{ old('alamat', $pelanggan->alamat) }}</textarea>
                @error('alamat')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- NPWP --}}
            <div class="mb-4">
                <label for="NPWP" class="block text-sm font-medium text-gray-700 mb-1">NPWP</label>
                <input type="text" name="NPWP" id="NPWP"
                    value="{{ old('NPWP', $pelanggan->NPWP) }}"
                    maxlength="20"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('NPWP') border-red-500 @enderror">
                @error('NPWP')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- PIC --}}
            <div class="mb-4">
                <label for="PIC" class="block text-sm font-medium text-gray-700 mb-1">PIC</label>
                <input type="text" name="PIC" id="PIC"
                    value="{{ old('PIC', $pelanggan->PIC) }}"
                    maxlength="50"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('PIC') border-red-500 @enderror">
                @error('PIC')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Kategori Pelanggan --}}
            <div class="mb-4">
                <label for="kategori_pelanggan_id" class="block text-sm font-medium text-gray-700 mb-1">Kategori Pelanggan</label>
                <select name="kategori_pelanggan_id" id="kategori_pelanggan_id"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('kategori_pelanggan_id') border-red-500 @enderror"
                    required>
                    <option value="">-- Pilih Kategori --</option>
                    @foreach($kategori_pelanggans as $kategori)
                    <option value="{{ $kategori->kategori_pelanggan_id }}"
                        {{ old('kategori_pelanggan_id', $pelanggan->kategori_pelanggan_id) == $kategori->kategori_pelanggan_id ? 'selected' : '' }}>
                        {{ $kategori->kategori_pelanggan }}
                    </option>
                    @endforeach
                </select>
                @error('kategori_pelanggan_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tipe Harga --}}
            <div class="mb-4">
                <label for="tipe_harga" class="block text-sm font-medium text-gray-700 mb-1">Tipe Harga</label>
                <select name="tipe_harga" id="tipe_harga"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm focus:ring-indigo-500 focus:border-indigo-500 @error('tipe_harga') border-red-500 @enderror">
                    <option value="Eceran" {{ old('tipe_harga', $pelanggan->tipe_harga) == 'Eceran' ? 'selected' : '' }}>Eceran</option>
                    <option value="Grosir" {{ old('tipe_harga', $pelanggan->tipe_harga) == 'Grosir' ? 'selected' : '' }}>Grosir</option>
                    <option value="Diskon" {{ old('tipe_harga', $pelanggan->tipe_harga) == 'Diskon' ? 'selected' : '' }}>Diskon</option>
                </select>
                @error('tipe_harga')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex justify-end space-x-3">
                <a href="{{ route('pelanggans.index') }}"
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300">
                    Batal
                </a>
                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow-md hover:bg-indigo-700">
                    Update Pelanggan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
