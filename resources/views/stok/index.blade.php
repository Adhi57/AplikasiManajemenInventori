@extends('layouts.app')

@section('page-title', 'Dashboard Monitoring Stok')

@section('content')
    <div x-data="stokDashboard()" class="space-y-6">

        {{-- ========================================= --}}
        {{-- HERO HEADER --}}
        {{-- ========================================= --}}
        <div class="bg-gradient-to-br from-red-950 via-red-900 to-red-950 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 w-72 h-72 bg-red-500/10 rounded-full blur-3xl -mr-24 -mt-24"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-amber-500/10 rounded-full blur-3xl -ml-16 -mb-16"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-11 h-11 bg-white/10 rounded-xl flex items-center justify-center backdrop-blur-sm border border-white/20">
                            <i class="fa-solid fa-warehouse text-xl text-amber-400"></i>
                        </div>
                        <div>
                            <p class="text-sm text-red-200/80 font-medium">{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</p>
                            <h1 class="text-2xl font-bold">Dashboard Monitoring Stok</h1>
                        </div>
                    </div>
                    <p class="text-sm text-red-200/60 max-w-lg mt-1">Pantau persediaan, kapasitas gudang, dan kadaluarsa barang secara real-time. Ambil keputusan cepat dengan data yang akurat.</p>
                </div>

                {{-- Quick Actions --}}
                <div class="flex flex-wrap gap-2 lg:justify-end">
                    @php
                        $quickActions = [
                            ['label' => 'Stok Opname', 'icon' => 'fa-clipboard-list', 'route' => route('stock.opname.index')],
                            ['label' => 'Reorder Point', 'icon' => 'fa-arrow-trend-up', 'route' => route('reorder_point.index')],
                            ['label' => 'Kadaluarsa', 'icon' => 'fa-clock', 'route' => '/tracking-kadaluarsa'],
                            ['label' => 'Katalog', 'icon' => 'fa-book-open', 'route' => '/katalog_barang'],
                        ];
                    @endphp
                    @foreach ($quickActions as $action)
                        <a href="{{ $action['route'] }}"
                            class="flex items-center gap-2 px-3.5 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                            <i class="fa-solid {{ $action['icon'] }} text-amber-400"></i>
                            <span class="hidden sm:inline">{{ $action['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- KPI SUMMARY CARDS --}}
        {{-- ========================================= --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            {{-- Total Stok --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all group cursor-pointer"
                 @click="statusFilter = ''; $dispatch('filter-changed')">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-blue-50 text-blue-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-cubes text-lg"></i>
                    </div>
                    <div class="flex items-center gap-1 text-[10px] font-bold text-blue-500 bg-blue-50 px-2 py-1 rounded-lg">
                        <i class="fa-solid fa-boxes-stacked text-[8px]"></i> ALL
                    </div>
                </div>
                <p class="text-xs font-semibold text-gray-500 mb-1 tracking-wide">Total Stok</p>
                <p class="text-2xl font-black text-gray-900">{{ number_format($totalKarton, 1, ',', '.') }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Karton · {{ $totalItems }} jenis barang</p>
            </div>

            {{-- Stok Aman --}}
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all group cursor-pointer"
                 @click="statusFilter = 'aman'; $dispatch('filter-changed')">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-emerald-50 text-emerald-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-shield-check text-lg"></i>
                    </div>
                    <div class="flex items-center gap-1 text-[10px] font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">
                        <i class="fa-solid fa-circle text-[5px]"></i> AMAN
                    </div>
                </div>
                <p class="text-xs font-semibold text-gray-500 mb-1 tracking-wide">Stok Aman</p>
                <p class="text-2xl font-black text-emerald-600">{{ $totalItems - $stokMenipis - $stokHabis }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Jenis barang · stok > 10 krt</p>
            </div>

            {{-- Stok Menipis --}}
            <div class="bg-white rounded-2xl border border-amber-100 p-5 shadow-sm hover:shadow-md transition-all group cursor-pointer relative overflow-hidden"
                 @click="statusFilter = 'menipis'; $dispatch('filter-changed')">
                @if($stokMenipis > 0)
                    <div class="absolute top-0 right-0 w-20 h-20 bg-amber-500/5 rounded-full -mr-6 -mt-6"></div>
                @endif
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-amber-50 text-amber-600 group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
                        </div>
                        @if($stokMenipis > 0)
                            <span class="flex items-center gap-1 text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-1 rounded-lg animate-pulse">
                                <i class="fa-solid fa-bell text-[8px]"></i> ALERT
                            </span>
                        @endif
                    </div>
                    <p class="text-xs font-semibold text-gray-500 mb-1 tracking-wide">Stok Menipis</p>
                    <p class="text-2xl font-black text-amber-600">{{ $stokMenipis }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Jenis barang · stok ≤ 10 krt</p>
                </div>
            </div>

            {{-- Stok Habis --}}
            <div class="bg-white rounded-2xl border border-red-100 p-5 shadow-sm hover:shadow-md transition-all group cursor-pointer relative overflow-hidden"
                 @click="statusFilter = 'habis'; $dispatch('filter-changed')">
                @if($stokHabis > 0)
                    <div class="absolute top-0 right-0 w-20 h-20 bg-red-500/5 rounded-full -mr-6 -mt-6"></div>
                @endif
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-red-50 text-red-600 group-hover:scale-110 transition-transform duration-300">
                            <i class="fa-solid fa-box-open text-lg"></i>
                        </div>
                        @if($stokHabis > 0)
                            <span class="flex items-center gap-1 text-[10px] font-bold text-red-700 bg-red-100 px-2 py-1 rounded-lg animate-pulse">
                                <i class="fa-solid fa-circle-exclamation text-[8px]"></i> KRITIS
                            </span>
                        @endif
                    </div>
                    <p class="text-xs font-semibold text-gray-500 mb-1 tracking-wide">Stok Habis</p>
                    <p class="text-2xl font-black text-red-600">{{ $stokHabis }}</p>
                    <p class="text-[10px] text-gray-400 mt-0.5">Jenis barang · stok = 0</p>
                </div>
            </div>

            {{-- Hampir Kadaluarsa --}}
            <div class="bg-white rounded-2xl border border-orange-100 p-5 shadow-sm hover:shadow-md transition-all group cursor-pointer">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-orange-50 text-orange-600 group-hover:scale-110 transition-transform duration-300">
                        <i class="fa-solid fa-hourglass-half text-lg"></i>
                    </div>
                    <div class="flex flex-col items-end gap-0.5">
                        @if($expiredCount > 0)
                            <span class="text-[9px] font-bold text-red-700 bg-red-100 px-2 py-0.5 rounded-full animate-pulse">{{ $expiredCount }} EXPIRED</span>
                        @endif
                        @if($nearExpiry > 0)
                            <span class="text-[9px] font-bold text-orange-700 bg-orange-100 px-2 py-0.5 rounded-full">{{ $nearExpiry }} SEGERA</span>
                        @endif
                    </div>
                </div>
                <p class="text-xs font-semibold text-gray-500 mb-1 tracking-wide">Kadaluarsa</p>
                <p class="text-2xl font-black text-orange-600">{{ $nearExpiry + $expiredCount }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">Batch · ≤ 60 hari / sudah expired</p>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- KAPASITAS GUDANG + CHARTS ROW --}}
        {{-- ========================================= --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Kapasitas Gudang (Enhanced) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-gauge-high text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm">Kapasitas Gudang</h3>
                        <p class="text-[10px] text-gray-400">Penggunaan ruang penyimpanan</p>
                    </div>
                </div>

                @php
                    $persentaseValue = $persentase ?? 0;
                    $progressWidth = min($persentaseValue, 100);
                    $warna = $persentaseValue >= 90 ? 'bg-red-500' : ($persentaseValue >= 70 ? 'bg-amber-500' : 'bg-emerald-500');
                    $warnaText = $persentaseValue >= 90 ? 'text-red-600' : ($persentaseValue >= 70 ? 'text-amber-600' : 'text-emerald-600');
                    $warnaBg = $persentaseValue >= 90 ? 'bg-red-50' : ($persentaseValue >= 70 ? 'bg-amber-50' : 'bg-emerald-50');
                @endphp

                {{-- Donut Placeholder Center --}}
                <div class="flex justify-center mb-5">
                    <div class="relative w-32 h-32">
                        <canvas id="kapasitasDonut"></canvas>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-2xl font-black {{ $warnaText }}">{{ number_format($persentaseValue, 1) }}%</span>
                            <span class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">Terpakai</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 text-gray-500">
                            <span class="w-2.5 h-2.5 rounded-full {{ $warna }}"></span> Terisi
                        </span>
                        <span class="font-bold {{ $warnaText }}">{{ number_format($totalStok ?? 0, 1, ',', '.') }} Krt</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="flex items-center gap-2 text-gray-500">
                            <span class="w-2.5 h-2.5 rounded-full bg-gray-200"></span> Tersisa
                        </span>
                        <span class="font-bold text-gray-600">{{ number_format(max(0, ($kapasitasMaks ?? 0) - ($totalStok ?? 0)), 1, ',', '.') }} Krt</span>
                    </div>
                    <div class="border-t border-gray-100 pt-3 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Maks. Kapasitas</span>
                        <span class="font-bold text-gray-800">{{ number_format($kapasitasMaks ?? 0, 0, ',', '.') }} Karton</span>
                    </div>
                </div>
            </div>

            {{-- Distribusi Stok per Kategori --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-violet-50 text-violet-600 flex items-center justify-center">
                        <i class="fa-solid fa-chart-pie text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm">Distribusi per Kategori</h3>
                        <p class="text-[10px] text-gray-400">Volume stok (Karton)</p>
                    </div>
                </div>
                <div class="h-44">
                    <canvas id="kategoriDonutChart"></canvas>
                </div>
                <div class="mt-4 grid grid-cols-2 gap-2">
                    @foreach ($stokPerKategori->take(4) as $idx => $kat)
                        @php
                            $katColors = ['bg-emerald-500', 'bg-blue-500', 'bg-amber-500', 'bg-violet-500'];
                        @endphp
                        <div class="flex items-center gap-2 text-xs">
                            <span class="w-2.5 h-2.5 rounded-full {{ $katColors[$idx % 4] }} flex-shrink-0"></span>
                            <span class="truncate text-gray-600">{{ $kat->kategori }}</span>
                            <span class="font-bold text-gray-800 ml-auto">{{ number_format($kat->total_stok, 0) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Tren Pergerakan Stok --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center gap-2 mb-5">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fa-solid fa-arrow-right-arrow-left text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-sm">Tren Pergerakan</h3>
                        <p class="text-[10px] text-gray-400">Masuk vs Keluar (6 bulan)</p>
                    </div>
                </div>
                <div class="h-52">
                    <canvas id="trendChart"></canvas>
                </div>
                <div class="mt-3 flex items-center justify-center gap-5">
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-3 h-2 rounded-sm bg-emerald-500"></span> Masuk
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-gray-500">
                        <span class="w-3 h-2 rounded-sm bg-red-400"></span> Keluar
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- ALERT NOTIFICATIONS + TOP ITEMS --}}
        {{-- ========================================= --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

            {{-- Alert Panel --}}
            <div class="xl:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-red-50 to-amber-50 px-5 py-4 border-b border-red-100/50 flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-1.5 h-6 bg-red-600 rounded-full"></div>
                        <h2 class="font-bold text-gray-800 text-sm">Perhatian Segera</h2>
                    </div>
                    <span class="bg-red-600 text-white text-[9px] px-2.5 py-1 rounded-full font-bold">
                        {{ $alertStokMenipis->count() + $alertNearExpiry->count() }}
                    </span>
                </div>

                <div class="p-5 space-y-5 max-h-[420px] overflow-y-auto custom-scrollbar">
                    {{-- Low Stock Alerts --}}
                    @if($alertStokMenipis->count() > 0)
                        <div>
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1.5 mb-3">
                                <i class="fa-solid fa-arrow-trend-down text-amber-500"></i> Stok Menipis
                            </h3>
                            <div class="space-y-2">
                                @foreach ($alertStokMenipis->take(5) as $item)
                                    <div class="flex items-center justify-between p-3 rounded-xl border border-amber-100 bg-amber-50/50 hover:bg-amber-50 hover:shadow-sm transition-all group">
                                        <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                            @if($item->foto_produk)
                                                <img src="{{ asset('storage/' . $item->foto_produk) }}" class="w-8 h-8 rounded-lg border border-amber-200 object-cover flex-shrink-0" alt="">
                                            @else
                                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                                    <i class="fa-solid fa-box text-amber-400 text-xs"></i>
                                                </div>
                                            @endif
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-gray-800 truncate">{{ $item->nama_barang }}</p>
                                                <p class="text-[9px] text-gray-400 font-mono">{{ $item->kode_barang }}</p>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            <span class="text-xs font-black {{ $item->total_stok <= 3 ? 'text-red-600' : 'text-amber-600' }}">
                                                {{ number_format($item->total_stok, 1) }} krt
                                            </span>
                                            <div class="w-16 bg-gray-100 rounded-full h-1 mt-1">
                                                <div class="{{ $item->total_stok <= 3 ? 'bg-red-500' : 'bg-amber-500' }} h-1 rounded-full" style="width: {{ min(($item->total_stok / 10) * 100, 100) }}%"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Near Expiry Alerts --}}
                    @if($alertNearExpiry->count() > 0)
                        <div class="{{ $alertStokMenipis->count() > 0 ? 'pt-4 border-t border-gray-100' : '' }}">
                            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest flex items-center gap-1.5 mb-3">
                                <i class="fa-solid fa-clock text-orange-500"></i> Mendekati Kadaluarsa
                            </h3>
                            <div class="space-y-2">
                                @foreach ($alertNearExpiry->take(5) as $item)
                                    @php
                                        $expDate = \Carbon\Carbon::parse($item->tgl_kadaluarsa);
                                        $isExpired = $expDate->isPast();
                                        $daysLeft = $isExpired ? 0 : (int) \Carbon\Carbon::now()->diffInDays($expDate);
                                    @endphp
                                    <div class="flex items-center justify-between p-3 rounded-xl border {{ $isExpired ? 'border-red-200 bg-red-50/50' : 'border-orange-100 bg-orange-50/50' }} hover:shadow-sm transition-all">
                                        <div class="flex items-center gap-2.5 min-w-0 pr-2">
                                            <div class="w-8 h-8 rounded-lg {{ $isExpired ? 'bg-red-100 text-red-600' : 'bg-orange-100 text-orange-600' }} flex items-center justify-center flex-shrink-0">
                                                <i class="fa-solid {{ $isExpired ? 'fa-skull-crossbones' : 'fa-hourglass-half' }} text-xs"></i>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-xs font-bold text-gray-800 truncate">{{ $item->nama_barang }}</p>
                                                <p class="text-[9px] text-gray-400">{{ $item->jumlah_stok }} krt · Exp: {{ $expDate->format('d/m/Y') }}</p>
                                            </div>
                                        </div>
                                        <span class="text-[9px] font-black {{ $isExpired ? 'text-red-700 bg-red-100 border-red-200' : 'text-orange-700 bg-orange-100 border-orange-200' }} uppercase px-2 py-1 rounded-md border whitespace-nowrap">
                                            {{ $isExpired ? 'EXPIRED' : $daysLeft . ' HARI' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($alertStokMenipis->count() == 0 && $alertNearExpiry->count() == 0)
                        <div class="border border-dashed border-emerald-200 bg-emerald-50/50 rounded-xl p-6 flex flex-col items-center justify-center text-center gap-2">
                            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center">
                                <i class="fa-solid fa-check text-emerald-600 text-xl"></i>
                            </div>
                            <span class="text-xs text-emerald-600 font-bold">SEMUA STOK DALAM KONDISI AMAN</span>
                            <span class="text-[10px] text-gray-400">Tidak ada alert yang perlu ditindaklanjuti</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Top Stok Items Chart --}}
            <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-5">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                            <i class="fa-solid fa-ranking-star text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-sm">Top 8 Stok Terbanyak</h3>
                            <p class="text-[10px] text-gray-400">Barang dengan volume stok tertinggi</p>
                        </div>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="topStokChart"></canvas>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- FILTER & SEARCH BAR --}}
        {{-- ========================================= --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <div class="flex flex-col lg:flex-row gap-4 items-end">
                {{-- Search --}}
                <div class="flex-1 w-full">
                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Pencarian</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                            <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                        </div>
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="filterTable()"
                            placeholder="Cari kode / nama barang..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-slate-500/20 focus:border-slate-500 transition shadow-sm">
                    </div>
                </div>

                {{-- Status Filter Pills --}}
                <div class="flex gap-1.5 flex-wrap">
                    <button @click="statusFilter = ''; filterTable()" :class="statusFilter === '' ? 'bg-slate-800 text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200">
                        <i class="fa-solid fa-border-all mr-1.5 text-[10px]"></i>Semua
                    </button>
                    <button @click="statusFilter = 'aman'; filterTable()" :class="statusFilter === 'aman' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100'"
                        class="px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200">
                        <i class="fa-solid fa-circle-check mr-1.5 text-[10px]"></i>Aman
                    </button>
                    <button @click="statusFilter = 'menipis'; filterTable()" :class="statusFilter === 'menipis' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100'"
                        class="px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200">
                        <i class="fa-solid fa-triangle-exclamation mr-1.5 text-[10px]"></i>Menipis
                    </button>
                    <button @click="statusFilter = 'habis'; filterTable()" :class="statusFilter === 'habis' ? 'bg-red-600 text-white shadow-sm' : 'bg-red-50 text-red-700 hover:bg-red-100'"
                        class="px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all duration-200">
                        <i class="fa-solid fa-circle-xmark mr-1.5 text-[10px]"></i>Habis
                    </button>
                </div>

                {{-- Server-side Filters --}}
                <form method="GET" action="{{ route('stok.index') }}" class="flex gap-2 items-end" id="serverFilterForm">
                    <input type="hidden" name="search" :value="searchQuery">

                    <div class="w-44">
                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Kategori</label>
                        <select name="kategori" onchange="document.getElementById('serverFilterForm').submit()"
                            class="w-full py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-slate-500/20 focus:border-slate-500 transition shadow-sm bg-white">
                            <option value="">Semua</option>
                            @foreach ($kategoris as $kat)
                                <option value="{{ $kat->id }}" {{ $kat->id == ($kategori ?? null) ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori_barang }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label class="flex items-center gap-2 px-3.5 py-2.5 bg-gray-50 border border-gray-200 rounded-xl cursor-pointer hover:bg-gray-100 transition">
                        <input type="checkbox" name="group" value="1" {{ $group ? 'checked' : '' }}
                            onchange="document.getElementById('serverFilterForm').submit()" class="text-slate-700 focus:ring-slate-500 rounded border-gray-300">
                        <span class="text-xs font-bold text-gray-700 whitespace-nowrap">Group by Barang</span>
                    </label>
                </form>
            </div>

            {{-- Active filter indicator --}}
            <div class="mt-3 flex items-center gap-2 text-xs text-gray-400" x-show="statusFilter || searchQuery" x-transition>
                <i class="fa-solid fa-filter text-[10px]"></i>
                <span>Filter aktif:</span>
                <span x-show="searchQuery" class="bg-slate-100 text-slate-600 font-bold px-2 py-0.5 rounded-md">"<span x-text="searchQuery"></span>"</span>
                <span x-show="statusFilter" class="font-bold px-2 py-0.5 rounded-md"
                    :class="{
                        'bg-emerald-100 text-emerald-700': statusFilter === 'aman',
                        'bg-amber-100 text-amber-700': statusFilter === 'menipis',
                        'bg-red-100 text-red-700': statusFilter === 'habis'
                    }" x-text="statusFilter.charAt(0).toUpperCase() + statusFilter.slice(1)"></span>
                <button @click="statusFilter = ''; searchQuery = ''; filterTable()" class="text-gray-400 hover:text-red-500 transition ml-1">
                    <i class="fa-solid fa-times-circle"></i> Reset
                </button>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- INTERACTIVE DATA TABLE --}}
        {{-- ========================================= --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-slate-50/50">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-5 bg-slate-700 rounded-full"></div>
                    <h2 class="font-bold text-gray-800">Data Stok Barang</h2>
                    <span class="text-[10px] font-bold text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md" x-text="visibleCount + ' item ditampilkan'"></span>
                </div>
                <div class="flex items-center gap-2 text-xs text-gray-400">
                    <i class="fa-solid fa-info-circle"></i>
                    <span>Klik header untuk sorting</span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm" id="stokTable">
                    <thead>
                        <tr class="bg-gray-50/80">
                            <th @click="sortBy('kode')" class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition select-none">
                                <div class="flex items-center gap-1.5">
                                    Kode Barang
                                    <i class="fa-solid fa-sort text-gray-300" :class="{'text-slate-600': sortColumn === 'kode'}"></i>
                                </div>
                            </th>
                            <th @click="sortBy('nama')" class="px-5 py-3.5 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition select-none">
                                <div class="flex items-center gap-1.5">
                                    Nama Barang
                                    <i class="fa-solid fa-sort text-gray-300" :class="{'text-slate-600': sortColumn === 'nama'}"></i>
                                </div>
                            </th>
                            @if(!$group)
                                <th @click="sortBy('kadaluarsa')" class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition select-none">
                                    <div class="flex items-center justify-center gap-1.5">
                                        Kadaluarsa
                                        <i class="fa-solid fa-sort text-gray-300" :class="{'text-slate-600': sortColumn === 'kadaluarsa'}"></i>
                                    </div>
                                </th>
                            @endif
                            <th @click="sortBy('stok')" class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100 transition select-none">
                                <div class="flex items-center justify-center gap-1.5">
                                    Stok (Karton)
                                    <i class="fa-solid fa-sort text-gray-300" :class="{'text-slate-600': sortColumn === 'stok'}"></i>
                                </div>
                            </th>
                            <th class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                Isi/Karton
                            </th>
                            <th class="px-5 py-3.5 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                                Total Pcs
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($stokBarangs as $stok)
                            @php
                                $expiryDate = $stok->tgl_kadaluarsa ? strtotime($stok->tgl_kadaluarsa) : null;
                                $daysToExpiry = $expiryDate ? floor(($expiryDate - time()) / (60 * 60 * 24)) : null;

                                $kartonCount = $group ? $stok->total_karton : $stok->jumlah_stok;
                                $isiPerKarton = $stok->barang->jml_barang_per_karton ?? 1;
                                $totalPcs = $kartonCount * $isiPerKarton;

                                // Status determination
                                $statusLevel = 'aman';
                                $statusColor = 'emerald';
                                $statusLabel = 'Aman';
                                $statusIcon = 'fa-circle-check';
                                if ($kartonCount <= 0) {
                                    $statusLevel = 'habis';
                                    $statusColor = 'red';
                                    $statusLabel = 'Habis';
                                    $statusIcon = 'fa-circle-xmark';
                                } elseif ($kartonCount <= 10) {
                                    $statusLevel = 'menipis';
                                    $statusColor = 'amber';
                                    $statusLabel = 'Menipis';
                                    $statusIcon = 'fa-triangle-exclamation';
                                }

                                // Row background
                                $rowBg = '';
                                if ($statusLevel === 'habis') $rowBg = 'bg-red-50/40';
                                elseif ($statusLevel === 'menipis') $rowBg = 'bg-amber-50/30';

                                // Expiry row highlight
                                if (!$group && $daysToExpiry !== null) {
                                    if ($daysToExpiry < 0) $rowBg = 'bg-red-50/60';
                                    elseif ($daysToExpiry < 30) $rowBg = 'bg-red-50/30';
                                    elseif ($daysToExpiry < 60) $rowBg = 'bg-amber-50/30';
                                }
                            @endphp

                            <tr class="hover:bg-blue-50/40 transition-colors {{ $rowBg }} stok-row"
                                data-kode="{{ strtolower($stok->kode_barang) }}"
                                data-nama="{{ strtolower($stok->barang->nama_barang ?? '') }}"
                                data-stok="{{ $kartonCount }}"
                                data-status="{{ $statusLevel }}"
                                data-kadaluarsa="{{ $expiryDate ?? '' }}"
                                x-data="{ showDetail: false }">
                                {{-- Kode Barang --}}
                                <td class="px-5 py-3.5">
                                    <span class="font-mono text-xs text-gray-500 bg-gray-100 px-2.5 py-1 rounded-lg">{{ $stok->kode_barang }}</span>
                                </td>

                                {{-- Nama Barang --}}
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        @if($stok->barang && $stok->barang->foto_produk)
                                            <img src="{{ asset('storage/' . $stok->barang->foto_produk) }}" class="w-8 h-8 rounded-lg border border-gray-100 object-cover flex-shrink-0" alt="">
                                        @else
                                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                <i class="fa-solid fa-box text-gray-300 text-xs"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">{{ $stok->barang->nama_barang ?? '-' }}</p>
                                            <p class="text-[10px] text-gray-400">{{ $stok->barang->kategori->nama_kategori_barang ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kadaluarsa --}}
                                @if(!$group)
                                    <td class="px-5 py-3.5 text-center">
                                        @if($expiryDate)
                                            <span class="text-xs text-gray-700 font-medium">{{ date('d M Y', $expiryDate) }}</span>
                                            @if($daysToExpiry !== null)
                                                @if($daysToExpiry < 0)
                                                    <span class="block text-[10px] font-black text-red-600 mt-0.5">
                                                        <i class="fa-solid fa-skull-crossbones text-[8px] mr-0.5"></i> EXPIRED
                                                    </span>
                                                @elseif($daysToExpiry < 60)
                                                    <span class="block text-[10px] font-bold mt-0.5 {{ $daysToExpiry < 30 ? 'text-red-600' : 'text-amber-600' }}">
                                                        <i class="fa-solid fa-clock text-[8px] mr-0.5"></i> {{ $daysToExpiry }} hari
                                                    </span>
                                                @endif
                                            @endif
                                        @else
                                            <span class="text-gray-300">—</span>
                                        @endif
                                    </td>
                                @endif

                                {{-- Stok --}}
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center px-3 py-1.5 bg-{{ $statusColor }}-50 text-{{ $statusColor }}-700 font-black text-sm rounded-lg border border-{{ $statusColor }}-100">
                                        {{ number_format($kartonCount, 2, ',', '.') }}
                                    </span>
                                </td>

                                {{-- Status Badge --}}
                                <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider
                                        {{ $statusLevel === 'aman' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : '' }}
                                        {{ $statusLevel === 'menipis' ? 'bg-amber-100 text-amber-700 border border-amber-200' : '' }}
                                        {{ $statusLevel === 'habis' ? 'bg-red-100 text-red-700 border border-red-200' : '' }}">
                                        <i class="fa-solid {{ $statusIcon }} text-[8px]"></i>
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                {{-- Isi/Karton --}}
                                <td class="px-5 py-3.5 text-center text-gray-600">{{ $isiPerKarton }}</td>

                                {{-- Total Pcs --}}
                                <td class="px-5 py-3.5 text-center">
                                    <span class="font-semibold text-gray-800">{{ number_format($totalPcs, 0, ',', '.') }}</span>
                                    <span class="text-[10px] text-gray-400 ml-0.5">{{ $stok->barang->satuan_jual ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $group ? 6 : 7 }}" class="px-5 py-16 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                                            <i class="fa-solid fa-box-open text-2xl text-gray-300"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-500">Tidak ada data stok ditemukan</p>
                                        <p class="text-xs text-gray-400 mt-0.5">Coba ubah filter atau kata kunci pencarian</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- No Results (client-side) --}}
            <div x-show="visibleCount === 0 && (searchQuery || statusFilter)" x-transition class="px-6 py-12 text-center border-t border-gray-100" style="display:none;">
                <div class="flex flex-col items-center">
                    <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3">
                        <i class="fa-solid fa-filter-circle-xmark text-xl text-gray-300"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">Tidak ada data cocok dengan filter</p>
                    <button @click="statusFilter = ''; searchQuery = ''; filterTable()" class="mt-2 text-xs font-bold text-blue-600 hover:underline">
                        <i class="fa-solid fa-arrow-rotate-left mr-1"></i> Reset Filter
                    </button>
                </div>
            </div>
        </div>

    </div>

    {{-- ========================================= --}}
    {{-- CHART.JS & ALPINE SCRIPTS --}}
    {{-- ========================================= --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function stokDashboard() {
            return {
                searchQuery: '{{ $search ?? '' }}',
                statusFilter: '',
                sortColumn: '',
                sortDirection: 'asc',
                visibleCount: document.querySelectorAll('.stok-row').length,

                init() {
                    this.filterTable();
                    this.initCharts();
                },

                filterTable() {
                    const rows = document.querySelectorAll('.stok-row');
                    let count = 0;
                    const query = this.searchQuery.toLowerCase().trim();

                    rows.forEach(row => {
                        const kode = row.dataset.kode || '';
                        const nama = row.dataset.nama || '';
                        const status = row.dataset.status || '';
                        const stok = parseFloat(row.dataset.stok) || 0;

                        let show = true;

                        // Text search
                        if (query && !kode.includes(query) && !nama.includes(query)) {
                            show = false;
                        }

                        // Status filter
                        if (this.statusFilter && status !== this.statusFilter) {
                            show = false;
                        }

                        row.style.display = show ? '' : 'none';
                        if (show) count++;
                    });

                    this.visibleCount = count;
                },

                sortBy(column) {
                    if (this.sortColumn === column) {
                        this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
                    } else {
                        this.sortColumn = column;
                        this.sortDirection = 'asc';
                    }

                    const tbody = document.querySelector('#stokTable tbody');
                    const rows = Array.from(tbody.querySelectorAll('.stok-row'));
                    const dir = this.sortDirection === 'asc' ? 1 : -1;

                    rows.sort((a, b) => {
                        let valA, valB;
                        switch (column) {
                            case 'kode':
                                valA = a.dataset.kode || '';
                                valB = b.dataset.kode || '';
                                return dir * valA.localeCompare(valB);
                            case 'nama':
                                valA = a.dataset.nama || '';
                                valB = b.dataset.nama || '';
                                return dir * valA.localeCompare(valB);
                            case 'stok':
                                valA = parseFloat(a.dataset.stok) || 0;
                                valB = parseFloat(b.dataset.stok) || 0;
                                return dir * (valA - valB);
                            case 'kadaluarsa':
                                valA = a.dataset.kadaluarsa ? parseInt(a.dataset.kadaluarsa) : Infinity;
                                valB = b.dataset.kadaluarsa ? parseInt(b.dataset.kadaluarsa) : Infinity;
                                return dir * (valA - valB);
                            default:
                                return 0;
                        }
                    });

                    rows.forEach(row => tbody.appendChild(row));
                },

                initCharts() {
                    this.$nextTick(() => {
                        // 1. Kapasitas Donut
                        const kapEl = document.getElementById('kapasitasDonut');
                        if (kapEl) {
                            new Chart(kapEl, {
                                type: 'doughnut',
                                data: {
                                    labels: ['Terisi', 'Tersisa'],
                                    datasets: [{
                                        data: [{{ $totalStok ?? 0 }}, {{ max(0, ($kapasitasMaks ?? 0) - ($totalStok ?? 0)) }}],
                                        backgroundColor: [
                                            '{{ $persentaseValue >= 90 ? "#ef4444" : ($persentaseValue >= 70 ? "#f59e0b" : "#10b981") }}',
                                            '#f1f5f9'
                                        ],
                                        borderWidth: 0,
                                        cutout: '78%'
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                                    animation: { animateRotate: true, duration: 1200 }
                                }
                            });
                        }

                        // 2. Kategori Donut
                        const katData = @json($stokPerKategori);
                        const chartColors = ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444', '#ec4899', '#06b6d4', '#84cc16'];
                        const katEl = document.getElementById('kategoriDonutChart');
                        if (katEl) {
                            new Chart(katEl, {
                                type: 'doughnut',
                                data: {
                                    labels: katData.map(i => i.kategori),
                                    datasets: [{
                                        data: katData.map(i => i.total_stok),
                                        backgroundColor: katData.map((_, i) => chartColors[i % chartColors.length]),
                                        borderWidth: 2,
                                        borderColor: '#ffffff',
                                        cutout: '65%',
                                        hoverOffset: 6
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: { cornerRadius: 8, padding: 10, bodyFont: { family: 'Poppins', size: 11 } }
                                    },
                                    animation: { animateRotate: true, duration: 1200 }
                                }
                            });
                        }

                        // 3. Trend Chart
                        const trendEl = document.getElementById('trendChart');
                        if (trendEl) {
                            new Chart(trendEl, {
                                type: 'line',
                                data: {
                                    labels: @json($trendLabels),
                                    datasets: [
                                        {
                                            label: 'Masuk',
                                            data: @json($trendMasukData),
                                            borderColor: '#10b981',
                                            backgroundColor: 'rgba(16, 185, 129, 0.08)',
                                            borderWidth: 2.5,
                                            pointRadius: 4,
                                            pointBackgroundColor: '#10b981',
                                            pointBorderColor: '#fff',
                                            pointBorderWidth: 2,
                                            fill: true,
                                            tension: 0.4
                                        },
                                        {
                                            label: 'Keluar',
                                            data: @json($trendKeluarData),
                                            borderColor: '#f87171',
                                            backgroundColor: 'rgba(248, 113, 113, 0.08)',
                                            borderWidth: 2.5,
                                            pointRadius: 4,
                                            pointBackgroundColor: '#f87171',
                                            pointBorderColor: '#fff',
                                            pointBorderWidth: 2,
                                            fill: true,
                                            tension: 0.4
                                        }
                                    ]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: { cornerRadius: 8, padding: 10, bodyFont: { family: 'Poppins', size: 11 }, mode: 'index', intersect: false }
                                    },
                                    scales: {
                                        x: { grid: { display: false }, ticks: { font: { family: 'Poppins', size: 10 }, color: '#94a3b8' } },
                                        y: { grid: { color: '#f1f5f9', borderDash: [2, 4] }, ticks: { font: { family: 'Poppins', size: 10 }, color: '#94a3b8' }, border: { display: false } }
                                    },
                                    interaction: { mode: 'nearest', axis: 'x', intersect: false }
                                }
                            });
                        }

                        // 4. Top Stok Bar Chart
                        const topData = @json($topStokItems);
                        const topEl = document.getElementById('topStokChart');
                        if (topEl) {
                            new Chart(topEl, {
                                type: 'bar',
                                data: {
                                    labels: topData.map(i => i.nama_barang.length > 20 ? i.nama_barang.substr(0, 20) + '…' : i.nama_barang),
                                    datasets: [{
                                        label: 'Stok (Karton)',
                                        data: topData.map(i => i.total_stok),
                                        backgroundColor: topData.map((_, i) => {
                                            const colors = ['#3b82f6', '#10b981', '#8b5cf6', '#f59e0b', '#ef4444', '#ec4899', '#06b6d4', '#84cc16'];
                                            return colors[i % colors.length] + 'cc';
                                        }),
                                        borderRadius: 6,
                                        barPercentage: 0.7
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    indexAxis: 'y',
                                    plugins: {
                                        legend: { display: false },
                                        tooltip: { cornerRadius: 8, padding: 10, bodyFont: { family: 'Poppins', size: 11 } }
                                    },
                                    scales: {
                                        x: {
                                            grid: { color: '#f1f5f9', borderDash: [2, 4] },
                                            ticks: { font: { family: 'Poppins', size: 10 }, color: '#94a3b8' },
                                            border: { display: false }
                                        },
                                        y: {
                                            grid: { display: false },
                                            ticks: { font: { family: 'Poppins', size: 10, weight: '600' }, color: '#374151' }
                                        }
                                    }
                                }
                            });
                        }
                    });
                }
            }
        }
    </script>

    {{-- CUSTOM SCROLLBAR CSS --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #e5e7eb;
            border-radius: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #d1d5db;
        }

        /* Row highlight animation for critical items */
        @keyframes pulse-bg {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        .stok-row[data-status="habis"] {
            animation: pulse-bg 3s ease-in-out infinite;
        }
    </style>
@endsection