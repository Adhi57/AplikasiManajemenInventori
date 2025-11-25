@extends('layouts.app')

@section('page-title', 'Tracking Kadaluarsa Barang')

@section('content')

<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-screen mx-auto bg-white shadow-xl rounded-xl p-8 border border-gray-200">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 border-b pb-4">
            <div>
                <h1 class="text-2xl font-semibold text-gray-800">Tracking Kadaluarsa Barang</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Pantau masa kedaluwarsa barang berdasarkan batch stok.
                </p>
            </div>

            <form method="GET" class="flex items-center gap-3">
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Cari kode / nama barang..."
                    class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-600 text-sm shadow-sm">
                <button class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800 shadow-md">
                    Cari
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-md">
            <table class="w-full text-sm text-gray-700">
                <thead>
                    <tr class="bg-red-600 text-white uppercase text-xs font-semibold">
                        <th class="p-4 text-left">PO ID</th>
                        <th class="p-4 text-left">Kode Barang</th>
                        <th class="p-4 text-left">Nama Barang</th>
                        <th class="p-4 text-center">Tanggal Kadaluarsa</th>
                        <th class="p-4 text-center">Sisa Hari</th>
                        <th class="p-4 text-center">Stok (Karton)</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody>
    @php
        // Grup stok berdasarkan kode_barang
        $grouped = $stok->groupBy(function($item){
            return $item->barang->nama_barang;
        });
    @endphp

    @forelse ($grouped as $namaBarang => $items)

        {{-- HEADER GRUP BARANG --}}
        <tr class="bg-gray-100 border-b">
            <td colspan="7" class="p-4 font-semibold text-gray-800 text-lg">
                {{ strtoupper($namaBarang) }}
            </td>
            
        </tr>

        {{-- LIST BATCH DALAM GRUP --}}
        @foreach ($items as $s)

            @php
                $expiryDate = strtotime($s->tgl_kadaluarsa);
                $daysLeft = floor(($expiryDate - time()) / (60 * 60 * 24));

                $rowClass = $daysLeft < 30 ? 'bg-red-100 text-red-700 font-semibold'
                        : ($daysLeft < 60 ? 'bg-yellow-100 text-yellow-700 font-medium'
                        : '');
            @endphp

            <tr class="border-b border-gray-100 hover:bg-red-50 transition {{ $rowClass }}">
                <td class="p-4">{{ $s->po_id }}</td>
                <td class="p-4">{{ $s->kode_barang }}</td>
                <td class="p-4">{{ $s->barang->nama_barang }}</td>

                <td class="p-4 text-center">
                    {{ date('d M Y', $expiryDate) }}
                </td>

                <td class="p-4 text-center">
                    {{ $daysLeft }} hari
                </td>

                <td class="p-4 text-center font-bold text-red-700">
                    {{ number_format($s->jumlah_stok, 2, ',', '.') }}
                </td>

                <td class="p-4 text-center">
                    {{-- Tombol Hapus --}}
                    <form action="{{ route('tracking_kadaluarsa.destroy', $s->id) }}"
                        method="POST" class="inline-block ml-1"
                        onsubmit="return confirm('Hapus stok ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1.5 text-xs bg-red-600 text-white rounded-lg shadow hover:bg-red-700">
                            Hapus
                        </button>
                    </form>

                </td>
            </tr>
        @endforeach

    @empty
        <tr>
            <td colspan="7" class="text-center py-10 text-gray-500">
                Tidak ada data kadaluarsa ditemukan.
            </td>
        </tr>
    @endforelse
</tbody>

            </table>
        </div>

    </div>
</div>

@endsection
