@extends('layouts.app')
@section('page-title', 'Laporan Barang Masuk')

@section('content')

    @php
        $totalPO = $laporans->count();
        $totalItems = $laporans->sum(fn($l) => $l->details->sum('quantity_diterima'));
        $totalRusak = $laporans->sum(fn($l) => $l->details->sum('quantity_rusak'));
        $totalNilai = $laporans->sum(fn($l) => $l->details->sum('subtotal'));
    @endphp

    <div class="space-y-6">

        {{-- HEADER --}}
        <x-page-header title="Laporan Barang Masuk" description="Ringkasan penerimaan barang dari supplier berdasarkan Purchase Order." icon="fa-file-invoice">
            <x-slot name="actions">
                <a href="{{ route('laporan.barang-masuk.cetak', request()->all()) }}" target="_blank"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                    <i class="fa-solid fa-file-pdf text-amber-400"></i>
                    <span>Export PDF</span>
                </a>
            </x-slot>
        </x-page-header>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-red-100 flex items-center justify-center">
                        <i class="fa-solid fa-file-invoice text-red-700"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total PO</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalPO, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <i class="fa-solid fa-boxes-stacked text-emerald-700"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Item Diterima</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalItems, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-amber-700"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Rusak</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalRusak, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center">
                        <i class="fa-solid fa-money-bill-wave text-blue-700"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Nilai</p>
                        <p class="text-lg font-bold text-gray-900">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER SECTION --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form method="GET" action="{{ route('laporan.barang-masuk.index') }}"
                class="flex flex-col md:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Dari
                        Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}"
                        class="w-full py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sampai
                        Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}"
                        class="w-full py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Cari No PO /
                        Supplier</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="No PO atau Supplier..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                    </div>
                </div>
                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit"
                        class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-800 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        <i class="fa-solid fa-filter text-xs"></i> Filter
                    </button>
                    <a href="{{ route('laporan.barang-masuk.index') }}"
                        class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">
                        <i class="fa-solid fa-rotate-right text-xs"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- DATA PER PO --}}
        @forelse ($laporans as $laporan)
            @php
                $total_po = $laporan->details->sum('subtotal');
            @endphp

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- PO Header --}}
                <div
                    class="px-5 py-4 bg-gradient-to-r from-red-50 to-white border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-red-800 text-white text-xs font-bold rounded-lg">PO</span>
                            <span class="font-bold text-gray-900">{{ $laporan->po_id ?? '-' }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="fa-solid fa-truck-field text-xs text-gray-400 mr-1"></i>
                            Supplier: <strong>{{ $laporan->supplier->namaSupplier ?? '-' }}</strong>
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg">
                            <i class="fa-solid fa-arrow-down text-[10px] mr-1"></i> Barang Masuk
                        </span>
                        <span class="text-sm text-gray-500">
                            <i class="fa-regular fa-calendar text-xs mr-1"></i>
                            {{ \Carbon\Carbon::parse($laporan->tanggal_masuk)->translatedFormat('d M Y') }}
                        </span>
                    </div>
                </div>

                {{-- Items Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600">
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nama Barang</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider">Diterima</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider">Rusak</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider">Satuan</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($laporan->details as $detail)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-3 font-medium text-gray-800">{{ $detail->barang->nama_barang ?? '-' }}</td>
                                    <td class="px-5 py-3 text-center">
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-md">
                                            {{ number_format($detail->quantity_diterima, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 text-center">
                                        @if($detail->quantity_rusak > 0)
                                            <span class="px-2 py-0.5 bg-red-50 text-red-600 text-xs font-bold rounded-md">
                                                {{ number_format($detail->quantity_rusak, 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">0</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3 text-center text-gray-500">{{ $detail->satuan }}</td>
                                    <td class="px-5 py-3 text-right font-semibold text-gray-800">
                                        Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50 border-t-2 border-gray-200">
                                <td colspan="4" class="px-5 py-3 text-right text-sm font-bold text-gray-700">
                                    Total PO {{ $laporan->po_id ?? '' }}
                                </td>
                                <td class="px-5 py-3 text-right text-sm font-bold text-red-700">
                                    Rp {{ number_format($total_po, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            {{-- EMPTY STATE --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fa-solid fa-inbox text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-700 mb-1">Belum Ada Data</h3>
                <p class="text-sm text-gray-500 mb-4">Belum ada data barang masuk sesuai filter yang dipilih.</p>
                <a href="{{ route('laporan.barang-masuk.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-800 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-rotate-right text-xs"></i> Reset Filter
                </a>
            </div>
        @endforelse

        {{-- INFO --}}
        @if($laporans->count() > 0)
            <div
                class="flex items-center justify-between text-sm text-gray-500 bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-3">
                <span>Menampilkan <strong class="text-gray-700">{{ $laporans->count() }}</strong> data barang masuk.</span>
                <span class="font-semibold text-red-700">Grand Total: Rp {{ number_format($totalNilai, 0, ',', '.') }}</span>
            </div>
        @endif

    </div>

@endsection