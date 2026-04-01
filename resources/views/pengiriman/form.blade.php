@extends('layouts.app')
@section('page-title', isset($pengiriman) ? 'Edit Data Pengiriman' : 'Tambah Data Pengiriman')
@section('content')

@php
    // Count available approved SJs (not yet assigned to a Pengiriman)
    $availableSJCount = 0;
    if (!isset($pengiriman)) {
        foreach ($surat_jalans as $sj) {
            if ($sj->status == 'Disetujui' && !in_array($sj->sj_id, $existing_sj_ids ?? [])) {
                $availableSJCount++;
            }
        }
    }

    // Check linked SJ status on edit
    $editSJApproved = true;
    $editSJStatus = null;
    if (isset($pengiriman)) {
        $linkedSJ = $surat_jalans->firstWhere('sj_id', $pengiriman->sj_id);
        $editSJStatus = $linkedSJ->status ?? 'Tidak Ditemukan';
        $editSJApproved = $editSJStatus === 'Disetujui';
    }
@endphp

{{-- Page Header --}}
<x-page-header title="{{ isset($pengiriman) ? 'Edit Data Pengiriman' : 'Tambah Pengiriman Baru' }}" description="{{ isset($pengiriman) ? 'Perbarui informasi pengiriman untuk SJ: ' . $pengiriman->sj_id : 'Buat data pengiriman baru berdasarkan Surat Jalan yang disetujui' }}" icon="{{ isset($pengiriman) ? 'fa-pen-to-square' : 'fa-truck-fast' }}">
    <x-slot name="actions">
        <a href="{{ route('pengiriman.index') }}"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
            <i class="fa-solid fa-arrow-left text-amber-400"></i>
            <span>Batal</span>
        </a>
    </x-slot>
</x-page-header>

