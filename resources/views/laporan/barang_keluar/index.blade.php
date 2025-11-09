@extends('layouts.app')

@section('page-title', 'Laporan Barang Keluar')

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

<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-lg p-6">

        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-6 print:hidden">
            <h1 class="text-2xl font-bold text-gray-800">📦 Laporan Barang Keluar</h1>

            {{-- Tombol Print --}}
            <button id="print-button" onclick="window.print()" 
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium shadow">
                🖨️ Cetak Laporan
            </button>
        </div>

        {{-- FILTER --}}
        <form method="GET" id="filter-section" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6 print:hidden">
            <div>
                <label class="text-sm font-medium text-gray-700">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ request('dari') }}" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}" 
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div>
                <label class="text-sm font-medium text-gray-700">Cari Barang / Pelanggan</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama barang / pelanggan..."
                       class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium shadow">
                    🔍 Tampilkan
                </button>
            </div>
        </form>

        {{-- DAFTAR LAPORAN --}}
        @forelse ($laporans as $lap)
            <div class="border rounded-lg mb-6 overflow-hidden">
                <div class="bg-gray-100 px-4 py-3 flex justify-between items-center">
                    <div>
                        <p class="font-semibold text-gray-800">Surat Jalan: 
                            <span class="text-indigo-700">{{ $lap->sj_id ?? '-' }}</span>
                        </p>
                        <p class="text-sm text-gray-600">
                            Tanggal Keluar: {{ \Carbon\Carbon::parse($lap->tanggal_keluar)->translatedFormat('d M Y H:i') }}
                        </p>
                    </div>
                    <span class="text-green-700 font-semibold">Terkirim</span>
                </div>

                {{-- Detail Barang Keluar --}}
                <table class="w-full text-sm border-t border-gray-300">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="p-2 text-left">Kode Barang</th>
                            <th class="p-2 text-left">Nama Barang</th>
                            <th class="p-2 text-center">Jumlah Keluar</th>
                            <th class="p-2 text-right">Harga Jual</th>
                            <th class="p-2 text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach($lap->details as $detail)
                            @php $total += $detail->subtotal; @endphp
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2">{{ $detail->kode_barang }}</td>
                                <td class="p-2">{{ $detail->barang->nama_barang ?? '-' }}</td>
                                <td class="p-2 text-center">{{ number_format($detail->jumlah_keluar, 0, ',', '.') }}</td>
                                <td class="p-2 text-right">Rp {{ number_format($detail->harga_jual, 2, ',', '.') }}</td>
                                <td class="p-2 text-right font-semibold text-green-700">
                                    Rp {{ number_format($detail->subtotal, 2, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-gray-50 font-semibold text-gray-800">
                        <tr>
                            <td colspan="4" class="p-2 text-right">Total Pengiriman Ini:</td>
                            <td class="p-2 text-right text-green-700">Rp {{ number_format($total, 2, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @empty
            <p class="text-gray-500 text-center py-8">Belum ada laporan barang keluar sesuai filter.</p>
        @endforelse

        {{-- Info --}}
        <div class="text-sm text-gray-500 mt-4 text-right">
            Menampilkan {{ $laporans->count() }} laporan.
        </div>

    </div>
</div>
@endsection
