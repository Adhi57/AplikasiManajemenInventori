@extends('layouts.app')
@section('page-title', 'Laporan Barang Masuk')

@section('content')

<style>
    @media print {

        #main-topbar,
        #main-navbar,
        .hidden-on-print {
            display: none !important;
        }

        body,
        #app {
            margin: 0 !important;
            padding: 0 !important;
        }

        .max-w-4xl.mx-auto {
            max-width: none !important;
            margin: 0 !important;
        }
    }
</style>

<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-lg p-6">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6 print:hidden">
            <h1 class="text-2xl font-bold text-gray-800">Laporan Barang Masuk</h1>

            <a href="{{ route('laporan.barang-masuk.cetak', request()->all()) }}" target="_blank"
                class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow">
                📄 Export PDF
            </a>
        </div>

        {{-- FILTER --}}
        <form method="GET" id="filter-section"
            action="{{ route('laporan.barang-masuk.index') }}"
            class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6 print:hidden">

            <div>
                <label class="text-sm font-medium text-gray-700">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ request('dari') }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Cari No PO / Supplier</label>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="No PO atau Supplier..."
                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
            </div>

            <div class="flex items-end space-x-2 col-span-2">
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow">
                    🔍 Tampilkan
                </button>

                <a href="{{ route('laporan.barang-masuk.index') }}"
                    class="w-full text-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium shadow">
                    📄 Lihat Semua
                </a>
            </div>
        </form>
    


        {{-- TABEL DATA --}}
        @php
        $grouped = $laporans->groupBy('po_id');
        @endphp

        @forelse ($laporans as $laporan)
        @php
        $total_po = $laporan->details->sum('subtotal');
        @endphp

        <div class="border rounded-lg mb-6 overflow-hidden">
            <div class="bg-gray-100 px-4 py-3 flex justify-between items-center">
                <div>
                    <p class="font-semibold text-gray-800">
                        No PO: <span class="text-indigo-700">{{ $laporan->po_id ?? '-' }}</span>
                    </p>
                    <p class="text-sm text-gray-600">
                        Supplier: {{ $laporan->supplier->namaSupplier ?? '-' }}
                    </p>
                </div>
                <span class="text-blue-700 font-semibold">Barang Masuk</span>
            </div>

            <table class="w-full text-sm border-t border-gray-300">
                <thead class="bg-gray-50 text-gray-700">
                    <tr>
                        <th class="p-2 text-left">Tanggal Masuk</th>
                        <th class="p-2 text-left">Nama Barang</th>
                        <th class="p-2 text-center">Jumlah Diterima</th>
                        <th class="p-2 text-center">Rusak</th>
                        <th class="p-2 text-center">Satuan</th>
                        <th class="p-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporan->details as $detail)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-2">
                            {{ \Carbon\Carbon::parse($laporan->tanggal_masuk)->translatedFormat('d M Y') }}
                        </td>
                        <td class="p-2">
                            {{ $detail->barang->nama_barang ?? '-' }}
                        </td>
                        <td class="p-2 text-center">
                            {{ number_format($detail->quantity_diterima, 0, ',', '.') }}
                        </td>
                        <td class="p-2 text-center">
                            {{ number_format($detail->quantity_rusak, 0, ',', '.') }}
                        </td>
                        <td class="p-2 text-center">
                            {{ $detail->satuan }}
                        </td>
                        <td class="p-2 text-right font-semibold text-green-700">
                            Rp {{ number_format($detail->subtotal, 2, ',', '.') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-semibold text-gray-800">
                    <tr>
                        <td colspan="5" class="p-2 text-right">
                            Total PO {{ $laporan->po_id ?? '-' }}
                        </td>
                        <td class="p-2 text-right text-green-700">
                            Rp {{ number_format($total_po, 2, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @empty
        <p class="text-gray-500 text-center py-8">
            Belum ada data barang masuk sesuai filter.
        </p>
        @endforelse


        {{-- INFO --}}
        <div class="text-sm text-gray-500 mt-4 text-right">
            Menampilkan {{ $laporans->count() }} data barang masuk.
        </div>
    </div>
</div>

@endsection