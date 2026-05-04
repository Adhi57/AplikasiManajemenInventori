<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Surat Jalan - {{ $suratJalan->sj_id }}</title>
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
            width: 50%;
        }

        .total-box {
            display: table-cell;
            width: 50%;
        }

        .total-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10pt;
        }

        .total-table td {
            padding: 4px 10px;
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

        .total-table .subtotal-row {
            border-top: 1px solid #ccc;
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

        /* === FOOTER / TTD === */
        .footer {
            margin-top: 25px;
        }

        .ttd-section {
            display: table;
            width: 100%;
            margin-top: 20px;
        }

        .ttd-box {
            display: table-cell;
            width: 33.3%;
            text-align: center;
            font-size: 10pt;
            vertical-align: top;
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
                <img src="{{ public_path('assets/images/logo.png') }}" alt="Logo">
            </div>
            <div class="kop-text">
                <h1>{{ $appSettings['nama_perusahaan'] ?? 'CV. Berkah Jaya Lumintu' }}</h1>
                <h2>Distributor Bahan Pokok & Kebutuhan Sehari-hari</h2>
                <p>{{ $appSettings['alamat_perusahaan'] ?? '-' }}</p>
                <p>Telp: {{ $appSettings['telepon_perusahaan'] ?? '-' }} &bull; Email:
                    {{ $appSettings['email_perusahaan'] ?? '-' }}
                </p>
            </div>
        </div>

        {{-- DOCUMENT TITLE --}}
        <div class="doc-title">
            <h2>Surat Jalan</h2>
            <p>No: {{ $suratJalan->sj_id }}</p>
        </div>

        {{-- STATUS --}}
        @php
            $statusClass = match ($suratJalan->status) {
                'Pending' => 'status-pending',
                'Disetujui' => 'status-disetujui',
                'Ditolak' => 'status-ditolak',
                default => 'status-pending',
            };
        @endphp

        {{-- INFO --}}
        <div class="info-section">
            <table class="info-table">
                <tr>
                    <td class="label">Tanggal</td>
                    <td class="separator">:</td>
                    <td>{{ $suratJalan->tanggal_surat ? \Carbon\Carbon::parse($suratJalan->tanggal_surat)->format('d F Y') : '-' }}
                    </td>
                    <td class="label" style="text-align:right">Status</td>
                    <td class="separator">:</td>
                    <td><span class="{{ $statusClass }} status-badge">{{ $suratJalan->status }}</span></td>
                </tr>
                <tr>
                    <td class="label">Dibuat Oleh</td>
                    <td class="separator">:</td>
                    <td>{{ $suratJalan->user->nama_lengkap ?? '-' }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </div>

        {{-- PENGIRIM & PENERIMA --}}
        <div style="display:table; width:100%; margin-bottom:15px;">
            <div
                style="display:table-cell; width:48%; padding:10px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:4px; vertical-align:top;">
                <table class="info-table">
                    <tr>
                        <td colspan="3" style="font-weight:bold; color:#8b0000; font-size:11pt; padding-bottom:5px;">
                            Pengirim</td>
                    </tr>
                    <tr>
                        <td class="label">Perusahaan</td>
                        <td class="separator">:</td>
                        <td><strong>{{ $appSettings['nama_perusahaan'] ?? 'CV. Berkah Jaya Lumintu' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Alamat</td>
                        <td class="separator">:</td>
                        <td>{{ $appSettings['alamat_perusahaan'] ?? '-' }}</td>
                    </tr>
                </table>
            </div>
            <div style="display:table-cell; width:4%;"></div>
            <div
                style="display:table-cell; width:48%; padding:10px; background:#f9fafb; border:1px solid #e5e7eb; border-radius:4px; vertical-align:top;">
                <table class="info-table">
                    <tr>
                        <td colspan="3" style="font-weight:bold; color:#8b0000; font-size:11pt; padding-bottom:5px;">
                            Penerima</td>
                    </tr>
                    <tr>
                        <td class="label">Pelanggan</td>
                        <td class="separator">:</td>
                        <td><strong>{{ $suratJalan->pelanggan->nama_pelanggan ?? '-' }}</strong></td>
                    </tr>
                    <tr>
                        <td class="label">Penerima</td>
                        <td class="separator">:</td>
                        <td>{{ $suratJalan->nama_penerima ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alamat</td>
                        <td class="separator">:</td>
                        <td>{{ $suratJalan->alamat_penerima ?? '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- ITEM TABLE --}}
        <p style="font-size:10pt; margin-bottom:5px; font-weight:bold;">Barang-barang yang dikirimkan sebagai berikut:
        </p>
        <table class="item-table">
            <thead>
                <tr>
                    <th style="width:30px;">No</th>
                    <th style="text-align:left;">Nama Barang</th>
                    <th style="width:80px;">Kode</th>
                    <th style="width:55px;">Qty</th>
                    <th style="width:55px;">Satuan</th>
                    <th style="width:105px;">Harga Satuan</th>
                    <th style="width:105px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @foreach ($suratJalan->details as $i => $detail)
                    @php
                        $harga = $detail->harga_satuan ?? 0;
                        $itemTotal = $detail->quantity * $harga;
                        $subtotal += $itemTotal;
                    @endphp
                    <tr>
                        <td class="text-center">{{ $i + 1 }}</td>
                        <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                        <td class="text-center" style="font-size:9pt;">{{ $detail->kode_barang }}</td>
                        <td class="text-center">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                        <td class="text-center">{{ $detail->satuan ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- TOTAL --}}
        @php
            $diskonPersen = floatval($suratJalan->diskon_pelanggan ?? 0);
            $diskon = $subtotal * ($diskonPersen / 100);
            $biayaKirim = floatval($suratJalan->biaya_pengiriman ?? 0);
            $total = $subtotal + $biayaKirim - $diskon;
            $ppnRate = floatval($appSettings['ppn_persen'] ?? 11) / 100;
            $pajak = $total * $ppnRate;
            $grandTotal = $total + $pajak;
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
                        <td class="label">Biaya Kirim</td>
                        <td class="value">Rp {{ number_format($biayaKirim, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Diskon ({{ $diskonPersen }}%)</td>
                        <td class="value">- Rp {{ number_format($diskon, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="subtotal-row">
                        <td class="label subtotal-row" style="font-weight:bold;">Total</td>
                        <td class="value subtotal-row">Rp {{ number_format($total, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Pajak ({{ $appSettings['ppn_persen'] ?? 11 }}%)</td>
                        <td class="value">Rp {{ number_format($pajak, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="grand">
                        <td class="label grand">Total Akhir</td>
                        <td class="value grand">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- FOOTER TTD --}}
        <div class="footer">
            <div class="ttd-section">
                <div class="ttd-box">
                    <div class="title">Pengirim,</div>
                    <div class="name">{{ $suratJalan->user->nama_lengkap ?? '-' }}</div>
                    <div class="role">{{ $suratJalan->user->role ?? '-' }}</div>
                </div>
                <div class="ttd-box">
                    <div class="title">Disetujui Oleh,</div>
                    <div class="name">{{ $suratJalan->approver->nama_lengkap ?? '-' }}</div>
                    <div class="role">{{ $suratJalan->approver->role ?? '-' }}</div>
                </div>
                <div class="ttd-box">
                    <div class="title">Penerima,</div>
                    <div class="name">{{ $suratJalan->nama_penerima ?? '______________________' }}</div>
                    <div class="role">Pelanggan</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>