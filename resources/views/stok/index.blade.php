@extends('layouts.app')

@section('page-title', 'Monitoring Stok Barang')

@section('content')
<div class="min-h-screen bg-gray-100 p-6">
    <div class="max-w-7xl mx-auto bg-white shadow-lg rounded-lg p-6">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Monitoring Stok Barang</h1>
            <form method="GET" action="{{ route('stok.index') }}" class="flex space-x-2">
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    placeholder="Cari kode / nama barang..."
                    class="border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring focus:border-blue-400">

                <select name="kategori" class="border border-gray-300 rounded-md px-3 py-2 text-sm text-gray-700">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ $kat->id == $kategori ? 'selected' : '' }}>
                        {{ $kat->nama_kategori_barang }}
                    </option>
                    @endforeach
                </select>


                <label class="flex items-center text-sm text-gray-700 ml-2">
                    <input type="checkbox" name="group" value="1"
                        {{ $group ? 'checked' : '' }}
                        onchange="this.form.submit()" class="mr-2">
                    Group by Barang
                </label>

                <button class="bg-indigo-600 text-white px-4 rounded-md hover:bg-indigo-700">Terapkan</button>
            </form>
        </div>

        {{-- Bar Kapasitas Gudang --}}
        <div class="mb-8">
            <div class="flex justify-between text-sm font-medium text-gray-700 mb-2">
                <span>Kapasitas Gudang</span>
                <span>{{ number_format($totalStok, 2) }} / {{ number_format($kapasitasMaks) }} karton ({{ $persentase }}%)</span>
            </div>
            @php
            $warna = $persentase >= 90 ? 'bg-red-600' :
            ($persentase >= 70 ? 'bg-yellow-500' :
            'bg-green-500');
            @endphp
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden shadow-inner">
                <div class="{{ $warna }} h-4 rounded-full transition-all duration-500" style="width: {{ $persentase }}%"></div>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-200 text-sm text-gray-700">
                <thead>
                    <tr class="bg-gray-200 text-gray-800 uppercase text-xs font-semibold">
                        <th class="p-3 text-left">Kode Barang</th>
                        <th class="p-3 text-left">Nama Barang</th>
                        @if(!$group)
                        <th class="p-3 text-center">Tanggal Kadaluarsa</th>
                        @endif
                        <th class="p-3 text-center">Jumlah Stok (Karton)</th>
                        <th class="p-3 text-center">Isi per Karton</th>
                        <th class="p-3 text-center">Total (pcs)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stokBarangs as $stok)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="p-3">{{ $stok->kode_barang }}</td>
                        <td class="p-3">{{ $stok->barang->nama_barang ?? '-' }}</td>

                        @if(!$group)
                        <td class="p-3 text-center">{{ $stok->tgl_kadaluarsa ? date('d M Y', strtotime($stok->tgl_kadaluarsa)) : '-' }}</td>
                        @endif

                        <td class="p-3 text-center font-semibold text-indigo-700">
                            {{ number_format($group ? $stok->total_karton : $stok->jumlah_stok, 2) }}
                        </td>
                        <td class="p-3 text-center">{{ $stok->barang->jml_barang_per_karton ?? 1 }}</td>
                        <td class="p-3 text-center text-gray-800">
                            {{ number_format(($group ? $stok->total_karton : $stok->jumlah_stok) * ($stok->barang->jml_barang_per_karton ?? 1), 0) }} pcs
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $group ? 5 : 6 }}" class="text-center py-4 text-gray-500">
                            Tidak ada data stok.
                        </td>
                    </tr>
                    @endforelse
@endsection