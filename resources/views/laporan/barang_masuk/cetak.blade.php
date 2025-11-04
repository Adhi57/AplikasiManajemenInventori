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
        th, td { border: 1px solid #000; padding: 5px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total { background-color: #f9f9f9; font-weight: bold; }
        .grand-total { text-align: right; font-weight: bold; margin-top: 15px; }
        hr { border: 0.5px solid #999; margin: 15px 0; }
    </style>
</head>
<body>
    <h2>LAPORAN BARANG MASUK CV. Berkah Jaya Lumintu</h2>
    <p>Periode: {{ $start ?? '-' }} s/d {{ $end ?? '-' }}</p>
    <hr>

    @php
        $grouped = $data->groupBy('po_id');
        $grandTotal = 0;
    @endphp

    @foreach ($grouped as $po_id => $rows)
        @php
            $supplier = $rows->first()->namaSupplier ?? '-';
            $total_po = $rows->sum('subtotal');
            $grandTotal += $total_po;
        @endphp

        <h3>No PO: {{ $po_id }} — Supplier: {{ $supplier }}</h3>

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
                @foreach ($rows as $row)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row->tanggal_masuk)->format('d/m/Y') }}</td>
                        <td>{{ $row->nama_barang }}</td>
                        <td class="text-center">{{ $row->quantity_diterima }}</td>
                        <td class="text-center">{{ $row->quantity_rusak }}</td>
                        <td class="text-center">{{ $row->satuan }}</td>
                        <td class="text-right">{{ number_format($row->subtotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr class="total">
                    <td colspan="5" class="text-right">Total Pembelian: </td>
                    <td class="text-right">{{ number_format($total_po, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    @endforeach

    <hr>
    <p class="grand-total">TOTAL KESELURUHAN: Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
</body>
</html>
