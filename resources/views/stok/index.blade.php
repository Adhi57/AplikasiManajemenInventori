@extends('layouts.app')

@section('page-title', 'Monitoring Stok Barang')

@section('content')

<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-screen mx-auto bg-white shadow-xl rounded-xl p-8 border border-gray-200">

        {{-- Header & Search/Filter Controls --}}
        <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 border-b pb-4">
            <div>
            <h1 class="text-2xl font-semibold text-gray-800">Monitoring Stok Barang</h1>
            <p class="text-sm text-gray-500 mt-1">
                Pantau persediaan, kapasitas gudang, dan masa kadaluarsa barang.
            </p>
            </div>

            <form method="GET" action="{{ route('stok.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="text" name="search" value="{{ $search ?? '' }}"
                    placeholder="Cari kode / nama barang..."
                    class="flex-grow min-w-[200px] border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 shadow-sm transition duration-150 text-sm">

                <select name="kategori" class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-gray-700 shadow-sm">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoris as $kat)
                    <option value="{{ $kat->id }}" {{ $kat->id == ($kategori ?? null) ? 'selected' : '' }}>
                        {{ $kat->nama_kategori_barang }}
                    </option>
                    @endforeach
                </select>


                <label class="flex items-center text-sm font-medium text-gray-700 ml-2">
                    <input type="checkbox" name="group" value="1"
                        {{ $group ? 'checked' : '' }}
                        onchange="this.form.submit()" class="mr-2 text-red-600 focus:ring-red-500 rounded border-gray-300">
                    Group by Barang
                </label>

                {{-- Tombol Terapkan diubah ke Red-700 --}}
                <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded-lg hover:bg-red-800 transition duration-150 font-medium shadow-md">
                    Terapkan
                </button>
            </form>
        </div>

        {{-- Bar Kapasitas Gudang --}}
        <div class="mb-8 p-4 bg-gray-100 rounded-lg border border-gray-200 shadow-inner">
            <div class="flex justify-between text-sm font-semibold text-gray-700 mb-2">
                <span>Kapasitas Gudang</span>
                {{-- Memastikan format angka konsisten --}}
                <span>{{ number_format($totalStok ?? 0, 2, ',', '.') }} / {{ number_format($kapasitasMaks ?? 0, 0, ',', '.') }} karton ({{ number_format($persentase ?? 0, 2, ',', '.') }}%)</span>
            </div>
            @php
            $persentaseValue = $persentase ?? 0;
            $progressWidth = min($persentaseValue, 100); // Batasi maksimal 100% pada bar visual

            $warna = $persentaseValue >= 90 ? 'bg-red-600' :
            ($persentaseValue >= 70 ? 'bg-yellow-500' :
            'bg-green-600'); // Hijau tua untuk stok ideal
            @endphp
            <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden shadow">
                <div class="{{ $warna }} h-4 rounded-full transition-all duration-500" style="width: {{ $progressWidth }}%"></div>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-md">
            <table class="w-full text-sm text-gray-700">
                <thead>
                    <tr class="bg-red-600 text-white uppercase text-xs font-semibold tracking-wider">
                        <th class="p-4 text-left">Kode Barang</th>
                        <th class="p-4 text-left">Nama Barang</th>
                        @if(!$group)
                        <th class="p-4 text-center">Tanggal Kadaluarsa</th>
                        @endif
                        <th class="p-4 text-center">Jumlah Stok (Karton)</th>
                        <th class="p-4 text-center">Isi per Karton</th>
                        <th class="p-4 text-center">Total (pcs)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stokBarangs as $stok)
                    @php
                    // Menghitung hari tersisa untuk kadaluarsa
                    $expiryDate = $stok->tgl_kadaluarsa ? strtotime($stok->tgl_kadaluarsa) : null;
                    $daysToExpiry = $expiryDate ? floor(($expiryDate - time()) / (60 * 60 * 24)) : null;

                    // Styling untuk stok yang hampir kadaluarsa (< 60 hari)
                        $expiryClass='' ;
                        if (!$group && $daysToExpiry !==null) {
                        if ($daysToExpiry < 30) {
                        $expiryClass='bg-red-100 font-bold text-red-700' ;
                        } elseif ($daysToExpiry < 60) {
                        $expiryClass='bg-yellow-100 font-semibold text-yellow-700' ;
                        }
                        }

                        // Menghitung Total Pieces
                        $kartonCount=$group ? $stok->total_karton : $stok->jumlah_stok;
                        $isiPerKarton = $stok->barang->jml_barang_per_karton ?? 1;
                        $totalPcs = $kartonCount * $isiPerKarton;
                        @endphp

                        <tr class="border-b border-gray-100 hover:bg-red-50 transition duration-100 {{ $expiryClass }}">
                            <td class="p-4 font-medium">{{ $stok->kode_barang }}</td>
                            <td class="p-4">{{ $stok->barang->nama_barang ?? '-' }}</td>

                            @if(!$group)
                            <td class="p-4 text-center">
                                {{ $stok->tgl_kadaluarsa ? date('d M Y', $expiryDate) : '-' }}
                                @if ($daysToExpiry !== null && $daysToExpiry < 60)
                                    <span class="block text-xs mt-1 font-semibold">({{ $daysToExpiry }} hari lagi)</span>
                                    @endif
                            </td>
                            @endif

                            {{-- Jumlah Stok Karton (Ditekankan) --}}
                            <td class="p-4 text-center font-bold text-red-700 bg-red-50/50">
                                {{ number_format($kartonCount, 2, ',', '.') }}
                            </td>

                            <td class="p-4 text-center">{{ $isiPerKarton }}</td>

                            {{-- Total PCS --}}
                            <td class="p-4 text-center text-gray-800 font-semibold">
                                {{ number_format($totalPcs, 0, ',', '.') }} pcs
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $group ? 5 : 6 }}" class="text-center py-10 text-gray-500 bg-gray-50 text-base font-medium">
                                <svg class="w-8 h-8 inline-block mr-2 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10m0-4l-8 4"></path>
                                </svg>
                                Tidak ada data stok ditemukan. Coba ubah filter pencarian Anda.
                            </td>
                        </tr>
                        @endforelse
                </tbody>
            </table>
        </div>
        {{-- End Table Div --}}

    </div>
    {{-- End max-w-7xl Div --}}
</div>
{{-- End min-h-screen Div --}}
@endsection