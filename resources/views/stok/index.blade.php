@extends('layouts.app')

@section('page-title', 'Monitoring Stok Barang')

@section('content')
    <div class="space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Monitoring Stok Barang</h1>
                <p class="text-sm text-gray-500 mt-1">Pantau persediaan, kapasitas gudang, dan masa kadaluarsa barang.</p>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <form method="GET" action="{{ route('stok.index') }}" class="flex flex-col md:flex-row gap-3 items-end">
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Cari
                        Barang</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            placeholder="Ketik kode / nama barang..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                    </div>
                </div>

                <div class="w-full md:w-52">
                    <label
                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Kategori</label>
                    <select name="kategori"
                        class="w-full py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm bg-white">
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ $kat->id == ($kategori ?? null) ? 'selected' : '' }}>
                                {{ $kat->nama_kategori_barang }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <label
                    class="flex items-center gap-2 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" name="group" value="1" {{ $group ? 'checked' : '' }}
                        onchange="this.form.submit()" class="text-red-600 focus:ring-red-500 rounded border-gray-300">
                    <span class="text-sm font-medium text-gray-700 whitespace-nowrap">Group by Barang</span>
                </label>

                <div class="flex gap-2">
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-800 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                        <i class="fa-solid fa-filter text-xs"></i> Terapkan
                    </button>
                </div>
            </form>
        </div>

        {{-- CAPACITY BAR --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-2 h-5 bg-emerald-500 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Kapasitas Gudang</h2>
            </div>

            @php
                $persentaseValue = $persentase ?? 0;
                $progressWidth = min($persentaseValue, 100);
                $warna = $persentaseValue >= 90 ? 'bg-red-500' : ($persentaseValue >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                $warnaText = $persentaseValue >= 90 ? 'text-red-700' : ($persentaseValue >= 70 ? 'text-amber-700' : 'text-emerald-700');
            @endphp

            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-500">Terisi</span>
                <span class="text-sm font-bold {{ $warnaText }}">
                    {{ number_format($totalStok ?? 0, 2, ',', '.') }} /
                    {{ number_format($kapasitasMaks ?? 0, 0, ',', '.') }} karton
                    ({{ number_format($persentaseValue, 1, ',', '.') }}%)
                </span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div class="{{ $warna }} h-3 rounded-full transition-all duration-700 ease-out"
                    style="width: {{ $progressWidth }}%"></div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-5 bg-blue-500 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Data Stok</h2>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kode Barang</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nama Barang</th>
                            @if(!$group)
                                <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                    Kadaluarsa</th>
                            @endif
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Stok (Karton)</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Isi/Karton</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($stokBarangs as $stok)
                            @php
                                $expiryDate = $stok->tgl_kadaluarsa ? strtotime($stok->tgl_kadaluarsa) : null;
                                $daysToExpiry = $expiryDate ? floor(($expiryDate - time()) / (60 * 60 * 24)) : null;

                                $rowBg = '';
                                if (!$group && $daysToExpiry !== null) {
                                    if ($daysToExpiry < 30)
                                        $rowBg = 'bg-red-50';
                                    elseif ($daysToExpiry < 60)
                                        $rowBg = 'bg-amber-50';
                                }

                                $kartonCount = $group ? $stok->total_karton : $stok->jumlah_stok;
                                $isiPerKarton = $stok->barang->jml_barang_per_karton ?? 1;
                                $totalPcs = $kartonCount * $isiPerKarton;
                            @endphp

                            <tr class="hover:bg-blue-50/30 transition-colors {{ $rowBg }}">
                                <td class="px-4 py-3">
                                    <span
                                        class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $stok->kode_barang }}</span>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-800">{{ $stok->barang->nama_barang ?? '-' }}</td>

                                @if(!$group)
                                    <td class="px-4 py-3 text-center">
                                        @if($expiryDate)
                                            <span class="text-sm text-gray-700">{{ date('d M Y', $expiryDate) }}</span>
                                            @if($daysToExpiry !== null && $daysToExpiry < 60)
                                                <span
                                                    class="block text-xs font-bold mt-0.5 {{ $daysToExpiry < 30 ? 'text-red-600' : 'text-amber-600' }}">
                                                    {{ $daysToExpiry }} hari lagi
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                @endif

                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 bg-emerald-50 text-emerald-700 font-bold text-sm rounded-lg">
                                        {{ number_format($kartonCount, 2, ',', '.') }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-center text-gray-600">{{ $isiPerKarton }}</td>

                                <td class="px-4 py-3 text-center font-semibold text-gray-800">
                                    {{ number_format($totalPcs, 0, ',', '.') }} {{ $stok->barang->satuan_jual ?? '-' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $group ? 5 : 6 }}" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fa-solid fa-box-open text-xl text-gray-400"></i>
                                        </div>
                                        <p class="text-sm text-gray-500">Tidak ada data stok ditemukan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection