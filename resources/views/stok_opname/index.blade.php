@extends('layouts.app')
@section('page-title', 'Stock Opname')

@section('content')
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Stock Opname</h1>
                <p class="text-sm text-gray-500 mt-1">Lakukan pengecekan stok, kondisi barang, dan catat alasan perubahan.
                </p>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-5 bg-blue-500 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Data Stock Opname</h2>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kode Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nama Barang</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Stok Baik</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Stok Rusak</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kadaluarsa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Alasan Update</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($stok as $item)
                            <tr class="hover:bg-blue-50/30 transition-colors group">
                                <form action="{{ route('stock.opname.update') }}" method="POST" class="contents">
                                    @csrf
                                    <input type="hidden" name="stok_id" value="{{ $item->id }}">

                                    <td class="px-4 py-3">
                                        <span
                                            class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $item->kode_barang }}</span>
                                    </td>
                                    <td class="px-4 py-3 font-medium text-gray-800">{{ $item->barang->nama_barang ?? '-' }}</td>

                                    <td class="px-4 py-3 text-center">
                                        <input type="number" name="jumlah_stok" value="{{ $item->jumlah_stok }}" step="any"
                                            class="w-24 border border-gray-200 rounded-lg px-2.5 py-1.5 text-sm text-center focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition bg-emerald-50 text-emerald-700 font-semibold">
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <input type="number" name="jumlah_stok_rusak" value="{{ $item->jumlah_stok_rusak }}"
                                            step="any"
                                            class="w-24 border border-gray-200 rounded-lg px-2.5 py-1.5 text-sm text-center focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition bg-red-50 text-red-700 font-semibold">
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <input type="date" name="tgl_kadaluarsa" value="{{ $item->tgl_kadaluarsa }}"
                                            class="border border-gray-200 rounded-lg px-2.5 py-1.5 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition">
                                    </td>

                                    <td class="px-4 py-3">
                                        <input type="text" name="alasan" placeholder="Alasan update..." required
                                            class="w-full border border-gray-200 rounded-lg px-3 py-1.5 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition placeholder-gray-400">
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        <button
                                            class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-red-800 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition-colors shadow-sm">
                                            <i class="fa-solid fa-save text-[10px]"></i> Update
                                        </button>
                                    </td>
                                </form>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fa-solid fa-clipboard-list text-xl text-gray-400"></i>
                                        </div>
                                        <p class="text-sm text-gray-500">Tidak ada data stok opname ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- LOG UPDATE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-5 bg-violet-500 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Riwayat Update Stok</h2>
            </div>

            @forelse($logs as $log)
                <div class="flex items-start gap-3 py-3 {{ !$loop->last ? 'border-b border-gray-50' : '' }}">
                    <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fa-solid fa-pen text-violet-600 text-xs"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm text-gray-700">
                            <span class="font-semibold text-red-700">{{ $log->user->nama_lengkap }}</span>
                            memperbarui
                            <span class="font-semibold text-gray-900">{{ $log->stok->barang->nama_barang ?? '-' }}</span>
                            <span class="text-gray-500">({{ $log->stok->kode_barang ?? 'N/A' }})</span>
                        </p>
                        <p class="text-sm text-gray-500 mt-0.5">
                            <i class="fa-solid fa-quote-left text-[10px] text-gray-300 mr-1"></i>
                            {{ $log->alasan_update }}
                        </p>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap flex-shrink-0 mt-1">
                        {{ $log->created_at->format('d M Y H:i') }}
                    </span>
                </div>
            @empty
                <div class="flex flex-col items-center py-6 text-center">
                    <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                        <i class="fa-solid fa-clock-rotate-left text-xl text-gray-400"></i>
                    </div>
                    <p class="text-sm text-gray-500">Belum ada riwayat update stok.</p>
                </div>
            @endforelse
        </div>

    </div>
@endsection