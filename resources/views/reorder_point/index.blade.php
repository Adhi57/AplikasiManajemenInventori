@extends('layouts.app')

@section('page-title', 'Peringatan Pemesanan Ulang')

@section('content')
    <div class="space-y-6" x-data="reorderPointApp()">

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    <i class="fa-solid fa-bell text-red-700 mr-2"></i>Peringatan Pemesanan Ulang
                </h1>
                <p class="text-sm text-gray-500 mt-1">Pantau kapan barang perlu dipesan kembali agar stok tidak habis.</p>
            </div>
        </div>

        {{-- CARA KERJA --}}
        <div class="bg-gradient-to-r from-red-800 to-red-900 rounded-2xl shadow-lg p-5 text-white">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 bg-white/15 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-circle-info text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg mb-2">Cara Kerja Halaman Ini</h3>
                    <div class="space-y-1 text-sm text-red-100">
                        <p>Semua data dihitung <strong class="text-white">otomatis dari sistem:</strong></p>
                        <ul class="list-disc list-inside space-y-0.5 ml-1">
                            <li><strong class="text-white">Pemakaian/hari</strong> — dari data barang keluar 7 hari
                                terakhir</li>
                            <li><strong class="text-white">Waktu tunggu pengiriman</strong> — dari riwayat Purchase Order
                                yang sudah diterima</li>
                            <li><strong class="text-white">Stok Pengaman & Batas Minimum</strong> — dihitung otomatis
                                berdasarkan rumus</li>
                        </ul>
                    </div>
                    <div class="mt-3 bg-white/10 rounded-xl px-4 py-2.5 text-xs text-red-100 inline-flex items-start gap-2">
                        <i class="fa-solid fa-calculator text-yellow-300 mt-0.5"></i>
                        <div>
                            <span class="text-white font-semibold">Rumus:</span>
                            Stok Pengaman = Pemakaian/hari × (Waktu Terlama − Waktu Rata-rata)
                            <span class="mx-1">|</span>
                            Batas Minimum = (Pemakaian/hari × Waktu Rata-rata) + Stok Pengaman
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- SUMMARY CARDS --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            {{-- Total Barang --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-boxes-stacked text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Total Barang</p>
                        <p class="text-xl font-bold text-gray-800" x-text="items.length"></p>
                    </div>
                </div>
            </div>

            {{-- Perlu Pesan --}}
            <div class="bg-white rounded-2xl shadow-sm border border-red-200 p-5 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-red-50 rounded-full -mr-6 -mt-6"></div>
                <div class="flex items-center gap-3 relative">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Perlu Segera Dipesan</p>
                        <p class="text-xl font-bold text-red-700" x-text="needReorderCount"></p>
                    </div>
                </div>
            </div>

            {{-- Stok Aman --}}
            <div class="bg-white rounded-2xl shadow-sm border border-emerald-100 p-5 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-20 h-20 bg-emerald-50 rounded-full -mr-6 -mt-6"></div>
                <div class="flex items-center gap-3 relative">
                    <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold">Stok Masih Aman</p>
                        <p class="text-xl font-bold text-emerald-700" x-text="safeCount"></p>
                    </div>
                </div>
            </div>
        </div>

        {{-- FILTER --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex flex-col md:flex-row gap-4 items-end">
                {{-- Search --}}
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        <i class="fa-solid fa-magnifying-glass mr-1"></i>Cari Barang
                    </label>
                    <form method="GET" action="{{ route('reorder_point.index') }}" class="relative">
                        <input type="hidden" name="kategori" value="{{ $kategori ?? '' }}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            placeholder="Ketik kode / nama barang..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                    </form>
                </div>

                {{-- Kategori Filter --}}
                <div class="w-full md:w-52">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">
                        <i class="fa-solid fa-tag mr-1"></i>Kategori
                    </label>
                    <form method="GET" action="{{ route('reorder_point.index') }}">
                        <input type="hidden" name="search" value="{{ $search ?? '' }}">
                        <select name="kategori" onchange="this.form.submit()"
                            class="w-full py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm bg-white">
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoris as $kat)
                                <option value="{{ $kat->kategori_barang_id }}" {{ $kat->kategori_barang_id == ($kategori ?? null) ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori_barang }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-5 bg-red-600 rounded-full"></div>
                    <h2 class="font-semibold text-gray-800">Daftar Analisis Barang</h2>
                </div>
                <div class="text-xs text-gray-400 flex items-center gap-1">
                    <i class="fa-solid fa-robot"></i>
                    Semua data dihitung otomatis dari sistem
                </div>
            </div>

            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Kode</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Nama Barang</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Stok Sekarang
                                <span class="block text-[10px] text-gray-400 font-normal normal-case">(karton)</span>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Pemakaian/Hari
                                <span class="block text-[10px] text-gray-400 font-normal normal-case">(karton)</span>
                            </th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Waktu Tunggu
                                <span class="block text-[10px] text-gray-400 font-normal normal-case">Terlama
                                    (hari)</span>
                            </th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Waktu Tunggu
                                <span class="block text-[10px] text-gray-400 font-normal normal-case">Rata-rata
                                    (hari)</span>
                            </th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">
                                Stok Pengaman
                                <span class="block text-[10px] text-gray-400 font-normal normal-case">(karton)</span>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Batas Minimum
                                <span class="block text-[10px] text-gray-400 font-normal normal-case">(ROP
                                    karton)</span>
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="(item, idx) in items" :key="item.kode_barang">
                            <tr class="hover:bg-blue-50/30 transition-colors" :class="{
                                            'bg-red-50/60 border-l-4 border-l-red-400': calcRop(item) > 0 && item.stok_karton <= calcRop(item),
                                            'border-l-4 border-l-transparent': calcRop(item) === 0 || item.stok_karton > calcRop(item)
                                        }">
                                {{-- Kode --}}
                                <td class="px-4 py-3">
                                    <span class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded"
                                        x-text="item.kode_barang"></span>
                                </td>

                                {{-- Nama --}}
                                <td class="px-4 py-3">
                                    <span class="font-medium text-gray-800" x-text="item.nama_barang"></span>
                                </td>

                                {{-- Stok Sekarang --}}
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center px-2.5 py-1 font-bold text-sm rounded-lg" :class="calcRop(item) > 0 && item.stok_karton <= calcRop(item)
                                                    ? 'bg-red-100 text-red-700'
                                                    : 'bg-emerald-50 text-emerald-700'"
                                        x-text="formatNumber(item.stok_karton)"></span>
                                </td>

                                {{-- Pemakaian/Hari --}}
                                <td class="px-4 py-3 text-center">
                                    <span class="text-gray-600" x-text="formatNumber(item.d_avg, 4)"></span>
                                </td>

                                {{-- Waktu Tunggu Terlama --}}
                                <td class="px-4 py-3 text-center">
                                    <template x-if="item.jumlah_po > 0">
                                        <span class="font-semibold text-gray-700" x-text="item.l_max + ' hari'"></span>
                                    </template>
                                    <template x-if="item.jumlah_po === 0">
                                        <span class="text-gray-400 text-xs">Belum ada PO</span>
                                    </template>
                                </td>

                                {{-- Waktu Tunggu Rata-rata --}}
                                <td class="px-4 py-3 text-center">
                                    <template x-if="item.jumlah_po > 0">
                                        <span class="font-semibold text-gray-700" x-text="item.l_avg + ' hari'"></span>
                                    </template>
                                    <template x-if="item.jumlah_po === 0">
                                        <span class="text-gray-400 text-xs">Belum ada PO</span>
                                    </template>
                                </td>

                                {{-- Stok Pengaman --}}
                                <td class="px-4 py-3 text-center">
                                    <span class="font-semibold text-gray-700"
                                        x-text="formatNumber(calcSafetyStock(item), 2)"></span>
                                </td>

                                {{-- Batas Minimum (ROP) --}}
                                <td class="px-4 py-3 text-center">
                                    <span class="font-bold text-gray-800 text-base"
                                        x-text="formatNumber(calcRop(item), 2)"></span>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3 text-center">
                                    <template x-if="calcRop(item) > 0 && item.stok_karton <= calcRop(item)">
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 text-red-700 font-bold text-xs rounded-full shadow-sm">
                                            <span class="relative flex h-2 w-2">
                                                <span
                                                    class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                            </span>
                                            Segera Pesan
                                        </span>
                                    </template>
                                    <template x-if="item.jumlah_po === 0">
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 text-gray-500 font-medium text-xs rounded-full">
                                            <i class="fa-solid fa-minus text-[10px]"></i>
                                            Belum Ada Data
                                        </span>
                                    </template>
                                    <template
                                        x-if="item.jumlah_po > 0 && (calcRop(item) === 0 || item.stok_karton > calcRop(item))">
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-100 text-emerald-700 font-bold text-xs rounded-full">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                                            Stok Aman
                                        </span>
                                    </template>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State --}}
                        <template x-if="items.length === 0">
                            <tr>
                                <td colspan="9" class="px-4 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                            <i class="fa-solid fa-box-open text-2xl text-gray-400"></i>
                                        </div>
                                        <p class="text-sm text-gray-500 font-medium">Tidak ada data barang ditemukan.</p>
                                        <p class="text-xs text-gray-400 mt-1">Coba ubah filter pencarian Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Legend --}}
            <div class="mt-4 flex flex-wrap gap-4 text-xs text-gray-500 border-t border-gray-100 pt-4">
                <div class="flex items-center gap-2">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                    </span>
                    <span><strong>Segera Pesan</strong> — stok sudah di bawah batas minimum</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    <span><strong>Stok Aman</strong> — stok masih mencukupi</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex rounded-full h-2.5 w-2.5 bg-gray-300"></span>
                    <span><strong>Belum Ada Data</strong> — belum pernah ada Purchase Order diterima</span>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function reorderPointApp() {
            return {
                items: @json($items),

                calcSafetyStock(item) {
                    const dAvg = item.d_avg || 0;
                    const lMax = item.l_max || 0;
                    const lAvg = item.l_avg || 0;
                    return dAvg * (lMax - lAvg);
                },

                calcRop(item) {
                    const dAvg = item.d_avg || 0;
                    const lAvg = item.l_avg || 0;
                    const ss = this.calcSafetyStock(item);
                    return (dAvg * lAvg) + ss;
                },

                get needReorderCount() {
                    return this.items.filter(item => {
                        const rop = this.calcRop(item);
                        return rop > 0 && item.stok_karton <= rop;
                    }).length;
                },

                get safeCount() {
                    return this.items.filter(item => {
                        const rop = this.calcRop(item);
                        return rop === 0 || item.stok_karton > rop;
                    }).length;
                },

                formatNumber(val, decimals = 2) {
                    if (val === null || val === undefined) return '0';
                    return parseFloat(val).toLocaleString('id-ID', {
                        minimumFractionDigits: decimals,
                        maximumFractionDigits: decimals
                    });
                }
            };
        }
    </script>
@endpush