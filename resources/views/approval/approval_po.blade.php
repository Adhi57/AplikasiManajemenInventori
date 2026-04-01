@extends('layouts.app')
@section('page-title', 'Approval PO')
@section('content')

    {{-- Page Header --}}
    <x-page-header title="Approval Purchase Order" description="Proses permintaan pembelian dari tim procurement" icon="fa-clipboard-check">
    </x-page-header>


    {{-- Summary Stat Cards --}}
    @php
        $totalPO = $PO_List->count();
        $pending = $PO_List->where('status_po', 'Pending')->count();
        $disetujui = $PO_List->where('status_po', 'Disetujui')->count();
        $ditolak = $PO_List->where('status_po', 'Ditolak')->count();
        $diterima = $PO_List->where('status_po', 'Diterima')->count();
    @endphp
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gray-100 flex items-center justify-center">
                    <i class="fa-solid fa-file-lines text-gray-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide">Total</p>
                    <p class="text-xl font-bold text-gray-800">{{ $totalPO }}</p>
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
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fa-solid fa-box-open text-blue-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-blue-500 tracking-wide">Diterima</p>
                    <p class="text-xl font-bold text-gray-800">{{ $diterima }}</p>
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
        <form method="GET" action="{{ route('approval.approval_po') }}" class="flex flex-wrap items-end gap-3">
            <div class="flex-grow min-w-[200px]">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1">Cari</label>
                <div class="relative">
                    <i
                        class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="PO ID, pembuat, atau supplier..."
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
                    <option value="Diterima" {{ request('status') == 'Diterima' ? 'selected' : '' }}>Diterima (Gudang)
                    </option>
                </select>
            </div>
            <button type="submit"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-800 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition">
                <i class="fa-solid fa-filter text-xs"></i> Filter
            </button>
            <a href="{{ route('approval.approval_po') }}"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
                <i class="fa-solid fa-rotate-left text-xs"></i> Reset
            </a>
        </form>
    </div>

    {{-- Data Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @if ($PO_List->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-red-800 text-white text-xs uppercase tracking-wider">
                            <th class="px-4 py-3 text-left">#</th>
                            <th class="px-4 py-3 text-left">No. PO</th>
                            <th class="px-4 py-3 text-left">Tanggal</th>
                            <th class="px-4 py-3 text-left">Supplier</th>
                            <th class="px-4 py-3 text-left">Pembuat</th>
                            <th class="px-4 py-3 text-center">Jumlah Item</th>
                            <th class="px-4 py-3 text-right">Total</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($PO_List as $index => $PO)
                            @php
                                $poSubtotal = $PO->details->sum(fn($d) => $d->quantity * $d->harga_satuan);
                                $poTotal = $poSubtotal + ($poSubtotal * floatval($appSettings['ppn_persen'] ?? 11) / 100);
                                $statusCfg = match ($PO->status_po) {
                                    'Pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'fa-clock'],
                                    'Disetujui' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'fa-circle-check'],
                                    'Ditolak' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-circle-xmark'],
                                    'Diterima' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-box-open'],
                                    default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-700', 'icon' => 'fa-question'],
                                };
                            @endphp
                            <tr class="hover:bg-red-50/30 transition">
                                <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-700 rounded-lg text-xs font-bold">
                                        <i class="fa-solid fa-file-lines text-[10px]"></i>
                                        {{ $PO->po_id }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-600">
                                    <i class="fa-regular fa-calendar text-gray-400 text-xs mr-1"></i>
                                    {{ \Carbon\Carbon::parse($PO->tanggal_po)->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-building text-red-500 text-[10px]"></i>
                                        </div>
                                        <span
                                            class="font-medium text-gray-800 text-sm">{{ $PO->supplier->namaSupplier ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-sm">{{ $PO->user->nama_lengkap ?? '-' }}</td>
                                <td class="px-4 py-3 text-center font-semibold text-gray-700">{{ $PO->details->count() }}</td>
                                <td class="px-4 py-3 text-right font-semibold text-gray-800">Rp
                                    {{ number_format($poTotal, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 {{ $statusCfg['bg'] }} {{ $statusCfg['text'] }} rounded-full text-[11px] font-bold">
                                        <i class="fa-solid {{ $statusCfg['icon'] }} text-[9px]"></i>
                                        {{ $PO->status_po }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('approval.show_po', $PO->po_id) }}"
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
                <p class="text-gray-500 font-medium">Tidak ada data Purchase Order yang sesuai filter.</p>
            </div>
        @endif
    </div>

@endsection