{{-- Guard: No Approved SJ (Create) OR Linked SJ Not Approved (Edit) --}}
@if((!isset($pengiriman) && $availableSJCount === 0) || (isset($pengiriman) && !$editSJApproved))
<div class="bg-white rounded-2xl border border-amber-200 shadow-sm overflow-hidden">
    <div class="px-5 py-3 bg-amber-50 border-b border-amber-200">
        <h3 class="font-bold text-amber-800 text-sm flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            {{ isset($pengiriman) ? 'Tidak Dapat Mengedit Pengiriman' : 'Tidak Dapat Membuat Pengiriman' }}
        </h3>
    </div>
    <div class="p-8 text-center">
        <div class="w-16 h-16 bg-amber-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-file-circle-xmark text-amber-500 text-3xl"></i>
        </div>
        @if(isset($pengiriman))
            <h2 class="text-lg font-bold text-gray-900 mb-2">Surat Jalan Belum Disetujui</h2>
            <p class="text-sm text-gray-500 max-w-md mx-auto mb-3">
                Surat Jalan <strong class="text-gray-800">{{ $pengiriman->sj_id }}</strong> saat ini berstatus
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold
                    {{ $editSJStatus === 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700' }}">
                    {{ $editSJStatus }}
                </span>
            </p>
            <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">
                Data pengiriman hanya dapat diedit jika Surat Jalan berstatus <strong class="text-amber-700">"Disetujui"</strong>.
                Silakan ajukan persetujuan terlebih dahulu.
            </p>
        @else
            <h2 class="text-lg font-bold text-gray-900 mb-2">Belum Ada Surat Jalan yang Disetujui</h2>
            <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">
                Untuk membuat data pengiriman, Surat Jalan harus memiliki status <strong class="text-amber-700">"Disetujui"</strong> terlebih dahulu.
                Silakan ajukan persetujuan Surat Jalan melalui menu Approval.
            </p>
        @endif
        <div class="flex items-center justify-center gap-3">
            <a href="{{ route('pengiriman.index') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold text-sm rounded-xl hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <a href="{{ route('approval.approval_surat_jalan') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-800 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition shadow-sm">
                <i class="fa-solid fa-check-circle"></i> Approval Surat Jalan
            </a>
        </div>
    </div>
</div>
@else

<form
    action="{{ isset($pengiriman) ? route('pengiriman.update', $pengiriman->pengiriman_id) : route('pengiriman.store') }}"
    method="POST"
    class="space-y-6"
>
    @csrf
    @if(isset($pengiriman))
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ===== LEFT COLUMN: Main Form ===== --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Section 1: Surat Jalan & Jadwal --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-file-lines text-red-500"></i>
                        Detail Rute & Jadwal
                    </h3>
                </div>
                <div class="p-5 space-y-5">
                    {{-- Nomor Surat Jalan --}}
                    <div>
                        <label for="sj_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                            Nomor Surat Jalan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-file-lines absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <select
                                name="sj_id"
                                id="sj_id"
                                class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition bg-white appearance-none
                                {{ isset($pengiriman) ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                                {{ isset($pengiriman) ? 'disabled' : '' }}
                            >
                                <option value="">-- Pilih Surat Jalan --</option>
                                @foreach($surat_jalans as $sj)
                                    @php
                                        $is_selected_in_edit = isset($pengiriman) && $pengiriman->sj_id == $sj->sj_id;
                                        $is_not_sent = !in_array($sj->sj_id, $existing_sj_ids ?? []);
                                    @endphp
                                    @if($sj->status == 'Disetujui' && ($is_not_sent || $is_selected_in_edit))
                                    <option value="{{ $sj->sj_id }}"
                                        {{ (old('sj_id') ?? $pengiriman->sj_id ?? '') == $sj->sj_id ? 'selected' : '' }}>
                                        {{ $sj->sj_id }} — {{ $sj->pelanggan->nama_pelanggan ?? 'Pelanggan Tidak Ditemukan' }}
                                    </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        @error('sj_id')
                        <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                            <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Tanggal Pengiriman & Sampai --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="tanggal_pengiriman" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Tanggal Mulai Pengiriman
                            </label>
                            <div class="relative">
                                <i class="fa-regular fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="date" name="tanggal_pengiriman" id="tanggal_pengiriman"
                                    value="{{ old('tanggal_pengiriman', isset($pengiriman) ? $pengiriman->tanggal_pengiriman : '') }}"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                            </div>
                            @error('tanggal_pengiriman')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                        <div>
                            <label for="tanggal_sampai" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Estimasi Tanggal Sampai
                            </label>
                            <div class="relative">
                                <i class="fa-regular fa-calendar-check absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="date" name="tanggal_sampai" id="tanggal_sampai"
                                    value="{{ old('tanggal_sampai', isset($pengiriman) ? $pengiriman->tanggal_sampai : '') }}"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                            </div>
                            @error('tanggal_sampai')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Informasi Kendaraan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-car text-red-500"></i>
                        Informasi Kendaraan & Driver
                    </h3>
                </div>
                <div class="p-5 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        {{-- Nama Kendaraan --}}
                        <div>
                            <label for="nama_kendaraan" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Nama Kendaraan
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-truck absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="nama_kendaraan" id="nama_kendaraan"
                                    value="{{ old('nama_kendaraan', $pengiriman->nama_kendaraan ?? '') }}"
                                    placeholder="Contoh: Colt Diesel"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                            </div>
                            @error('nama_kendaraan')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- Nama Driver --}}
                        <div>
                            <label for="nama_driver" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Nama Driver
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="nama_driver" id="nama_driver"
                                    value="{{ old('nama_driver', $pengiriman->nama_driver ?? '') }}"
                                    placeholder="Contoh: Budi Santoso"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                            </div>
                            @error('nama_driver')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>

                        {{-- No Polisi --}}
                        <div>
                            <label for="no_polisi" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Nomor Polisi
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-hashtag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="no_polisi" id="no_polisi"
                                    value="{{ old('no_polisi', $pengiriman->no_polisi ?? '') }}"
                                    placeholder="Contoh: B 1234 XY"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition uppercase">
                            </div>
                            @error('no_polisi')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 3: Status & Catatan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-clipboard-list text-red-500"></i>
                        Status & Catatan
                    </h3>
                </div>
                <div class="p-5 space-y-5">
                    {{-- Status (only on edit) --}}
                    @if(isset($pengiriman))
                    <div>
                        <label for="status_pengiriman" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                            Status Pengiriman
                        </label>
                        @php
                            $statusOptions = [
                                'Menunggu' => ['icon' => 'fa-clock', 'checked' => 'border-amber-500 bg-amber-50 text-amber-700', 'unchecked' => 'border-gray-200 text-gray-500 hover:border-gray-300'],
                                'Dalam Perjalanan' => ['icon' => 'fa-truck', 'checked' => 'border-blue-500 bg-blue-50 text-blue-700', 'unchecked' => 'border-gray-200 text-gray-500 hover:border-gray-300'],
                                'Dibatalkan' => ['icon' => 'fa-circle-xmark', 'checked' => 'border-red-500 bg-red-50 text-red-700', 'unchecked' => 'border-gray-200 text-gray-500 hover:border-gray-300'],
                            ];
                            $currentStatus = old('status_pengiriman', $pengiriman->status_pengiriman);
                        @endphp
                        <div class="flex flex-wrap gap-2" x-data="{ currentStatus: '{{ $currentStatus }}' }">
                            @foreach($statusOptions as $status => $opt)
                            <label class="cursor-pointer" @click="currentStatus = '{{ $status }}'">
                                <input type="radio" name="status_pengiriman" value="{{ $status }}"
                                    class="hidden" :checked="currentStatus === '{{ $status }}'">
                                <span class="inline-flex items-center gap-1.5 px-4 py-2 border-2 rounded-xl text-sm font-semibold transition-all"
                                    :class="currentStatus === '{{ $status }}' ? '{{ $opt['checked'] }}' : '{{ $opt['unchecked'] }}'">
                                    <i class="fa-solid {{ $opt['icon'] }} text-xs"></i>
                                    {{ $status }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Catatan --}}
                    <div>
                        <label for="catatan" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                            Catatan Pengiriman
                        </label>
                        <div class="relative">
                            <textarea name="catatan" id="catatan" rows="3"
                                placeholder="Tambahkan catatan penting terkait pengiriman ini..."
                                class="w-full border border-gray-300 rounded-xl py-2.5 px-4 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition resize-none">{{ old('catatan', $pengiriman->catatan ?? '') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT COLUMN: Summary & Actions ===== --}}
        <div class="lg:col-span-1">
            <div class="sticky top-6 space-y-6">

                {{-- Summary Card --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-red-800">
                        <h3 class="font-bold text-white text-sm flex items-center gap-2">
                            <i class="fa-solid fa-circle-info"></i>
                            Ringkasan
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-file-lines text-red-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Surat Jalan</p>
                                <p class="text-sm font-bold text-gray-800" id="summary-sj">
                                    {{ isset($pengiriman) ? $pengiriman->sj_id : 'Belum dipilih' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-truck text-blue-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Kendaraan</p>
                                <p class="text-sm font-bold text-gray-800" id="summary-kendaraan">
                                    {{ isset($pengiriman) && $pengiriman->nama_kendaraan ? $pengiriman->nama_kendaraan : 'Belum diisi' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                <i class="fa-solid fa-id-card text-emerald-500 text-xs"></i>
                            </div>
                            <div>
                                <p class="text-[10px] uppercase font-bold text-gray-400">Driver</p>
                                <p class="text-sm font-bold text-gray-800" id="summary-driver">
                                    {{ isset($pengiriman) && $pengiriman->nama_driver ? $pengiriman->nama_driver : 'Belum diisi' }}
                                </p>
                            </div>
                        </div>

                        @if(isset($pengiriman))
                        <div class="pt-3 border-t border-gray-100">
                            <p class="text-[10px] uppercase font-bold text-gray-400 mb-1.5">Status</p>
                            @php
                                $cfg = [
                                    'Menunggu' => 'bg-amber-100 text-amber-700',
                                    'Dalam Perjalanan' => 'bg-blue-100 text-blue-700',
                                    'Terkirim' => 'bg-emerald-100 text-emerald-700',
                                    'Dibatalkan' => 'bg-red-100 text-red-700',
                                ][$pengiriman->status_pengiriman] ?? 'bg-gray-100 text-gray-700';
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 {{ $cfg }} rounded-full text-xs font-bold" id="summary-status">
                                {{ $pengiriman->status_pengiriman }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-red-800 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200 focus:ring-4 focus:ring-red-200">
                        <i class="fa-solid fa-floppy-disk"></i>
                        {{ isset($pengiriman) ? 'Perbarui Data' : 'Simpan Pengiriman' }}
                    </button>
                    <a href="{{ route('pengiriman.index') }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
                        <i class="fa-solid fa-xmark"></i>
                        Batal
                    </a>
                </div>

            </div>
        </div>

    </div>
</form>

@push('scripts')
<script>
    // Live update summary card as user types
    document.getElementById('sj_id')?.addEventListener('change', function() {
        const text = this.options[this.selectedIndex]?.text || 'Belum dipilih';
        document.getElementById('summary-sj').textContent = text === '-- Pilih Surat Jalan --' ? 'Belum dipilih' : text.split('—')[0].trim();
    });
    document.getElementById('nama_kendaraan')?.addEventListener('input', function() {
        document.getElementById('summary-kendaraan').textContent = this.value || 'Belum diisi';
    });
    document.getElementById('nama_driver')?.addEventListener('input', function() {
        document.getElementById('summary-driver').textContent = this.value || 'Belum diisi';
    });
</script>
@endpush

@endif
@endsection