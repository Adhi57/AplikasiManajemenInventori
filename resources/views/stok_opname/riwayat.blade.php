@extends('layouts.app')

@section('page-title', 'Riwayat Stock Opname')

@section('content')
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Riwayat Stock Opname</h1>
                <p class="text-sm text-gray-500 mt-1">Catatan seluruh perubahan stok dari proses stock opname.</p>
            </div>
            <a href="{{ route('stock.opname.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-semibold hover:bg-gray-50 transition-colors shadow-sm">
                <i class="fa-solid fa-arrow-left text-xs text-gray-400"></i> Kembali
            </a>
        </div>

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
                            placeholder="Cari kode barang, nama barang, alasan, atau nama petugas..."
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
                <div class="w-2 h-5 bg-violet-500 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Log Perubahan Stok</h2>
                <span class="px-2 py-0.5 bg-violet-100 text-violet-700 text-xs font-semibold rounded-full ml-1">
                    {{ $logs->total() }} data
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
                                Tanggal</th>
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
                                Alasan</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Petugas</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($logs as $i => $log)
                            <tr class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors">
                                <td class="px-4 py-3 text-center text-gray-500">{{ $logs->firstItem() + $i }}</td>
                                <td class="px-4 py-3 text-gray-700">
                                    <div class="text-sm font-medium">{{ $log->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $log->stok->kode_barang ?? '-' }}</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800">
                                    {{ $log->stok->barang->nama_barang ?? '-' }}</td>

                                {{-- Stok Baik: Sebelum → Sesudah --}}
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $baikDiff = $log->stok_baik_sesudah - $log->stok_baik_sebelum;
                                    @endphp
                                    <div class="flex flex-col items-center gap-0.5">
                                        <div class="flex items-center gap-1 text-xs">
                                            <span class="text-gray-400">{{ number_format($log->stok_baik_sebelum, 0, ',', '.') }}</span>
                                            <i class="fa-solid fa-arrow-right text-[8px] text-gray-300"></i>
                                            <span class="font-bold text-gray-800">{{ number_format($log->stok_baik_sesudah, 0, ',', '.') }}</span>
                                        </div>
                                        @if($baikDiff != 0)
                                            <span class="text-[10px] font-bold {{ $baikDiff > 0 ? 'text-emerald-600' : 'text-red-600' }}">
                                                {{ $baikDiff > 0 ? '+' : '' }}{{ number_format($baikDiff, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-[10px] text-gray-400">—</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Stok Rusak: Sebelum → Sesudah --}}
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $rusakDiff = $log->stok_rusak_sesudah - $log->stok_rusak_sebelum;
                                    @endphp
                                    <div class="flex flex-col items-center gap-0.5">
                                        <div class="flex items-center gap-1 text-xs">
                                            <span class="text-gray-400">{{ number_format($log->stok_rusak_sebelum, 0, ',', '.') }}</span>
                                            <i class="fa-solid fa-arrow-right text-[8px] text-gray-300"></i>
                                            <span class="font-bold text-gray-800">{{ number_format($log->stok_rusak_sesudah, 0, ',', '.') }}</span>
                                        </div>
                                        @if($rusakDiff != 0)
                                            <span class="text-[10px] font-bold {{ $rusakDiff > 0 ? 'text-red-600' : 'text-emerald-600' }}">
                                                {{ $rusakDiff > 0 ? '+' : '' }}{{ number_format($rusakDiff, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-[10px] text-gray-400">—</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Kadaluarsa --}}
                                <td class="px-4 py-3 text-center">
                                    @if($log->tgl_kadaluarsa_sebelum != $log->tgl_kadaluarsa_sesudah)
                                        <div class="flex flex-col items-center gap-0.5 text-xs">
                                            <span class="text-gray-400">{{ $log->tgl_kadaluarsa_sebelum ? \Carbon\Carbon::parse($log->tgl_kadaluarsa_sebelum)->format('d/m/Y') : '-' }}</span>
                                            <i class="fa-solid fa-arrow-down text-[8px] text-gray-300"></i>
                                            <span class="font-medium text-gray-800">{{ $log->tgl_kadaluarsa_sesudah ? \Carbon\Carbon::parse($log->tgl_kadaluarsa_sesudah)->format('d/m/Y') : '-' }}</span>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">{{ $log->tgl_kadaluarsa_sesudah ? \Carbon\Carbon::parse($log->tgl_kadaluarsa_sesudah)->format('d/m/Y') : '-' }}</span>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <span class="px-2.5 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full">
                                        {{ $log->alasan_update }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-6 h-6 bg-gray-200 rounded-full flex items-center justify-center text-gray-600 text-[10px] font-bold">
                                            {{ strtoupper(substr($log->user->nama_lengkap ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $log->user->nama_lengkap ?? '-' }}</span>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fa-solid fa-clock-rotate-left text-xl text-gray-400"></i>
                                        </div>
                                        <p class="text-sm text-gray-500">Belum ada riwayat stock opname.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINATION --}}
            @if ($logs->hasPages())
                <div class="mt-4">
                    {{ $logs->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
