@extends('layouts.app')
@section('page-title', 'Surat Jalan dan Pengiriman Barang')

@section('content')
<style>
    @media print {
        #main-topbar, 
        #main-navbar, 
        .hidden-on-print { 
            display: none !important;
        }

        body, #app {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .max-w-4xl.mx-auto {
            max-width: none !important;
            margin: 0 !important;
        }
    }
</style>

<div class="max-w-4xl mx-auto p-8 bg-white shadow-lg rounded-lg print:p-0 print:shadow-none">

    {{-- HEADER --}}
    <header class="text-center mb-6">
        <h2 class="text-xl font-bold mb-1 print:text-lg">SURAT JALAN BARANG</h2>
        <p class="text-sm font-semibold">CV. Berkah Jaya Lumintu</p>
        <p class="text-xs">Jl. Wonokerso I, Wonokerso, Kecamatan Tembarak, Kabupaten Temanggung</p>
    </header>

    <hr class="mb-4 border-gray-400">

    {{-- DETAIL SURAT --}}
    <div class="text-sm mb-6">
        <div class="grid grid-cols-2 gap-x-12">
            <div>
                <p><strong>Nomor:</strong> {{ $suratJalan->sj_id ?? '...........' }}</p>
                <p><strong>Tanggal:</strong> 
                    {{ $suratJalan->tanggal_surat 
                        ? \Carbon\Carbon::parse($suratJalan->tanggal_surat)->translatedFormat('d F Y')
                        : '...........' }}
                </p>
                <p><strong>Nama Pelanggan:</strong> {{ $suratJalan->pelanggan->nama_pelanggan ?? '..............................' }}</p>
                <p><strong>Status:</strong> {{ $suratJalan->status ?? '...........' }}</p>
            </div>
            <div>
                <p class="mb-1"><strong>Tujuan:</strong></p>
                <p class="pl-4 leading-tight">
                    {{ $suratJalan->alamat_penerima ?? '..............................' }}<br>
                    ({{ $suratJalan->pelanggan->PIC ?? '...................' }})
                </p>
            </div>
        </div>
    </div>

    {{-- TABEL BARANG --}}
    <div class="overflow-x-auto mb-10 border border-gray-300 rounded-lg">
        <table class="min-w-full text-sm text-gray-700 border-collapse">
            <thead class="bg-gray-50 border-b border-gray-300">
                <tr class="text-left font-semibold text-gray-800">
                    <th class="py-2 px-3 border-r w-1/12">No.</th>
                    <th class="py-2 px-3 border-r w-4/12">Nama Barang</th>
                    <th class="py-2 px-3 border-r w-2/12">Kode Barang</th>
                    <th class="py-2 px-3 border-r w-1/12 text-center">Jumlah</th>
                    <th class="py-2 px-3 border-r w-1/12 text-center">Satuan</th>
                    <th class="py-2 px-3 w-3/12 text-right">Harga Total</th>
                </tr>
            </thead>
            <tbody>
                @php $subtotal = 0; @endphp
                @forelse($suratJalan->details as $index => $detail)
                    @php
                        $hargaJual = $detail->barang->harga_jual ?? 0;
                        $totalItem = $detail->quantity * $hargaJual;
                        $subtotal += $totalItem;
                    @endphp
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-2 px-3 border-r">{{ $index + 1 }}</td>
                        <td class="py-2 px-3 border-r">{{ $detail->barang->nama_barang ?? '-' }}</td>
                        <td class="py-2 px-3 border-r">{{ $detail->kode_barang }}</td>
                        <td class="py-2 px-3 border-r text-center">{{ number_format($detail->quantity, 0, ',', '.') }}</td>
                        <td class="py-2 px-3 border-r text-center">{{ $detail->satuan ?? '-' }}</td>
                        <td class="py-2 px-3 text-right">{{ 'Rp ' . number_format($totalItem, 2, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-400">
                            Tidak ada barang dalam surat jalan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            {{-- RINGKASAN --}}
            @php
                $diskonPersen = floatval($suratJalan->diskon_pelanggan/100 ?? 0);
                $diskon = $subtotal * $diskonPersen;
                $biaya_kirim = floatval($suratJalan->biaya_pengiriman ?? 0);
                $total = $subtotal + $biaya_kirim - $diskon;
                $pajak = $total * 0.11;
                $grandTotal = $total + $pajak;
            @endphp


            <tfoot class="bg-gray-50 text-gray-800">
                <tr>
                    <td colspan="5" class="text-right font-semibold py-2 px-3 border-t border-gray-300">Subtotal</td>
                    <td class="text-right py-2 px-3 border-t border-gray-300">{{ 'Rp ' . number_format($subtotal, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right font-semibold py-2 px-3 border-t border-gray-300">Biaya Pengiriman</td>
                    <td class="text-right py-2 px-3 border-t border-gray-300">{{ 'Rp ' . number_format($biaya_kirim, 2, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="5" class="text-right font-semibold py-2 px-3 border-t border-gray-300">Diskon Pelanggan</td>
                    <td class="text-right py-2 px-3 border-t border-gray-300"> - {{ 'Rp ' . number_format($diskon, 2, ',', '.') }}</td>
                </tr>
                
                <tr>
                    <td colspan="5" class="text-right font-bold py-2 px-3 border-t border-gray-400 text-lg">Total</td>
                    <td class="text-right font-bold py-2 px-3 border-t border-gray-400 text-lg text-green-700">
                        {{ 'Rp ' . number_format($total, 2, ',', '.') }}
                    </td>
                </tr>

                <tr>
                    <td colspan="5" class="text-right font-semibold py-2 px-3 border-t border-gray-300">Pajak (11%)</td>
                    <td class="text-right py-2 px-3 border-t border-gray-300">{{ 'Rp ' . number_format($pajak, 2, ',', '.') }}</td>
                </tr>

                <tr>
                    <td colspan="5" class="text-right font-bold py-2 px-3 border-t border-gray-400 text-lg">Total Akhir</td>
                    <td class="text-right font-bold py-2 px-3 border-t border-gray-400 text-lg text-green-700">
                        {{ 'Rp ' . number_format($grandTotal, 2, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- TANDA TANGAN --}}
    <div class="grid grid-cols-2 gap-4 text-center text-sm mt-12">
        <div>
            <p>Dikeluarkan oleh,</p>
            <br><br><br>
            <p>( Ttd )</p>
            <p class="mt-4 font-semibold">{{ $suratJalan->user->nama_lengkap ?? '....................................' }}</p>
            <p class="text-xs text-gray-600">CV. Berkah Jaya Lumintu</p>
        </div>
        <div>
            <p>Penerima,</p>
            <br><br><br>
            <p>( Ttd )</p>
            <p class="mt-4 font-semibold">{{ $suratJalan->nama_penerima ?? '....................................' }}</p>
        </div>
    </div>

    {{-- TOMBOL CETAK --}}
    <div class="mt-10 text-center print:hidden">
        <button onclick="window.print()" 
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded shadow-md transition duration-150">
            Cetak Surat Jalan (PDF / Kertas)
        </button>
        <a href="{{ route('surat_jalan.index') }}" 
            class="ml-4 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded shadow-md transition duration-150">
            Kembali
        </a>
    </div>

</div>
@endsection
