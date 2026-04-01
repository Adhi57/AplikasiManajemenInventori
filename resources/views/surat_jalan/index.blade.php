@extends('layouts.app')
@section('page-title', 'Surat Jalan')
@section('content')

    <x-page-header title="Surat Jalan & Pengiriman" description="Kelola dokumen surat jalan untuk pengiriman barang ke pelanggan" icon="fa-file-lines">
    <x-slot name="actions">
        <a href="{{ route('surat_jalan.create') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-red-950 shadow-md transition-all text-sm font-bold hover:scale-105 duration-200">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Surat Jalan</span>
        </a>
    </x-slot>
</x-page-header>

    {{-- Summary Stat Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        {{-- Total --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center">
                    <i class="fa-solid fa-file-lines text-gray-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-gray-400 tracking-wide">Total SJ</p>
                    <p class="text-xl font-bold text-gray-800">{{ $statusCounts->total ?? 0 }}</p>
                </div>
            </div>
        </div>
        {{-- Pending --}}
        <div class="bg-white rounded-2xl border border-amber-100 shadow-sm p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center">
                    <i class="fa-solid fa-clock text-amber-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-amber-500 tracking-wide">Pending</p>
                    <p class="text-xl font-bold text-amber-700">{{ $statusCounts->pending ?? 0 }}</p>
                </div>
            </div>
        </div>
        {{-- Disetujui --}}
        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                    <i class="fa-solid fa-circle-check text-emerald-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-emerald-500 tracking-wide">Disetujui</p>
                    <p class="text-xl font-bold text-emerald-700">{{ $statusCounts->disetujui ?? 0 }}</p>
                </div>
            </div>
        </div>
        {{-- Dikirim --}}
        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-4 hover:shadow-md transition-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                    <i class="fa-solid fa-truck text-blue-600 text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] uppercase font-bold text-blue-500 tracking-wide">Dikirim</p>
                    <p class="text-xl font-bold text-blue-700">{{ $statusCounts->dikirim ?? 0 }}</p>
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
                        placeholder="No. SJ atau nama pelanggan..."
                        class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                </div>
            </div>
            <div class="w-full md:w-48">
                <label for="status" class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status</label>
                <select name="status" id="status"
                    class="w-full border border-gray-300 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    <option value="">Semua Status</option>
                    <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Disetujui" {{ request('status') == 'Disetujui' ? 'selected' : '' }}>Disetujui</option>
                    <option value="Ditolak" {{ request('status') == 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="Dikirim" {{ request('status') == 'Dikirim' ? 'selected' : '' }}>Dikirim</option>
                    <option value="Selesai" {{ request('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            <button type="submit"
                class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-5 py-2 bg-red-800 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition shadow-sm">
                <i class="fa-solid fa-filter text-xs"></i>
                Filter
            </button>
            @if(request('search') || request('status'))
                <a href="{{ route('surat_jalan.index') }}"
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
                        <th class="py-3 px-4 text-left">Penerima & Alamat</th>
                        <th class="py-3 px-3 text-center">Item</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-left">Dibuat Oleh</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($suratJalans as $i => $sj)
                        <tr class="hover:bg-red-50/50 transition-colors duration-150">
                            {{-- # --}}
                            <td class="py-3 px-4 text-center text-xs font-bold text-gray-400">
                                {{ $suratJalans->firstItem() + $i }}
                            </td>

                            {{-- No SJ --}}
                            <td class="py-3 px-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-gray-100 text-gray-800 rounded-lg text-xs font-mono font-bold">
                                    <i class="fa-solid fa-file-lines text-red-400 text-[10px]"></i>
                                    {{ $sj->sj_id }}
                                </span>
                            </td>

                            {{-- Tanggal Surat --}}
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <i class="fa-regular fa-calendar text-gray-400 text-xs"></i>
                                    <span
                                        class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($sj->tanggal_surat)->format('d M Y') }}</span>
                                </div>
                            </td>

                            {{-- Pelanggan --}}
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                                        <i class="fa-solid fa-user text-red-500 text-[10px]"></i>
                                    </div>
                                    <span
                                        class="font-medium text-gray-800 truncate max-w-[140px]">{{ $sj->pelanggan->nama_pelanggan ?? '-' }}</span>
                                </div>
                            </td>

                            {{-- Penerima & Alamat --}}
                            <td class="py-3 px-4">
                                <p class="font-medium text-gray-800 text-xs">{{ $sj->nama_penerima ?? '-' }}</p>
                                <p class="text-[11px] text-gray-500 truncate max-w-[180px]" title="{{ $sj->alamat_penerima }}">
                                    <i class="fa-solid fa-location-dot text-gray-400 mr-1"></i>{{ $sj->alamat_penerima ?? '-' }}
                                </p>
                            </td>

                            {{-- Jumlah Item --}}
                            <td class="py-3 px-3 text-center">
                                <span
                                    class="inline-flex items-center justify-center w-7 h-7 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">
                                    {{ $sj->details_count }}
                                </span>
                            </td>

                            {{-- Subtotal --}}
                            <td class="py-3 px-4 text-right">
                                <span class="font-semibold text-gray-800 text-xs">Rp
                                    {{ number_format($sj->subtotal ?? 0, 0, ',', '.') }}</span>
                            </td>

                            {{-- Status Badge --}}
                            <td class="py-3 px-4 text-center">
                                @php
                                    $statusConfig = [
                                        'Pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'fa-clock'],
                                        'Disetujui' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'fa-circle-check'],
                                        'Ditolak' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'fa-circle-xmark'],
                                        'Dikirim' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'fa-truck'],
                                        'Selesai' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-700', 'icon' => 'fa-flag-checkered'],
                                    ];
                                    $cfg = $statusConfig[$sj->status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'icon' => 'fa-question'];
                                @endphp
                                <span
                                    class="inline-flex items-center gap-1 px-2.5 py-1 {{ $cfg['bg'] }} {{ $cfg['text'] }} rounded-full text-[11px] font-bold">
                                    <i class="fa-solid {{ $cfg['icon'] }} text-[9px]"></i>
                                    {{ $sj->status }}
                                </span>
                            </td>

                            {{-- Dibuat Oleh --}}
                            <td class="py-3 px-4 text-xs text-gray-500">
                                {{ $sj->user->nama_lengkap ?? '-' }}
                            </td>

                            {{-- Aksi --}}
                            <td class="py-3 px-4 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('surat_jalan.show', $sj->sj_id) }}"
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-gray-100 text-gray-700 rounded-lg text-xs font-medium hover:bg-red-100 hover:text-red-700 transition">
                                        <i class="fa-solid fa-eye text-[10px]"></i>
                                        Detail
                                    </a>
                                    @if ($sj->status === 'Pending')
                                        <form id="delete-form-{{ $sj->sj_id }}"
                                            action="{{ route('surat_jalan.destroy', $sj->sj_id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('{{ $sj->sj_id }}')"
                                                class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-50 text-red-600 rounded-lg text-xs font-medium hover:bg-red-100 transition">
                                                <i class="fa-solid fa-trash text-[10px]"></i>
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                    <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                                        <i class="fa-solid fa-file-lines text-2xl text-gray-300"></i>
                                    </div>
                                    <p class="text-sm font-medium">Tidak ada data Surat Jalan</p>
                                    <p class="text-xs">Belum ada surat jalan yang dibuat atau cocok dengan filter</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($suratJalans->hasPages())
            <div class="border-t border-gray-100 px-6 py-4">
                {{ $suratJalans->links() }}
            </div>
        @endif
    </div>

    @push('scripts')
        <script>
            function confirmDelete(sjId) {
                Swal.fire({
                    title: 'Hapus Surat Jalan?',
                    text: `Anda yakin ingin menghapus Surat Jalan ${sjId}? Tindakan ini tidak dapat dibatalkan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    customClass: { popup: 'swal-custom-popup' },
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById(`delete-form-${sjId}`).submit();
                    }
                });
            }
        </script>
    @endpush

@endsection