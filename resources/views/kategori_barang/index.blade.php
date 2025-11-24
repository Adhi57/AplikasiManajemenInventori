@extends('layouts.app')
@section('page-title', 'Data Master / Kategori Barang')
@section('content')

<!-- Main Container -->
<div class="p-4 sm:p-6 bg-gray-50 rounded-xl shadow-inner min-h-[80vh]">

    <div class="flex flex-col lg:flex-row gap-6">

        <!-- KIRI: TABEL DAFTAR KATEGORI -->
        <div class="lg:flex-1 bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200">
            <div class="p-6">
                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg shadow-sm font-medium" role="alert">
                    {{ session('success') }}
                </div>
                @endif
        
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Daftar Kategori Barang</h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700" id="kategori-table">
                        <thead class="text-xs text-white uppercase bg-red-700 border-b-4 border-red-500">
                            <tr>
                                <th class="px-6 py-3 rounded-tl-xl">ID</th>
                                <th class="px-6 py-3">Nama Kategori</th>
                                <th class="px-6 py-3 text-center rounded-tr-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kategori_barang as $kat)
                            <tr class="text-left text-xs border-b even:bg-red-50 odd:bg-white hover:bg-red-100 transition duration-100">
                                <td class="px-6 py-3 font-semibold">{{ $kat->kategori_barang_id }}</td>
                                <td class="px-6 py-3 font-medium">{{ $kat->nama_kategori_barang }}</td>
                                
                                <!-- Aksi -->
                                <td class="px-6 py-3 text-center whitespace-nowrap">
                                    <!-- EDIT -->
                                    <a href="{{ route('kategori_barang.edit', $kat->kategori_barang_id) }}" 
                                      class="inline-block text-gray-500 hover:text-amber-600 transition duration-150 p-1"
                                      title="Edit Kategori">
                                      <i class="fa-solid fa-pen-to-square cursor-pointer text-sm"></i>
                                    </a>
        
                                    <!-- Delete -->
                                    <form action="{{ route('kategori_barang.destroy', $kat->kategori_barang_id) }}" 
                                      method="POST" 
                                      class="inline-block ml-2"
                                      onsubmit="return confirm('Yakin ingin menghapus kategori {{ $kat->nama_kategori_barang }}? Aksi ini tidak dapat dibatalkan.');">
                                      @csrf
                                      @method('DELETE')
                                      <button type="submit" class="text-gray-500 hover:text-red-700 transition duration-150 p-1" title="Hapus Kategori">
                                        <i class="fa-solid fa-trash-can cursor-pointer text-sm"></i>
                                      </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- KANAN: FORM TAMBAH/EDIT -->
        <div class="lg:w-96 bg-white p-6 rounded-xl shadow-xl border border-gray-200">
            @php
                $isEdit = isset($editKategori);
                $kategori = $isEdit ? $editKategori : new \App\Models\KategoriBarang(); // Pastikan \App\Models\KategoriBarang
                $actionRoute = $isEdit 
                    ? route('kategori_barang.update', $kategori->kategori_barang_id)
                    : route('kategori_barang.store');
            @endphp

            <h2 class="text-xl font-bold mb-6 text-gray-800 border-b pb-2">{{ $isEdit ? 'Edit Kategori Barang' : 'Tambah Kategori Barang' }}</h2>

            <form action="{{ $actionRoute }}" method="POST">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <div class="mb-6">
                    <label for="nama_kategori_barang" class="block mb-2 font-medium text-gray-700">Nama Kategori</label>
                    <input type="text" name="nama_kategori_barang" id="nama_kategori_barang"
                           value="{{ old('nama_kategori_barang', $kategori->nama_kategori_barang) }}"
                           class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-red-500 focus:border-red-500 transition duration-150 shadow-sm" required>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <!-- Tombol Batal akan selalu ada, mengarahkan kembali ke index -->
                    <a href="{{ route('kategori_barang.index') }}" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition duration-150">
                        <i class="fa-solid fa-xmark mr-1"></i> Batal
                    </a>
                    
                    <button type="submit" 
                        class="px-4 py-2 bg-red-700 text-white font-semibold rounded-lg shadow-md shadow-red-300 hover:bg-red-800 transition duration-150">
                        <i class="fa-solid {{ $isEdit ? 'fa-save' : 'fa-plus' }} mr-1"></i>
                        {{ $isEdit ? 'Simpan Perubahan' : 'Tambah Kategori' }}
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>

@endsection