@extends('layouts.app')

@section('page-title', 'Detail Pengiriman: ' . ($pengiriman->sj_id ?? 'N/A'))

@section('content')

<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-5xl mx-auto">
        <div class="bg-white shadow-xl rounded-lg overflow-hidden">

            {{-- Header --}}
            <div class="p-6 bg-gradient-to-t from-red-600 to-red-700 text-white flex justify-between items-center">
                <h1 class="text-2xl font-bold">Detail Pengiriman</h1>
                <a href="{{ route('pengiriman.index') }}" 
                   class="bg-white text-indigo-600 px-4 py-2 rounded-lg font-medium hover:bg-indigo-100 transition">
                    &larr; Kembali ke Daftar
                </a>
            </div>

            {{-- Konten Detail --}}
            <div class="p-6 space-y-8">
                
                {{-- Bagian 1: Status dan Aksi --}}
                <div class="border-b pb-4 flex justify-between items-center">
                    <div class="flex items-center space-x-4">
                        <span class="text-lg font-semibold text-gray-700">Nomor Surat Jalan:</span>
                        <span class="text-xl font-extrabold text-indigo-700">{{ $pengiriman->sj_id ?? 'N/A' }}</span>
                    </div>

                    <div class="flex items-center space-x-4">
                        @php
                            $color = match($pengiriman->status_pengiriman) {
                                'Menunggu' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                                'Dalam Perjalanan' => 'bg-blue-100 text-blue-800 border-blue-300',
                                'Terkirim' => 'bg-green-100 text-green-800 border-green-300',
                                'Dibatalkan' => 'bg-red-100 text-red-800 border-red-300',
                                default => 'bg-gray-100 text-gray-800 border-gray-300'
                            };
                        @endphp
                        <span class="text-base font-semibold">Status Saat Ini:</span>
                        <span class="px-4 py-1.5 text-sm font-bold rounded-full border {{ $color }} shadow-sm">
                            {{ $pengiriman->status_pengiriman }}
                        </span>

                        {{-- Tombol Edit --}}
                        <a href="{{ route('pengiriman.edit', $pengiriman->pengiriman_id) }}" 
                           class="text-sm font-medium bg-yellow-400 px-3 py-1.5 rounded-lg shadow-sm hover:bg-yellow-500">
                            <i class="fas fa-edit mr-1"></i> Edit
                        </a>

                        {{-- Tombol Tandai Terkirim --}}
                        @if($pengiriman->status_pengiriman !== 'Terkirim')
                            <form action="{{ route('pengiriman.updateStatus', $pengiriman->pengiriman_id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status_pengiriman" value="Terkirim">
                                <button type="submit" 
                                    class="text-sm font-medium bg-green-600 text-white px-3 py-1.5 rounded-lg shadow-sm hover:bg-green-700 transition">
                                    <i class="fas fa-check mr-1"></i> Tandai Terkirim
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                {{-- Bagian 2: Data Pengiriman --}}
                <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Informasi Pengiriman</h2>
                <div class="grid grid-cols-2 gap-y-4 gap-x-8 text-gray-700">
                    <div class="my-3">
                        <p class="font-medium text-sm text-gray-500">Tanggal Mulai Pengiriman</p>
                        <p class="text-lg">{{ $pengiriman->tanggal_pengiriman ? date('d F Y', strtotime($pengiriman->tanggal_pengiriman)) : '-' }}</p>
                    </div>

                    <div class="my-3">
                        <p class="font-medium text-sm text-gray-500">Tanggal Sampai Tujuan</p>
                        <p class="text-lg">{{ $pengiriman->tanggal_sampai ? date('d F Y', strtotime($pengiriman->tanggal_sampai)) : '-' }}</p>
                    </div>

                    <div class="my-3">
                        <p class="font-medium text-sm text-gray-500">Nama Kendaraan</p>
                        <p class="text-lg">{{ $pengiriman->nama_kendaraan ?? '-' }}</p>
                    </div>

                    <div class="my-3">
                        <p class="font-medium text-sm text-gray-500">Nomor Polisi</p>
                        <p class="text-lg font-mono uppercase">{{ $pengiriman->no_polisi ?? '-' }}</p>
                    </div>

                    <div class="my-3">
                        <p class="font-medium text-sm text-gray-500">Nama Driver</p>
                        <p class="text-lg">{{ $pengiriman->nama_driver ?? '-' }}</p>
                    </div>
                </div>

                {{-- Catatan --}}
                <div class="pt-4 border-t">
                    <p class="font-medium text-sm text-gray-500 mb-1">Catatan Pengiriman</p>
                    <div class="p-3 bg-gray-50 border rounded-lg italic text-gray-600 whitespace-pre-wrap">
                        {{ $pengiriman->catatan ?? 'Tidak ada catatan.' }}
                    </div>
                </div>

                {{-- Bagian 3: Detail Surat Jalan --}}
                @if (isset($pengiriman->suratJalan))
                    <div class="pt-6 border-t border-indigo-200">
                        <h2 class="text-xl font-bold text-indigo-700 mb-4">Informasi Surat Jalan Terkait</h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-indigo-50/50 rounded-lg">
                            <div>
                                <p class="font-medium text-sm text-gray-500">Pelanggan</p>
                                <p class="text-lg font-semibold">{{ $pengiriman->suratJalan->pelanggan->nama_pelanggan ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-sm text-gray-500">Tanggal Dibuat SJ</p>
                                <p class="text-lg">{{ $pengiriman->suratJalan->tanggal_sj ? date('d F Y', strtotime($pengiriman->suratJalan->tanggal_sj)) : 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-sm text-gray-500">Status Persetujuan SJ</p>
                                <p class="text-lg font-semibold text-green-700">{{ $pengiriman->suratJalan->status ?? 'N/A' }}</p>
                            </div>
                        </div>

                        <div class="mt-4">
                            <a href="{{ route('surat_jalan.show', $pengiriman->suratJalan->sj_id) }}" 
                               class="text-sm text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                                Lihat Detail Surat Jalan Lengkap &rarr;
                            </a>
                        </div>
                    </div>
                @endif
                
            </div>
        </div>
    </div>
</div>

@endsection
