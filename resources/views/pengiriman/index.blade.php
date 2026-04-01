@extends('layouts.app')
@section('page-title', 'Pengiriman Barang')
@section('content')

    <x-page-header title="Pengiriman Barang" description="Kelola dan lacak status pengiriman barang ke pelanggan" icon="fa-truck-fast">
    <x-slot name="actions">
        <a href="{{ route('pengiriman.create') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-red-950 shadow-md transition-all text-sm font-bold hover:scale-105 duration-200">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Pengiriman</span>
        </a>
    </x-slot>
</x-page-header>

    {{-- Summary Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
        {{-- Total --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                    <i class="fa-solid fa-boxes-stacked text-gray-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide">Total</p>
                    <p class="text-xl font-bold text-gray-800">{{ $statusCounts->total ?? 0 }}</p>
                </div>
            </div>
        </div>
        {{-- Menunggu --}}
        <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-amber-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-amber-500 tracking-wide">Menunggu</p>
                    <p class="text-xl font-bold text-amber-700">{{ $statusCounts->menunggu ?? 0 }}</p>
                </div>
            </div>
        </div>
        {{-- Dalam Perjalanan --}}
        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fa-solid fa-truck text-blue-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-blue-500 tracking-wide">Perjalanan</p>
                    <p class="text-xl font-bold text-blue-700">{{ $statusCounts->dalam_perjalanan ?? 0 }}</p>
                </div>
            </div>
        </div>
        {{-- Terkirim --}}
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-emerald-500 tracking-wide">Terkirim</p>
                    <p class="text-xl font-bold text-emerald-700">{{ $statusCounts->terkirim ?? 0 }}</p>
                </div>
            </div>
        </div>
        {{-- Dibatalkan --}}
        <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                    <i class="fa-solid fa-circle-xmark text-red-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-red-500 tracking-wide">Dibatalkan</p>
                    <p class="text-xl font-bold text-red-700">{{ $statusCounts->dibatalkan ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <form method="GET" class="flex flex-col md:flex-row items-end gap-3">
            <div class="flex-1 w-full">
                <label for="search" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Cari</label>
                <div class="relative">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="No. SJ, nama driver, nopol, atau pelanggan..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                </div>
            </div>
            <div class="w-full md:w-52">
                <label for="status" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status
                    Pengiriman</label>
                <select name="status" id="status"
                    class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    <option value="">Semua Status</option>
                    <option value="Menunggu" {{ request('status') == 'Menunggu' ? 'selected' : '' }}>Menunggu</option>
                    <option value="Dalam Perjalanan" {{ request('status') == 'Dalam Perjalanan' ? 'selected' : '' }}>Dalam
                        Perjalanan</option>
                    <option value="Terkirim" {{ request('status') == 'Terkirim' ? 'selected' : '' }}>Terkirim</option>
                    <option value="Dibatalkan" {{ request('status') == 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <button type="submit"
                class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-5 py-2 bg-red-800 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition shadow-sm">
                <i class="fa-solid fa-filter text-xs"></i>
                Filter
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('pengiriman.index') }}"
                    class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
                    <i class="fa-solid fa-xmark text-xs"></i>
                    Reset
                </a>
            @endif
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-red-800 text-white text-xs uppercase tracking-wide">
                    <tr>
                        <th class="py-3 px-4 text-center w-10">#</th>
                        <th class="py-3 px-4 text-left">No. Surat Jalan</th>
                        <th class="py-3 px-4 text-left">Tanggal Surat</th>
                        <th class="py-3 px-4 text-left">Pelanggan</th>
                        <th class="py-3 px-4 text-left">Driver & Kendaraan</th>
                        <th class="py-3 px-4 text-center">Tgl Kirim</th>
                        <th class="py-3 px-4 text-center">Tgl Sampai</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pengirimans as $index => $p)
                        <tr class="hover:bg-red-50/50 transition-colors duration-150">
                            {{-- # --}}
                            <td class="py-3 px-4 text-center text-xs font-bold text-gray-400">
                                {{ $pengirimans->firstItem() + $index }}
                            </td>

                            {{-- No SJ --}}
                            <td class="py-3 px-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-800 rounded-lg text-xs font-mono font-bold">
                                    <i class="fa-solid fa-file-lines text-red-400 text-[10px]"></i>
                                    {{ $p->sj_id }}
                                </span>
                            </td>

                            {{-- Tanggal Surat --}}
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                                    <span
                                        class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($p->suratJalan->tanggal_surat)->format('d M Y') }}</span>
                                </div>
                            </td>

                            {{-- Pelanggan --}}
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-user text-red-500 text-[10px]"></i>
                                    </div>
                                    <span
                                        class="font-medium text-gray-800 truncate max-w-[130px]">{{ $p->suratJalan->pelanggan->nama_pelanggan ?? '-' }}</span>
                                </div>
                            </td>

                            {{-- Driver & Kendaraan --}}
                            <td class="py-3 px-4">
                                @if($p->nama_driver || $p->no_polisi)
                                    <div class="space-y-0.5">
                                        <p class="font-medium text-gray-800 text-xs flex items-center gap-1">
                                            <i class="fa-solid fa-id-card text-gray-400 text-[10px]"></i>
                                            {{ $p->nama_driver ?? '-' }}
                                        </p>
                                        <p class="text-[11px] text-gray-500 flex items-center gap-1">
                                            <i class="fa-solid fa-car text-gray-400 text-[10px]"></i>
                                            <span class="font-mono uppercase">{{ $p->no_polisi ?? '-' }}</span>
                                            @if($p->nama_kendaraan)
                                                <span class="text-gray-400">·</span> {{ $p->nama_kendaraan }}
                                            @endif
                                        </p>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400 italic">Belum diisi</span>
                                @endif
                            </td>

                            {{-- Tanggal Kirim --}}
                            <td class="py-3 px-4 text-center">
                                @if($p->tanggal_pengiriman)
                                    <span
                                        class="text-xs font-medium text-gray-700">{{ date('d M Y', strtotime($p->tanggal_pengiriman)) }}</span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Tanggal Sampai --}}
                            <td class="py-3 px-4 text-center">
                                @if($p->tanggal_sampai)
                                    <span
                                        class="text-xs font-medium text-emerald-700">{{ date('d M Y', strtotime($p->tanggal_sampai)) }}</span>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3 px-4 text-center">
                                @php
                                    $statusConfig = [
                                        'Menunggu' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'fa-clock'],
                                        'Dalam Perjalanan' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-truck'],
                                        'Terkirim' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'fa-circle-check'],
                                        'Dibatalkan' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-circle-xmark'],
                                    ];
                                    $cfg = $statusConfig[$p->status_pengiriman] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'fa-question'];
                                @endphp
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 {{ $cfg['bg'] }} {{ $cfg['text'] }} rounded-full text-[11px] font-bold whitespace-nowrap">
                                    <i class="fa-solid {{ $cfg['icon'] }} text-[9px]"></i>
                                    {{ $p->status_pengiriman }}
                                </span>
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('pengiriman.show', $p->pengiriman_id) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-red-100 hover:text-red-700 transition">
                                        <i class="fa-solid fa-eye text-[10px]"></i>
                                        Detail
                                    </a>
                                    @if ($p->status_pengiriman !== 'Terkirim')
                                        <a href="{{ route('pengiriman.edit', $p->pengiriman_id) }}"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-medium hover:bg-amber-100 transition">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                            Edit
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa-solid fa-truck text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium">Belum ada data pengiriman</p>
                                    <p class="text-xs">Buat pengiriman baru untuk memulai</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($pengirimans->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $pengirimans->links() }}
            </div>
        @endif
    </div>

@endsection