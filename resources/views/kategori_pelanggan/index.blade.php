@extends('layouts.app')
@section('page-title', 'Data Master / Kategori Pelanggan')
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
        
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Daftar Kategori Pelanggan</h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-700" id="kategori-table">
                        <thead class="text-xs text-white uppercase bg-red-700 border-b-4 border-red-500">
                            <tr>
                                <th class="px-6 py-3 rounded-tl-xl">ID</th>
                                <th class="px-6 py-3">Nama Kategori</th>
                                <th class="px-6 py-3">Jumlah Diskon</th>
                                <th class="px-6 py-3 text-center rounded-tr-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($kategori_pelanggan as $kat)
                            <tr class="text-left text-xs border-b even:bg-red-50 odd:bg-white hover:bg-red-100 transition duration-100">
                                <td class="px-6 py-2 font-semibold">{{ $kat->kategori_pelanggan_id }}</td>
                                <td class="px-6 py-2">
                                    <span class="font-medium px-2 py-0.5 rounded-full text-white text-[10px] uppercase tracking-wider
                                        @if($kat->kategori_pelanggan == 'Retail') bg-blue-600 shadow-md shadow-blue-300
                                        @elseif($kat->kategori_pelanggan == 'Grosir') bg-green-600 shadow-md shadow-green-300
                                        @else bg-gray-500 shadow-md shadow-gray-300 @endif">
                                        {{ $kat->kategori_pelanggan }}
                                    </span>
                                </td>
                                <td class="px-6 py-2 text-base font-bold text-red-700">{{ $kat->jumlah_diskon }}%</td>
                                
                                <!-- Aksi -->
                                <td class="px-6 py-2 text-center whitespace-nowrap">
                                    <!-- EDIT -->
                                    <a href="{{ route('kategori_pelanggan.edit', $kat->kategori_pelanggan_id) }}" 
                                      class="inline-block text-gray-500 hover:text-amber-600 transition duration-150 p-1"
                                      title="Edit Kategori">
                                      <i class="fa-solid fa-pen-to-square cursor-pointer text-sm"></i>
                                    </a>
        
                                    <!-- Delete -->
                                    <form action="{{ route('kategori_pelanggan.destroy', $kat->kategori_pelanggan_id) }}" 
                                      method="POST" 
                                      class="inline-block ml-2"
                                      onsubmit="return confirm('Yakin ingin menghapus kategori {{ $kat->kategori_pelanggan }}? Aksi ini tidak dapat dibatalkan.');">
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
            $kategori = $isEdit ? $editKategori : new \App\Models\Kategori_Pelanggan();
            $actionRoute = $isEdit
                ? route('kategori_pelanggan.update', $kategori->kategori_pelanggan_id)
                : route('kategori_pelanggan.store');

            // Contoh enum (pastikan ini sesuai dengan model/migrasi Anda)
            $enumValues = ['Retail', 'Grosir', 'Biasa'];
            @endphp

            <h2 class="text-xl font-bold mb-6 text-gray-800 border-b pb-2">{{ $isEdit ? 'Edit Kategori Pelanggan' : 'Tambah Kategori Pelanggan' }}</h2>

            <form action="{{ $actionRoute }}" method="POST">
                @csrf
                @if($isEdit)
                    @method('PUT')
                @endif

                <!-- Nama Kategori (Select/Enum) -->
                <div class="mb-4">
                    <label for="kategori_pelanggan" class="block mb-2 font-medium text-gray-700">Nama Kategori</label>
                    <select name="kategori_pelanggan" id="kategori_pelanggan"
                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-red-500 focus:border-red-500 transition duration-150 shadow-sm" required>
                        <option value="">-- Pilih Kategori Pelanggan --</option>
                        @foreach($enumValues as $value)
                            <option value="{{ $value }}" {{ old('kategori_pelanggan', $kategori->kategori_pelanggan) == $value ? 'selected' : '' }}>
                                {{ $value }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Jumlah Diskon -->
                <div class="mb-6">
                    <label for="jumlah_diskon" class="block mb-2 font-medium text-gray-700">Jumlah Diskon (%)</label>
                    <input type="number" name="jumlah_diskon" id="jumlah_diskon"
                        class="w-full border border-gray-300 rounded-lg p-2.5 focus:ring-red-500 focus:border-red-500 transition duration-150 shadow-sm"
                        value="{{ old('jumlah_diskon', $kategori->jumlah_diskon) }}"
                        min="0" max="100" required>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    @if($isEdit)
                    <a href="{{ route('kategori_pelanggan.index') }}" 
                        class="px-4 py-2 bg-gray-200 text-gray-700 font-medium rounded-lg hover:bg-gray-300 transition duration-150">
                        <i class="fa-solid fa-xmark mr-1"></i> Batal Edit
                    </a>
                    @endif
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