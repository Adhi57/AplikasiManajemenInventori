@extends('layouts.app')
@section('page-title', isset($pengiriman) ? 'Edit Data Pengiriman' : 'Tambah Data Pengiriman')
@section('content')

<div class="max-w-4xl mx-auto mt-8">
    <div class="bg-white shadow-xl rounded-xl p-8">
        <h1 class="text-3xl font-extrabold mb-8 text-indigo-800 border-b pb-3">
            {{ isset($pengiriman) ? 'Edit Data Pengiriman' : 'Tambah Data Pengiriman Baru' }}
        </h1>

        <form 
            action="{{ isset($pengiriman) ? route('pengiriman.update', $pengiriman->pengiriman_id) : route('pengiriman.store') }}" 
            method="POST"
        >
            @csrf
            @if(isset($pengiriman))
                @method('PUT')
            @endif

            {{-- Bagian 1: Informasi Surat Jalan --}}
            <div class="mb-6 p-4 border rounded-lg bg-indigo-50/50">
                <h2 class="text-xl font-semibold mb-4 text-indigo-700">Detail Rute</h2>
                
                {{-- NOMOR SURAT JALAN --}}
                <div class="mb-4">
                    <label for="sj_id" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Surat Jalan</label>
                    <select 
                        name="sj_id" 
                        id="sj_id"
                        class="w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out bg-white appearance-none 
                        {{ isset($pengiriman) ? 'bg-gray-100 cursor-not-allowed' : '' }}" 
                        {{ isset($pengiriman) ? 'disabled' : '' }}
                    >
                        <option value="">-- Pilih Surat Jalan --</option>
                        @foreach($surat_jalans as $sj)
                            
                            {{-- LOGIKA BARU: Pastikan status Disetujui DAN belum ada di tabel pengirimans --}}
                            @php
                                $is_selected_in_edit = isset($pengiriman) && $pengiriman->sj_id == $sj->sj_id;
                                $is_not_sent = !in_array($sj->sj_id, $existing_sj_ids ?? []);
                            @endphp

                            @if($sj->status == 'Disetujui' && ($is_not_sent || $is_selected_in_edit))
                            <option value="{{ $sj->sj_id }}"
                                {{ (old('sj_id') ?? $pengiriman->sj_id ?? '') == $sj->sj_id ? 'selected' : '' }}>
                                {{ $sj->sj_id }} - {{ $sj->pelanggan->nama_pelanggan ?? 'Pelanggan Tidak Ditemukan' }}
                            </option>
                            @endif
                            
                        @endforeach
                    </select>
                    @error('sj_id')
                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- TANGGAL PENGIRIMAN & SAMPAI --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="tanggal_pengiriman" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Mulai Pengiriman</label>
                        <input type="date" name="tanggal_pengiriman" id="tanggal_pengiriman"
                            value="{{ old('tanggal_pengiriman', isset($pengiriman) ? $pengiriman->tanggal_pengiriman : '') }}"
                            class="w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                        @error('tanggal_pengiriman')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="tanggal_sampai" class="block text-sm font-semibold text-gray-700 mb-1">Tanggal Sampai Tujuan (Estimasi) </label>
                        <input type="date" name="tanggal_sampai" id="tanggal_sampai"
                            value="{{ old('tanggal_sampai', isset($pengiriman) ? $pengiriman->tanggal_sampai : '') }}"
                            class="w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                        @error('tanggal_sampai')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Bagian 2: Informasi Kendaraan dan Driver --}}
            <div class="mb-6 p-4 border rounded-lg">
                <h2 class="text-xl font-semibold mb-4 text-indigo-700">Informasi Kendaraan</h2>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Nama Kendaraan --}}
                    <div>
                        <label for="nama_kendaraan" class="block text-sm font-semibold text-gray-700 mb-1">Nama Kendaraan</label>
                        <input type="text" name="nama_kendaraan" id="nama_kendaraan"
                            value="{{ old('nama_kendaraan', $pengiriman->nama_kendaraan ?? '') }}"
                            placeholder="Contoh: Colt Diesel"
                            class="w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                        @error('nama_kendaraan')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Nama Driver --}}
                    <div>
                        <label for="nama_driver" class="block text-sm font-semibold text-gray-700 mb-1">Nama Driver</label>
                        <input type="text" name="nama_driver" id="nama_driver"
                            value="{{ old('nama_driver', $pengiriman->nama_driver ?? '') }}"
                            placeholder="Contoh: Budi Santoso"
                            class="w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">
                        @error('nama_driver')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    {{-- No Polisi --}}
                    <div>
                        <label for="no_polisi" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Polisi</label>
                        <input type="text" name="no_polisi" id="no_polisi"
                            value="{{ old('no_polisi', $pengiriman->no_polisi ?? '') }}"
                            placeholder="Contoh: B 1234 XY"
                            class="w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out uppercase">
                        @error('no_polisi')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Bagian 3: Status dan Catatan --}}
            <div class="mb-6 p-4 border rounded-lg bg-gray-50/50">
                <h2 class="text-xl font-semibold mb-4 text-indigo-700">Status & Catatan</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- STATUS (hanya saat edit) --}}
                    @if(isset($pengiriman))
                    <div>
                        <label for="status_pengiriman" class="block text-sm font-semibold text-gray-700 mb-1">Status Pengiriman</label>
                        <select name="status_pengiriman" id="status_pengiriman" class="w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out bg-white appearance-none">
                            @foreach(['Menunggu', 'Dalam Perjalanan', 'Dibatalkan'] as $status)
                            <option value="{{ $status }}"
                                {{ (old('status_pengiriman', $pengiriman->status_pengiriman) == $status) ? 'selected' : '' }}>
                                {{ $status }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>

                {{-- CATATAN --}}
                <div class="mt-4">
                    <label for="catatan" class="block text-sm font-semibold text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan" id="catatan" rows="3" 
                        placeholder="Tambahkan catatan penting terkait pengiriman..."
                        class="w-full border border-gray-300 rounded-lg py-2.5 px-4 focus:ring-indigo-500 focus:border-indigo-500 transition duration-150 ease-in-out">{{ old('catatan', $pengiriman->catatan ?? '') }}</textarea>
                </div>
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="mt-8 flex justify-end space-x-4 border-t pt-4">
                <a href="{{ route('pengiriman.index') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition duration-150 ease-in-out">
                    Batal
                </a>
                <button type="submit" 
                    class="px-6 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition duration-150 ease-in-out shadow-md hover:shadow-lg">
                    <i class="fas fa-save mr-2"></i> {{ isset($pengiriman) ? 'Perbarui Data' : 'Simpan Pengiriman' }}
                </button>
            </div>
        </form>
    </div>
</div>

@endsection