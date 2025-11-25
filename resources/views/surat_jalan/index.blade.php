@extends('layouts.app')
@section('page-title', 'Surat Jalan dan Pengiriman Barang')
@section('content')

<div class="min-h-scree py-8 px-4 sm:px-6 lg:px-8">
    <div class=" mx-auto bg-white shadow-xl rounded-xl p-8 border border-gray-200">
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-8 border-b pb-4">
            <h1 class="text-2xl font-semibold text-gray-800">Daftar Surat Jalan</h1>
            <a href="{{ route('surat_jalan.create') }}"
                class="mt-4 md:mt-0 bg-red-600 text-white px-5 py-2.5 rounded-lg font-semibold shadow-lg hover:bg-red-700 transition duration-300 transform hover:scale-[1.02] flex items-center justify-center text-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                Tambah Surat Jalan
            </a>
        </div>

        @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 shadow-md" role="alert">
            <p>{{ session('success') }}</p>
        </div>
        @endif

        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200 shadow-inner">
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700">Cari SJ / Pelanggan:</label>
                <input type="text" name="search" id="search" value="{{ request('search') }}" placeholder="SJ-2024001 atau Nama Pelanggan"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-red-500 focus:border-red-500 transition">
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Filter Status:</label>
                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm px-3 py-2 focus:ring-red-500 focus:border-red-500 transition">
                    <option value="">Semua Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="Terkirim" {{ request('status') == 'Terkirim' ? 'selected' : '' }}>Terkirim</option>
                </select>
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full bg-red-600 text-white px-4 py-2.5 rounded-md font-medium shadow-md hover:bg-red-700 transition">
                    Terapkan Filter
                </button>
            </div>
        </form>

        {{-- Tabel Data --}}
        <div class="overflow-x-auto rounded-xl border border-gray-200 shadow-lg">
            <table class="w-full text-sm text-left text-gray-700">
                <thead class="bg-red-600 text-white uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-4 py-3 rounded-tl-xl">#</th>
                        <th class="px-4 py-3">ID Surat Jalan</th>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Pelanggan</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Dibuat Oleh</th>
                        <th class="px-4 py-3 text-center rounded-tr-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($suratJalans as $i => $sj)
                    <tr class="bg-white hover:bg-red-50 transition duration-150">
                        <td class="px-4 py-3 font-medium">{{ $loop->iteration }}</td>
                        <td class="px-4 py-3 font-bold text-gray-900">{{ $sj->sj_id }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($sj->tanggal_surat)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ $sj->pelanggan->nama_pelanggan ?? '-' }}</td>
                        <td class="px-4 py-3">
                            {{-- Badge Status --}}
                            <span class="px-3 py-1 rounded-full text-xs font-semibold shadow-sm
                                    {{ $sj->status == 'Pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $sj->status == 'Disetujui' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $sj->status == 'Ditolak' ? 'bg-red-100 text-red-700' : '' }}
                                    {{ $sj->status == 'Terkirim' ? 'bg-indigo-100 text-indigo-700' : '' }}">
                                {{ $sj->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $sj->user->nama_lengkap ?? '-' }}</td>
                        <td class="px-4 py-3 text-center space-x-2 whitespace-nowrap">
                            {{-- Tombol Detail --}}
                            <a href="{{ route('surat_jalan.show', $sj->sj_id) }}"
                                class="text-sm font-medium text-red-600 hover:text-red-800 transition duration-150 hover:underline">
                                Lihat Detail
                            </a>
                            @if ($sj->status === 'Pending')

                            {{-- Tombol Hapus (dengan Form dan Konfirmasi JS) --}}
                            <form action="{{ route('surat_jalan.destroy', $sj->sj_id) }}" method="POST" class="inline"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus Surat Jalan {{ $sj->sj_id }}? Tindakan ini tidak dapat dibatalkan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="text-sm font-medium text-red-500 hover:text-red-700 transition duration-150 ml-2">
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-500 text-base font-medium bg-gray-50/50">
                            <svg class="w-8 h-8 inline-block mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            Tidak ada data Surat Jalan yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $suratJalans->links() }}
        </div>
    </div>
</div>
@endsection