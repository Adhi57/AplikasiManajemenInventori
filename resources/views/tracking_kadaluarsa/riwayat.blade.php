@extends('layouts.app')

@section('page-title', 'Riwayat Hapus Stok')

@section('content')
    <div class="space-y-6">

        {{-- HEADER --}}
        <x-page-header title="Riwayat Hapus Stok" description="Catatan stok yang dihapus dari tracking kadaluarsa." icon="fa-hourglass-half">
            <x-slot name="actions">
                <a href="{{ route('tracking_kadaluarsa.index') }}"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                    <i class="fa-solid fa-arrow-left text-amber-400"></i>
                    <span>Kembali</span>
                </a>
            </x-slot>
        </x-page-header>

        {{-- FILTER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Cari
                        Riwayat</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari kode barang, nama barang, atau nama penghapus..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                    </div>
                </div>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-800 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Cari
                </button>
            </form>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-5 bg-amber-500 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Log Penghapusan Stok</h2>
                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full ml-1">
                    {{ $riwayat->total() }} data
                </span>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider w-12">
                                No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Tanggal Hapus</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">PO
                                ID</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kode Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nama Barang</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Stok</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Rusak</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kadaluarsa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Alasan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Dihapus Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($riwayat as $i => $r)
                            <tr class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors">
                                <td class="px-4 py-3 text-center text-gray-500">{{ $riwayat->firstItem() + $i }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    <div class="text-sm font-medium">{{ $r->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $r->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $r->po_id ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $r->kode_barang }}</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $r->nama_barang }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold text-xs rounded-lg">
                                        {{ number_format($r->jumlah_stok, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-red-50 text-red-700 font-bold text-xs rounded-lg">
                                        {{ number_format($r->jumlah_stok_rusak, 2, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-700 text-xs">
                                    {{ $r->tgl_kadaluarsa ? $r->tgl_kadaluarsa->format('d M Y') : '-' }}
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full">
                                        {{ $r->alasan }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 text-[10px] font-bold">
                                            {{ strtoupper(substr($r->dihapus_oleh, 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $r->dihapus_oleh }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fa-solid fa-inbox text-gray-400 text-lg"></i>
                                        </div>
                                        <p class="text-sm text-gray-500">Belum ada riwayat penghapusan stok.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($riwayat->hasPages())
                <div class="mt-4">
                    {{ $riwayat->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection