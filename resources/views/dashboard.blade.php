@extends('layouts.app')

@section('content')

    {{-- WRAPPER ALPINE.JS --}}
    <div x-data="{ activeTab: 'overview' }" class="space-y-6">

        {{-- ========================================= --}}
        {{-- HEADER & QUICK ACTIONS (HERO AREA) --}}
        {{-- ========================================= --}}
        <div class="bg-gradient-to-br from-red-950 via-red-900 to-red-950 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-72 h-72 bg-red-500/10 rounded-full blur-3xl -mr-24 -mt-24"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl -ml-16 -mb-16"></div>
            
            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-11 h-11 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <i class="fa-solid fa-user-shield text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-red-200/80 font-medium">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                            <h1 class="text-2xl font-bold">
                                Halo, {{ auth()->user()->nama_lengkap ?? auth()->user()->name ?? 'User' }} 👋
                            </h1>
                        </div>
                    </div>
                    <p class="text-sm text-red-200/60 max-w-lg mt-1">Berikut ringkasan performa dan aktivitas Gudang Fanjaya. Pantau stok, pesanan, dan jadwal pengiriman Anda hari ini.</p>
                </div>

                {{-- Quick Actions Pill-Row --}}
                <div class="flex flex-wrap gap-2 lg:justify-end">
                    @php
                        $quickActions = [
                            ['label' => 'Buat PO', 'icon' => 'fa-file-circle-plus', 'route' => route('purchase_orders.buat_permintaan')],
                            ['label' => 'Surat Jalan', 'icon' => 'fa-file-invoice', 'route' => route('surat_jalan.create')],
                            ['label' => 'Verifikasi', 'icon' => 'fa-clipboard-check', 'route' => '/verifBarang'],
                            ['label' => 'Stok', 'icon' => 'fa-warehouse', 'route' => route('stok.index')],
                            ['label' => 'Katalog', 'icon' => 'fa-book-open', 'route' => '/katalog_barang'],
                        ];
                    @endphp
                    @foreach ($quickActions as $action)
                        <a href="{{ $action['route'] }}"
                            class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                            <i class="fa-solid {{ $action['icon'] }} text-amber-400"></i> <span class="hidden sm:inline">{{ $action['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- KPI STAT CARDS (6 CARDS) --}}
        {{-- ========================================= --}}
        @php
            $stats = [
                [
                    'title' => 'Total Produk',
                    'value' => $jumlahProduk,
                    'suffix' => 'Varian Aktif',
                    'icon' => 'fa-box-open',
                    'bg' => 'bg-slate-50',
                    'iconColor' => 'text-slate-600',
                ],
                [
                    'title' => 'Stok Tersedia',
                    'value' => $jumlahKarton,
                    'suffix' => 'Karton',
                    'icon' => 'fa-cubes',
                    'bg' => 'bg-emerald-50',
                    'iconColor' => 'text-emerald-500',
                ],
                [
                    'title' => 'Barang Rusak',
                    'value' => $jumlahKartonRusak,
                    'suffix' => 'Karton',
                    'icon' => 'fa-box-tissue',
                    'bg' => 'bg-amber-50',
                    'iconColor' => 'text-amber-500',
                ],
                [
                    'title' => 'Masuk Bulan Ini',
                    'value' => $totalMasukBulanIni,
                    'suffix' => 'Karton',
                    'icon' => 'fa-arrow-down',
                    'bg' => 'bg-blue-50',
                    'iconColor' => 'text-blue-500',
                ],
                [
                    'title' => 'Keluar Bulan Ini',
                    'value' => number_format((float)$totalKeluarBulanIni, 1),
                    'suffix' => 'Karton',
                    'icon' => 'fa-arrow-up',
                    'bg' => 'bg-red-50',
                    'iconColor' => 'text-red-500',
                ],
                [
                    'title' => 'Omset ' . \Carbon\Carbon::now()->format('M Y'),
                    'value' => 'Rp ' . number_format((float)$omsetBulanIni, 0, ',', '.'),
                    'suffix' => 'Pendapatan',
                    'icon' => 'fa-money-bill-trend-up',
                    'bg' => 'bg-violet-50',
                    'iconColor' => 'text-violet-500',
                ],
            ];
        @endphp

        <div class="grid grid-cols-2 lg:grid-cols-6 gap-4">
            @foreach ($stats as $stat)
                <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm hover:shadow-md transition-shadow group">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center {{ $stat['bg'] }} {{ $stat['iconColor'] }} group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid {{ $stat['icon'] }} text-lg"></i>
                        </div>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 mb-1 tracking-wide">{{ $stat['title'] }}</p>
                        <p class="text-xl font-bold text-gray-900 truncate">{{ $stat['value'] }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5 truncate">{{ $stat['suffix'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ========================================= --}}
        {{-- MAIN CONTENT FULL WIDTH & BOTTOM ROW --}}
        {{-- ========================================= --}}
        <div class="flex flex-col gap-8">

            {{-- WIDGET UTAMA (Tabbed Data) --}}
            <div class="w-full flex flex-col gap-6">
                
                {{-- TAB NAVIGATION --}}
                <div class="flex items-center gap-1.5 p-1.5 bg-gray-100/80 rounded-2xl overflow-x-auto border border-gray-200/50 shadow-inner w-max">
                    <button @click="activeTab = 'overview'" 
                            :class="activeTab === 'overview' ? 'bg-white text-gray-800 shadow shadow-gray-200 border border-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50'"
                            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie opacity-70"></i> Ikhtisar Stok
                    </button>
                    @if(auth()->user()->role === 'HeadGudang' || auth()->user()->role === 'Head' || auth()->user()->role === 'SuperAdmin') 
                    <button @click="activeTab = 'analitik'" 
                            :class="activeTab === 'analitik' ? 'bg-white text-gray-800 shadow shadow-gray-200 border border-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50'"
                            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex items-center gap-2">
                        <i class="fa-solid fa-chart-line opacity-70"></i> Analitik Bisnis
                    </button>
                    @endif
                    <button @click="activeTab = 'logs'" 
                            :class="activeTab === 'logs' ? 'bg-white text-gray-800 shadow shadow-gray-200 border border-gray-100' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-200/50'"
                            class="px-5 py-2.5 rounded-xl text-sm font-bold transition-all whitespace-nowrap flex items-center gap-2">
                        <i class="fa-solid fa-clock-rotate-left opacity-70"></i> Log Transaksi
                    </button>
                </div>

                {{-- TAB 1: OVERVIEW STOK --}}
                <div x-show="activeTab === 'overview'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                    
                    {{-- Capacity & Supplier Info Row --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kapasitas Gudang --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-5">
                            <div class="relative flex-shrink-0 w-20 h-20">
                                <canvas id="stokDonutChart"></canvas>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-gray-800 text-sm mb-1">Kapasitas Gudang</h3>
                                <div class="flex items-end justify-between mb-1.5">
                                    <span class="text-xl font-extrabold text-gray-900">{{ $jumlahKarton + $jumlahKartonRusak }}</span>
                                    <span class="text-xs font-semibold text-gray-400">/ {{ number_format(floatval($appSettings['kapasitas_gudang'] ?? 1000), 0, ',', '.') }} Krt</span>
                                </div>
                                @php
                                    $allStock = $jumlahKarton + $jumlahKartonRusak;
                                    $cpMax = floatval($appSettings['kapasitas_gudang'] ?? 1000);
                                    $cpUsed = $cpMax > 0 ? min(round(($allStock / $cpMax) * 100, 1), 100) : 0;
                                    $cpColor = $cpUsed >= 90 ? 'bg-red-500' : ($cpUsed >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                                @endphp
                                <div class="w-full bg-gray-100/80 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full {{ $cpColor }} transition-all duration-1000" style="width: {{ $cpUsed }}%"></div>
                                </div>
                                <p class="text-[10px] text-gray-400 mt-1 text-right font-medium">{{ $cpUsed }}% Terpakai</p>
                            </div>
                        </div>

                        {{-- Client Info --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex">
                            <div class="w-1/2 border-r border-gray-100 flex flex-col items-center justify-center p-2">
                                <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-2xl flex items-center justify-center mb-3">
                                    <i class="fa-solid fa-truck-field text-xl"></i>
                                </div>
                                <p class="text-[10px] text-gray-400 tracking-widest font-bold uppercase mb-0.5">Supplier</p>
                                <p class="text-2xl font-black text-gray-900">{{ $totalSupplier }}</p>
                            </div>
                            <div class="w-1/2 flex flex-col items-center justify-center p-2">
                                <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-2xl flex items-center justify-center mb-3">
                                    <i class="fa-solid fa-users text-xl"></i>
                                </div>
                                <p class="text-[10px] text-gray-400 tracking-widest font-bold uppercase mb-0.5">Pelanggan</p>
                                <p class="text-2xl font-black text-gray-900">{{ $totalPelanggan }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Approval & Pengiriman --}}
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-5 border-b border-gray-100 flex items-center gap-2">
                            <div class="w-2 h-5 bg-indigo-500 rounded-full"></div>
                            <h2 class="font-bold text-gray-800">Menunggu Tindakan & Pengiriman</h2>
                        </div>
                        <div class="p-5 grid grid-cols-1 xl:grid-cols-2 gap-6">
                            
                            {{-- Pending PO & SJ --}}
                            <div class="space-y-5">
                                {{-- PO --}}
                                <div>
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="text-sm font-bold text-gray-700"><i class="fa-solid fa-file-circle-exclamation text-slate-400 mr-1.5"></i> Approval PO</h3>
                                        <a href="{{ route('approval.approval_po') }}" class="text-[10px] font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-md hover:bg-slate-200 transition">Lihat Semua</a>
                                    </div>
                                    @if($poPending->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($poPending as $po)
                                            <div class="flex items-center gap-3 p-2.5 bg-slate-50 hover:bg-slate-100 rounded-xl border border-slate-100/50 transition-colors">
                                                <div class="w-9 h-9 bg-white shadow-sm text-slate-600 rounded-lg flex items-center justify-center text-sm border border-slate-200/50"><i class="fa-solid fa-file-signature"></i></div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $po->po_id }}</p>
                                                    <p class="text-[10px] text-gray-500 truncate">Dari: {{ $po->user->nama_lengkap ?? '-' }}</p>
                                                </div>
                                                <div><span class="px-2 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-700">Pending</span></div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="bg-gray-50 rounded-xl p-3 text-center border border-dashed border-gray-200">
                                            <p class="text-xs font-semibold text-gray-400">Tidak ada PO Pending.</p>
                                        </div>
                                    @endif
                                </div>
                                
                                {{-- SJ --}}
                                <div>
                                    <div class="flex justify-between items-center mb-3">
                                        <h3 class="text-sm font-bold text-gray-700"><i class="fa-solid fa-file-invoice text-indigo-400 mr-1.5"></i> Approval Surat Jalan</h3>
                                        <a href="{{ route('approval.approval_surat_jalan') }}" class="text-[10px] font-bold text-indigo-500 bg-indigo-50 px-2.5 py-1 rounded-md hover:bg-indigo-100 transition">Lihat Semua</a>
                                    </div>
                                    @if($sjPending->count() > 0)
                                        <div class="space-y-2">
                                            @foreach($sjPending as $sj)
                                            <div class="flex items-center gap-3 p-2.5 bg-indigo-50/50 hover:bg-indigo-50 rounded-xl border border-indigo-100/50 transition-colors">
                                                <div class="w-9 h-9 bg-white shadow-sm text-indigo-600 rounded-lg flex items-center justify-center text-sm border border-indigo-200/50"><i class="fa-solid fa-truck-ramp-box"></i></div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $sj->sj_id }}</p>
                                                    <p class="text-[10px] text-gray-500 truncate">Pelanggan: {{ $sj->pelanggan->nama_pelanggan ?? '-' }}</p>
                                                </div>
                                                <div><span class="px-2 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-700">Pending</span></div>
                                            </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="bg-gray-50 rounded-xl p-3 text-center border border-dashed border-gray-200">
                                            <p class="text-xs font-semibold text-gray-400">Tidak ada SJ Pending.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Status Pengiriman --}}
                            <div>
                                <h3 class="text-sm font-bold text-gray-700 mb-3"><i class="fa-solid fa-truck-fast text-sky-500 mr-1.5"></i> Status Pengiriman Minggu Ini</h3>
                                <div class="grid grid-cols-2 gap-3">
                                    @php
                                        $dStats = [
                                            ['l' => 'Menunggu', 'k' => 'Menunggu', 'icon' => 'fa-clock', 'col' => 'text-amber-600', 'bg' => 'bg-amber-50', 'bdr' => 'border-amber-100'],
                                            ['l' => 'Dikirim', 'k' => 'Dalam Perjalanan', 'icon' => 'fa-truck-fast', 'col' => 'text-blue-600', 'bg' => 'bg-blue-50', 'bdr' => 'border-blue-100'],
                                            ['l' => 'Selesai', 'k' => 'Terkirim', 'icon' => 'fa-clipboard-check', 'col' => 'text-emerald-600', 'bg' => 'bg-emerald-50', 'bdr' => 'border-emerald-100'],
                                            ['l' => 'Dibatalkan', 'k' => 'Dibatalkan', 'icon' => 'fa-xmark-circle', 'col' => 'text-red-600', 'bg' => 'bg-red-50', 'bdr' => 'border-red-100'],
                                        ];
                                    @endphp
                                    @foreach($dStats as $ds)
                                    <div class="{{ $ds['bg'] }} border {{ $ds['bdr'] }} rounded-2xl p-4 flex flex-col justify-center items-center text-center relative overflow-hidden group">
                                        <i class="fa-solid {{ $ds['icon'] }} absolute -bottom-3 -right-3 text-5xl opacity-5 {{ $ds['col'] }} group-hover:scale-110 transition-transform"></i>
                                        <span class="text-3xl font-black {{ $ds['col'] }} mb-1">{{ $statusPengiriman[$ds['k']] ?? 0 }}</span>
                                        <span class="text-[10px] font-bold text-gray-500 tracking-wider uppercase">{{ $ds['l'] }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- TAB 2: ANALITIK (Khusus Head) --}}
                @if(auth()->user()->role === 'HeadGudang' || auth()->user()->role === 'Head' || auth()->user()->role === 'SuperAdmin')
                <div x-show="activeTab === 'analitik'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" style="display: none;">
                    
                    {{-- Row 1: Charts --}}
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
                        {{-- Pergerakan Barang --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fa-solid fa-arrow-right-arrow-left text-sm"></i></div>
                                <div>
                                    <h3 class="font-bold text-gray-800 text-sm">Tren Pergerakan Barang</h3>
                                    <p class="text-[10px] text-gray-400">Data bulanan Masuk/Keluar</p>
                                </div>
                            </div>
                            <div class="h-48">
                                <canvas id="pergerakanBarangChart"></canvas>
                            </div>
                        </div>
                        
                        {{-- Stok per Kategori --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center"><i class="fa-solid fa-layer-group text-sm"></i></div>
                                <div>
                                    <h3 class="font-bold text-gray-800 text-sm">Kapasitas per Kategori</h3>
                                    <p class="text-[10px] text-gray-400">Total volume stok (Karton)</p>
                                </div>
                            </div>
                            <div class="h-48">
                                <canvas id="stokKategoriChart"></canvas>
                            </div>
                        </div>
                    </div>

                    {{-- Row 2: Charts --}}
                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                        {{-- Omset --}}
                        <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center"><i class="fa-solid fa-chart-area text-sm"></i></div>
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-sm">Omset Penjualan</h3>
                                        <p class="text-[10px] text-gray-400">Performa pendapatan (Rp)</p>
                                    </div>
                                </div>
                                <div class="bg-violet-50 px-3 py-1.5 rounded-lg border border-violet-100 text-right">
                                    <p class="text-[9px] font-bold text-violet-500 uppercase tracking-widest">Bulan Ini</p>
                                    <p class="text-sm font-black text-violet-700">Rp {{ number_format((float)$omsetBulanIni, 0, ',', '.') }}</p>
                                </div>
                            </div>
                            <div class="h-40">
                                <canvas id="omsetChart"></canvas>
                            </div>
                        </div>

                        {{-- Quality Control --}}
                        <div class="xl:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                            <div class="flex items-center gap-2 mb-4">
                                <div class="w-8 h-8 rounded-lg bg-pink-50 text-pink-600 flex items-center justify-center"><i class="fa-solid fa-clipboard-check text-sm"></i></div>
                                <div>
                                    <h3 class="font-bold text-gray-800 text-sm">Kualitas QC</h3>
                                    <p class="text-[10px] text-gray-400">Penerimaan barang</p>
                                </div>
                            </div>
                            <div class="flex justify-center h-32 mb-4 relative">
                                <canvas id="kualitasPenerimaanChart"></canvas>
                            </div>
                            <div class="flex justify-center gap-4">
                                <div class="text-center">
                                    <span class="w-2 h-2 rounded-full bg-blue-500 inline-block mb-1"></span>
                                    <p class="text-xs font-bold text-gray-700">Baik</p>
                                    <p class="text-[10px] text-gray-500">{{ number_format((float)$totalMasukBulanIni, 0, ',', '.') }} krt</p>
                                </div>
                                <div class="text-center">
                                    <span class="w-2 h-2 rounded-full bg-red-500 inline-block mb-1"></span>
                                    <p class="text-xs font-bold text-gray-700">Rusak</p>
                                    <p class="text-[10px] text-gray-500">{{ number_format((float)($totalRusakBulanIni ?? 0), 0, ',', '.') }} krt</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                @endif

                {{-- TAB 3: LOGS --}}
                <div x-show="activeTab === 'logs'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6" style="display: none;">
                    
                    <div class="grid grid-cols-1 gap-6">
                        {{-- Log Barang Masuk --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
                                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2"><div class="w-7 h-7 bg-blue-100 text-blue-600 rounded-md flex items-center justify-center relative"><i class="fa-solid fa-arrow-down text-xs"></i></div> Log Masuk (<span class="text-blue-600">{{ \Carbon\Carbon::now()->format('M Y') }}</span>)</h3>
                                <a href="/laporan/barang-masuk" class="text-[10px] font-bold text-blue-600 border border-blue-200 bg-blue-50 px-2.5 py-1 rounded-md hover:bg-blue-100 transition">Semua Log</a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead class="bg-white text-gray-400 text-[10px] uppercase tracking-wider font-bold">
                                        <tr class="border-b border-gray-100">
                                            <th class="py-3 px-5">TGL & WAKTU</th>
                                            <th class="py-3 px-5">BARANG</th>
                                            <th class="py-3 px-5 text-center">QTY</th>
                                            <th class="py-3 px-5">SUPPLIER</th>
                                            <th class="py-3 px-5 text-right">PO REF</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 divide-y divide-gray-50">
                                        @foreach ($barangMasukBulanIni as $item)
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="py-3 px-5">
                                                    <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d M') }}</span>
                                                    <span class="text-xs text-gray-400 ml-1">{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('H:i') }}</span>
                                                </td>
                                                <td class="py-3 px-5">
                                                    <p class="font-bold text-gray-800 text-xs">{{ $item->nama_barang }}</p>
                                                </td>
                                                <td class="py-3 px-5 text-center">
                                                    <span class="inline-flex items-center gap-1 text-xs font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg">
                                                        <i class="fa-solid fa-plus text-[8px]"></i> {{ number_format((float)$item->quantity_diterima, 0) }} krt
                                                    </span>
                                                </td>
                                                <td class="py-3 px-5"><p class="text-xs font-medium text-gray-500 truncate max-w-[150px]">{{ $item->namaSupplier }}</p></td>
                                                <td class="py-3 px-5 text-right"><span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">{{ $item->po_id }}</span></td>
                                            </tr>
                                        @endforeach
                                        @if($barangMasukBulanIni->count() === 0)
                                            <tr><td colspan="5" class="text-center py-6 text-xs font-medium text-gray-400">Belum ada barang masuk bulan ini</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Log Barang Keluar --}}
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                            <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
                                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2"><div class="w-7 h-7 bg-red-100 text-red-600 rounded-md flex items-center justify-center relative"><i class="fa-solid fa-arrow-up text-xs"></i></div> Log Keluar (<span class="text-red-600">{{ \Carbon\Carbon::now()->format('M Y') }}</span>)</h3>
                                <a href="/laporan/barang-keluar" class="text-[10px] font-bold text-red-600 border border-red-200 bg-red-50 px-2.5 py-1 rounded-md hover:bg-red-100 transition">Semua Log</a>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-sm whitespace-nowrap">
                                    <thead class="bg-white text-gray-400 text-[10px] uppercase tracking-wider font-bold">
                                        <tr class="border-b border-gray-100">
                                            <th class="py-3 px-5">TGL & WAKTU</th>
                                            <th class="py-3 px-5">BARANG</th>
                                            <th class="py-3 px-5 text-center">QTY</th>
                                            <th class="py-3 px-5">PELANGGAN</th>
                                            <th class="py-3 px-5 text-right">SJ REF</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-gray-600 divide-y divide-gray-50">
                                        @foreach ($barangKeluarBulanIni->take(5) as $item)
                                            <tr class="hover:bg-slate-50 transition">
                                                <td class="py-3 px-5">
                                                    <span class="font-semibold text-gray-800">{{ \Carbon\Carbon::parse($item->tanggal_keluar)->format('d M') }}</span>
                                                    <span class="text-xs text-gray-400 ml-1">{{ \Carbon\Carbon::parse($item->tanggal_keluar)->format('H:i') }}</span>
                                                </td>
                                                <td class="py-3 px-5">
                                                    <p class="font-bold text-gray-800 text-xs">{{ $item->nama_barang }}</p>
                                                </td>
                                                <td class="py-3 px-5 text-center">
                                                    <span class="inline-flex items-center gap-1 text-xs font-black text-amber-600 bg-amber-50 px-2.5 py-1 rounded-lg">
                                                        <i class="fa-solid fa-minus text-[8px]"></i> {{ rtrim(rtrim(number_format((float)$item->jumlah_keluar, 2, ',', '.'), '0'), ',') }} {{ $item->satuan_jual }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-5"><p class="text-xs font-medium text-gray-500 truncate max-w-[150px]">{{ $item->nama_pelanggan ?? '-' }}</p></td>
                                                <td class="py-3 px-5 text-right"><span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">{{ $item->sj_id ?? $item->lap_keluar_id }}</span></td>
                                            </tr>
                                        @endforeach
                                        @if($barangKeluarBulanIni->count() === 0)
                                            <tr><td colspan="5" class="text-center py-6 text-xs font-medium text-gray-400">Belum ada barang keluar bulan ini</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            {{-- WIDGET BAWAH: Alerts & List Terbaru --}}
            <div class="w-full grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                
                {{-- Action Required Widget --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-red-50 to-white px-5 py-4 border-b border-red-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-1.5 h-6 bg-red-600 rounded-full"></div>
                            <h2 class="font-bold text-red-900">Perhatian Khusus</h2>
                        </div>
                        <i class="fa-solid fa-triangle-exclamation text-red-300"></i>
                    </div>

                    <div class="p-5 space-y-6">
                        {{-- Expired Focus --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1.5"><i class="fa-solid fa-clock-rotate-left"></i> Barang Kadaluarsa</h3>
                                @if($jumlahSudahExpired > 0)
                                <span class="bg-red-600 text-white text-[9px] px-2 py-0.5 rounded-full font-bold animate-pulse">{{ $jumlahSudahExpired }} EXPIRED</span>
                                @endif
                            </div>
                            @if ($barangExpired->count() == 0)
                                <div class="border border-dashed border-emerald-200 bg-emerald-50/50 rounded-xl p-3 flex flex-col items-center justify-center text-center gap-1.5 min-h-[80px]">
                                    <i class="fa-solid fa-check-circle text-emerald-500 text-lg"></i>
                                    <span class="text-[10px] text-emerald-600 font-bold tracking-wide">TIDAK ADA BARANG KADALUARSA</span>
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach ($barangExpired->take(3) as $item)
                                        @php
                                            $expDate = \Carbon\Carbon::parse($item->tgl_kadaluarsa);
                                            $isExpired = $expDate->isPast();
                                            $daysLeft = $isExpired ? (int) Carbon\Carbon::now()->diffInDays($expDate) : (int) Carbon\Carbon::now()->diffInDays($expDate, false);
                                            $bgClass = $isExpired ? 'bg-red-50 border-red-200' : 'bg-orange-50 border-orange-200';
                                            $textClass = $isExpired ? 'text-red-700' : 'text-orange-700';
                                        @endphp
                                        <div class="flex items-center justify-between p-3 rounded-xl border {{ $bgClass }} hover:shadow-sm transition-shadow">
                                            <div class="flex items-center gap-3 min-w-0 pr-2">
                                                <div class="w-8 h-8 rounded-lg {{ $isExpired ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600' }} flex items-center justify-center flex-shrink-0 text-xs">
                                                    <i class="fa-solid {{ $isExpired ? 'fa-skull-crossbones' : 'fa-hourglass-half' }}"></i>
                                                </div>
                                                <div class="min-w-0">
                                                    <p class="text-xs font-bold text-gray-900 truncate">{{ $item->barang->nama_barang }}</p>
                                                    <p class="text-[10px] text-gray-500 font-medium">{{ $item->jumlah_stok }} krt · Exp: {{ $expDate->format('d/m/y') }}</p>
                                                </div>
                                            </div>
                                            <div class="flex-shrink-0 text-right">
                                                <span class="text-[9px] font-black {{ $textClass }} uppercase bg-white px-2 py-1 rounded-md shadow-sm border {{ $isExpired ? 'border-red-100' : 'border-orange-100' }} block whitespace-nowrap">
                                                    {{ $isExpired ? 'KADALUARSA' : $daysLeft . ' HARI' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                    @if($barangExpired->count() > 3)
                                        <a href="/tracking-kadaluarsa" class="block w-full py-2 text-center text-[10px] text-red-600 font-bold hover:bg-red-50 rounded-lg transition-colors mt-1">LIHAT SEMUA ({{ $barangExpired->count() }}) <i class="fa-solid fa-arrow-right ml-0.5"></i></a>
                                    @endif
                                </div>
                            @endif
                        </div>

                        {{-- Low Stock Focus --}}
                        <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1.5"><i class="fa-solid fa-arrow-trend-down"></i> Stok Menipis</h3>
                                @if($lowStockItems->count() > 0)
                                <span class="bg-amber-100 text-amber-700 border border-amber-200 text-[9px] px-2 py-0.5 rounded-full font-bold">{{ $lowStockItems->count() }} ITEM</span>
                                @endif
                            </div>
                            @if ($lowStockItems->count() == 0)
                                <div class="border border-dashed border-emerald-200 bg-emerald-50/50 rounded-xl p-3 flex flex-col items-center justify-center text-center gap-1.5 min-h-[80px]">
                                    <i class="fa-solid fa-check-circle text-emerald-500 text-lg"></i>
                                    <span class="text-[10px] text-emerald-600 font-bold tracking-wide">SEMUA STOK AMAN</span>
                                </div>
                            @else
                                <div class="space-y-2.5">
                                    @foreach ($lowStockItems->take(4) as $item)
                                        <div class="flex flex-col p-3 rounded-xl border border-gray-100 bg-white hover:border-amber-200 hover:shadow-sm transition-all group">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                                    @if($item->foto_produk)
                                                        <img src="{{ asset('storage/' . $item->foto_produk) }}" class="w-7 h-7 rounded border border-gray-100 object-cover flex-shrink-0" alt="">
                                                    @else
                                                        <div class="w-7 h-7 rounded bg-gray-100 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-image text-gray-300 text-[10px]"></i></div>
                                                    @endif
                                                    <p class="text-xs font-bold text-gray-800 truncate">{{ $item->nama_barang }}</p>
                                                </div>
                                                <div class="bg-amber-50 px-2 py-0.5 rounded text-[10px] font-black {{ $item->total_stok <= 3 ? 'text-red-600' : 'text-amber-600' }} whitespace-nowrap">
                                                    {{ number_format((float)$item->total_stok, 1) }} krt
                                                </div>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-1.5 relative overflow-hidden">
                                                <div class="{{ $item->total_stok <= 3 ? 'bg-red-500' : 'bg-amber-500' }} h-1.5 rounded-full transition-all duration-1000" style="width: {{ min((floatval($item->total_stok)/10)*100, 100) }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                    <a href="{{ route('stok.index') }}" class="block w-full py-2 text-center text-[10px] text-amber-600 font-bold hover:bg-amber-50 rounded-lg transition-colors mt-1">CEK INVENTORI <i class="fa-solid fa-arrow-right ml-0.5"></i></a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Barang Terlaris --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded bg-orange-50 text-orange-600 flex items-center justify-center"><i class="fa-solid fa-fire text-sm"></i></div>
                            <h3 class="font-bold text-gray-800 text-sm">Top Barang Terlaris</h3>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="space-y-3">
                            @foreach ($barangTerlaris->take(5) as $idx => $item)
                                <div class="flex items-center justify-between group">
                                    <div class="flex items-center gap-3 min-w-0">
                                        <div class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0 {{ $idx == 0 ? 'bg-gradient-to-br from-amber-300 to-orange-400 text-white shadow-sm' : ($idx == 1 ? 'bg-gradient-to-br from-gray-300 to-gray-400 text-white shadow-sm' : ($idx == 2 ? 'bg-gradient-to-br from-orange-200 to-orange-300 text-white shadow-sm' : 'bg-slate-50 text-slate-400 border border-slate-100')) }}">
                                            {{ $idx + 1 }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold text-gray-800 truncate max-w-[160px] group-hover:text-orange-600 transition-colors">{{ $item->nama_barang }}</p>
                                            <p class="text-[9px] text-gray-400 uppercase tracking-wider font-semibold">{{ $item->nama_kategori }}</p>
                                        </div>
                                    </div>
                                    <span class="bg-blue-50/50 border border-blue-100 text-blue-700 font-black text-[10px] px-2 py-1 rounded-md flex-shrink-0">{{ number_format((float)$item->kali_terjual, 0) }}x</span>
                                </div>
                                @if(!$loop->last)
                                    <div class="border-b border-gray-50/50 w-full ml-10"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Retur Terbaru --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded bg-pink-50 text-pink-600 flex items-center justify-center"><i class="fa-solid fa-arrow-rotate-left text-sm"></i></div>
                            <h3 class="font-bold text-gray-800 text-sm">Retur Terbaru</h3>
                        </div>
                        <a href="{{ route('retur.index') }}" class="text-[10px] text-pink-600 font-bold hover:underline">Semua</a>
                    </div>
                    <div class="p-5">
                        @if ($recentReturs->count() == 0)
                            <div class="text-center py-4">
                                <p class="text-xs text-gray-400 font-medium">Tidak ada retur diproses.</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach ($recentReturs->take(4) as $retur)
                                    <div class="flex items-start justify-between bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                        <div class="min-w-0 pr-2">
                                            <p class="font-bold text-gray-800 text-xs truncate">{{ $retur->barang->nama_barang ?? '-' }} <span class="bg-red-100 text-red-700 px-1.5 py-0.5 rounded ml-1 text-[9px]">-{{ $retur->qty_retur }}</span></p>
                                            <p class="text-[9px] text-gray-400 mt-0.5 font-medium">Ref: {{ $retur->po_id }}</p>
                                        </div>
                                        @php
                                            $scColors = [
                                                'Menunggu Konfirmasi' => 'bg-amber-100 text-amber-700 border-amber-200',
                                                'Dikonfirmasi'        => 'bg-blue-100 text-blue-700 border-blue-200',
                                                'Sesuai'              => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                                'Dibatalkan'          => 'bg-red-100 text-red-700 border-red-200'
                                            ];
                                            $sc = $scColors[$retur->status_retur] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                        @endphp
                                        <span class="px-2 py-0.5 rounded border {{ $sc }} font-bold text-[8px] uppercase tracking-wider whitespace-nowrap">{{ $retur->status_retur }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- CHART SCRIPTS --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            // Re-render charts when analytic tab is shown to fix size issues when initially hidden
            Alpine.effect(() => {
                const tab = Alpine.store('activeTab');
                // Basic check, trigger window resize to force Chart.js re-render if needed
                setTimeout(() => window.dispatchEvent(new Event('resize')), 50);
            });
        });

        const bulanID = ["", "Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"];
        const initChart = (id, config) => { const el = document.getElementById(id); if (el) new Chart(el, config); };

        const chartDefaults = {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { cornerRadius: 8, padding: 10, titleFont: {family: 'Inter', size: 12}, bodyFont: {family: 'Inter', size: 11} } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { family: 'Inter', size: 10 }, color: '#94a3b8' } },
                y: { grid: { color: '#f1f5f9', borderDash: [2, 4] }, ticks: { font: { family: 'Inter', size: 10 }, color: '#94a3b8' }, border: { display: false } }
            }
        };

        // 1. STOK DONUT (Always visible at start)
        initChart('stokDonutChart', {
            type: 'doughnut',
            data: {
                labels: ['Stok Baik', 'Stok Rusak'],
                datasets: [{ 
                    data: [{{ $jumlahKarton }}, {{ $jumlahKartonRusak }}], 
                    backgroundColor: ['#10b981', '#ef4444'], 
                    borderWidth: 2, 
                    borderColor: '#ffffff',
                    hoverOffset: 4,
                    cutout: '72%' 
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, tooltip: { backgroundColor: '#1e293b' } } }
        });

        // Other charts explicitly checking if variables exist (Head role)
        @if(auth()->user()->role === 'HeadGudang' || auth()->user()->role === 'Head' || auth()->user()->role === 'SuperAdmin')
            setTimeout(() => {
                // Pergerakan Barang
                const masukData = @json($barangMasuk->toArray());
                const keluarData = @json($barangKeluar->toArray());
                const allMonths = [...new Set([...Object.keys(masukData), ...Object.keys(keluarData)])].sort((a,b)=>a-b);
                
                initChart('pergerakanBarangChart', {
                    type: 'bar',
                    data: {
                        labels: allMonths.map(m => bulanID[m]),
                        datasets: [
                            { label: 'Masuk', data: allMonths.map(m=>masukData[m]||0), backgroundColor: '#3b82f6', borderRadius: {topLeft: 4, topRight: 4}, barPercentage: 0.6 },
                            { label: 'Keluar', data: allMonths.map(m=>keluarData[m]||0), backgroundColor: '#ef4444', borderRadius: {topLeft: 4, topRight: 4}, barPercentage: 0.6 }
                        ]
                    },
                    options: { 
                        ...chartDefaults, 
                        plugins: { ...chartDefaults.plugins, legend: { display: true, position: 'top', align: 'end', labels: {boxWidth: 8, usePointStyle: true, font: {size: 10}} } } 
                    }
                });

                // Stok Kategori
                const katData = @json($stokPerKategori);
                const colors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#8b5cf6', '#ec4899', '#06b6d4', '#84cc16'];
                initChart('stokKategoriChart', {
                    type: 'bar',
                    data: {
                        labels: katData.map(i=>i.kategori),
                        datasets: [{ 
                            data: katData.map(i=>i.total_stok), 
                            backgroundColor: katData.map((_, i) => colors[i % colors.length] + 'dd'),
                            borderRadius: 4 
                        }]
                    },
                    options: { 
                        ...chartDefaults, 
                        indexAxis: 'y', 
                        scales: { x: chartDefaults.scales.y, y: { ...chartDefaults.scales.x, grid: {display: false} } } 
                    }
                });

                // Kualitas QC
                const qcTotal = {{ $totalMasukBulanIni + ($totalRusakBulanIni ?? 0) }};
                initChart('kualitasPenerimaanChart', {
                    type: 'doughnut',
                    data: {
                        labels: ['Diterima Baik', 'Diterima Rusak'],
                        datasets: [{ 
                            data: [{{ $totalMasukBulanIni }}, {{ $totalRusakBulanIni ?? 0 }}], 
                            backgroundColor: ['#3b82f6', '#ef4444'], 
                            borderWidth: 2, borderColor: '#fff', cutout: '78%' 
                        }]
                    },
                    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } },
                    plugins: [{
                        id: 'centerTextQC',
                        afterDraw(chart) {
                            const { ctx, width, height } = chart;
                            ctx.save();
                            const val = qcTotal > 0 ? (({{ $totalMasukBulanIni }} / qcTotal) * 100).toFixed(1) : 0;
                            ctx.font = 'bold 18px Inter';
                            ctx.fillStyle = '#1e293b';
                            ctx.textAlign = 'center';
                            ctx.textBaseline = 'middle';
                            ctx.fillText(val + '%', width / 2, height / 2 - 4);
                            ctx.font = 'bold 9px Inter';
                            ctx.fillStyle = '#94a3b8';
                            ctx.fillText('BAIK', width / 2, height / 2 + 12);
                            ctx.restore();
                        }
                    }]
                });

                // Omset
                initChart('omsetChart', {
                    type: 'line',
                    data: {
                        labels: @json(array_keys($omsetBulanan->toArray())).map(m => bulanID[m]),
                        datasets: [{
                            label: 'Omset',
                            data: @json(array_values($omsetBulanan->toArray())),
                            borderColor: '#8b5cf6', backgroundColor: 'rgba(139,92,246,0.1)', 
                            borderWidth: 2.5, pointRadius: 4, pointBackgroundColor: '#8b5cf6', pointBorderColor: '#fff',
                            fill: true, tension: 0.4
                        }]
                    },
                    options: { 
                        ...chartDefaults, 
                        scales: { 
                            ...chartDefaults.scales,
                            y: { ...chartDefaults.scales.y, ticks: { ...chartDefaults.scales.y.ticks, callback: v => (v/1000000).toFixed(1)+'jt' } } 
                        },
                        plugins: {
                            ...chartDefaults.plugins,
                            tooltip: { ...chartDefaults.plugins.tooltip, callbacks: { label: c => 'Rp ' + c.parsed.y.toLocaleString('id-ID') } }
                        }
                    }
                });
            }, 100);
        @endif
    </script>
@endsection