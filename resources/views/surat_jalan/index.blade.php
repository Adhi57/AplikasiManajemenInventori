@extends('layouts.app')
@section('page-title', 'Surat Jalan dan Pengiriman Barang')
@section('content')

<div class="min-h-screen bg-gray-50 py-6 px-4">
    <div class="max-w-6xl mx-auto bg-white shadow-lg rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Daftar Surat Jalan</h1>
            <a href="{{ route('surat_jalan.create') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                + Tambah Surat Jalan
            </a>
        </div>

        @if (session('success'))
        <div class="bg-green-100 text-green-700 px-4 py-2 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <form method="GET" class="flex gap-2 mb-4">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari SJ atau pelanggan..."
                class="flex-1 border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
            <select name="status" class="border-gray-300 rounded-md px-3 py-2">
                <option value="">Semua Status</option>
                <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
            </select>
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Filter</button>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600 border">
                <thead class="bg-gray-100 text-gray-800 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-2">#</th>
                        <th class="px-4 py-2">ID Surat Jalan</th>
                        <th class="px-4 py-2">Tanggal</th>
                        <th class="px-4 py-2">Pelanggan</th>
                        <th class="px-4 py-2">Status</th>
                        <th class="px-4 py-2">Dibuat Oleh</th>
                        <th class="px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($suratJalans as $i => $sj)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $loop->iteration }}</td>
                        <td class="px-4 py-2 font-semibold text-gray-800">{{ $sj->sj_id }}</td>
                        <td class="px-4 py-2">{{ \Carbon\Carbon::parse($sj->tanggal_surat)->format('d/m/Y') }}</td>
                        <td class="px-4 py-2">{{ $sj->pelanggan->nama_pelanggan ?? '-' }}</td>
                        <td class="px-4 py-2">
                            <span class="px-2 py-1 rounded text-xs font-semibold
                                    {{ $sj->status == 'Pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $sj->status == 'Disetujui' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $sj->status == 'Ditolak' ? 'bg-red-100 text-red-700' : '' }}">
                                {{ $sj->status }}
                            </span>
                        </td>
                        <td class="px-4 py-2">{{ $sj->user->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-2 text-center space-x-2">
                            {{-- Tombol Detail --}}
                            <a href="{{ route('surat_jalan.show', $sj->sj_id) }}"
                                class="text-indigo-600 hover:underline">Detail</a>

                            {{-- Tombol Kirimkan Barang --}}
                            @if ($sj->status === 'Disetujui')
                            <form action="#" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="submit"
                                    class="text-white bg-green-500 hover:bg-green-700 px-3 py-1 rounded-md text-xs font-semibold transition">
                                    Kirimkan Barang
                                </button>
                            </form>
                            @endif
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-gray-500">Tidak ada data surat jalan</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $suratJalans->links() }}
        </div>
    </div>
</div>
@endsection