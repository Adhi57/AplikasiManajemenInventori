@extends('layouts.app')
@section('page-title', isset($pengiriman) ? 'Edit Data Pengiriman' : 'Tambah Data Pengiriman')
@section('content')

<div class="max-w-3xl mx-auto bg-white shadow-lg rounded-lg p-6">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        {{ isset($pengiriman) ? 'Edit Pengiriman' : 'Tambah Pengiriman' }}
    </h1>

    <form 
        action="{{ isset($pengiriman) ? route('pengiriman.update', $pengiriman->pengiriman_id) : route('pengiriman.store') }}" 
        method="POST"
    >
        @csrf
        @if(isset($pengiriman))
            @method('PUT')
        @endif

        {{-- NOMOR SURAT JALAN --}}
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">Nomor Surat Jalan</label>
            <select name="sj_id" class="w-full border rounded-md py-2 px-3" {{ isset($pengiriman) ? 'disabled' : '' }}>
                <option value="">-- Pilih Surat Jalan --</option>
                @foreach($surat_jalans as $sj)
                    <option value="{{ $sj->sj_id }}" 
                        {{ (old('sj_id') ?? $pengiriman->sj_id ?? '') == $sj->sj_id ? 'selected' : '' }}>
                        {{ $sj->sj_id }} - {{ $sj->pelanggan->nama_pelanggan ?? '' }}
                    </option>
                @endforeach
            </select>
            @error('sj_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- DRIVER & NO POLISI --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Nama Driver</label>
                <input type="text" name="nama_driver"
                    value="{{ old('nama_driver', $pengiriman->nama_driver ?? '') }}"
                    class="w-full border rounded-md py-2 px-3">
                @error('nama_driver')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">No Polisi</label>
                <input type="text" name="no_polisi"
                    value="{{ old('no_polisi', $pengiriman->no_polisi ?? '') }}"
                    class="w-full border rounded-md py-2 px-3">
                @error('no_polisi')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- TANGGAL --}}
        <div class="grid grid-cols-2 gap-4 mt-4">
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Pengiriman</label>
                <input type="date" name="tanggal_pengiriman"
                    value="{{ old('tanggal_pengiriman', isset($pengiriman) ? $pengiriman->tanggal_pengiriman : '') }}"
                    class="w-full border rounded-md py-2 px-3">
                @error('tanggal_pengiriman')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai"
                    value="{{ old('tanggal_sampai', isset($pengiriman) ? $pengiriman->tanggal_sampai : '') }}"
                    class="w-full border rounded-md py-2 px-3">
                @error('tanggal_sampai')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- STATUS (hanya saat edit) --}}
        @if(isset($pengiriman))
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Status Pengiriman</label>
            <select name="status_pengiriman" class="w-full border rounded-md py-2 px-3">
                @foreach(['Menunggu', 'Dalam Perjalanan', 'Terkirim', 'Dibatalkan'] as $status)
                    <option value="{{ $status }}" 
                        {{ (old('status_pengiriman', $pengiriman->status_pengiriman) == $status) ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- CATATAN --}}
        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Catatan</label>
            <textarea name="catatan" rows="3" class="w-full border rounded-md py-2 px-3">{{ old('catatan', $pengiriman->catatan ?? '') }}</textarea>
        </div>

        {{-- TOMBOL --}}
        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('pengiriman.index') }}" 
               class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                Batal
            </a>
            <button type="submit" 
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                {{ isset($pengiriman) ? 'Perbarui' : 'Simpan' }}
            </button>
        </div>
    </form>
</div>

@endsection
