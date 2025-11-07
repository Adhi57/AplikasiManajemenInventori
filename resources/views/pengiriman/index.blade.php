@extends('layouts.app')
@section('page-title', 'Daftar Pengiriman')
@section('content')

<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Pengiriman Barang</h1>
            <a href="{{ route('pengiriman.create') }}"
               class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
               + Tambah Pengiriman
            </a>
        </div>

        {{-- Pesan sukses --}}
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-700 border">
                <thead class="bg-gray-100 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Nomor Surat Jalan</th>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Driver</th>
                        <th class="px-4 py-3">Kendaraan</th>
                        <th class="px-4 py-3">Tanggal Kirim</th>
                        <th class="px-4 py-3">Tanggal Sampai</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pengirimans as $index => $p)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">{{ $loop->iteration + ($pengirimans->currentPage() - 1) * $pengirimans->perPage() }}</td>
                            <td class="px-4 py-3 font-medium text-indigo-600">
                                {{ $p->sj_id }}
                            </td>
                            <td class="px-4 py-3">{{ $p->suratJalan->pelanggan->nama_pelanggan ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $p->nama_driver ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $p->no_polisi ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $p->tanggal_pengiriman ? date('d/m/Y', strtotime($p->tanggal_pengiriman)) : '-' }}</td>
                            <td class="px-4 py-3">{{ $p->tanggal_sampai ? date('d/m/Y', strtotime($p->tanggal_sampai)) : '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $color = match($p->status_pengiriman) {
                                        'Menunggu' => 'bg-yellow-100 text-yellow-800',
                                        'Dalam Perjalanan' => 'bg-blue-100 text-blue-800',
                                        'Terkirim' => 'bg-green-100 text-green-800',
                                        'Dibatalkan' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span class="px-3 py-1 text-xs font-medium rounded-full {{ $color }}">
                                    {{ $p->status_pengiriman }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center space-x-2">
                                <a href="{{ route('pengiriman.show', $p->pengiriman_id) }}" 
                                   class="text-blue-600 hover:text-blue-800">Detail</a>
                                <a href="{{ route('pengiriman.edit', $p->pengiriman_id) }}" 
                                   class="text-yellow-600 hover:text-yellow-800">Edit</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-6 text-gray-400">Belum ada data pengiriman</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $pengirimans->links() }}
        </div>
    </div>
</div>

@endsection
