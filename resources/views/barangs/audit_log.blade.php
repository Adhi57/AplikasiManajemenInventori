@extends('layouts.app')
@section('page-title', 'Data Master / Riwayat Perubahan Barang')
@section('content')

<x-page-header title="Riwayat Perubahan Barang" description="Log aktivitas perubahan data barang secara detail" icon="fa-timeline">
    <x-slot name="actions">
        <a href="{{ route('barangs.index') }}"
            class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
            <i class="fa-solid fa-arrow-left text-amber-400"></i>
            <span>Kembali ke Data Barang</span>
        </a>
    </x-slot>
</x-page-header>

{{-- Filters --}}
<div class="mb-6">
    <form method="GET" action="{{ route('barangs.auditLogs') }}" class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="search" name="search" placeholder="Cari kode barang, nama, atau user..." value="{{ request('search') }}"
                class="w-full h-10 pl-9 pr-4 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition shadow-sm">
        </div>
        <div class="relative">
            <select name="aksi"
                class="h-10 border border-gray-300 rounded-xl pl-3 pr-8 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-sm bg-white appearance-none">
                <option value="">Semua Aksi</option>
                <option value="created" {{ request('aksi') == 'created' ? 'selected' : '' }}>Ditambahkan</option>
                <option value="updated" {{ request('aksi') == 'updated' ? 'selected' : '' }}>Diperbarui</option>
                <option value="deleted" {{ request('aksi') == 'deleted' ? 'selected' : '' }}>Dihapus</option>
                <option value="restored" {{ request('aksi') == 'restored' ? 'selected' : '' }}>Dipulihkan</option>
                <option value="force_deleted" {{ request('aksi') == 'force_deleted' ? 'selected' : '' }}>Hapus Permanen</option>
            </select>
        </div>
        <button type="submit"
            class="inline-flex items-center gap-2 h-10 px-4 bg-red-800 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition shadow-sm">
            <i class="fa-solid fa-filter text-xs"></i> Filter
        </button>
    </form>
</div>

