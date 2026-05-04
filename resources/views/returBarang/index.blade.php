@extends('layouts.app')
@section('page-title', 'Retur Barang')
@section('content')

    <div class="min-h-screen bg-gray-50/50 py-6 px-2 md:px-6" x-data="returActions()">

        {{-- ========================================= --}}
        {{-- HEADER --}}
        {{-- ========================================= --}}
        <x-page-header title="Manajemen Retur Barang" description="Memastikan dan mengonfirmasi retur barang yang telah diverifikasi fisik." icon="fa-rotate-left">
        </x-page-header>

        {{-- ========================================= --}}
        {{-- STAT CARDS --}}
        {{-- ========================================= --}}
        @php
            $stats = [
                [
                    'title' => 'Total Retur',
                    'value' => $totalRetur,
                    'suffix' => 'Item',
                    'icon' => 'fa-boxes-stacked',
                    'gradient' => 'from-slate-600 to-slate-800',
                ],
                [
                    'title' => 'Menunggu Persetujuan',
                    'value' => $totalPending,
                    'suffix' => 'Pending',
                    'icon' => 'fa-clock',
                    'gradient' => 'from-amber-500 to-amber-700',
                    'pulse' => $totalPending > 0,
                ],
                [
                    'title' => 'Disetujui',
                    'value' => $totalDisetujui,
                    'suffix' => 'Approved',
                    'icon' => 'fa-circle-check',
                    'gradient' => 'from-emerald-500 to-emerald-700',
                ],
                [
                    'title' => 'Ditolak',
                    'value' => $totalDitolak,
                    'suffix' => 'Rejected',
                    'icon' => 'fa-circle-xmark',
                    'gradient' => 'from-red-500 to-red-700',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            @foreach ($stats as $stat)
                <div
                    class="relative overflow-hidden bg-gradient-to-br {{ $stat['gradient'] }} text-white rounded-2xl p-5 shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 group">
                    <div
                        class="absolute -top-4 -right-4 w-24 h-24 bg-white/10 rounded-full group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-sm font-medium text-white/80">{{ $stat['title'] }}</p>
                            <div class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/20 {{ isset($stat['pulse']) && $stat['pulse'] ? 'animate-pulse' : '' }}">
                                <i class="fa-solid {{ $stat['icon'] }} text-lg"></i>
                            </div>
                        </div>
                        <p class="text-3xl font-extrabold tracking-tight">{{ $stat['value'] }}</p>
                        <p class="text-xs text-white/60 mt-1">{{ $stat['suffix'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ========================================= --}}
        {{-- PROGRESS BAR - Rasio Persetujuan --}}
        {{-- ========================================= --}}
        @php
            $approvalRate = $totalRetur > 0 ? round(($totalDisetujui / $totalRetur) * 100, 1) : 0;
            $rejectRate = $totalRetur > 0 ? round(($totalDitolak / $totalRetur) * 100, 1) : 0;
            $pendingRate = $totalRetur > 0 ? round(($totalPending / $totalRetur) * 100, 1) : 0;
        @endphp
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
            <div class="flex items-center gap-2 mb-3">
                <div class="w-2 h-5 bg-red-600 rounded-full"></div>
                <h2 class="font-semibold text-gray-800 text-sm">Rasio Status Retur</h2>
                <span class="ml-auto text-xs text-gray-400">Total Qty Retur: <strong
                        class="text-red-600">{{ number_format($totalQtyRetur, 2, ',', '.') }}</strong> Karton</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-4 overflow-hidden flex">
                @if ($approvalRate > 0)
                    <div class="bg-emerald-500 h-4 transition-all duration-700 ease-out flex items-center justify-center"
                        style="width: {{ $approvalRate }}%" title="Disetujui: {{ $approvalRate }}%">
                        @if ($approvalRate > 10)
                            <span class="text-[10px] font-bold text-white">{{ $approvalRate }}%</span>
                        @endif
                    </div>
                @endif
                @if ($pendingRate > 0)
                    <div class="bg-amber-400 h-4 transition-all duration-700 ease-out flex items-center justify-center"
                        style="width: {{ $pendingRate }}%" title="Pending: {{ $pendingRate }}%">
                        @if ($pendingRate > 10)
                            <span class="text-[10px] font-bold text-white">{{ $pendingRate }}%</span>
                        @endif
                    </div>
                @endif
                @if ($rejectRate > 0)
                    <div class="bg-red-500 h-4 transition-all duration-700 ease-out flex items-center justify-center"
                        style="width: {{ $rejectRate }}%" title="Ditolak: {{ $rejectRate }}%">
                        @if ($rejectRate > 10)
                            <span class="text-[10px] font-bold text-white">{{ $rejectRate }}%</span>
                        @endif
                    </div>
                @endif
            </div>
            <div class="flex items-center gap-5 mt-2.5">
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 bg-emerald-500 rounded-full"></span>
                    <span class="text-xs text-gray-500">Disetujui ({{ $approvalRate }}%)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 bg-amber-400 rounded-full"></span>
                    <span class="text-xs text-gray-500">Pending ({{ $pendingRate }}%)</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2.5 h-2.5 bg-red-500 rounded-full"></span>
                    <span class="text-xs text-gray-500">Ditolak ({{ $rejectRate }}%)</span>
                </div>
            </div>
        </div>

        {{-- ========================================= --}}
        {{-- MAIN TABLE CARD --}}
        {{-- ========================================= --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">

            {{-- Header & Filter Controls --}}
            <div class="flex flex-col md:flex-row justify-between md:items-center mb-5 pb-4 border-b border-gray-100">
                <div class="flex items-center gap-2 mb-4 md:mb-0">
                    <div class="w-2 h-5 bg-red-600 rounded-full"></div>
                    <h2 class="font-semibold text-gray-800">Daftar Barang Retur</h2>
                    <span
                        class="px-2.5 py-0.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-full">{{ $returs->total() }}
                        data</span>
                </div>

                <form method="GET" class="flex flex-wrap items-center gap-2.5">
                    {{-- Input Search --}}
                    <div class="relative">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input type="text" name="search" value="{{ $search }}"
                            placeholder="Cari kode barang / PO ID"
                            class="border border-gray-200 rounded-xl pl-9 pr-3 py-2.5 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:outline-none shadow-sm min-w-[200px] bg-gray-50/50 hover:bg-white transition-colors">
                    </div>

                    {{-- Select Status --}}
                    <div class="relative">
                        <i
                            class="fa-solid fa-filter absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <select name="status"
                            class="border border-gray-200 rounded-xl pl-9 pr-8 py-2.5 text-sm text-gray-700 shadow-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 focus:outline-none bg-gray-50/50 hover:bg-white transition-colors appearance-none cursor-pointer">
                            <option value="">Semua Status</option>
                            <option value="Pending" {{ $status == 'Pending' ? 'selected' : '' }}>⏳ Pending</option>
                            <option value="Disetujui" {{ $status == 'Disetujui' ? 'selected' : '' }}>✅ Disetujui
                            </option>
                            <option value="Ditolak" {{ $status == 'Ditolak' ? 'selected' : '' }}>❌ Ditolak</option>
                        </select>
                    </div>

                    {{-- Tombol Filter --}}
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-700 text-white rounded-xl font-semibold text-sm hover:bg-red-800 transition-all duration-200 shadow-sm hover:shadow-md active:scale-95">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i> Filter
                    </button>

                    @if ($search || $status)
                        <a href="{{ route('retur.index') }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl font-medium text-sm hover:bg-gray-200 transition-colors">
                            <i class="fa-solid fa-xmark text-xs"></i> Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Tabel --}}
            <div class="overflow-x-auto rounded-xl border border-gray-100">
                <table class="min-w-full text-sm" id="retur-table">
                    {{-- Header Tabel --}}
                    <thead>
                        <tr class="bg-gray-50">
                            <th
                                class="py-3.5 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                #</th>
                            <th
                                class="py-3.5 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Referensi PO</th>
                            <th
                                class="py-3.5 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Barang</th>
                            <th
                                class="py-3.5 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Qty Retur</th>
                            <th
                                class="py-3.5 px-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Alasan</th>
                            <th
                                class="py-3.5 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="py-3.5 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Tanggal Retur</th>
                            <th
                                class="py-3.5 px-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse ($returs as $index => $retur)
                            <tr class="hover:bg-red-50/40 transition-colors duration-150 group"
                                id="retur-row-{{ $retur->retur_id }}">
                                {{-- Nomor --}}
                                <td class="py-3.5 px-4">
                                    <span
                                        class="w-7 h-7 inline-flex items-center justify-center rounded-lg bg-gray-100 text-gray-500 text-xs font-bold">
                                        {{ $returs->firstItem() + $index }}
                                    </span>
                                </td>

                                {{-- PO ID --}}
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i class="fa-solid fa-file-invoice text-blue-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800 text-sm">{{ $retur->po_id }}</p>
                                            <p class="text-[11px] text-gray-400">Retur #{{ $retur->retur_id }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Barang --}}
                                <td class="py-3.5 px-4">
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm">
                                            {{ $retur->barang->nama_barang ?? '-' }}</p>
                                        <p class="text-[11px] text-gray-400">{{ $retur->kode_barang }}</p>
                                    </div>
                                </td>

                                {{-- Qty Retur --}}
                                <td class="py-3.5 px-4 text-center">
                                    <span
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-red-50 text-red-700 font-bold text-sm rounded-lg border border-red-100">
                                        <i class="fa-solid fa-arrow-rotate-left text-[10px]"></i>
                                        {{ number_format($retur->qty_retur, 2, ',', '.') }}
                                    </span>
                                    <p class="text-[10px] text-gray-400 mt-0.5">Karton</p>
                                </td>

                                {{-- Alasan --}}
                                <td class="px-4 py-3.5">
                                    <div x-data="{ editing: false, alasan: '{{ addslashes($retur->alasan) }}', saving: false, saved: false }"
                                        @click.away="editing = false" class="relative max-w-[200px]">
                                        {{-- Teks normal --}}
                                        <div x-show="!editing"
                                            class="cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-all duration-150 border border-transparent hover:border-gray-200 group/alasan"
                                            @click="editing = true">
                                            <div class="flex items-center gap-1.5">
                                                <span x-show="alasan" x-text="alasan"
                                                    class="text-sm text-gray-700"></span>
                                                <span x-show="!alasan"
                                                    class="text-sm text-gray-400 italic">Belum diisi</span>
                                                <i
                                                    class="fa-solid fa-pen text-[9px] text-gray-300 group-hover/alasan:text-red-400 transition-colors"></i>
                                            </div>
                                        </div>

                                        {{-- Input edit --}}
                                        <div x-show="editing" x-transition class="flex gap-2 items-center">
                                            <input type="text" x-model="alasan"
                                                class="border border-gray-200 rounded-lg px-3 py-1.5 w-full text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 outline-none shadow-sm"
                                                @keydown.enter.prevent="
                                                    saving = true;
                                                    fetch('{{ route('retur.updateAlasan', $retur->retur_id) }}', {
                                                        method: 'PATCH',
                                                        headers: { 
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({ alasan })
                                                    })
                                                    .then(res => res.json())
                                                    .then(data => { 
                                                        saving = false;
                                                        if(data.success){ 
                                                            editing = false;
                                                            saved = true;
                                                            setTimeout(() => saved = false, 2000);
                                                        }
                                                    })
                                                ">
                                            <button @click="editing = false"
                                                class="text-gray-400 hover:text-red-600 transition-colors p-1">
                                                <i class="fa-solid fa-xmark text-sm"></i>
                                            </button>
                                        </div>

                                        {{-- Saved indicator --}}
                                        <div x-show="saved" x-transition
                                            class="absolute -top-2 -right-2 px-1.5 py-0.5 bg-emerald-500 text-white text-[9px] font-bold rounded-full">
                                            <i class="fa-solid fa-check"></i>
                                        </div>
                                    </div>
                                </td>

                                {{-- Badge Status --}}
                                <td class="py-3.5 px-4 text-center">
                                    @if ($retur->status_retur === 'Pending')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-xl text-xs font-semibold border border-amber-200 shadow-sm">
                                            <span class="w-1.5 h-1.5 bg-amber-500 rounded-full animate-pulse"></span>
                                            Pending
                                        </span>
                                    @elseif ($retur->status_retur === 'Disetujui')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-xl text-xs font-semibold border border-emerald-200 shadow-sm">
                                            <i class="fa-solid fa-circle-check text-[10px]"></i>
                                            Disetujui
                                        </span>
                                    @elseif ($retur->status_retur === 'Ditolak')
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-700 rounded-xl text-xs font-semibold border border-red-200 shadow-sm">
                                            <i class="fa-solid fa-circle-xmark text-[10px]"></i>
                                            Ditolak
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-50 text-gray-600 rounded-xl text-xs font-semibold border border-gray-200 shadow-sm">
                                            {{ $retur->status_retur }}
                                        </span>
                                    @endif
                                </td>

                                {{-- Tanggal Retur --}}
                                <td class="py-3.5 px-4 text-center">
                                    <div x-data="{ editing: false, tg: '{{ $retur->tanggal_retur?->format('Y-m-d') }}' }"
                                        @click.away="editing = false" class="relative">

                                        {{-- tampilan normal --}}
                                        <div x-show="!editing"
                                            class="cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-all duration-150 border border-transparent hover:border-gray-200 inline-flex items-center gap-1.5 group/tgl"
                                            @click="editing = true">
                                            <i
                                                class="fa-regular fa-calendar text-gray-400 text-xs group-hover/tgl:text-red-400 transition-colors"></i>
                                            <span
                                                class="text-sm text-gray-700">{{ $retur->tanggal_retur?->format('d M Y') }}</span>
                                        </div>

                                        {{-- input date --}}
                                        <div x-show="editing" x-transition class="flex gap-2 items-center">
                                            <input type="date" x-model="tg"
                                                class="border border-gray-200 rounded-lg px-3 py-1.5 w-full text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 outline-none shadow-sm"
                                                @keydown.enter.prevent="
                                                    fetch('{{ route('retur.updateTanggal', $retur->retur_id) }}', {
                                                        method: 'PATCH',
                                                        headers: {
                                                            'Content-Type': 'application/json',
                                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                                        },
                                                        body: JSON.stringify({ tanggal_retur: tg })
                                                    })
                                                    .then(res => res.json())
                                                    .then(data => {
                                                        if(data.success) {
                                                            editing = false;
                                                            window.location.reload();
                                                        }
                                                    })
                                                ">
                                            <button @click="editing = false"
                                                class="text-gray-400 hover:text-red-600 transition-colors p-1">
                                                <i class="fa-solid fa-xmark text-sm"></i>
                                            </button>
                                        </div>

                                    </div>
                                </td>

                                {{-- Tombol Aksi --}}
                                <td class="py-3.5 px-4 text-center">
                                    @if ($retur->status_retur === 'Pending')
                                        @if(auth()->user()->role !== 'Staff')
                                        <div class="flex gap-2 justify-center">
                                            {{-- SETUJUI --}}
                                            <button @click="konfirmasiSesuai('{{ $retur->retur_id }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 text-white rounded-xl text-xs font-semibold hover:bg-emerald-700 transition-all duration-200 shadow-sm hover:shadow-md active:scale-95"
                                                title="Setujui Retur">
                                                <i class="fa-solid fa-check text-[10px]"></i> Setujui
                                            </button>

                                            {{-- TOLAK --}}
                                            <button @click="batalkanRetur('{{ $retur->retur_id }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-600 text-white rounded-xl text-xs font-semibold hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md active:scale-95"
                                                title="Tolak Retur">
                                                <i class="fa-solid fa-xmark text-[10px]"></i> Tolak
                                            </button>
                                        </div>
                                        @else
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-600 rounded-xl text-xs font-medium border border-amber-200">
                                                <i class="fa-solid fa-clock text-[10px]"></i> Menunggu
                                            </span>
                                        </div>
                                        @endif
                                    @else
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('retur.show', $retur->retur_id) }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-gray-100 text-gray-600 rounded-xl text-xs font-medium hover:bg-gray-200 transition-all duration-200"
                                                title="Lihat Detail">
                                                <i class="fa-solid fa-eye text-[10px]"></i> Detail
                                            </a>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-16 bg-gray-50/50">
                                    <div class="flex flex-col items-center">
                                        <div
                                            class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="fa-solid fa-box-open text-red-400 text-2xl"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium text-base mb-1">Tidak ada data retur
                                            ditemukan</p>
                                        <p class="text-gray-400 text-sm">Coba ubah filter pencarian atau lihat semua
                                            data.</p>
                                        @if ($search || $status)
                                            <a href="{{ route('retur.index') }}"
                                                class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-red-700 text-white rounded-xl text-xs font-semibold hover:bg-red-800 transition-colors shadow-sm">
                                                <i class="fa-solid fa-rotate-left text-[10px]"></i> Reset Filter
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-5 flex items-center justify-between">
                <p class="text-xs text-gray-400">
                    Menampilkan {{ $returs->firstItem() ?? 0 }} - {{ $returs->lastItem() ?? 0 }} dari
                    {{ $returs->total() }} data
                </p>
                <div>
                    {{ $returs->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function returActions() {
                return {

                    // ======================= SETUJUI =======================
                    konfirmasiSesuai(retur_id) {
                        Swal.fire({
                            title: 'Setujui Retur?',
                            html: '<p class="text-sm text-gray-500">Pastikan barang sudah diperiksa secara fisik sebelum menyetujui retur ini.</p>',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: '<i class="fa-solid fa-check mr-1"></i> Ya, Setujui',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'swal-custom-popup'
                            },
                            reverseButtons: true,
                        }).then(async (result) => {
                            if (result.isConfirmed) {
                                // Show loading
                                Swal.fire({
                                    title: 'Memproses...',
                                    html: '<p class="text-sm text-gray-500">Menyetujui retur barang</p>',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    customClass: {
                                        popup: 'swal-custom-popup'
                                    },
                                    didOpen: () => Swal.showLoading()
                                });

                                const res = await fetch(`/retur-barang/${retur_id}/konfirmasi`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                    }
                                });

                                const data = await res.json();

                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message,
                                        timer: 1500,
                                        showConfirmButton: false,
                                        timerProgressBar: true,
                                        customClass: {
                                            popup: 'swal-custom-popup swal-success-popup'
                                        },
                                    }).then(() => {
                                        window.location.reload();
                                    });

                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: data.message,
                                        customClass: {
                                            popup: 'swal-custom-popup'
                                        }
                                    });
                                }
                            }
                        });
                    },

                    // ======================= TOLAK =======================
                    batalkanRetur(retur_id) {

                        Swal.fire({
                            title: 'Tolak Retur?',
                            html: '<p class="text-sm text-gray-500">Retur akan ditandai sebagai <strong class="text-red-600">Ditolak</strong>. Tindakan ini tidak dapat dibatalkan.</p>',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: '<i class="fa-solid fa-xmark mr-1"></i> Ya, Tolak',
                            cancelButtonText: 'Batal',
                            customClass: {
                                popup: 'swal-custom-popup'
                            },
                            reverseButtons: true,
                        }).then(async (result) => {

                            if (result.isConfirmed) {
                                Swal.fire({
                                    title: 'Memproses...',
                                    html: '<p class="text-sm text-gray-500">Menolak retur barang</p>',
                                    allowOutsideClick: false,
                                    showConfirmButton: false,
                                    customClass: {
                                        popup: 'swal-custom-popup'
                                    },
                                    didOpen: () => Swal.showLoading()
                                });

                                const res = await fetch(`/retur-barang/${retur_id}/batal`, {
                                    method: 'PATCH',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    }
                                });

                                const data = await res.json();

                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: data.message,
                                        timer: 1500,
                                        showConfirmButton: false,
                                        timerProgressBar: true,
                                        customClass: {
                                            popup: 'swal-custom-popup swal-success-popup'
                                        },
                                    }).then(() => window.location.reload());
                                } else {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal',
                                        text: data.message,
                                        customClass: {
                                            popup: 'swal-custom-popup'
                                        }
                                    });
                                }
                            }

                        });
                    }

                }
            }
        </script>
    @endpush

@endsection