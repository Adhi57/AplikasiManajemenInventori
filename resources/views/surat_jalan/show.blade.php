@extends('layouts.app')
@section('page-title', 'Detail SJ - ' . $suratJalan->sj_id)
@section('content')

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-800 flex items-center justify-center shadow">
                    <i class="fa-solid fa-file-signature text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Surat Jalan</h1>
                    <p class="text-sm text-gray-500">Review dokumen surat jalan sebelum memberikan persetujuan</p>
                </div>
            </div>
            <a href="{{ route('approval.approval_surat_jalan') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    {{-- Status Header --}}
    @php
        $statusCfg = match ($suratJalan->status) {
            'Pending' => ['bg' => 'from-amber-500 to-amber-600', 'icon' => 'fa-clock', 'label' => 'Menunggu Persetujuan'],
            'Disetujui' => ['bg' => 'from-emerald-500 to-emerald-600', 'icon' => 'fa-circle-check', 'label' => 'Disetujui'],
            'Ditolak' => ['bg' => 'from-red-500 to-red-600', 'icon' => 'fa-circle-xmark', 'label' => 'Ditolak'],
            default => ['bg' => 'from-gray-500 to-gray-600', 'icon' => 'fa-question', 'label' => $suratJalan->status],
        };
    @endphp
    <div
        class="bg-gradient-to-r {{ $statusCfg['bg'] }} rounded-2xl p-5 mb-6 shadow-lg flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                <i class="fa-solid {{ $statusCfg['icon'] }} text-white text-xl"></i>
            </div>
            <div>
                <p class="text-white/70 text-xs uppercase font-bold tracking-wide">Status Surat Jalan</p>
                <p class="text-white text-lg font-bold">{{ $statusCfg['label'] }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="bg-white/20 text-white px-4 py-2 rounded-xl text-sm font-bold">
                <i class="fa-solid fa-hashtag text-xs mr-1"></i>{{ $suratJalan->sj_id }}
            </span>
            <span class="bg-white/20 text-white px-4 py-2 rounded-xl text-sm font-bold">
                <i class="fa-regular fa-calendar text-xs mr-1"></i>
                {{ $suratJalan->tanggal_surat ? \Carbon\Carbon::parse($suratJalan->tanggal_surat)->format('d M Y') : '-' }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        {{-- Info Perusahaan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-building text-red-500"></i>
                    Pengirim
                </h3>
            </div>
            <div class="p-5 space-y-3">
                <p class="font-bold text-gray-800">{{ $appSettings['nama_perusahaan'] ?? 'CV. Berkah Jaya Lumintu' }}</p>
                <p class="text-sm text-gray-600">{{ $appSettings['alamat_perusahaan'] ?? '-' }}</p>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide">Dibuat Oleh</p>
                    <p class="text-sm font-semibold text-gray-800 flex items-center gap-2 mt-0.5">
                        <span class="w-6 h-6 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-user text-red-500 text-[9px]"></i>
                        </span>
                        {{ $suratJalan->user->nama_lengkap ?? '-' }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Info Pelanggan / Tujuan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                    <i class="fa-solid fa-location-dot text-red-500"></i>
                    Tujuan Pengiriman
                </h3>
            </div>
            <div class="p-5 space-y-3">
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide">Pelanggan</p>
                    <p class="text-sm font-bold text-gray-800">{{ $suratJalan->pelanggan->nama_pelanggan ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide">Penerima</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $suratJalan->nama_penerima ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide">Alamat</p>
                    <p class="text-sm text-gray-600">{{ $suratJalan->alamat_penerima ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide">PIC</p>
                    <p class="text-sm text-gray-600">{{ $suratJalan->pelanggan->PIC ?? '-' }}</p>
                </div>
            </div>
        </div>

        {{-- Info Keuangan Quick Summary --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-5 py-3 border-b border-gray-100 bg-red-800">
                <h3 class="font-bold text-white text-sm flex items-center gap-2">
                    <i class="fa-solid fa-calculator"></i>
                    Ringkasan Nilai
                </h3>
            </div>
            @php
                $subtotal = 0;
                foreach ($suratJalan->details as $d) {
                    $subtotal += $d->quantity * ($d->harga_satuan ?? 0);
                }
                $diskonPersen = floatval($suratJalan->diskon_pelanggan ?? 0);
                $diskon = $subtotal * ($diskonPersen / 100);
                $biayaKirim = floatval($suratJalan->biaya_pengiriman ?? 0);
                $total = $subtotal + $biayaKirim - $diskon;
                $ppnRate = floatval($appSettings['ppn_persen'] ?? 11) / 100;
                $pajak = $total * $ppnRate;
                $grandTotal = $total + $pajak;
            @endphp
            <div class="p-5 space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Subtotal</span>
                    <span class="text-sm font-semibold text-gray-800">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Biaya Kirim</span>
                    <span class="text-sm text-gray-700">Rp {{ number_format($biayaKirim, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Diskon ({{ $diskonPersen }}%)</span>
                    <span class="text-sm text-gray-700">- Rp {{ number_format($diskon, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center border-t border-gray-100 pt-2">
                    <span class="text-xs text-gray-500">Pajak ({{ $appSettings['ppn_persen'] ?? 11 }}%)</span>
                    <span class="text-sm text-red-600 font-semibold">Rp {{ number_format($pajak, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center border-t-2 border-gray-200 pt-3">
                    <span class="text-sm font-bold text-gray-800">Total Akhir</span>
                    <span class="text-lg font-bold text-emerald-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Item Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked text-red-500"></i>
                Daftar Barang
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-xs uppercase text-gray-500 tracking-wider border-b">
                        <th class="px-4 py-3 text-left">No</th>
                        <th class="px-4 py-3 text-left">Nama Barang</th>
                        <th class="px-4 py-3 text-left">Kode</th>
                        <th class="px-4 py-3 text-center">Qty</th>
                        <th class="px-4 py-3 text-center">Satuan</th>
                        <th class="px-4 py-3 text-right">Harga Satuan</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($suratJalan->details as $index => $detail)
                        @php
                            $hargaSatuan = $detail->harga_satuan ?? 0;
                            $totalItem = $detail->quantity * $hargaSatuan;
                        @endphp
                        <tr class="hover:bg-red-50/30 transition">
                            <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-medium text-gray-800">{{ $detail->barang->nama_barang ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded text-xs font-mono">{{ $detail->kode_barang }}</span>
                            </td>
                            <td class="px-4 py-3 text-center font-semibold">{{ number_format($detail->quantity, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center text-gray-600">{{ $detail->satuan ?? '-' }}</td>
                            <td class="px-4 py-3 text-right text-gray-700">Rp {{ number_format($hargaSatuan, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp
                                {{ number_format($totalItem, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

                <tfoot class="bg-gray-50">
                    <tr class="border-t border-gray-200">
                        <td colspan="6" class="px-4 py-2.5 text-right font-semibold text-gray-600">Subtotal</td>
                        <td class="px-4 py-2.5 text-right font-semibold text-gray-800">Rp
                            {{ number_format($subtotal, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="px-4 py-2.5 text-right font-semibold text-gray-600">Biaya Pengiriman</td>
                        <td class="px-4 py-2.5 text-right text-gray-700">Rp {{ number_format($biayaKirim, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="px-4 py-2.5 text-right font-semibold text-gray-600">Diskon Pelanggan
                            ({{ $diskonPersen }}%)</td>
                        <td class="px-4 py-2.5 text-right text-gray-700">- Rp {{ number_format($diskon, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="border-t border-gray-200">
                        <td colspan="6" class="px-4 py-2.5 text-right font-bold text-gray-700">Total</td>
                        <td class="px-4 py-2.5 text-right font-bold text-gray-800">Rp
                            {{ number_format($total, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="6" class="px-4 py-2.5 text-right font-semibold text-gray-600">Pajak
                            ({{ $appSettings['ppn_persen'] ?? 11 }}%)</td>
                        <td class="px-4 py-2.5 text-right text-red-600 font-semibold">Rp
                            {{ number_format($pajak, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="border-t-2 border-gray-300">
                        <td colspan="6" class="px-4 py-3 text-right font-bold text-gray-800 text-base">Total Akhir</td>
                        <td class="px-4 py-3 text-right font-bold text-emerald-700 text-base">Rp
                            {{ number_format($grandTotal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection