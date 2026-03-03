<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Masuk</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            margin: 3cm 3cm 3cm 3cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #333;
            line-height: 1.5;
        }

        .page {
            padding: 1cm;
        }

        /* === HEADER / KOP SURAT === */
        .kop-surat {
            display: table;
            width: 100%;
            border-bottom: 4px double #8b0000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .kop-logo {
            display: table-cell;
            width: 80px;
            vertical-align: middle;
        }

        .kop-logo img {
            width: 70px;
            height: auto;
        }

        .kop-text {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .kop-text h1 {
            font-size: 18pt;
            font-weight: bold;
            color: #8b0000;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .kop-text h2 {
            font-size: 10pt;
            font-weight: normal;
            color: #555;
            margin-top: 2px;
        }

        .kop-text p {
            font-size: 9pt;
            color: #666;
        }

        /* === DOCUMENT TITLE === */
        .doc-title {
            text-align: center;
            margin: 20px 0 15px;
        }

        .doc-title h2 {
            font-size: 14pt;
            text-decoration: underline;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-title p {
            font-size: 10pt;
            color: #555;
            margin-top: 2px;
        }

        /* === INFO SECTION === */
        .info-section {
            margin-bottom: 15px;
            font-size: 10pt;
        }

        .info-section .label {
            font-weight: bold;
            color: #444;
        }

        /* === PO HEADER === */
        .po-header {
            background: #f9fafb;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            margin-top: 20px;
            margin-bottom: 5px;
        }

        .po-header h3 {
            font-size: 11pt;
            color: #8b0000;
            margin: 0;
        }

        .po-header p {
            font-size: 9pt;
            color: #555;
            margin: 0;
        }

        /* === ITEM TABLE === */
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0 15px;
            font-size: 10pt;
        }

        .item-table thead th {
            background: #8b0000;
            color: #fff;
            padding: 8px 10px;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
            text-transform: uppercase;
        }

        .item-table tbody td {
            padding: 6px 10px;
            border-bottom: 1px solid #ddd;
        }

        .item-table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .item-table .text-right {
            text-align: right;
        }

        .item-table .text-center {
            text-align: center;
        }

        .item-table tfoot td {
            padding: 8px 10px;
            font-weight: bold;
            border-top: 2px solid #ccc;
        }

        /* === TOTAL TABLE === */
        .total-section {
            display: table;
            width: 100%;
            margin-top: 10px;
        }

        .total-spacer {
            display: table-cell;
            width: 55%;
        }

        .total-box {
            display: table-cell;
            width: 45%;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .total-table td {
            padding: 5px 10px;
        }

        .total-table .label {
            text-align: right;
            color: #444;
        }

        .total-table .value {
            text-align: right;
            font-weight: bold;
        }

        .total-table .grand {
            border-top: 2px solid #8b0000;
            font-size: 12pt;
            color: #8b0000;
        }

        /* === FOOTER / TTD === */
        .footer {
            margin-top: 30px;
        }

        .ttd-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .ttd-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            font-size: 10pt;
        }

        .ttd-box .title {
            font-weight: bold;
            margin-bottom: 60px;
        }

        .ttd-box .name {
            font-weight: bold;
            text-decoration: underline;
            margin-top: 5px;
        }

        .ttd-box .role {
            font-size: 9pt;
            color: #555;
        }

        .page-break {
            page-break-before: always;
        }
    </style>
</head>

<body>
    <div class="page">
        {{-- KOP SURAT --}}
        <div class="kop-surat">
            <div class="kop-logo">
                <img src="{{ public_path('assets/images/Logo.png') }}" alt="Logo">
            </div>
            <div class="kop-text">
                <h1>CV. Berkah Jaya Lumintu</h1>
                <h2>Distributor Bahan Pokok & Kebutuhan Sehari-hari</h2>
                <p>Wonokerso I RT 02/02, Wonokerso, Tembarak, Kab. Temanggung</p>
                <p>Telp: 08123456789 &bull; Email: berkahjayalumintu@gmail.com</p>
            </div>
        </div>

        {{-- DOCUMENT TITLE --}}
        <div class="doc-title">
            <h2>Laporan Barang Masuk</h2>
            <p>Periode: {{ $start ?? '-' }} s/d {{ $end ?? '-' }}</p>
        </div>

        {{-- INFO --}}
        <div class="info-section">
            <span class="label">Tanggal Cetak:</span> {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <span class="label">Jumlah PO:</span> {{ $data->count() }}
        </div>

        {{-- DATA PER PO --}}
        @php $grandTotal = 0;
        $no = 1; @endphp

        @forelse ($data as $laporan)
            @php
                $total_po = $laporan->details->sum('subtotal');
                $grandTotal += $total_po;
            @endphp

            <div class="po-header">
                <h3>No PO: {{ $laporan->po_id ?? '-' }}</h3>
                <p>Supplier: {{ $laporan->supplier->namaSupplier ?? '-' }}
                    &nbsp;|&nbsp; Tanggal Masuk: {{ \Carbon\Carbon::parse($laporan->tanggal_masuk)->format('d/m/Y') }}
                </p>
            </div>

            <table class="item-table">
                <thead>
                    <tr>
                        <th style="width:30px;">No</th>
                        <th style="text-align:left;">Nama Barang</th>
                        <th style="width:80px;">Diterima</th>
                        <th style="width:70px;">Rusak</th>
                        <th style="width:60px;">Satuan</th>
                        <th style="width:110px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($laporan->details as $detail)
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                            <td class="text-center">{{ number_format($detail->quantity_diterima, 0, ',', '.') }}</td>
                            <td class="text-center">{{ number_format($detail->quantity_rusak, 0, ',', '.') }}</td>
                            <td class="text-center">{{ $detail->satuan }}</td>
                            <td class="text-right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">Total Pembelian PO {{ $laporan->po_id ?? '' }}</td>
                        <td class="text-right">Rp {{ number_format($total_po, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @empty
            <p style="text-align:center; margin:30px 0; color:#999;">Tidak ada data barang masuk.</p>
        @endforelse

        {{-- GRAND TOTAL --}}
        @if($data->count() > 0)
            <div class="total-section">
                <div class="total-spacer"></div>
                <div class="total-box">
                    <table class="total-table">
                        <tr class="grand">
                            <td class="label grand">Total Keseluruhan</td>
                            <td class="value grand">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        @endif

        {{-- FOOTER TTD --}}
        <div class="footer">
            <div class="ttd-section">
                <div class="ttd-box">
                    <div class="title">Mengetahui,</div>
                    <div class="name">_________________________</div>
                    <div class="role">Kepala Gudang</div>
                </div>
                <div class="ttd-box">
                    <div class="title">Dibuat Oleh,</div>
                    <div class="name">_________________________</div>
                    <div class="role">Staff Administrasi</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>