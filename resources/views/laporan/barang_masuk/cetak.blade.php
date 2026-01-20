<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Masuk</title>
    <style>
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; margin: 20px; }
        h2 { text-align: center; margin-bottom: 5px; }
        p { text-align: center; margin: 0; font-size: 10px; }
        h3 { color: #004085; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th, td { border: 1px solid #000; padding: 5px; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total { background-color: #f9f9f9; font-weight: bold; }
        .grand-total { text-align: right; font-weight: bold; margin-top: 15px; }
        hr { border: 0.5px solid #999; margin: 15px 0; }
    </style>
</head>
<body>

<h2>LAPORAN BARANG MASUK<br>CV. Berkah Jaya Lumintu</h2>
<p>Periode: {{ $start ?? '-' }} s/d {{ $end ?? '-' }}</p>
<hr>

@php
    $grandTotal = 0;
@endphp

@forelse ($data as $laporan)
    @php
        $total_po = $laporan->details->sum('subtotal');
        $grandTotal += $total_po;
    @endphp

    <h3>
        No PO: {{ $laporan->po_id ?? '-' }} —
        Supplier: {{ $laporan->supplier?->namaSupplier ?? '-' }}
    </h3>

    <table>
        <thead>
            <tr>
                <th>Tanggal Masuk</th>
                <th>Nama Barang</th>
                <th>Diterima</th>
                <th>Rusak</th>
                <th>Satuan</th>
                <th class="text-right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($laporan->details as $detail)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($laporan->tanggal_masuk)->format('d/m/Y') }}</td>
                    <td>{{ $detail->barang?->nama_barang ?? '-' }}</td>
                    <td class="text-center">{{ number_format($detail->quantity_diterima, 0, ',', '.') }}</td>
                    <td class="text-center">{{ number_format($detail->quantity_rusak, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $detail->satuan }}</td>
                    <td class="text-right">
                        {{ number_format($detail->subtotal, 0, ',', '.') }}
                    </td>
                </tr>
            @endforeach

            <tr class="total">
                <td colspan="5" class="text-right">Total Pembelian</td>
                <td class="text-right">{{ number_format($total_po, 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>
@empty
    <p style="text-align:center;">Tidak ada data barang masuk.</p>
@endforelse

<hr>
<p class="grand-total">
    TOTAL KESELURUHAN: Rp {{ number_format($grandTotal, 0, ',', '.') }}
</p>

</body>
</html>
