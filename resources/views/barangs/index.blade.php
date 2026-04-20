@extends('layouts.app')
@section('page-title', 'Data Master / Barang')
@section('content')

<x-page-header title="Data Barang" description="Kelola semua data produk dan barang" icon="fa-boxes-stacked">
    <x-slot name="actions">
        <a href="{{ route('barangs.auditLogs') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
            <i class="fa-solid fa-timeline text-amber-400"></i>
            <span>Log Perubahan</span>
        </a>
        <a href="{{ route('barangs.trashed') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
            <i class="fa-solid fa-clock-rotate-left text-amber-400"></i>
            <span>Riwayat Barang</span>
        </a>
        <a href="{{ route('barangs.create') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-red-950 shadow-md transition-all text-sm font-bold hover:scale-105 duration-200">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Barang Baru</span>
        </a>
    </x-slot>
</x-page-header>

{{-- Filters --}}
<div class="mb-6">
    <form method="GET" action="{{ route('barangs.index') }}" class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="search" name="search" placeholder="Cari kode atau nama barang..."
                value="{{ request('search') }}"
                class="w-full h-10 pl-9 pr-4 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition shadow-sm">
        </div>
        <div class="relative">
            <select name="kategori_id"
                class="h-10 border border-gray-300 rounded-xl pl-3 pr-8 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-sm bg-white appearance-none">
                <option value="">Semua Kategori</option>
                @foreach ($kategoriBarangs as $kategori)
                <option value="{{ $kategori->kategori_barang_id }}"
                    {{ request('kategori_id') == $kategori->kategori_barang_id ? 'selected' : '' }}>
                    {{ $kategori->nama_kategori_barang }}
                </option>
                @endforeach
            </select>
        </div>
        <button type="submit"
            class="inline-flex items-center gap-2 h-10 px-4 bg-red-800 text-white text-sm font-semibold rounded-xl hover:bg-red-700 transition shadow-sm">
            <i class="fa-solid fa-filter text-xs"></i> Filter
        </button>
    </form>
</div>

{{-- Success Alert --}}
@if(session('success'))
<div class="mb-4 bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
    <div class="px-5 py-3 bg-emerald-50 flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-600"></i>
        <span class="text-sm font-medium text-emerald-700">{{ session('success') }}</span>
    </div>
</div>
@endif

{{-- Table Card --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
            <i class="fa-solid fa-table-list text-red-500"></i>
            Daftar Barang
        </h3>
    </div>
    <div class="p-5">
        <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
            <table id="barang-table" class="w-full text-sm text-left text-gray-700">
                <thead class="text-xs text-white uppercase bg-red-800 sticky top-0 z-10">
                    <tr>
                        <th class="px-3 py-3 text-center rounded-tl-xl">Kode</th>
                        <th class="px-5 py-3">Nama Barang</th>
                        <th class="px-3 py-3 text-center">Satuan</th>
                        <th class="px-5 py-3">Kategori</th>
                        <th class="px-5 py-3">Supplier</th>
                        <th class="px-3 py-3 text-center">Jml/Karton</th>
                        <th class="px-3 py-3 text-center">Tipe Harga</th>
                        <th class="px-5 py-3">H. Jual</th>
                        <th class="px-5 py-3">H. Beli</th>
                        <th class="px-5 py-3 text-center rounded-tr-xl">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($barangs as $barang)
                    <tr class="text-xs border-b border-gray-100 even:bg-gray-50/50 hover:bg-red-50 transition duration-100">
                        <td class="px-3 py-2.5 font-semibold text-center text-gray-900">{{ $barang->kode_barang }}</td>
                        <td class="px-5 py-2.5 font-medium">{{ $barang->nama_barang }}</td>
                        <td class="px-3 py-2.5 text-center">{{ $barang->satuan_jual }}</td>
                        <td class="px-5 py-2.5">{{ $barang->kategori->nama_kategori_barang ?? '-' }}</td>
                        <td class="px-5 py-2.5">{{ $barang->supplier->namaSupplier ?? '-' }}</td>
                        <td class="px-3 py-2.5 text-center">{{ $barang->jml_barang_per_karton }}</td>
                        <td class="px-3 py-2.5 text-center">
                            @php
                                $tipeBadge = match($barang->tipe_harga_barang) {
                                    'Eceran' => 'bg-blue-100 text-blue-700',
                                    'Grosir' => 'bg-emerald-100 text-emerald-700',
                                    default => 'bg-red-100 text-red-700',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $tipeBadge }}">
                                {{ $barang->tipe_harga_barang }}
                            </span>
                        </td>
                        <td class="px-5 py-2.5 font-semibold text-gray-800">Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                        <td class="px-5 py-2.5 font-semibold text-gray-800">Rp{{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                        <td class="px-5 py-2.5 text-center whitespace-nowrap">
                            <a href="{{ route('barangs.show', $barang->kode_barang) }}"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition" title="Detail">
                                <i class="fa-solid fa-eye text-xs"></i>
                            </a>
                            <a href="{{ route('barangs.edit', $barang->kode_barang) }}"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition" title="Edit">
                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                            </a>
                            <button type="button"
                                onclick="confirmDelete('{{ $barang->kode_barang }}', '{{ $barang->nama_barang }}')"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Hapus">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                            <form id="delete-form-{{ $barang->kode_barang }}"
                                action="{{ route('barangs.destroy', $barang->kode_barang) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
        {{ $barangs->appends(request()->query())->links() }}
    </div>
</div>

@push('scripts')
    <script>
        function confirmDelete(kode, name) {
            Swal.fire({
                title: 'Hapus Barang?',
                text: `Anda yakin ingin menghapus "${name}" (${kode})? Data akan dipindahkan ke riwayat.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'swal-custom-popup' },
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`delete-form-${kode}`).submit();
                }
            });
        }
    </script>
@endpush

@endsection