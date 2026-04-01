@extends('layouts.app')
@section('page-title', 'Stock Opname')

@section('content')
    <div class="space-y-6">

        {{-- HEADER --}}
        <x-page-header title="Stock Opname" description="Lakukan pengecekan stok, kondisi barang, dan catat alasan perubahan." icon="fa-boxes-stacked">
            <x-slot name="actions">
                <a href="{{ route('stock.opname.riwayat') }}"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                    <i class="fa-solid fa-clock-rotate-left text-amber-400"></i>
                    <span>Riwayat Update</span>
                </a>
            </x-slot>
        </x-page-header>

        {{-- FILTER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form method="GET" class="flex flex-col sm:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Cari
                        Barang</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search }}" placeholder="Ketik kode / nama barang..."
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
                    <tbody>
                        @php
                            $grouped = $stok->groupBy(function ($item) {
                                return $item->barang->nama_barang ?? 'Barang Dihapus';
                            });
                        @endphp

                        @forelse ($grouped as $namaBarang => $items)
                            {{-- GROUP HEADER --}}
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <td colspan="7" class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 bg-slate-800 rounded-lg flex items-center justify-center">
                                            <i class="fa-solid fa-boxes-stacked text-white text-xs"></i>
                                        </div>
                                        <span class="font-bold text-gray-800">{{ strtoupper($namaBarang) }}</span>
                                        <span
                                            class="px-2 py-0.5 bg-slate-200 text-slate-600 text-xs font-semibold rounded-full">
                                            {{ $items->count() }} batch
                                        </span>
                                    </div>
                                </td>
                            </tr>

                            {{-- BATCH ROWS --}}
                            @foreach ($items as $item)
                                <tr class="border-b border-gray-50 hover:bg-blue-50/30 transition-colors group">
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
                            @endforeach

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
    </div>
@endsection