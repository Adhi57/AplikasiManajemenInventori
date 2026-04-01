@extends('layouts.app')
@section('page-title',  'Data Master / Pelanggan')
@section('content')

<div class="container mx-auto p-6">

    <div class="bg-white shadow rounded-xl max-w-2xl mx-auto p-6">
        <x-page-header title="Detail Pelanggan" description="Detail informasi pelanggan: {{ $pelanggans->nama_pelanggan }}" icon="fa-users">
            <x-slot name="actions">
                <a href="{{ route('pelanggans.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                    <i class="fa-solid fa-arrow-left text-amber-400"></i>
                    <span>Kembali</span>
                </a>
            </x-slot>
        </x-page-header>

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
