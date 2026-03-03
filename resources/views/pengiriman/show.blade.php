@extends('layouts.app')

@section('page-title', 'Detail Pengiriman: ' . ($pengiriman->sj_id ?? 'N/A'))

@section('content')

    {{-- Print Styles --}}
    <style>
        @media print {

            #main-topbar,
            #main-navbar,
            .no-print {
                display: none !important;
            }

            body,
            #app {
                margin: 0 !important;
                padding: 0 !important;
                background: white !important;
            }

            .print-container {
                max-width: none !important;
                margin: 0 !important;
                padding: 20px !important;
                box-shadow: none !important;
                border: none !important;
            }

            .print-break-inside-avoid {
                break-inside: avoid;
            }

            @page {
                margin: 15mm;
                size: A4;
            }
        }
    </style>

    {{-- Action Bar (hidden on print) --}}
    <div class="mb-6 no-print">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-800 flex items-center justify-center shadow">
                    <i class="fa-solid fa-truck-fast text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Detail Pengiriman</h1>
                    <p class="text-sm text-gray-500">Informasi lengkap pengiriman barang</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                {{-- Print Button --}}
                <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 text-gray-700 font-medium text-sm rounded-xl hover:bg-gray-50 transition shadow-sm">
                    <i class="fa-solid fa-print text-gray-500"></i>
                    Cetak PDF
                </button>
                {{-- Edit Button --}}
                @if ($pengiriman->status_pengiriman !== 'Terkirim')
                    <a href="{{ route('pengiriman.edit', $pengiriman->pengiriman_id) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-amber-50 border border-amber-200 text-amber-700 font-medium text-sm rounded-xl hover:bg-amber-100 transition">
                        <i class="fa-solid fa-pen"></i>
                        Edit
                    </a>
                @endif
                {{-- Mark Terkirim Button --}}
                @if($pengiriman->status_pengiriman !== 'Terkirim' && $pengiriman->status_pengiriman !== 'Menunggu' && $pengiriman->suratJalan->status == 'Disetujui')
                    <form action="{{ route('pengiriman.updateStatus', $pengiriman->pengiriman_id) }}" method="POST"
                        class="inline" id="form-terkirim">
                        @csrf
                        <input type="hidden" name="status_pengiriman" value="Terkirim">
                        <button type="button" onclick="confirmTerkirim()"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white font-semibold text-sm rounded-xl hover:bg-emerald-700 transition shadow-sm">
                            <i class="fa-solid fa-circle-check"></i>
                            Tandai Terkirim
                        </button>
                    </form>
                @endif
                {{-- Back --}}
                <a href="{{ route('pengiriman.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <div class="print-container space-y-6">

        {{-- ===== Print Header (only visible on print) ===== --}}
        <div class="hidden print:block text-center mb-6">
            <h2 class="text-xl font-bold mb-1">DETAIL PENGIRIMAN BARANG</h2>
            <p class="text-sm font-semibold">CV. Berkah Jaya Lumintu</p>
            <p class="text-xs">Jl. Wonokerso I, Wonokerso, Kecamatan Tembarak, Kabupaten Temanggung</p>
            <hr class="mt-4 border-gray-400">
        </div>

        {{-- ===== Status & SJ Header ===== --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden print-break-inside-avoid">
            <div class="bg-gradient-to-r from-red-800 to-red-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-200 text-xs font-semibold uppercase tracking-wide">Nomor Surat Jalan</p>
                        <h2 class="text-white text-xl font-bold mt-0.5">{{ $pengiriman->sj_id ?? 'N/A' }}</h2>
                    </div>
                    <div class="flex items-center gap-3">
                        @php
                            $statusConfig = [
                                'Menunggu' => ['bg' => 'bg-amber-400', 'text' => 'text-amber-900', 'icon' => 'fa-clock'],
                                'Dalam Perjalanan' => ['bg' => 'bg-blue-400', 'text' => 'text-blue-900', 'icon' => 'fa-truck'],
                                'Terkirim' => ['bg' => 'bg-emerald-400', 'text' => 'text-emerald-900', 'icon' => 'fa-circle-check'],
                                'Dibatalkan' => ['bg' => 'bg-red-400', 'text' => 'text-red-900', 'icon' => 'fa-circle-xmark'],
                            ];
                            $cfg = $statusConfig[$pengiriman->status_pengiriman] ?? ['bg' => 'bg-gray-400', 'text' => 'text-gray-900', 'icon' => 'fa-question'];
                        @endphp
                        <span
                            class="inline-flex items-center gap-1.5 px-4 py-1.5 {{ $cfg['bg'] }} {{ $cfg['text'] }} rounded-full text-sm font-bold">
                            <i class="fa-solid {{ $cfg['icon'] }} text-xs"></i>
                            {{ $pengiriman->status_pengiriman }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Status Timeline --}}
            <div class="px-6 py-4 bg-gray-50/50">
                @php
                    $steps = ['Menunggu', 'Dalam Perjalanan', 'Terkirim'];
                    $currentIdx = array_search($pengiriman->status_pengiriman, $steps);
                    if ($currentIdx === false)
                        $currentIdx = -1;
                @endphp
                <div class="flex items-center justify-between">
                    @foreach($steps as $idx => $step)
                        <div class="flex items-center {{ $idx < count($steps) - 1 ? 'flex-1' : '' }}">
                            <div class="flex flex-col items-center">
                                <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                                    {{ $idx <= $currentIdx ? 'bg-emerald-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                                    @if($idx < $currentIdx)
                                        <i class="fa-solid fa-check"></i>
                                    @elseif($idx == $currentIdx)
                                        <i class="fa-solid fa-circle text-[6px]"></i>
                                    @else
                                        {{ $idx + 1 }}
                                    @endif
                                </div>
                                <span
                                    class="text-[10px] font-semibold mt-1.5 {{ $idx <= $currentIdx ? 'text-emerald-700' : 'text-gray-400' }}">{{ $step }}</span>
                            </div>
                            @if($idx < count($steps) - 1)
                                <div
                                    class="flex-1 h-0.5 mx-2 mt-[-16px] {{ $idx < $currentIdx ? 'bg-emerald-400' : 'bg-gray-200' }}">
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ===== Info Cards Grid ===== --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- Informasi Pengiriman --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden print-break-inside-avoid">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-truck text-red-500"></i>
                        Informasi Pengiriman
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide mb-0.5">Tanggal Kirim</p>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $pengiriman->tanggal_pengiriman ? date('d F Y', strtotime($pengiriman->tanggal_pengiriman)) : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide mb-0.5">Tanggal Sampai</p>
                            <p
                                class="text-sm font-semibold {{ $pengiriman->tanggal_sampai ? 'text-emerald-700' : 'text-gray-800' }}">
                                {{ $pengiriman->tanggal_sampai ? date('d F Y', strtotime($pengiriman->tanggal_sampai)) : '-' }}
                            </p>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide mb-0.5">Nama Driver</p>
                            <p class="text-sm font-semibold text-gray-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-id-card text-gray-400 text-xs"></i>
                                {{ $pengiriman->nama_driver ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide mb-0.5">Nomor Polisi</p>
                            <p class="text-sm font-bold text-gray-800 font-mono uppercase">
                                {{ $pengiriman->no_polisi ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide mb-0.5">Nama Kendaraan</p>
                        <p class="text-sm font-semibold text-gray-800 flex items-center gap-1.5">
                            <i class="fa-solid fa-car text-gray-400 text-xs"></i>
                            {{ $pengiriman->nama_kendaraan ?? '-' }}
                        </p>
                    </div>
                    @if($pengiriman->catatan)
                        <div class="mt-2 pt-3 border-t border-gray-100">
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide mb-1">Catatan</p>
                            <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl text-sm text-gray-700 italic">
                                {{ $pengiriman->catatan }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Informasi Tujuan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden print-break-inside-avoid">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-location-dot text-red-500"></i>
                        Informasi Tujuan
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide mb-0.5">Pelanggan</p>
                        <p class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                            <span class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-building text-red-500 text-[10px]"></i>
                            </span>
                            {{ $pengiriman->suratJalan->pelanggan->nama_pelanggan ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide mb-0.5">Penerima</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $pengiriman->suratJalan->nama_penerima ?? '-' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide mb-0.5">Alamat Pengiriman</p>
                        <p class="text-sm text-gray-700">{{ $pengiriman->suratJalan->alamat_penerima ?? '-' }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide mb-0.5">Tanggal Surat
                                Jalan</p>
                            <p class="text-sm font-semibold text-gray-800">
                                {{ $pengiriman->suratJalan->tanggal_surat ? date('d F Y', strtotime($pengiriman->suratJalan->tanggal_surat)) : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide mb-0.5">Status SJ</p>
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 bg-emerald-100 text-emerald-700 rounded-full text-[11px] font-bold">
                                <i class="fa-solid fa-circle-check text-[9px]"></i>
                                {{ $pengiriman->suratJalan->status ?? '-' }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide mb-0.5">Dibuat Oleh</p>
                        <p class="text-sm text-gray-700">{{ $pengiriman->suratJalan->user->nama_lengkap ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Item Table ===== --}}
        @if(isset($pengiriman->suratJalan) && $pengiriman->suratJalan->details->count() > 0)
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden print-break-inside-avoid">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-boxes-stacked text-red-500"></i>
                            Daftar Barang yang Dikirim
                        </h3>
                        <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2.5 py-0.5 rounded-full">
                            {{ $pengiriman->suratJalan->details->count() }} item
                        </span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-red-800 text-white text-xs uppercase tracking-wide">
                            <tr>
                                <th class="py-3 px-4 text-center w-10">#</th>
                                <th class="py-3 px-4 text-left">Kode Barang</th>
                                <th class="py-3 px-4 text-left">Nama Barang</th>
                                <th class="py-3 px-3 text-center">Jumlah</th>
                                <th class="py-3 px-3 text-center">Satuan</th>
                                <th class="py-3 px-4 text-right">Harga Satuan</th>
                                <th class="py-3 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @php $grandSubtotal = 0; @endphp
                            @foreach($pengiriman->suratJalan->details as $idx => $detail)
                                @php
                                    $harga = $detail->harga_satuan ?? 0;
                                    $subtotal = $detail->quantity * $harga;
                                    $grandSubtotal += $subtotal;
                                @endphp
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 px-4 text-center text-xs font-bold text-gray-400">{{ $idx + 1 }}</td>
                                    <td class="py-3 px-4">
                                        <span
                                            class="inline-flex items-center gap-1 px-2 py-0.5 bg-gray-100 rounded-lg text-xs font-mono font-semibold text-gray-700">
                                            <i class="fa-solid fa-barcode text-gray-400 text-[10px]"></i>
                                            {{ $detail->kode_barang }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 font-medium text-gray-800">{{ $detail->barang->nama_barang ?? '-' }}</td>
                                    <td class="py-3 px-3 text-center font-bold text-gray-700">
                                        {{ number_format($detail->quantity, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3 text-center text-gray-500">{{ $detail->satuan ?? '-' }}</td>
                                    <td class="py-3 px-4 text-right text-gray-700">Rp {{ number_format($harga, 0, ',', '.') }}</td>
                                    <td class="py-3 px-4 text-right font-semibold text-gray-800">Rp
                                        {{ number_format($subtotal, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        {{-- Summary Footer --}}
                        @php
                            $diskonPersen = floatval($pengiriman->suratJalan->diskon_pelanggan ?? 0);
                            $diskon = $grandSubtotal * ($diskonPersen / 100);
                            $biayaKirim = floatval($pengiriman->suratJalan->biaya_pengiriman ?? 0);
                            $total = $grandSubtotal + $biayaKirim - $diskon;
                            $pajak = $total * 0.11;
                            $grandTotal = $total + $pajak;
                        @endphp
                        <tfoot class="bg-gray-50">
                            <tr class="border-t border-gray-200">
                                <td colspan="6" class="py-2.5 px-4 text-right text-sm font-semibold text-gray-600">Subtotal
                                    Barang</td>
                                <td class="py-2.5 px-4 text-right font-semibold text-gray-800">Rp
                                    {{ number_format($grandSubtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="py-2 px-4 text-right text-sm font-medium text-gray-500">Biaya Pengiriman
                                </td>
                                <td class="py-2 px-4 text-right text-gray-700">Rp {{ number_format($biayaKirim, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6" class="py-2 px-4 text-right text-sm font-medium text-gray-500">Diskon
                                    ({{ $diskonPersen }}%)</td>
                                <td class="py-2 px-4 text-right text-red-600">- Rp {{ number_format($diskon, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="border-t border-gray-300">
                                <td colspan="6" class="py-2.5 px-4 text-right text-sm font-bold text-gray-700">Total</td>
                                <td class="py-2.5 px-4 text-right font-bold text-gray-900">Rp
                                    {{ number_format($total, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td colspan="6" class="py-2 px-4 text-right text-sm font-medium text-gray-500">PPN (11%)</td>
                                <td class="py-2 px-4 text-right text-gray-700">Rp {{ number_format($pajak, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-t-2 border-red-300">
                                <td colspan="6" class="py-3 px-4 text-right text-base font-bold text-red-800">Total Akhir</td>
                                <td class="py-3 px-4 text-right text-base font-bold text-red-800">Rp
                                    {{ number_format($grandTotal, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif

        {{-- ===== Signature Area (visible on print) ===== --}}
        <div class="hidden print:block mt-12 print-break-inside-avoid">
            <div class="grid grid-cols-2 gap-4 text-center text-sm">
                <div>
                    <p>Pengirim,</p>
                    <br><br><br>
                    <p>( Ttd )</p>
                    <p class="mt-4 font-semibold">{{ $pengiriman->nama_driver ?? '....................................' }}
                    </p>
                    <p class="text-xs text-gray-600">Driver</p>
                </div>
                <div>
                    <p>Penerima,</p>
                    <br><br><br>
                    <p>( Ttd )</p>
                    <p class="mt-4 font-semibold">
                        {{ $pengiriman->suratJalan->nama_penerima ?? '....................................' }}</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmTerkirim() {
                Swal.fire({
                    title: 'Konfirmasi Pengiriman Selesai?',
                    text: 'Barang akan ditandai sebagai terkirim dan stok akan dikurangi secara otomatis menggunakan metode FEFO.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Tandai Terkirim',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'swal-custom-popup' },
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('form-terkirim').submit();
                    }
                });
            }
        </script>
    @endpush

@endsection