@extends('layouts.app')

@section('content')

<div class="container mx-auto p-6">

    <div class="bg-white shadow rounded-xl max-w-2xl mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Detail Pelanggan</h1>
            <a href="{{ route('pelanggans.index') }}" 
               class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg">
               ← Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-800">
            <div>
                <p class="font-semibold text-gray-600">Kode Pelanggan:</p>
                <p class="mb-2">{{ $pelanggans->pelanggan_id }}</p>
            </div>

            <div>
                <p class="font-semibold text-gray-600">Nama Pelanggan:</p>
                <p class="mb-2">{{ $pelanggans->nama_pelanggan }}</p>
            </div>

            <div class="md:col-span-2">
                <p class="font-semibold text-gray-600">Alamat:</p>
                <p class="mb-2">{{ $pelanggans->alamat }}</p>
            </div>

            <div>
                <p class="font-semibold text-gray-600">NPWP:</p>
                <p class="mb-2">{{ $pelanggans->NPWP }}</p>
            </div>

            <div>
                <p class="font-semibold text-gray-600">PIC (Penanggung Jawab):</p>
                <p class="mb-2">{{ $pelanggans->PIC }}</p>
            </div>

            <div>
                <p class="font-semibold text-gray-600">Kategori Pelanggan:</p>
                <p class="mb-2">{{ $pelanggans->kategori_pelanggan->kategori_pelanggan ?? '-' }}</p>
            </div>

            <div>
                <p class="font-semibold text-gray-600">Tipe Harga:</p>
                <p class="mb-2">{{ $pelanggans->tipe_harga ?? '-' }}</p>
            </div>
        </div>

        <div class="flex justify-end mt-6 space-x-3">
            <a href="{{ route('pelanggans.edit', $pelanggans->pelanggan_id) }}"
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow">
                Edit
            </a>
            <form action="{{ route('pelanggans.destroy', $pelanggans->pelanggan_id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pelanggan ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow">
                    Hapus
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
