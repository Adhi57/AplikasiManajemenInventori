@extends('layouts.app')
@section('page-title', 'Stock Opname')

@section('content')

<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-screen mx-auto bg-white shadow-xl rounded-xl p-8 border border-gray-200">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 border-b pb-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Stock Opname</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Lakukan pengecekan stok, kondisi barang, dan catat alasan perubahan.
                </p>
            </div>
        </div>

        {{-- Notifikasi --}}
        @if(session('success'))
        <div class="p-4 mb-6 bg-green-500 text-white rounded-lg shadow-md flex items-center gap-2">
            <span class="font-semibold">✅ Berhasil:</span> {{ session('success') }}
        </div>
        @endif

        {{-- Tabel Stok Opname --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-md">
            <table class="w-full text-sm text-gray-700">
                <thead>
                    <tr class="bg-red-600 text-white uppercase text-xs font-semibold tracking-wider">
                        <th class="p-4 text-left">Kode Barang</th>
                        <th class="p-4 text-left">Nama Barang</th>
                        <th class="p-4 text-center">Stok Baik</th>
                        <th class="p-4 text-center">Stok Rusak</th>
                        <th class="p-4 text-center">Kadaluarsa</th>
                        <th class="p-4 text-left">Alasan Update</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stok as $item)
                    <tr class="border-b border-gray-100 hover:bg-red-50 transition duration-100">
                        <form action="{{ route('stock.opname.update') }}" method="POST" class="contents">
                            @csrf
                            <input type="hidden" name="stok_id" value="{{ $item->id }}">

                            <td class="p-4 font-medium">{{ $item->kode_barang }}</td>
                            <td class="p-4">{{ $item->barang->nama_barang ?? '-' }}</td>

                            <td class="p-4 text-center">
                                <input type="number" name="jumlah_stok"
                                    class="w-24 border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-red-600 focus:border-red-600"
                                    value="{{ $item->jumlah_stok }}">
                            </td>

                            <td class="p-4 text-center">
                                <input type="number" name="jumlah_stok_rusak"
                                    class="w-24 border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-red-600 focus:border-red-600"
                                    value="{{ $item->jumlah_stok_rusak }}">
                            </td>

                            <td class="p-4 text-center">
                                <input type="date" name="tgl_kadaluarsa"
                                    class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-red-600 focus:border-red-600"
                                    value="{{ $item->tgl_kadaluarsa }}">
                            </td>

                            <td class="p-4">
                                <input type="text" name="alasan"
                                    placeholder="Alasan update..."
                                    class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:ring-red-600 focus:border-red-600"
                                    required>
                            </td>

                            <td class="p-4 text-center">
                                <button
                                    class="px-4 py-1.5 bg-red-700 text-white rounded-lg text-xs font-semibold shadow hover:bg-red-800 transition">
                                    Update
                                </button>
                            </td>
                        </form>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-10 text-gray-500 bg-gray-50 text-base font-medium">
                            <svg class="w-8 h-8 inline-block mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10m0-4l-8 4"></path>
                            </svg>
                            Tidak ada data stok opname ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Log Update --}}
        <div class="mt-8">
            <h2 class="text-lg font-bold text-gray-800 mb-4">📝 Riwayat Update Stok</h2>
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 shadow-sm">
                @forelse($logs as $log)
                    <div class="mb-2 text-sm text-gray-700">
                        <span class="font-semibold text-red-700">{{ $log->user->nama_lengkap }}</span>
                        memperbarui <span class="font-semibold">{{ $log->stok->barang->nama_barang ?? '-' }}</span>
                        ({{ $log->stok->kode_barang }}) → 
                        <span class="italic">{{ $log->alasan_update }}</span>
                        <span class="text-gray-500">[{{ $log->created_at->format('d M Y H:i') }}]</span>
                    </div>
                @empty
                    <p class="text-gray-500 italic">Belum ada riwayat update stok.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>

@endsection