{{-- Timeline --}}
<div class="space-y-4">
    @if($logs->isEmpty())
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 text-center">
            <i class="fa-solid fa-clipboard-list text-5xl text-gray-200 mb-4"></i>
            <p class="text-sm font-medium text-gray-400">Belum ada riwayat perubahan</p>
            <p class="text-xs text-gray-300 mt-1">Log akan otomatis tercatat saat data barang ditambah, diubah, atau dihapus</p>
        </div>
    @else
        @foreach($logs as $log)
            @php
                $aksiBadge = match($log->aksi) {
                    'created' => ['bg-emerald-100 text-emerald-700', 'fa-plus-circle', 'Ditambahkan'],
                    'updated' => ['bg-blue-100 text-blue-700', 'fa-pen-to-square', 'Diperbarui'],
                    'deleted' => ['bg-red-100 text-red-700', 'fa-trash-can', 'Dihapus'],
                    'restored' => ['bg-amber-100 text-amber-700', 'fa-rotate-left', 'Dipulihkan'],
                    'force_deleted' => ['bg-red-200 text-red-800', 'fa-skull-crossbones', 'Hapus Permanen'],
                    default => ['bg-gray-100 text-gray-700', 'fa-circle-question', $log->aksi],
                };

                $labelMap = [
                    'kode_barang' => 'Kode Barang',
                    'nama_barang' => 'Nama Barang',
                    'satuan_jual' => 'Satuan Jual',
                    'kategori_barang_id' => 'Kategori',
                    'id_supplier' => 'Supplier',
                    'jml_barang_per_karton' => 'Jml/Karton',
                    'foto_produk' => 'Foto Produk',
                    'tipe_harga_barang' => 'Tipe Harga',
                    'harga_jual' => 'Harga Jual',
                    'harga_beli' => 'Harga Beli',
                    'berlaku_mulai' => 'Berlaku Mulai',
                    'berlaku_sampai' => 'Berlaku Sampai',
                ];
            @endphp

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200">
                {{-- Header --}}
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $aksiBadge[0] }}">
                            <i class="fa-solid {{ $aksiBadge[1] }} text-[9px]"></i>
                            {{ $aksiBadge[2] }}
                        </span>
                        <div>
                            <span class="font-bold text-gray-800 text-sm">{{ $log->nama_barang }}</span>
                            <span class="text-gray-400 text-xs ml-1">({{ $log->kode_barang }})</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-400">
                        <span class="flex items-center gap-1.5">
                            <i class="fa-solid fa-user text-[10px]"></i>
                            <span class="font-medium text-gray-600">{{ $log->user_nama ?? 'Sistem' }}</span>
                        </span>
                        <span class="flex items-center gap-1.5">
                            <i class="fa-regular fa-clock text-[10px]"></i>
                            {{ $log->waktu->format('d M Y, H:i') }} WIB
                        </span>
                    </div>
                </div>

                {{-- Body: Detail perubahan --}}
                <div class="p-5">
                    @if($log->aksi === 'updated' && $log->kolom_berubah)
                        <div class="space-y-2">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">
                                <i class="fa-solid fa-code-compare text-blue-500"></i> Detail Perubahan
                            </p>
                            <div class="overflow-x-auto">
                                <table class="w-full text-xs">
                                    <thead>
                                        <tr class="border-b border-gray-200">
                                            <th class="text-left py-2 px-3 font-bold text-gray-500 uppercase tracking-wider text-[10px] w-1/4">Kolom</th>
                                            <th class="text-left py-2 px-3 font-bold text-red-500 uppercase tracking-wider text-[10px] w-[37.5%]">Sebelum</th>
                                            <th class="text-left py-2 px-3 font-bold text-emerald-500 uppercase tracking-wider text-[10px] w-[37.5%]">Sesudah</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($log->kolom_berubah as $kolom)
                                            <tr class="border-b border-gray-50 hover:bg-gray-50/50">
                                                <td class="py-2 px-3 font-semibold text-gray-700">
                                                    {{ $labelMap[$kolom] ?? ucwords(str_replace('_', ' ', $kolom)) }}
                                                </td>
                                                <td class="py-2 px-3">
                                                    <span class="inline-block bg-red-50 text-red-600 px-2 py-0.5 rounded-lg font-medium line-through">
                                                        @if(in_array($kolom, ['harga_jual', 'harga_beli']))
                                                            Rp{{ number_format((float) ($log->data_lama[$kolom] ?? 0), 0, ',', '.') }}
                                                        @else
                                                            {{ $log->data_lama[$kolom] ?? '-' }}
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="py-2 px-3">
                                                    <span class="inline-block bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-lg font-medium">
                                                        @if(in_array($kolom, ['harga_jual', 'harga_beli']))
                                                            Rp{{ number_format((float) ($log->data_baru[$kolom] ?? 0), 0, ',', '.') }}
                                                        @else
                                                            {{ $log->data_baru[$kolom] ?? '-' }}
                                                        @endif
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @elseif($log->aksi === 'created' && $log->data_baru)
                        <div class="space-y-2">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">
                                <i class="fa-solid fa-circle-plus text-emerald-500"></i> Data Barang Baru
                            </p>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                @foreach(['kode_barang', 'nama_barang', 'satuan_jual', 'harga_jual', 'harga_beli', 'tipe_harga_barang', 'jml_barang_per_karton'] as $field)
                                    @if(isset($log->data_baru[$field]))
                                        <div class="bg-emerald-50/50 rounded-xl px-3 py-2 border border-emerald-100">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $labelMap[$field] ?? $field }}</p>
                                            <p class="text-xs font-semibold text-gray-800 mt-0.5">
                                                @if(in_array($field, ['harga_jual', 'harga_beli']))
                                                    Rp{{ number_format((float) ($log->data_baru[$field] ?? 0), 0, ',', '.') }}
                                                @else
                                                    {{ $log->data_baru[$field] }}
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @elseif(in_array($log->aksi, ['deleted', 'force_deleted']) && $log->data_lama)
                        <div class="space-y-2">
                            <p class="text-xs font-bold text-gray-500 uppercase tracking-wide mb-3">
                                <i class="fa-solid fa-circle-minus text-red-500"></i> Data Barang yang Dihapus
                            </p>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2">
                                @foreach(['kode_barang', 'nama_barang', 'satuan_jual', 'harga_jual', 'harga_beli', 'tipe_harga_barang', 'jml_barang_per_karton'] as $field)
                                    @if(isset($log->data_lama[$field]))
                                        <div class="bg-red-50/50 rounded-xl px-3 py-2 border border-red-100">
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $labelMap[$field] ?? $field }}</p>
                                            <p class="text-xs font-semibold text-gray-800 mt-0.5 line-through">
                                                @if(in_array($field, ['harga_jual', 'harga_beli']))
                                                    Rp{{ number_format((float) ($log->data_lama[$field] ?? 0), 0, ',', '.') }}
                                                @else
                                                    {{ $log->data_lama[$field] }}
                                                @endif
                                            </p>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @else
                        <p class="text-xs text-gray-400 italic">
                            <i class="fa-solid fa-info-circle"></i>
                            {{ $log->keterangan ?? 'Tidak ada detail perubahan' }}
                        </p>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@endsection
