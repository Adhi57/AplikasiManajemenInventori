@extends('layouts.app')

@section('page-title', 'Laporan Barang Keluar')

@section('content')

    @php
        $totalSJ = $laporans->count();
        $totalItemsKeluar = $laporans->sum(fn($l) => $l->details->sum('jumlah_keluar'));
        $grandTotalValue = 0;
        foreach ($laporans as $lap) {
            $tb = 0;
            foreach ($lap->details as $d) {
                $sjd = $lap->pengiriman->suratJalan->details->firstWhere('kode_barang', $d->kode_barang);
                $h = $sjd->harga_satuan ?? $d->harga_jual;
                $tb += $d->jumlah_keluar * $h;
            }
            $sj = $lap->pengiriman->suratJalan;
            $bk = floatval($sj->biaya_pengiriman ?? 0);
            $dp = floatval($sj->diskon_pelanggan ?? 0);
            $dk = $tb * ($dp / 100);
            $t = $tb + $bk - $dk;
            $grandTotalValue += ($t + $t * floatval($appSettings['ppn_persen'] ?? 11) / 100);
        }
    @endphp

    <div class="space-y-6">
        {{-- HEADER --}}
        <x-page-header title="Laporan Barang Keluar" description="Ringkasan pengiriman barang ke pelanggan berdasarkan Surat Jalan." icon="fa-boxes-packing">
            <x-slot name="actions">
                <a href="{{ route('laporan.barang_keluar.cetak', request()->all()) }}" target="_blank"
                    class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                    <i class="fa-solid fa-file-pdf text-amber-400"></i>
                    <span>Export PDF</span>
                </a>
            </x-slot>
        </x-page-header>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-red-100 flex items-center justify-center"><i
                            class="fa-solid fa-file-invoice text-red-700"></i></div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Surat Jalan</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalSJ, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center"><i
                            class="fa-solid fa-boxes-stacked text-amber-700"></i></div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Item Keluar</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($totalItemsKeluar, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-emerald-100 flex items-center justify-center"><i
                            class="fa-solid fa-money-bill-wave text-emerald-700"></i></div>
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Grand Total Nilai</p>
                        <p class="text-lg font-bold text-gray-900">Rp {{ number_format($grandTotalValue, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form method="GET" action="{{ route('laporan.barang_keluar.index') }}"
                class="flex flex-col md:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Dari
                        Tanggal</label>
                    <input type="date" name="dari" value="{{ request('dari') }}"
                        class="w-full py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Sampai
                        Tanggal</label>
                    <input type="date" name="sampai" value="{{ request('sampai') }}"
                        class="w-full py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                </div>
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Cari Barang /
                        Pelanggan</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i
                                class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i></div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Nama barang / pelanggan..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                    </div>
                </div>
                <div class="flex gap-2 w-full md:w-auto">
                    <button type="submit"
                        class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-800 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        <i class="fa-solid fa-filter text-xs"></i> Filter
                    </button>
                    <a href="{{ route('laporan.barang_keluar.index') }}"
                        class="flex-1 md:flex-none inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition-colors">
                        <i class="fa-solid fa-rotate-right text-xs"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- DATA --}}
        @forelse ($laporans as $lap)
            @php $totalBarang = 0; @endphp
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div
                    class="px-5 py-4 bg-gradient-to-r from-red-50 to-white border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 bg-red-800 text-white text-xs font-bold rounded-lg">SJ</span>
                            <span class="font-bold text-gray-900">{{ $lap->sj_id ?? '-' }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1"><i class="fa-solid fa-user text-xs text-gray-400 mr-1"></i>
                            Pelanggan: <strong>{{ $lap->pengiriman->suratJalan->pelanggan->nama_pelanggan ?? '-' }}</strong></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1.5 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg"><i
                                class="fa-solid fa-arrow-up text-[10px] mr-1"></i> Terkirim</span>
                        <span class="text-sm text-gray-500"><i class="fa-regular fa-calendar text-xs mr-1"></i>
                            {{ \Carbon\Carbon::parse($lap->tanggal_keluar)->translatedFormat('d M Y H:i') }}</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 text-gray-600">
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Kode</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Nama Barang</th>
                                <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider">Jumlah</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider">Harga Jual</th>
                                <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($lap->details as $detail)
                                @php
                                    $sjDetail = $lap->pengiriman->suratJalan->details->firstWhere('kode_barang', $detail->kode_barang);
                                    $harga = $sjDetail->harga_satuan ?? $detail->harga_jual;
                                    $itemSubtotal = $detail->jumlah_keluar * $harga;
                                    $totalBarang += $itemSubtotal;
                                @endphp
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $detail->kode_barang }}</td>
                                    <td class="px-5 py-3 font-medium text-gray-800">{{ $detail->barang->nama_barang ?? '-' }}</td>
                                    <td class="px-5 py-3 text-center"><span
                                            class="px-2 py-0.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-md">{{ number_format($detail->jumlah_keluar, 0, ',', '.') }}
                                            {{ $detail->barang->satuan_jual ?? '' }}</span></td>
                                    <td class="px-5 py-3 text-right text-gray-600">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-right font-semibold text-gray-800">Rp
                                        {{ number_format($itemSubtotal, 0, ',', '.') }}</td>
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
                            $grandTotal = $total + $pajak;
                        @endphp
                        <tfoot class="bg-gray-50">
                            <tr class="border-t border-gray-200">
                                <td colspan="4" class="px-5 py-2.5 text-right text-sm text-gray-600">Total Barang:</td>
                                <td class="px-5 py-2.5 text-right text-sm font-semibold text-gray-800">Rp
                                    {{ number_format($totalBarang, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-5 py-2 text-right text-sm text-gray-600">Biaya Kirim:</td>
                                <td class="px-5 py-2 text-right text-sm text-gray-700">Rp
                                    {{ number_format($biayaKirim, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-5 py-2 text-right text-sm text-gray-600">Diskon
                                    ({{ $diskonPersen }}%):</td>
                                <td class="px-5 py-2 text-right text-sm text-gray-700">- Rp
                                    {{ number_format($diskon, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-t border-gray-200">
                                <td colspan="4" class="px-5 py-2.5 text-right text-sm font-bold text-gray-700">Total:</td>
                                <td class="px-5 py-2.5 text-right text-sm font-bold text-gray-800">Rp
                                    {{ number_format($total, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="px-5 py-2 text-right text-sm text-gray-600">Pajak ({{ $appSettings['ppn_persen'] ?? 11 }}%):</td>
                                <td class="px-5 py-2 text-right text-sm text-red-600">Rp
                                    {{ number_format($pajak, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-t-2 border-gray-300">
                                <td colspan="4" class="px-5 py-3 text-right text-base font-bold text-gray-900">Grand Total:</td>
                                <td class="px-5 py-3 text-right text-base font-bold text-red-700">Rp
                                    {{ number_format($grandTotal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4"><i
                        class="fa-solid fa-inbox text-2xl text-gray-400"></i></div>
                <h3 class="text-lg font-semibold text-gray-700 mb-1">Belum Ada Data</h3>
                <p class="text-sm text-gray-500 mb-4">Belum ada laporan barang keluar sesuai filter.</p>
                <a href="{{ route('laporan.barang_keluar.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-red-800 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm"><i
                        class="fa-solid fa-rotate-right text-xs"></i> Reset Filter</a>
            </div>
        @endforelse

        @if($laporans->count() > 0)
            <div
                class="flex items-center justify-between text-sm text-gray-500 bg-white rounded-2xl shadow-sm border border-gray-100 px-5 py-3">
                <span>Menampilkan <strong class="text-gray-700">{{ $laporans->count() }}</strong> laporan.</span>
                <span class="font-semibold text-red-700">Grand Total: Rp
                    {{ number_format($grandTotalValue, 0, ',', '.') }}</span>
            </div>
        @endif
    </div>
@endsection