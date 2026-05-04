@extends('layouts.app')

@section('page-title', 'Log Stok Keluar')

@section('content')
<div class="space-y-6">

    {{-- HEADER --}}
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-red-50 text-red-600 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-arrow-right-from-bracket text-xl"></i>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-800">Riwayat Stok Keluar</h1>
                <p class="text-xs text-gray-500 mt-1">Laporan histori pengurangan stok dari sistem FEFO maupun proses lainnya</p>
            </div>
        </div>
        <div>
            <a href="{{ route('stok.index') }}" class="px-4 py-2.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-xl text-sm font-bold transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Stok
            </a>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <form method="GET" action="{{ route('stok.log_keluar') }}" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1 w-full">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Pencarian</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                        <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari kode barang / nama barang / sumber..."
                        class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-slate-500/20 focus:border-slate-500 transition shadow-sm">
                </div>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-slate-800 text-white hover:bg-slate-700 rounded-xl text-sm font-bold transition-all shadow-sm">
                Cari Log
            </button>
            @if($search)
                <a href="{{ route('stok.log_keluar') }}" class="px-5 py-2.5 border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl text-sm font-bold transition-all shadow-sm">
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50/80 text-[10px] font-bold text-gray-500 uppercase tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Barang</th>
                        <th class="px-6 py-4 text-center">Batch (PO / Exp)</th>
                        <th class="px-6 py-4 text-center">Jumlah</th>
                        <th class="px-6 py-4">Sumber</th>
                        <th class="px-6 py-4">Eksekutor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($log->waktu)->format('d M Y') }}</div>
                                <div class="text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($log->waktu)->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($log->barang && $log->barang->foto_produk)
                                        <img src="{{ asset('storage/' . $log->barang->foto_produk) }}" class="w-8 h-8 rounded-lg border border-gray-100 object-cover flex-shrink-0" alt="">
                                    @else
                                        <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-box text-gray-300 text-xs"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $log->barang->nama_barang ?? '-' }}</p>
                                        <p class="text-[10px] font-mono text-gray-500 bg-gray-100 inline-block px-1.5 py-0.5 rounded mt-0.5">{{ $log->kode_barang }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded">{{ $log->po_id ?? '-' }}</span>
                                <div class="text-[10px] text-gray-400 mt-1">Exp: {{ $log->tgl_kadaluarsa ? \Carbon\Carbon::parse($log->tgl_kadaluarsa)->format('d/m/Y') : '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 bg-red-50 text-red-700 font-black rounded-lg border border-red-100">
                                    - {{ number_format($log->jumlah, 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs">
                                <span class="bg-blue-50 text-blue-700 px-2 py-1 rounded font-medium">{{ $log->sumber }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-gray-600 font-medium flex items-center gap-1.5">
                                    <i class="fa-solid fa-user-circle text-gray-400"></i> {{ $log->dieksekusi_oleh ?? '-' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                        <i class="fa-solid fa-folder-open text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-bold text-gray-500">Belum ada riwayat stok keluar</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
