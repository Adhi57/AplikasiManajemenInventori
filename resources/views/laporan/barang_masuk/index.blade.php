@extends('layouts.app')
@section('page-title', 'Laporan / Barang Masuk')

@section('content')
<div class="bg-white p-6 shadow-lg rounded-lg">
    <h2 class="text-2xl font-bold mb-4">Laporan Barang Masuk</h2>

    <form method="GET" action="{{ route('laporan.barang-masuk.index') }}" class="flex flex-wrap gap-4 mb-6">
        <select name="filter" class="border rounded p-2">
            <option value="semua" {{ request('filter') == 'semua' ? 'selected' : '' }}>Semua</option>
            <option value="mingguan" {{ request('filter') == 'mingguan' ? 'selected' : '' }}>Per Minggu Ini</option>
            <option value="bulanan" {{ request('filter') == 'bulanan' ? 'selected' : '' }}>Per Bulan Ini</option>
            <option value="rentang" {{ request('filter') == 'rentang' ? 'selected' : '' }}>Rentang Tanggal</option>
        </select>


        <input type="date" name="start_date" value="{{ request('start_date') }}" class="border p-2 rounded">
        <input type="date" name="end_date" value="{{ request('end_date') }}" class="border p-2 rounded">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>

        <a href="{{ route('laporan.barang-masuk.cetak', request()->all()) }}" target="_blank"
            class="bg-green-600 text-white px-4 py-2 rounded">Cetak PDF</a>
    </form>

    @php
        // Kelompokkan data berdasarkan po_id
        $grouped = $data->groupBy('po_id');
    @endphp

    @forelse ($grouped as $po_id => $rows)
        @php
            $supplier = $rows->first()->namaSupplier ?? '-';
            $total_po = $rows->sum('subtotal');
        @endphp

        <div class="mb-6">
            <h3 class="font-semibold text-lg text-blue-700 mb-2">
                No PO: {{ $po_id }} — Supplier: {{ $supplier }}
            </h3>

            <table class="w-full border-collapse border text-sm">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border p-2">Tanggal Masuk</th>
                        <th class="border p-2">Barang</th>
                        <th class="border p-2">Diterima</th>
                        <th class="border p-2">Rusak</th>
                        <th class="border p-2">Satuan</th>
                        <th class="border p-2 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rows as $row)
                        <tr>
                            <td class="border p-2">{{ \Carbon\Carbon::parse($row->tanggal_masuk)->format('d/m/Y') }}</td>
                            <td class="border p-2">{{ $row->nama_barang }}</td>
                            <td class="border p-2 text-center">{{ $row->quantity_diterima }}</td>
                            <td class="border p-2 text-center">{{ $row->quantity_rusak }}</td>
                            <td class="border p-2 text-center">{{ $row->satuan }}</td>
                            <td class="border p-2 text-right">{{ number_format($row->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 font-semibold">
                        <td colspan="5" class="border p-2 text-right">Total PO {{ $po_id }}</td>
                        <td class="border p-2 text-right">{{ number_format($total_po, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @empty
        <div class="text-center text-gray-500 py-10">
            Tidak ada data untuk ditampilkan.
        </div>
    @endforelse
</div>
@endsection
