<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Keluar</title>
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

        .info-section {
            margin-bottom: 15px;
            font-size: 10pt;
        }

        .info-section .label {
            font-weight: bold;
            color: #444;
        }

        .sj-header {
            background: #f9fafb;
            padding: 8px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            margin-top: 20px;
            margin-bottom: 5px;
        }

        .sj-header h3 {
            font-size: 11pt;
            color: #8b0000;
            margin: 0;
        }

        .sj-header p {
            font-size: 9pt;
            color: #555;
            margin: 0;
        }

        .item-table {
            width: 100%;
            border-collapse: collapse;
            margin: 5px 0 10px;
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
            padding: 5px 10px;
            font-size: 9pt;
        }

        .item-table tfoot .total-row td {
            border-top: 2px solid #ccc;
            font-weight: bold;
            font-size: 10pt;
        }

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
                <h1>{{ $appSettings['nama_perusahaan'] ?? 'CV. Berkah Jaya Lumintu' }}</h1>
                <h2>Distributor Bahan Pokok & Kebutuhan Sehari-hari</h2>
                <p>{{ $appSettings['alamat_perusahaan'] ?? '-' }}</p>
                <p>Telp: {{ $appSettings['telepon_perusahaan'] ?? '-' }} &bull; Email: {{ $appSettings['email_perusahaan'] ?? '-' }}</p>
            </div>
        </div>

        {{-- DOCUMENT TITLE --}}
        <div class="doc-title">
            <h2>Laporan Barang Keluar</h2>
            <p>Periode: {{ $start ?? '-' }} s/d {{ $end ?? '-' }}</p>
        </div>

        <div class="info-section">
            <span class="label">Tanggal Cetak:</span> {{ \Carbon\Carbon::now()->translatedFormat('d F Y, H:i') }}
            &nbsp;&nbsp;|&nbsp;&nbsp;
            <span class="label">Jumlah SJ:</span> {{ $data->count() }}
        </div>

        @php $grandTotal = 0;
        $no = 1; @endphp

        @forelse ($data as $lap)
            @php $totalBarang = 0; @endphp

            <div class="sj-header">
                <h3>Surat Jalan: {{ $lap->sj_id ?? '-' }}</h3>
                <p>
                    Pelanggan: {{ $lap->pengiriman->suratJalan->pelanggan->nama_pelanggan ?? '-' }}
                    &nbsp;|&nbsp;
                    Tanggal Keluar: {{ \Carbon\Carbon::parse($lap->tanggal_keluar)->format('d/m/Y H:i') }}
                </p>
            </div>

            <table class="item-table">
                <thead>
                    <tr>
                        <th style="width:30px;">No</th>
                        <th style="text-align:left;">Nama Barang</th>
                        <th style="width:80px;">Kode</th>
                        <th style="width:70px;">Jumlah</th>
                        <th style="width:100px;">Harga Jual</th>
                        <th style="width:110px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lap->details as $detail)
                        @php
                            $sjDetail = $lap->pengiriman->suratJalan->details->firstWhere('kode_barang', $detail->kode_barang);
                            $harga = $sjDetail->harga_satuan ?? $detail->harga_jual;
                            $itemSubtotal = $detail->jumlah_keluar * $harga;
                            $totalBarang += $itemSubtotal;
                        @endphp
                        <tr>
                            <td class="text-center">{{ $no++ }}</td>
                            <td>{{ $detail->barang->nama_barang ?? '-' }}</td>
                            <td class="text-center" style="font-size:9pt;">{{ $detail->kode_barang }}</td>
                            <td class="text-center">{{ number_format($detail->jumlah_keluar, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($itemSubtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                @php
                    $sj = $lap->pengiriman->suratJalan;
                    $biayaKirim = floatval($sj->biaya_pengiriman ?? 0);
                    $diskonPersen = floatval($sj->diskon_pelanggan ?? 0);
                    $diskon = $totalBarang * ($diskonPersen / 100);
                    $total = $totalBarang + $biayaKirim - $diskon;
                    $ppnRate = floatval($appSettings['ppn_persen'] ?? 11) / 100;
                    $pajak = $total * $ppnRate;
                    $sjGrandTotal = $total + $pajak;
                    $grandTotal += $sjGrandTotal;
                @endphp
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-right">Total Barang:</td>
                        <td class="text-right">Rp {{ number_format($totalBarang, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-right">Biaya Kirim:</td>
                        <td class="text-right">Rp {{ number_format($biayaKirim, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-right">Diskon ({{ $diskonPersen }}%):</td>
                        <td class="text-right">- Rp {{ number_format($diskon, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td colspan="5" class="text-right">Pajak ({{ $appSettings['ppn_persen'] ?? 11 }}%):</td>
                        <td class="text-right">Rp {{ number_format($pajak, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="total-row">
                        <td colspan="5" class="text-right">Grand Total SJ:</td>
                        <td class="text-right">Rp {{ number_format($sjGrandTotal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @empty
            <p style="text-align:center; margin:30px 0; color:#999;">Tidak ada data barang keluar.</p>
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
                    <div class="role">{{ auth()->user()->nama_lengkap }}</div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>