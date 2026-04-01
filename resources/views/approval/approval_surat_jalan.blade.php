@extends('layouts.app')
@section('page-title', 'Approval Surat Jalan')
@section('content')

    {{-- Page Header --}}
    <x-page-header title="Approval Surat Jalan" description="Proses persetujuan surat jalan pengiriman barang" icon="fa-file-signature">
    </x-page-header>

    {{-- Summary Stat Cards --}}
    @php
        $totalSJ = $suratJalans->count();
        $pending = $suratJalans->where('status', 'Pending')->count();
        $disetujui = $suratJalans->where('status', 'Disetujui')->count();
        $ditolak = $suratJalans->where('status', 'Ditolak')->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center">
                    <i class="fa-solid fa-file-lines text-gray-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide">Total</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalSJ }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-amber-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-amber-500 tracking-wide">Pending</p>
                    <p class="text-xl font-bold text-gray-800">{{ $pending }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-emerald-500 tracking-wide">Disetujui</p>
                    <p class="text-xl font-bold text-gray-800">{{ $disetujui }}</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-100 flex items-center justify-center">
                    <i class="fa-solid fa-circle-xmark text-red-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-red-500 tracking-wide">Ditolak</p>
                    <p class="text-xl font-bold text-gray-800">{{ $ditolak }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 mb-6">
        <form method="GET" action="{{ route('approval.approval_surat_jalan') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-grow min-w-[200px]">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Cari</label>
                <div class="relative">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="SJ ID, pelanggan, atau pembuat..."
                        class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                </div>
            </div>
            <div class="w-44">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Status</label>
                <select name="status"
                    class="w-full border border-gray-300 rounded-xl py-2 px-3 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    <option value="">Semua Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-800 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition">
                <i class="fa-solid fa-filter text-xs"></i> Filter
            </button>
            <a href="{{ route('approval.approval_surat_jalan') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
                <i class="fa-solid fa-rotate-left text-xs"></i> Reset
            </a>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if ($suratJalans->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-red-800 text-white text-xs uppercase tracking-wider">
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">No. SJ</th>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Pelanggan</th>
                            <th class="px-4 py-3 text-left">Penerima</th>
                            <th class="px-4 py-3 text-center">Jumlah Item</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($suratJalans as $index => $sj)
                            @php
                                $sjSubtotal = $sj->details->sum(fn($d) => $d->quantity * ($d->harga_satuan ?? 0));
                    $sjDiskon = $sjSubtotal * (floatval($sj->diskon_pelanggan ?? 0) / 100);
                    $sjBase = $sjSubtotal + floatval($sj->biaya_pengiriman ?? 0) - $sjDiskon;
                    $sjTotal = $sjBase + ($sjBase * floatval($appSettings['ppn_persen'] ?? 11) / 100);
                                $statusCfg = match ($sj->status) {
                                    'Pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'fa-clock'],
                                    'Disetujui' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'fa-circle-check'],
                                    'Ditolak' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-circle-xmark'],
                                    default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-question'],
                                };
                            @endphp
                            <tr class="hover:bg-red-50/30 transition">
                                <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-700 rounded-lg text-xs font-bold">
                                        <i class="fa-solid fa-file-invoice text-[10px]"></i>
                                        {{ $sj->sj_id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    <i class="fa-regular fa-calendar text-gray-400 text-xs mr-1"></i>
                                    {{ \Carbon\Carbon::parse($sj->tanggal_surat)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-user text-red-500 text-[10px]"></i>
                                        </div>
                                        <span
                                            class="font-medium text-gray-800 text-sm">{{ $sj->pelanggan->nama_pelanggan ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-sm">{{ $sj->nama_penerima ?? '-' }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-gray-700">{{ $sj->details->count() }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp
                                    {{ number_format($sjTotal, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 {{ $statusCfg['bg'] }} {{ $statusCfg['text'] }} rounded-full text-[11px] font-bold">
                                        <i class="fa-solid {{ $statusCfg['icon'] }} text-[9px]"></i>
                                        {{ $sj->status }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('approval.show_surat_jalan', $sj->sj_id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-800 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition">
                                        <i class="fa-solid fa-eye text-[10px]"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fa-solid fa-inbox text-gray-300 text-4xl mb-3"></i>
                <p class="text-gray-500 font-medium">Tidak ada surat jalan ditemukan.</p>
            </div>
        @endif
    </div>

@endsection