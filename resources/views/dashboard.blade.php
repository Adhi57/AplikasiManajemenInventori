@extends('layouts.app')

@section('content')

    {{-- ========================================= --}}
    {{-- HEADER: Greeting + Date --}}
    {{-- ========================================= --}}
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Selamat Datang, {{ auth()->user()->nama_lengkap ?? auth()->user()->name ?? 'User' }}! 👋
            </h1>
            <p class="text-sm text-gray-500 mt-1">Berikut ringkasan aktivitas gudang hari ini.</p>
        </div>
        <div
            class="flex items-center gap-2 text-sm text-gray-500 bg-white border border-gray-200 rounded-lg px-3 py-2 shadow-sm">
            <i class="fa-regular fa-calendar"></i>
            <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- SECTION 1: Stat Cards (6 cards) --}}
    {{-- ========================================= --}}
    @php
        $stats = [
            [
                'title' => 'Total Produk',
                'value' => $jumlahProduk,
                'suffix' => 'Varian',
                'icon' => 'fa-box-open',
                'gradient' => 'from-slate-600 to-slate-800',
            ],
            [
                'title' => 'Stok Gudang',
                'value' => $jumlahKarton,
                'suffix' => 'dari ' . number_format(floatval($appSettings['kapasitas_gudang'] ?? 1000), 0, ',', '.') . ' Karton',
                'icon' => 'fa-cubes',
                'gradient' => 'from-emerald-500 to-emerald-700',
                'capacity' => floatval($appSettings['kapasitas_gudang'] ?? 1000),
            ],
            [
                'title' => 'Stok Rusak',
                'value' => $jumlahKartonRusak,
                'suffix' => 'Karton',
                'icon' => 'fa-box-tissue',
                'gradient' => 'from-orange-500 to-orange-700',
            ],
            [
                'title' => 'Masuk Bulan Ini',
                'value' => $totalMasukBulanIni,
                'suffix' => 'Karton',
                'icon' => 'fa-arrow-down',
                'gradient' => 'from-blue-500 to-blue-700',
            ],
            [
                'title' => 'Keluar Bulan Ini',
                'value' => number_format($totalKeluarBulanIni, 1),
                'suffix' => 'Karton',
                'icon' => 'fa-arrow-up',
                'gradient' => 'from-red-500 to-red-700',
            ],
            [
                'title' => 'Omset Bulan Ini',
                'value' => 'Rp ' . number_format($omsetBulanIni, 0, ',', '.'),
                'suffix' => \Carbon\Carbon::now()->translatedFormat('F Y'),
                'icon' => 'fa-money-bill-trend-up',
                'gradient' => 'from-violet-500 to-violet-700',
            ],
        ];
    @endphp

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4 mb-6">
        @foreach ($stats as $stat)
            <div
                class="relative overflow-hidden bg-gradient-to-br {{ $stat['gradient'] }} text-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 group">
                <div
                    class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-500">
                </div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm font-medium text-white/80">{{ $stat['title'] }}</p>
                        <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20">
                            <i class="fa-solid {{ $stat['icon'] }} text-lg"></i>
                        </div>
                    </div>
                    <p class="text-2xl font-extrabold tracking-tight">{{ $stat['value'] }}</p>
                    <p class="text-xs text-white/60 mt-1">{{ $stat['suffix'] }}</p>
                    @if(isset($stat['capacity']))
                        @php
                            $capPct = $stat['capacity'] > 0 ? min(round(($stat['value'] / $stat['capacity']) * 100, 1), 100) : 0;
                        @endphp
                        <div class="mt-3">
                            <div class="w-full bg-white/20 rounded-full h-2 overflow-hidden">
                                <div class="h-2 rounded-full transition-all duration-700 {{ $capPct >= 90 ? 'bg-red-300' : ($capPct >= 70 ? 'bg-amber-300' : 'bg-white/70') }}"
                                    style="width: {{ $capPct }}%"></div>
                            </div>
                            <p class="text-[10px] text-white/50 mt-1">Kapasitas terpakai: {{ $capPct }}%</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    {{-- ========================================= --}}
    {{-- SECTION 1.5: Quick Actions --}}
    {{-- ========================================= --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-2 h-5 bg-gray-800 rounded-full"></div>
            <h2 class="font-semibold text-gray-800">Aksi Cepat</h2>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            @php
                $quickActions = [
                    ['label' => 'Buat PO', 'icon' => 'fa-file-circle-plus', 'route' => route('purchase_orders.buat_permintaan'), 'color' => 'bg-blue-50 text-blue-600 hover:bg-blue-100'],
                    ['label' => 'Surat Jalan', 'icon' => 'fa-file-invoice', 'route' => route('surat_jalan.create'), 'color' => 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100'],
                    ['label' => 'Verifikasi', 'icon' => 'fa-clipboard-check', 'route' => '/verifBarang', 'color' => 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100'],
                    ['label' => 'Stok Barang', 'icon' => 'fa-warehouse', 'route' => route('stok.index'), 'color' => 'bg-amber-50 text-amber-600 hover:bg-amber-100'],
                    ['label' => 'Retur', 'icon' => 'fa-rotate-left', 'route' => route('retur.index'), 'color' => 'bg-red-50 text-red-600 hover:bg-red-100'],
                    ['label' => 'Katalog', 'icon' => 'fa-book-open', 'route' => '/katalog_barang', 'color' => 'bg-violet-50 text-violet-600 hover:bg-violet-100'],
                ];
            @endphp

            @foreach ($quickActions as $action)
                <a href="{{ $action['route'] }}"
                    class="flex flex-col items-center gap-2 p-4 rounded-xl {{ $action['color'] }} transition-all duration-200 hover:shadow-sm group">
                    <div
                        class="w-10 h-10 flex items-center justify-center rounded-xl bg-white shadow-sm group-hover:scale-110 transition-transform">
                        <i class="fa-solid {{ $action['icon'] }} text-lg"></i>
                    </div>
                    <span class="text-xs font-semibold">{{ $action['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- SECTION 1.6: Info Bar (Suppliers + Pelanggan) --}}
    {{-- ========================================= --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-truck-field text-teal-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Supplier</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalSupplier }}</p>
            </div>
            <a href="/suppliers"
                class="ml-auto text-xs font-medium text-teal-600 hover:text-teal-800 border border-teal-200 px-3 py-1.5 rounded-lg hover:bg-teal-50 transition">
                Kelola <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-users text-sky-600 text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500">Total Pelanggan</p>
                <p class="text-2xl font-bold text-gray-900">{{ $totalPelanggan }}</p>
            </div>
            <a href="/pelanggans"
                class="ml-auto text-xs font-medium text-sky-600 hover:text-sky-800 border border-sky-200 px-3 py-1.5 rounded-lg hover:bg-sky-50 transition">
                Kelola <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
            </a>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- SECTION 2: Analitik Manajerial Head Gudang --}}
    {{-- ========================================= --}}
    @if(auth()->user()->role === 'HeadGudang' || auth()->user()->role === 'Head')
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
            <div class="flex items-center gap-2 mb-1">
                <div class="w-2 h-5 bg-gray-800 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Analitik Manajerial Gudang</h2>
            </div>
            <p class="text-xs text-gray-400 ml-4 mb-5">Data pendukung keputusan operasional Head Gudang</p>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

                {{-- ROW 1: LEFT - Pergerakan Barang (8 cols) --}}
                <div class="lg:col-span-8 bg-gradient-to-br from-slate-50 to-white rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-arrow-right-arrow-left text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm">Tren Pergerakan Barang</h3>
                                <p class="text-[11px] text-gray-400">Masuk vs Keluar per bulan</p>
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">Data
                            Tahunan</span>
                    </div>
                    <canvas id="pergerakanBarangChart" height="180"></canvas>
                </div>

                {{-- ROW 1: RIGHT - Kualitas Penerimaan (4 cols) --}}
                <div class="lg:col-span-4 bg-gradient-to-br from-slate-50 to-white rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-clipboard-check text-red-600 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm">Kualitas Penerimaan</h3>
                            <p class="text-[11px] text-gray-400">QC bulan {{ \Carbon\Carbon::now()->translatedFormat('F') }}</p>
                        </div>
                    </div>
                    <div class="flex justify-center mb-4">
                        <div class="w-40 h-40">
                            <canvas id="kualitasPenerimaanChart"></canvas>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between p-2.5 bg-blue-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 bg-blue-500 rounded-full"></span>
                                <span class="text-xs font-medium text-gray-700">Baik</span>
                            </div>
                            <span class="text-xs font-bold text-blue-700">{{ number_format($totalMasukBulanIni, 0, ',', '.') }}
                                krt</span>
                        </div>
                        <div class="flex items-center justify-between p-2.5 bg-red-50 rounded-lg">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                                <span class="text-xs font-medium text-gray-700">Rusak</span>
                            </div>
                            <span
                                class="text-xs font-bold text-red-700">{{ number_format($totalRusakBulanIni ?? 0, 0, ',', '.') }}
                                krt</span>
                        </div>
                        <div class="pt-2 border-t border-gray-100">
                            <div class="flex justify-between items-baseline">
                                <span class="text-[11px] text-gray-400">Total Kedatangan</span>
                                <span
                                    class="text-lg font-bold text-gray-900">{{ number_format($totalMasukBulanIni + ($totalRusakBulanIni ?? 0), 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ROW 2: LEFT - Distribusi Stok per Kategori (8 cols) --}}
                <div class="lg:col-span-8 bg-gradient-to-br from-slate-50 to-white rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-layer-group text-emerald-600 text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm">Distribusi Stok per Kategori</h3>
                                <p class="text-[11px] text-gray-400">Kapasitas gudang berdasarkan kategori</p>
                            </div>
                        </div>
                        <span class="text-[10px] text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">Satuan:
                            Karton</span>
                    </div>
                    <canvas id="stokKategoriChart" height="160"></canvas>
                </div>

                {{-- ROW 2: RIGHT - Status Pengiriman (4 cols) --}}
                <div class="lg:col-span-4 bg-gradient-to-br from-slate-50 to-white rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-teal-100 rounded-lg flex items-center justify-center">
                            <i class="fa-solid fa-truck-fast text-teal-600 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 text-sm">Status Pengiriman</h3>
                            <p class="text-[11px] text-gray-400">Rasio minggu ini</p>
                        </div>
                    </div>
                    <div class="flex justify-center mb-4">
                        <div class="w-40 h-40">
                            <canvas id="pengirimanChart"></canvas>
                        </div>
                    </div>
                    @php
                        $pengirimanLabels = [
                            'Menunggu' => ['icon' => 'fa-clock', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700'],
                            'Dalam Perjalanan' => ['icon' => 'fa-truck', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700'],
                            'Terkirim' => ['icon' => 'fa-check-circle', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700'],
                            'Dibatalkan' => ['icon' => 'fa-times-circle', 'bg' => 'bg-red-50', 'text' => 'text-red-700'],
                        ];
                    @endphp
                    <div class="space-y-2">
                        @foreach($pengirimanLabels as $label => $style)
                            <div class="flex items-center justify-between p-2.5 {{ $style['bg'] }} rounded-lg">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid {{ $style['icon'] }} {{ $style['text'] }} text-xs"></i>
                                    <span class="text-xs font-medium text-gray-700">{{ $label }}</span>
                                </div>
                                <span class="text-xs font-bold {{ $style['text'] }}">{{ $statusPengiriman[$label] ?? 0 }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ROW 3: FULL WIDTH - Omset Penjualan --}}
                <div class="lg:col-span-12 bg-gradient-to-br from-slate-50 to-white rounded-xl border border-gray-100 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-violet-100 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-chart-line text-violet-600 text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm">Tren Omset Penjualan</h3>
                                <p class="text-[11px] text-gray-400">Performa pendapatan bulanan</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="text-right">
                                <p class="text-[10px] text-gray-400">Omset Bulan Ini</p>
                                <p class="text-sm font-bold text-violet-700">Rp {{ number_format($omsetBulanIni, 0, ',', '.') }}
                                </p>
                            </div>
                            <span class="text-[10px] text-gray-400 bg-gray-50 px-2 py-1 rounded-md border border-gray-100">Data
                                Tahunan</span>
                        </div>
                    </div>
                    <canvas id="omsetChart" height="120"></canvas>
                </div>

            </div>
        </div>
    @endif

    {{-- ========================================= --}}
    {{-- SECTION 3: Stok + Expired | Barang Terlaris --}}
    {{-- ========================================= --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-6">

        {{-- LEFT COLUMN: Stok & Expired --}}
        <div class="lg:col-span-5 space-y-5">

            {{-- Ringkasan Stok Donut --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-2 h-5 bg-emerald-500 rounded-full"></div>
                    <h2 class="font-semibold text-gray-800">Ringkasan Stok Bulanan</h2>
                </div>

                <div class="flex flex-col md:flex-row items-center gap-6">
                    <div class="w-3/5">
                        <div class="w-full max-w-[200px] mx-auto">
                            <canvas id="stokDonutChart"></canvas>
                        </div>
                    </div>
                    <div class="w-2/5 space-y-3">
                        <div class="flex items-center gap-2.5">
                            <span
                                class="w-3 h-3 inline-block bg-emerald-500 rounded-full shadow-sm shadow-emerald-200"></span>
                            <div>
                                <span class="text-sm font-medium text-gray-800">Stok Baik</span>
                                <p class="text-xs text-gray-500">{{ $jumlahKarton }} karton</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <span class="w-3 h-3 inline-block bg-red-500 rounded-full shadow-sm shadow-red-200"></span>
                            <div>
                                <span class="text-sm font-medium text-gray-800">Stok Rusak</span>
                                <p class="text-xs text-gray-500">{{ $jumlahKartonRusak }} karton</p>
                            </div>
                        </div>
                        <div class="pt-3 border-t border-gray-100 mt-3">
                            <p class="text-xs text-gray-500 mb-0.5">Total Stok Gudang</p>
                            <p class="text-2xl font-bold text-gray-900">
                                {{ $jumlahKarton + $jumlahKartonRusak }}
                                <span class="text-sm font-normal text-gray-400">/
                                    {{ number_format(floatval($appSettings['kapasitas_gudang'] ?? 1000), 0, ',', '.') }}
                                    Karton</span>
                            </p>
                            @php
                                $totalAll = $jumlahKarton + $jumlahKartonRusak;
                                $kapMax = floatval($appSettings['kapasitas_gudang'] ?? 1000);
                                $pctUsed = $kapMax > 0 ? min(round(($totalAll / $kapMax) * 100, 1), 100) : 0;
                                $barColor = $pctUsed >= 90 ? 'bg-red-500' : ($pctUsed >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                                $pctColor = $pctUsed >= 90 ? 'text-red-600' : ($pctUsed >= 70 ? 'text-amber-600' : 'text-emerald-600');
                            @endphp
                            <div class="mt-2">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-[11px] text-gray-400">Kapasitas Gudang</span>
                                    <span class="text-xs font-bold {{ $pctColor }}">{{ $pctUsed }}%</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="{{ $barColor }} h-2 rounded-full transition-all duration-700 ease-out"
                                        style="width: {{ $pctUsed }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Barang Expired --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-5 bg-red-500 rounded-full"></div>
                        <h2 class="font-semibold text-gray-800">Barang Kadaluarsa</h2>
                    </div>
                    @if($jumlahSudahExpired > 0)
                        <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full animate-pulse">
                            {{ $jumlahSudahExpired }} Expired
                        </span>
                    @endif
                </div>

                @if ($barangExpired->count() == 0)
                    <div class="flex flex-col items-center py-6 text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fa-solid fa-check text-green-600 text-lg"></i>
                        </div>
                        <p class="text-sm text-gray-500">Tidak ada barang kadaluarsa atau mendekati kadaluarsa.</p>
                    </div>
                @else
                    <div class="space-y-2.5">
                        @foreach ($barangExpired as $item)
                            @php
                                $expDate = \Carbon\Carbon::parse($item->tgl_kadaluarsa);
                                $isExpired = $expDate->isPast();
                                $daysLeft = $isExpired
                                    ? (int) Carbon\Carbon::now()->diffInDays($expDate)
                                    : (int) Carbon\Carbon::now()->diffInDays($expDate, false);
                                $isUrgent = $isExpired || $daysLeft <= 30;
                            @endphp
                            <div
                                class="flex items-center justify-between p-3 rounded-xl {{ $isExpired ? 'bg-red-50 border border-red-200' : ($isUrgent ? 'bg-red-50 border border-red-100' : 'bg-amber-50 border border-amber-100') }} hover:shadow-sm transition">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 flex items-center justify-center rounded-lg {{ $isExpired ? 'bg-red-600' : ($isUrgent ? 'bg-red-500' : 'bg-amber-500') }} text-white text-sm">
                                        <i class="fa-solid {{ $isExpired ? 'fa-skull-crossbones' : 'fa-clock' }}"></i>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm">{{ $item->barang->nama_barang }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $isExpired ? 'Exp' : 'Exp' }}:
                                            {{ $expDate->format('d M Y') }}
                                            · {{ $item->jumlah_stok }} karton
                                        </p>
                                    </div>
                                </div>
                                <span
                                    class="px-2.5 py-1 text-xs font-bold rounded-full whitespace-nowrap {{ $isExpired ? 'bg-red-600 text-white' : ($isUrgent ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $isExpired ? 'EXPIRED ' . $daysLeft . ' hari' : $daysLeft . ' hari lagi' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="/tracking-kadaluarsa"
                            class="inline-flex items-center gap-1.5 bg-red-900 text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-red-800 transition-colors shadow-sm">
                            <i class="fa-solid fa-arrow-right text-[10px]"></i> Lihat Semua
                        </a>
                    </div>
                @endif
            </div>

            {{-- LOW STOCK ALERT --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-2 h-5 bg-orange-500 rounded-full"></div>
                    <h2 class="font-semibold text-gray-800">Stok Hampir Habis</h2>
                    @if($lowStockItems->count() > 0)
                        <span
                            class="px-2 py-0.5 bg-orange-100 text-orange-700 text-xs font-bold rounded-full">{{ $lowStockItems->count() }}
                            item</span>
                    @endif
                </div>

                @if ($lowStockItems->count() == 0)
                    <div class="flex flex-col items-center py-6 text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fa-solid fa-check text-green-600 text-lg"></i>
                        </div>
                        <p class="text-sm text-gray-500">Semua stok dalam kondisi aman.</p>
                    </div>
                @else
                    <div class="space-y-2.5">
                        @foreach ($lowStockItems as $item)
                            @php
                                $isCritical = $item->total_stok <= 3;
                            @endphp
                            <div
                                class="flex items-center justify-between p-3 rounded-xl {{ $isCritical ? 'bg-red-50 border border-red-100' : 'bg-orange-50 border border-orange-100' }} hover:shadow-sm transition">
                                <div class="flex items-center gap-3">
                                    <img src="{{ asset('storage/' . $item->foto_produk) }}" alt="{{ $item->nama_barang }}"
                                        class="w-9 h-9 rounded-lg object-cover border border-gray-100">
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm truncate max-w-[150px]">{{ $item->nama_barang }}
                                        </p>
                                        <p class="text-xs text-gray-400">{{ $item->kode_barang }}</p>
                                    </div>
                                </div>
                                <span
                                    class="px-2.5 py-1 text-xs font-bold rounded-full {{ $isCritical ? 'bg-red-100 text-red-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ number_format($item->total_stok, 1) }} Karton
                                </span>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('stok.index') }}"
                            class="inline-flex items-center gap-1.5 bg-orange-600 text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-orange-500 transition-colors shadow-sm">
                            <i class="fa-solid fa-arrow-right text-[10px]"></i> Lihat Stok
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- RIGHT COLUMN: Barang Terlaris + Retur --}}
        <div class="lg:col-span-7 space-y-5">
            {{-- Barang Terlaris --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-2 h-5 bg-amber-500 rounded-full"></div>
                    <h2 class="font-semibold text-gray-800">Barang Terlaris</h2>
                </div>

                <div class="space-y-1">
                    @foreach ($barangTerlaris as $item)
                        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-gray-50 transition-colors group">
                            @if($loop->iteration <= 3)
                                <div
                                    class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-bold
                                                                                                                                                                                                                {{ $loop->iteration == 1 ? 'bg-amber-100 text-amber-700' : ($loop->iteration == 2 ? 'bg-gray-100 text-gray-600' : 'bg-orange-100 text-orange-600') }}">
                                    {{ $loop->iteration }}
                                </div>
                            @else
                                <div
                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-gray-50 text-gray-400 text-sm font-bold">
                                    {{ $loop->iteration }}
                                </div>
                            @endif
                            <img src="{{ asset('storage/' . $item->foto_produk) }}" alt="{{ $item->nama_barang }}"
                                class="w-11 h-11 rounded-xl object-cover border border-gray-100 group-hover:border-gray-200 transition">
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold text-gray-800 text-sm truncate">{{ $item->nama_barang }}</p>
                                <p class="text-xs text-gray-400">{{ $item->nama_kategori }}</p>
                            </div>
                            <span class="px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg whitespace-nowrap">
                                {{ number_format($item->kali_terjual, 0) }}× Terjual
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- RETUR BARANG TERBARU --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-2 h-5 bg-pink-500 rounded-full"></div>
                        <h2 class="font-semibold text-gray-800">Retur Barang Terbaru</h2>
                    </div>
                    <a href="{{ route('retur.index') }}"
                        class="text-xs font-medium text-gray-500 hover:text-gray-700 border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition">
                        Lihat Semua <i class="fa-solid fa-arrow-right text-[10px] ml-1"></i>
                    </a>
                </div>

                @if ($recentReturs->count() == 0)
                    <div class="flex flex-col items-center py-6 text-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                            <i class="fa-solid fa-check text-green-600 text-lg"></i>
                        </div>
                        <p class="text-sm text-gray-500">Tidak ada retur barang.</p>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-xl border border-gray-100">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Barang</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">PO</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Qty</th>
                                    <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($recentReturs as $retur)
                                    <tr class="hover:bg-pink-50/30 transition-colors">
                                        <td class="px-4 py-2.5">
                                            <p class="font-medium text-gray-800 text-sm">{{ $retur->barang->nama_barang ?? '-' }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <span class="text-xs text-gray-500">{{ $retur->po_id }}</span>
                                        </td>
                                        <td class="px-4 py-2.5">
                                            <span class="text-sm font-semibold text-red-600">{{ $retur->qty_retur }}</span>
                                        </td>
                                        <td class="px-4 py-2.5">
                                            @php
                                                $statusColors = [
                                                    'Menunggu Konfirmasi' => 'bg-amber-100 text-amber-700',
                                                    'Dikonfirmasi' => 'bg-green-100 text-green-700',
                                                    'Sesuai' => 'bg-emerald-100 text-emerald-700',
                                                    'Dibatalkan' => 'bg-red-100 text-red-700',
                                                ];
                                                $statusColor = $statusColors[$retur->status_retur] ?? 'bg-gray-100 text-gray-700';
                                            @endphp
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $statusColor }}">
                                                {{ $retur->status_retur }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- SECTION 4: Approval & Pengiriman --}}
    {{-- ========================================= --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-6">

        {{-- Pending PO --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-5 bg-slate-700 rounded-full"></div>
                    <h2 class="font-semibold text-gray-800">Approval PO</h2>
                </div>
                @if($poPending->count() > 0)
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-full animate-pulse">
                        {{ $poPending->total() }} Pending
                    </span>
                @endif
            </div>

            <div class="space-y-2">
                @forelse($poPending as $po)
                    <div
                        class="flex items-center gap-3 p-3 bg-slate-50 rounded-xl border border-slate-100 hover:border-slate-200 transition">
                        <div class="w-9 h-9 bg-slate-800 text-white rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-file-signature text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate">{{ $po->po_id }}</p>
                            <p class="text-xs text-gray-400 truncate">Dari: {{ $po->user->nama_lengkap ?? '-' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <p class="text-xs text-gray-400">Tidak ada PO Pending.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-4">
                <a href="{{ route('approval.approval_po') }}"
                    class="inline-flex items-center gap-1.5 bg-slate-800 text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-slate-700 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-right text-[10px]"></i> Lihat Semua
                </a>
            </div>
        </div>

        {{-- Pending Surat Jalan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-5 bg-indigo-500 rounded-full"></div>
                    <h2 class="font-semibold text-gray-800">Approval Surat Jalan</h2>
                </div>
                @if($sjPending->count() > 0)
                    <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-xs font-bold rounded-full animate-pulse">
                        {{ $sjPending->total() }} Pending
                    </span>
                @endif
            </div>

            <div class="space-y-2">
                @forelse($sjPending as $sj)
                    <div
                        class="flex items-center gap-3 p-3 bg-indigo-50/50 rounded-xl border border-indigo-100 hover:border-indigo-200 transition">
                        <div class="w-9 h-9 bg-indigo-600 text-white rounded-lg flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-file-signature text-sm"></i>
                        </div>
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate">{{ $sj->sj_id }}</p>
                            <p class="text-xs text-gray-400 truncate">Pelanggan: {{ $sj->pelanggan->nama_pelanggan ?? '-' }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <p class="text-xs text-gray-400">Tidak ada Surat Jalan Pending.</p>
                    </div>
                @endforelse
            </div>
            <div class="mt-4">
                <a href="{{ route('approval.approval_surat_jalan') }}"
                    class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2 rounded-xl text-xs font-semibold hover:bg-indigo-500 transition-colors shadow-sm">
                    <i class="fa-solid fa-arrow-right text-[10px]"></i> Lihat Semua
                </a>
            </div>
        </div>

        {{-- Status Pengiriman Mingguan --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-2 h-5 bg-cyan-500 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Pengiriman Minggu Ini</h2>
            </div>

            @php
                $deliveryStatuses = [
                    ['label' => 'Menunggu', 'key' => 'Menunggu', 'icon' => 'fa-clock', 'color' => 'text-amber-500', 'bg' => 'bg-amber-50', 'border' => 'border-amber-100'],
                    ['label' => 'Dalam Pengiriman', 'key' => 'Dalam Perjalanan', 'icon' => 'fa-truck-fast', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50', 'border' => 'border-blue-100'],
                    ['label' => 'Terkirim', 'key' => 'Terkirim', 'icon' => 'fa-circle-check', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50', 'border' => 'border-emerald-100'],
                    ['label' => 'Dibatalkan', 'key' => 'Dibatalkan', 'icon' => 'fa-circle-xmark', 'color' => 'text-red-500', 'bg' => 'bg-red-50', 'border' => 'border-red-100'],
                ];
            @endphp

            <div class="space-y-2">
                @foreach ($deliveryStatuses as $ds)
                    <div class="flex items-center justify-between p-3 {{ $ds['bg'] }} rounded-xl border {{ $ds['border'] }}">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center shadow-sm">
                                <i class="fa-solid {{ $ds['icon'] }} {{ $ds['color'] }}"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-700">{{ $ds['label'] }}</span>
                        </div>
                        <span class="text-lg font-bold text-gray-800">{{ $statusPengiriman[$ds['key']] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- SECTION 5: Log Barang Masuk Terbaru --}}
    {{-- ========================================= --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 bg-blue-500 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Log Barang Masuk Terbaru</h2>
            </div>
            <a href="/laporan/barang-masuk"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-800 border border-gray-200 hover:border-gray-300 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition-all">
                <span>Lihat Semua</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Ref
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama
                            Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Satuan
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Supplier</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($barangMasukBulanIni as $bm)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-4 py-3"><span class="font-semibold text-gray-800">{{ $bm->po_id }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ $bm->nama_barang }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 text-emerald-700 font-semibold">
                                    <i class="fa-solid fa-arrow-down text-[10px]"></i> {{ $bm->quantity_diterima }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">Karton</td>
                            <td class="px-4 py-3 text-gray-500">{{ \Carbon\Carbon::parse($bm->tanggal_masuk)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs font-medium rounded-md">{{ $bm->namaSupplier }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- ========================================= --}}
    {{-- SECTION 5.5: Log Barang Keluar Terbaru --}}
    {{-- ========================================= --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
            <div class="flex items-center gap-2">
                <div class="w-2 h-5 bg-red-500 rounded-full"></div>
                <h2 class="font-semibold text-gray-800">Log Barang Keluar Terbaru</h2>
            </div>
            <a href="/laporan/barang-keluar"
                class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-800 border border-gray-200 hover:border-gray-300 px-3 py-1.5 rounded-lg hover:bg-gray-50 transition-all">
                <span>Lihat Semua</span> <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-100">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">No. Ref
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Nama
                            Barang</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Qty
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Pelanggan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($barangKeluarBulanIni as $bk)
                        <tr class="hover:bg-red-50/30 transition-colors">
                            <td class="px-4 py-3"><span
                                    class="font-semibold text-gray-800">{{ $bk->sj_id ?? $bk->lap_keluar_id }}</span></td>
                            <td class="px-4 py-3 text-gray-600">{{ $bk->nama_barang }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-1 text-red-600 font-semibold">
                                    <i class="fa-solid fa-arrow-up text-[10px]"></i>
                                    {{ rtrim(rtrim(number_format($bk->jumlah_keluar, 2, ',', '.'), '0'), ',') }}
                                    {{ $bk->satuan_jual }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">
                                {{ \Carbon\Carbon::parse($bk->tanggal_keluar)->format('d/m/Y') }}
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs font-medium rounded-md">{{ $bk->nama_pelanggan ?? '-' }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
                                Tidak ada data barang keluar bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($barangKeluarBulanIni->hasPages())
            <div class="mt-4">
                {{ $barangKeluarBulanIni->links() }}
            </div>
        @endif
    </div>

    </div>

    {{-- ========================================= --}}
    {{-- CHART.JS SCRIPTS --}}
    {{-- ========================================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const bulanID = ["", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];

        const chartDefaults = {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    titleFont: { size: 12, weight: '600' },
                    bodyFont: { size: 11 },
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 11, weight: '500' }, color: '#94a3b8' }
                },
                y: {
                    grid: { color: '#f1f5f9', drawBorder: false },
                    ticks: { font: { size: 11 }, color: '#94a3b8' },
                    border: { display: false }
                }
            }
        };

        // =============================================
        // CHART 1: Tren Pergerakan Barang (Combined Bar)
        // =============================================
        const ctxPergerakan = document.getElementById('pergerakanBarangChart');
        if (ctxPergerakan) {
            const masukData = @json($barangMasuk->toArray());
            const keluarData = @json($barangKeluar->toArray());
            const allMonths = [...new Set([...Object.keys(masukData), ...Object.keys(keluarData)])].sort((a, b) => a - b);

            new Chart(ctxPergerakan, {
                type: 'bar',
                data: {
                    labels: allMonths.map(m => bulanID[m]),
                    datasets: [
                        {
                            label: 'Barang Masuk',
                            data: allMonths.map(m => masukData[m] || 0),
                            backgroundColor: 'rgba(59, 130, 246, 0.85)',
                            borderRadius: 5,
                            maxBarThickness: 28,
                        },
                        {
                            label: 'Barang Keluar',
                            data: allMonths.map(m => keluarData[m] || 0),
                            backgroundColor: 'rgba(239, 68, 68, 0.85)',
                            borderRadius: 5,
                            maxBarThickness: 28,
                        }
                    ]
                },
                options: {
                    ...chartDefaults,
                    plugins: {
                        ...chartDefaults.plugins,
                        legend: { display: true, position: 'top', align: 'end', labels: { usePointStyle: true, pointStyle: 'rectRounded', boxWidth: 8, font: { size: 11, weight: '500' } } }
                    }
                }
            });
        }

        // =============================================
        // CHART 2: Kualitas Penerimaan (Doughnut)
        // =============================================
        const ctxKualitas = document.getElementById('kualitasPenerimaanChart');
        if (ctxKualitas) {
            const totalBaik = {{ $totalMasukBulanIni }};
            const totalRusak = {{ $totalRusakBulanIni ?? 0 }};
            const totalAll = totalBaik + totalRusak;
            const pctBaik = totalAll > 0 ? ((totalBaik / totalAll) * 100).toFixed(1) : 0;

            new Chart(ctxKualitas, {
                type: 'doughnut',
                data: {
                    labels: ['Diterima Baik', 'Diterima Rusak'],
                    datasets: [{
                        data: [totalBaik, totalRusak],
                        backgroundColor: ['#3b82f6', '#ef4444'],
                        borderWidth: 0,
                        cutout: '72%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1e293b', cornerRadius: 8, padding: 10 }
                    }
                },
                plugins: [{
                    id: 'centerText',
                    afterDraw(chart) {
                        const { ctx, width, height } = chart;
                        ctx.save();
                        ctx.font = 'bold 20px Inter, sans-serif';
                        ctx.fillStyle = '#1e293b';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(pctBaik + '%', width / 2, height / 2 - 6);
                        ctx.font = '10px Inter, sans-serif';
                        ctx.fillStyle = '#94a3b8';
                        ctx.fillText('Baik', width / 2, height / 2 + 12);
                        ctx.restore();
                    }
                }]
            });
        }

        // =============================================
        // CHART 3: Distribusi Stok per Kategori (Horizontal Bar)
        // =============================================
        const ctxKategori = document.getElementById('stokKategoriChart');
        if (ctxKategori) {
            const kategoriData = @json($stokPerKategori);
            const kategoriColors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
            new Chart(ctxKategori, {
                type: 'bar',
                data: {
                    labels: kategoriData.map(i => i.kategori),
                    datasets: [{
                        label: 'Stok (Karton)',
                        data: kategoriData.map(i => i.total_stok),
                        backgroundColor: kategoriData.map((_, idx) => kategoriColors[idx % kategoriColors.length] + 'dd'),
                        borderRadius: 5,
                        borderSkipped: false,
                        maxBarThickness: 36,
                    }]
                },
                options: {
                    ...chartDefaults,
                    indexAxis: 'y',
                    plugins: {
                        ...chartDefaults.plugins,
                        legend: { display: false },
                        tooltip: {
                            ...chartDefaults.plugins.tooltip,
                            callbacks: {
                                label: (ctx) => ctx.parsed.x.toLocaleString('id-ID') + ' Karton'
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { color: '#f1f5f9', drawBorder: false },
                            ticks: { font: { size: 11 }, color: '#94a3b8' },
                            border: { display: false }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { font: { size: 11, weight: '500' }, color: '#374151' }
                        }
                    }
                }
            });
        }

        // =============================================
        // CHART 4: Status Pengiriman (Doughnut)
        // =============================================
        const ctxPengiriman = document.getElementById('pengirimanChart');
        if (ctxPengiriman) {
            const statusData = @json($statusPengiriman);
            const statusLabels = Object.keys(statusData);
            const statusValues = Object.values(statusData);
            const statusTotal = statusValues.reduce((a, b) => a + b, 0);
            const statusColorMap = { 'Menunggu': '#f59e0b', 'Dalam Perjalanan': '#3b82f6', 'Terkirim': '#10b981', 'Dibatalkan': '#ef4444' };
            const statusColors = statusLabels.map(l => statusColorMap[l] || '#94a3b8');

            new Chart(ctxPengiriman, {
                type: 'doughnut',
                data: {
                    labels: statusLabels,
                    datasets: [{
                        data: statusValues,
                        backgroundColor: statusColors,
                        borderWidth: 0,
                        cutout: '72%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1e293b', cornerRadius: 8, padding: 10 }
                    }
                },
                plugins: [{
                    id: 'centerTextPengiriman',
                    afterDraw(chart) {
                        const { ctx, width, height } = chart;
                        ctx.save();
                        ctx.font = 'bold 22px Inter, sans-serif';
                        ctx.fillStyle = '#1e293b';
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'middle';
                        ctx.fillText(statusTotal, width / 2, height / 2 - 6);
                        ctx.font = '10px Inter, sans-serif';
                        ctx.fillStyle = '#94a3b8';
                        ctx.fillText('Total', width / 2, height / 2 + 12);
                        ctx.restore();
                    }
                }]
            });
        }

        // =============================================
        // CHART 5: Tren Omset Penjualan (Area Line)
        // =============================================
        const ctxOmset = document.getElementById('omsetChart');
        if (ctxOmset) {
            new Chart(ctxOmset, {
                type: 'line',
                data: {
                    labels: @json(array_keys($omsetBulanan->toArray())).map(m => bulanID[m]),
                    datasets: [{
                        label: 'Omset (Rp)',
                        data: @json(array_values($omsetBulanan->toArray())),
                        borderColor: '#8b5cf6',
                        backgroundColor: 'rgba(139, 92, 246, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointBackgroundColor: '#8b5cf6',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2.5,
                        pointHoverRadius: 7,
                    }]
                },
                options: {
                    ...chartDefaults,
                    plugins: {
                        ...chartDefaults.plugins,
                        legend: { display: false },
                        tooltip: {
                            ...chartDefaults.plugins.tooltip,
                            callbacks: {
                                label: (ctx) => 'Rp ' + ctx.parsed.y.toLocaleString('id-ID')
                            }
                        }
                    },
                    scales: {
                        ...chartDefaults.scales,
                        y: {
                            ...chartDefaults.scales.y,
                            ticks: {
                                ...chartDefaults.scales.y.ticks,
                                callback: (value) => 'Rp ' + (value / 1000000).toFixed(1) + 'jt'
                            }
                        }
                    }
                }
            });
        }

        // =============================================
        // Stok Donut Chart (Non-Head section)
        // =============================================
        const ctxStok = document.getElementById('stokDonutChart');
        if (ctxStok) {
            new Chart(ctxStok, {
                type: 'doughnut',
                data: {
                    labels: ['Stok Baik', 'Stok Rusak'],
                    datasets: [{
                        data: [{{ $jumlahKarton }}, {{ $jumlahKartonRusak }}],
                        backgroundColor: ['#10b981', '#ef4444'],
                        borderWidth: 0,
                        cutout: '70%',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: { backgroundColor: '#1e293b', cornerRadius: 8, padding: 10 }
                    }
                }
            });
        }
    </script>
@endsection