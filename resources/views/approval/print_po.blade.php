<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Purchase Order - {{ $purchaseOrder->po_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            color: #333;
            line-height: 1.5;
        }

        .page {
            padding: 30px 50px;
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
        }

        .info-table {
            width: 100%;
            font-size: 10pt;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .info-table .label {
            width: 130px;
            font-weight: bold;
            color: #444;
        }

        .info-table .separator {
            width: 15px;
            text-align: center;
        }

        /* === ITEM TABLE === */
        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
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
            padding: 7px 10px;
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

        /* === TOTAL TABLE === */
        .total-section {
            display: table;
            width: 100%;
            margin-top: 5px;
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

        /* === STATUS BADGE === */
        .status-badge {
            display: inline-block;
            padding: 3px 15px;
            border-radius: 4px;
            font-size: 10pt;
            font-weight: bold;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fcd34d;
        }

        .status-disetujui {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .status-ditolak {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .status-diterima {
            background: #dbeafe;
            color: #1e40af;
            border: 1px solid #93c5fd;
        }

        /* === FOOTER / TTD === */
        .footer {
            margin-top: 30px;
        }

        .terms {
            font-size: 9pt;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            margin-bottom: 20px;
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
            <h2>Purchase Order</h2>
            <p>No: {{ $purchaseOrder->po_id }}</p>
        </div>

        {{-- STATUS --}}
        @php
            $statusClass = match ($purchaseOrder->status_po) {
                'Pending' => 'status-pending',
                'Disetujui' => 'status-disetujui',
                'Ditolak' => 'status-ditolak',
                'Diterima' => 'status-diterima',
                default => 'status-pending',
            };
        @endphp

        {{-- INFO --}}
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td class="label">Tanggal</td>
                    <td class="separator">:</td>
                    <td>{{ \Carbon\Carbon::parse($purchaseOrder->tanggal_po)->format('d F Y') }}</td>
                    <td class="label" style="text-align:right">Status</td>
                    <td class="separator">:</td>
                    <td><span class="{{ $statusClass }} status-badge">{{ $purchaseOrder->status_po }}</span></td>
                </tr>
                <tr>
                    <td class="label">Dibuat Oleh</td>
                    <td class="separator">:</td>
                    <td>{{ $purchaseOrder->user->nama_lengkap ?? '-' }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>

        {{-- SUPPLIER INFO --}}
        <div class="info-section"
            style="background: #f9fafb; padding: 10px; border: 1px solid #e5e7eb; border-radius: 4px;">
            <table class="info-table">
                <tr>
                    <td colspan="6" style="font-weight:bold; color:#8b0000; font-size:11pt; padding-bottom:5px;">Kepada
                        Yth.</td>
                </tr>
                <tr>
                    <td class="label">Supplier</td>
                    <td class="separator">:</td>
                    <td colspan="4"><strong>{{ $purchaseOrder->supplier->namaSupplier ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td class="label">Alamat</td>
                    <td class="separator">:</td>
                    <td colspan="4">{{ $purchaseOrder->supplier->alamatSupplier ?? '-' }}</td>
                </tr>
                <tr>
                    <td class="label">Telp</td>
                    <td class="separator">:</td>
                    <td>{{ $purchaseOrder->supplier->noTelepon ?? '-' }}</td>
                    <td class="label" style="text-align:right">Email</td>
                    <td class="separator">:</td>
                    <td>{{ $purchaseOrder->supplier->email ?? '-' }}</td>
                </tr>
            </table>
        </div>

        {{-- ITEM TABLE --}}
        <p style="font-size:10pt; margin-bottom:5px; font-weight:bold;">Dengan ini kami bermaksud memesan barang-barang
            sebagai berikut:</p>
        <table class="item-table">
            <thead>
                <tr>
                    <th style="width:30px;">No</th>
                    <th style="text-align:left;">Nama Barang</th>
                    <th style="width:80px;">Kode</th>
                    <th style="width:60px;">Qty</th>
                    <th style="width:60px;">Satuan</th>
                    <th style="width:110px;">Harga Satuan</th>
                    <th style="width:110px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach ($purchaseOrder->details as $i => $detail)
                    @php
                        $itemTotal = $detail->quantity * $detail->harga_satuan;
                        $subtotal += $itemTotal;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                        <td class="text-center" style="font-size:9pt;">{{ $detail->kode_barang }}</td>
                        <td class="text-center">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $detail->satuan }}</td>
                        <td class="text-right">Rp {{ number_format($detail->harga_satuan, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- TOTAL --}}
        @php
            $pajak = $subtotal * 0.11;
            $grandTotal = $subtotal + $pajak;
        @endphp
        <div class="total-section">
            <div class="total-spacer"></div>
            <div class="total-box">
                <table class="total-table">
                    <tr>
                        <td class="label">Subtotal</td>
                        <td class="value">Rp {{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pajak (11%)</td>
                        <td class="value">Rp {{ number_format($pajak, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grand">
                        <td class="label grand">Jumlah Total</td>
                        <td class="value grand">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="footer">
            <div class="terms">
                <strong>Syarat & Ketentuan:</strong><br>
                1. Pembayaran dilakukan maksimal 30 hari setelah tanggal PO.<br>
                2. Barang yang dikirim harus sesuai dengan spesifikasi yang tertera.<br>
                3. Keterlambatan pengiriman akan dikenakan sanksi sesuai perjanjian.
            </div>

            <div class="ttd-section">
                <div class="ttd-box">
                    <div class="title">Dibuat Oleh,</div>
                    <div class="name">{{ $purchaseOrder->user->nama_lengkap ?? '-' }}</div>
                    <div class="role">Procurement</div>
                </div>
                <div class="ttd-box">
                    <div class="title">Disetujui Oleh,</div>
                    <div class="name">{{ Auth::user()->nama_lengkap }}</div>
                    <div class="role">Kepala Gudang</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>