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

        body, #app {
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
            <h1 class="text-2xl font-bold text-gray-800">📦 Laporan Barang Masuk</h1>

            <div class="flex space-x-2">
                {{-- Tombol Print --}}
                <button id="print-button" onclick="window.print()" 
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow">
                    🖨️ Cetak Laporan
                </button>

                {{-- Tombol Cetak PDF --}}
                <a href="{{ route('laporan.barang-masuk.cetak', request()->all()) }}" target="_blank"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow">
                    📄 Export PDF
                </a>
            </div>
        </div>

        {{-- FILTER --}}
        <form method="GET" id="filter-section" 
              action="{{ route('laporan.barang-masuk.index') }}"
              class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6 print:hidden">

            <div>
                <label class="text-sm font-medium text-gray-700">Tipe Filter</label>
                <select name="filter" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="semua" {{ request('filter') == 'semua' ? 'selected' : '' }}>Semua</option>
                    <option value="mingguan" {{ request('filter') == 'mingguan' ? 'selected' : '' }}>Minggu ini</option>
                    <option value="bulanan" {{ request('filter') == 'bulanan' ? 'selected' : '' }}>Bulan ini</option>
                    <option value="rentang" {{ request('filter') == 'rentang' ? 'selected' : '' }}>Rentang Tanggal</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div>
                <label class="text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}"
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>

            <div class="flex items-end col-span-2">
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow">
                    🔍 Tampilkan
                </button>
            </div>
        </form>

        {{-- TABEL DATA --}}
        @php
            $grouped = $data->groupBy('po_id');
        @endphp

        @forelse ($grouped as $po_id => $rows)
            @php
                $supplier = $rows->first()->namaSupplier ?? '-';
                $total_po = $rows->sum('subtotal');
            @endphp

            <div class="border rounded-lg mb-6 overflow-hidden">
                <div class="bg-gray-100 px-4 py-3 flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-800">
                            No PO: <span class="text-indigo-700">{{ $po_id }}</span>
                        </p>
                        <p class="text-sm text-gray-600">Supplier: {{ $supplier }}</p>
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
                        @foreach ($rows as $row)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ \Carbon\Carbon::parse($row->tanggal_masuk)->translatedFormat('d M Y') }}</td>
                                <td class="p-2">{{ $row->nama_barang }}</td>
                                <td class="p-2 text-center">{{ number_format($row->quantity_diterima, 0, ',', '.') }}</td>
                                <td class="p-2 text-center">{{ number_format($row->quantity_rusak, 0, ',', '.') }}</td>
                                <td class="p-2 text-center">{{ $row->satuan }}</td>
                                <td class="p-2 text-right font-semibold text-green-700">
                                    Rp {{ number_format($row->subtotal, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold text-gray-800">
                        <tr>
                            <td colspan="5" class="p-2 text-right">Total PO {{ $po_id }}</td>
                            <td class="p-2 text-right text-green-700">Rp {{ number_format($total_po, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @empty
            <p class="text-gray-500 text-center py-8">Belum ada data barang masuk sesuai filter.</p>
        @endforelse

        {{-- INFO --}}
        <div class="text-sm text-gray-500 mt-4 text-right">
            Menampilkan {{ $data->count() }} data barang masuk.
        </div>
    </div>
</div>

@endsection
