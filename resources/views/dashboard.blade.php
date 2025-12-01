@extends('layouts.app')

@section('content')

<div class="space-y-6">

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
        $stats = [
        ['title' => 'Total Produk Terdata', 'value' => $jumlahProduk, 'icon' => 'fa-box-open', 'color' => 'text-slate-600', 'bg' => 'bg-slate-100'],
        ['title' => 'Jumlah Stok Karton pada Gudang', 'value' => $jumlahKarton, 'icon' => 'fa-cubes', 'color' => 'text-green-600', 'bg' => 'bg-green-100'],
        ['title' => 'Barang Masuk Bulan ini (Karton)', 'value' => $totalMasukBulanIni, 'icon' => 'fa-arrow-down', 'color' => 'text-blue-600', 'bg' => 'bg-blue-100'],
        ['title' => 'Barang Keluar Bulan ini (Karton)', 'value' => number_format($totalKeluarBulanIni, 1), 'icon' => 'fa-arrow-up', 'color' => 'text-red-600', 'bg' => 'bg-red-100'],
        ];
        @endphp


        @foreach ($stats as $stat)
        <div class="bg-white p-4 rounded-xl shadow border border-gray-100 hover:shadow-lg transition duration-200">
            <div class="flex items-center justify-between">
                <div class="text-sm font-medium text-gray-500">{{ $stat['title'] }}</div>
                <div class="w-8 h-8 flex items-center justify-center rounded-lg {{ $stat['bg'] }} {{ $stat['color'] }}">
                    <i class="fa-solid {{ $stat['icon'] }} text-md"></i>
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 mt-1">{{ $stat['value'] }}</p>
        </div>
        @endforeach
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <div class="lg:col-span-5 space-y-6">
            <div class="bg-white p-5 rounded-xl shadow border border-gray-100">
                <div class="flex justify-between items-center mb-5">
                    <h2 class="text-lg font-semibold text-gray-800">Ringkasan Stok Bulanan</h2>
                    <div x-data="{ selectedFilter: 'Bulan ini' }" class="relative">
                        <button class="flex items-center gap-1 bg-white hover:bg-gray-100 px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-200 text-gray-700">
                            <span x-text="selectedFilter">Bulan ini</span>
                            <svg viewBox="0 0 20 20" fill="currentColor" class="w-4 h-4 text-gray-400">
                                <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="w-3/5">
                        <div id="hs-doughnut-chart" class="w-full max-w-sm">
                        </div>
                    </div>
                    <div class="w-2/5 space-y-3">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 inline-block bg-green-500 rounded-sm"></span>
                            <div>
                                <span class="text-sm font-medium text-gray-800">Stok Baik</span>
                                <p class="text-xs text-gray-500">80% / 360 pcs</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 inline-block bg-red-500 rounded-sm"></span>
                            <div>
                                <span class="text-sm font-medium text-gray-800">Stok Rusak</span>
                                <p class="text-xs text-gray-500">20% / 90 pcs</p>
                            </div>
                        </div>
                        <div class="pt-2 border-t mt-4">
                            <h3 class="text-xs font-medium text-gray-600">Total Stok Gudang</h3>
                            <p class="text-2xl font-bold text-slate-800">{{ $jumlahKarton }} <span class="text-sm font-normal text-gray-500">Karton</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl shadow border border-gray-100">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-triangle-exclamation text-red-600"></i>
                    Barang Mendekati Expired
                </h2>

                @if ($barangExpired->count() == 0)
                <p class="text-sm text-gray-500">Tidak ada barang yang mendekati kadaluarsa.</p>
                @else
                <div class="space-y-3">

                    @foreach ($barangExpired as $item)
                    @php
                    $daysLeft = (int) \Carbon\Carbon::now()->diffInDays($item->tgl_kadaluarsa, false);

                    $color = $daysLeft <= 30
                        ? 'bg-red-100 text-red-700 border-red-300'
                        : 'bg-yellow-100 text-yellow-700 border-yellow-300' ;
                        @endphp

                        <div class="flex items-center justify-between p-3 border border-gray-100 rounded-lg hover:bg-gray-50 transition">

                        {{-- Left --}}
                        <div class="flex items-center gap-3">

                            {{-- Icon --}}
                            <div class="w-10 h-10 flex items-center justify-center bg-red-600 text-white rounded-lg">
                                <i class="fa-solid fa-clock"></i>
                            </div>

                            {{-- Text --}}
                            <div>
                                <p class="font-medium text-gray-800 text-sm">
                                    {{ $item->barang->nama_barang }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Exp: {{ \Carbon\Carbon::parse($item->tgl_kadaluarsa)->format('d M Y') }}
                                </p>
                            </div>
                        </div>

                        {{-- Right --}}
                        <div class="px-3 py-1 text-xs font-semibold rounded-full border {{ $color }}">
                            {{ $daysLeft }} hari lagi
                        </div>

                </div>
                @endforeach

                <button class="mt-3 bg-red-900 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-800">
                    Lihat Semua Tracking Expired
                </button>

            </div>
            @endif
        </div>

    </div>

    <div class="lg:col-span-7 space-y-6">

        <div class="bg-white p-5 rounded-xl shadow border border-gray-100 h-full">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Barang Terlaris</h2>
            <div class="space-y-2">
                @foreach ( $barangTerlaris as $barangTerlaris )
                <div class="flex items-center gap-4 p-3 border-b border-gray-100">
                    <span class="text-xl font-bold text-slate-700 w-6">{{ $loop->iteration }}</span>
                    <img src="{{ asset('storage/' . $barangTerlaris->foto_produk) }}" alt="{{ $barangTerlaris->nama_barang }}" class="w-12 h-12 rounded-lg object-cover">
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 truncate">{{ $barangTerlaris->nama_barang }}</p>
                        <p class="text-xs text-gray-500">{{ $barangTerlaris->nama_kategori }}</p>
                    </div>
                    <span class="text-sm font-bold text-blue-700">{{ number_format($barangTerlaris->kali_terjual, 0) }} kali Terjual</span>
                </div>
                @endforeach

            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <div class="bg-white p-5 rounded-xl shadow border border-gray-100 overflow-x-auto">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Persetujuan PO Menunggu</h2>
        @forelse($poPending as $po)
        <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-lg mb-1">
            <div class="flex items-center gap-3">
                <div class="bg-slate-800 text-white p-2 rounded-lg text-lg">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
                <div>
                    <p class="font-medium text-gray-800 text-sm">{{ $po->po_id }}</p>
                    <p class="text-xs text-gray-500">Dari: {{ $po->user->nama_lengkap ?? '-' }}</p>
                </div>
            </div>
            <a href="{{ route('approval.show_po', $po->po_id) }}"
                class="bg-red-900 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-red-950">
                Detail
            </a>
        </div>
        @empty
        <p class="text-xs text-gray-400">Tidak ada PO pending.</p>
        @endforelse
        <button class="bg-red-900 text-white px-3 mt-2 py-1.5 rounded-lg text-xs font-medium hover:bg-slate-900">Lihat Lainnya</button>

    </div>

    <div class="bg-white p-5 rounded-xl shadow border border-gray-100 overflow-x-auto">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Persetujuan Surat Jalan Menunggu</h2>
        <div class="space-y-3">
            <div class="flex items-center justify-between p-3 bg-slate-50 border border-slate-200 rounded-lg">
                <div class="flex items-center gap-3">
                    <div class="bg-slate-800 text-white p-2 rounded-lg text-lg">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 text-sm">SJ-20252904-0023</p>
                        <p class="text-xs text-gray-500">Dari: Admin Gudang</p>
                    </div>
                </div>
                <button class="bg-red-900 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-slate-900">Detail</button>
            </div>
            <button class="bg-red-900 text-white px-3 py-1.5 rounded-lg text-xs font-medium hover:bg-slate-900">Lihat Lainnya</button>
        </div>
    </div>


    <!-- Status Pengiriman Mingguan -->

    <div class="bg-white p-5 rounded-xl shadow border border-gray-100 overflow-x-auto">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Status Pengiriman Mingguan</h2>
            <div class="space-y-1">
                <div class="flex items-center justify-between p-1 bg-slate-50 border border-slate-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="bg-white border text-white p-2 rounded-lg text-lg">
                            <i class="fa-solid fa-clock text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 text-sm">Menunggu</p>
                            <p class="text-xs text-gray-500">{{ $statusPengiriman['Menunggu'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center justify-between p-1 bg-slate-50 border border-slate-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="bg-white border text-white p-2 rounded-lg text-lg">
                            <i class="fa-solid fa-truck-fast text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 text-sm">Dalam Pengiriman</p>
                            <p class="text-xs text-gray-500">{{ $statusPengiriman['Dalam Perjalanan'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-1 bg-slate-50 border border-slate-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="bg-white border text-white p-2 rounded-lg text-lg">
                            <i class="fa-solid fa-check-circle text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 text-sm">Terkirim</p>
                            <p class="text-xs text-gray-500">{{ $statusPengiriman['Terkirim'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-1 bg-slate-50 border border-slate-200 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="bg-white border text-white p-2 rounded-lg text-lg">
                            <i class="fa-solid fa-xmark-circle text-red-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800 text-sm">Dibatalkan</p>
                            <p class="text-xs text-gray-500">{{ $statusPengiriman['Dibatalkan'] ?? 0 }}</p>
                        </div>
                    </div>
                </div>

            </div>
    </div>
</div>


<div class="bg-white p-5 rounded-xl shadow border border-gray-100 overflow-x-auto">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold text-gray-900">Log Barang Masuk Terbaru</h2>
        <button class="text-xs font-medium text-slate-600 hover:text-slate-800 px-3 py-1 rounded-full border border-gray-200 hover:bg-gray-50 transition">Lihat Semua Log</button>
    </div>

    <table class="min-w-full text-sm text-left text-gray-700 border-collapse">
        <thead class="text-xs text-gray-600 uppercase bg-gray-50">
            <tr>
                <th class="px-4 py-2 font-medium">No. Ref</th>
                <th class="px-4 py-2 font-medium">Nama Barang</th>
                <th class="px-4 py-2 font-medium">Qty</th>
                <th class="px-4 py-2 font-medium">Satuan</th>
                <th class="px-4 py-2 font-medium">Tanggal</th>
                <th class="px-4 py-2 font-medium">Gudang</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <tr class="bg-white hover:bg-slate-50 transition duration-100">
                <td class="px-4 py-2 font-medium text-gray-900">IN-10281</td>
                <td class="px-4 py-2">Dummy Product A</td>
                <td class="px-4 py-2 text-green-600 font-semibold">1961</td>
                <td class="px-4 py-2">Pcs</td>
                <td class="px-4 py-2 text-gray-500">2025-11-15</td>
                <td class="px-4 py-2 text-gray-500">Gudang Utama</td>
            </tr>
            <tr class="bg-white hover:bg-slate-50 transition duration-100">
                <td class="px-4 py-2 font-medium text-gray-900">IN-10282</td>
                <td class="px-4 py-2">Dummy Product B</td>
                <td class="px-4 py-2 text-green-600 font-semibold">500</td>
                <td class="px-4 py-2">Karton</td>
                <td class="px-4 py-2 text-gray-500">2025-11-16</td>
                <td class="px-4 py-2 text-gray-500">Gudang B</td>
            </tr>
            <tr class="bg-white hover:bg-slate-50 transition duration-100">
                <td class="px-4 py-2 font-medium text-gray-900">IN-10283</td>
                <td class="px-4 py-2">Dummy Product C</td>
                <td class="px-4 py-2 text-green-600 font-semibold">300</td>
                <td class="px-4 py-2">Pcs</td>
                <td class="px-4 py-2 text-gray-500">2025-11-17</td>
                <td class="px-4 py-2 text-gray-500">Gudang Utama</td>
            </tr>
        </tbody>
    </table>
</div>
</div>
@endsection