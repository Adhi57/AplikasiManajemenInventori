@extends('layouts.app')
@section('page-title', 'Data Master / Riwayat Barang')
@section('content')

<x-page-header title="Riwayat Barang" description="Daftar barang yang telah dihapus (diarsipkan)" icon="fa-clock-rotate-left">
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
    <form method="GET" action="{{ route('barangs.trashed') }}" class="flex flex-wrap items-center gap-3">
        <div class="relative flex-1 min-w-[200px]">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="search" name="search" placeholder="Cari kode atau nama barang..." value="{{ request('search') }}"
                class="w-full h-10 pl-9 pr-4 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition shadow-sm">
        </div>
        <div class="relative">
            <select name="kategori_id"
                class="h-10 border border-gray-300 rounded-xl pl-3 pr-8 text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 shadow-sm bg-white appearance-none">
                <option value="">Semua Kategori</option>
                @foreach ($kategoriBarangs as $kategori)
                    <option value="{{ $kategori->kategori_barang_id }}" {{ request('kategori_id') == $kategori->kategori_barang_id ? 'selected' : '' }}>
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

{{-- Info Banner --}}
<div class="mb-6 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex items-start gap-3">
    <i class="fa-solid fa-circle-info text-amber-500 text-lg mt-0.5"></i>
    <div>
        <p class="text-sm font-semibold text-amber-800">Tentang Riwayat Barang</p>
        <p class="text-xs text-amber-600 mt-1">Barang yang dihapus akan dipindahkan ke sini. Anda dapat memulihkan barang atau menghapusnya secara permanen. Barang yang memiliki riwayat transaksi (PO/Surat Jalan) tidak dapat dihapus permanen.</p>
    </div>
</div>

{{-- Success/Error Alert --}}
@if(session('success'))
<div class="mb-4 bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
    <div class="px-5 py-3 bg-emerald-50 flex items-center gap-2">
        <i class="fa-solid fa-circle-check text-emerald-600"></i>
        <span class="text-sm font-medium text-emerald-700">{{ session('success') }}</span>
    </div>
</div>
@endif

@if(session('error'))
<div class="mb-4 bg-white rounded-2xl border border-red-200 shadow-sm overflow-hidden">
    <div class="px-5 py-3 bg-red-50 flex items-center gap-2">
        <i class="fa-solid fa-circle-xmark text-red-600"></i>
        <span class="text-sm font-medium text-red-700">{{ session('error') }}</span>
    </div>
</div>
@endif

{{-- Table Card --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
            <i class="fa-solid fa-box-archive text-red-500"></i>
            Barang yang Dihapus
            @if($trashedBarangs->total() > 0)
                <span class="ml-2 px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-bold rounded-full">{{ $trashedBarangs->total() }} barang</span>
            @endif
        </h3>
    </div>
    <div class="p-5">
        @if($trashedBarangs->isEmpty())
            <div class="text-center py-16">
                <i class="fa-solid fa-check-double text-5xl text-gray-200 mb-4"></i>
                <p class="text-sm font-medium text-gray-400">Tidak ada barang yang dihapus</p>
                <p class="text-xs text-gray-300 mt-1">Semua data barang masih aktif</p>
            </div>
        @else
            <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-xs text-white uppercase bg-red-800 sticky top-0 z-10">
                        <tr>
                            <th class="px-3 py-3 text-center rounded-tl-xl">Kode</th>
                            <th class="px-5 py-3">Nama Barang</th>
                            <th class="px-5 py-3">Kategori</th>
                            <th class="px-5 py-3">Supplier</th>
                            <th class="px-3 py-3 text-center">Satuan</th>
                            <th class="px-5 py-3">H. Jual</th>
                            <th class="px-5 py-3">H. Beli</th>
                            <th class="px-5 py-3 text-center">Dihapus Pada</th>
                            <th class="px-5 py-3 text-center rounded-tr-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trashedBarangs as $barang)
                            <tr class="text-xs border-b border-gray-100 even:bg-gray-50/50 hover:bg-red-50 transition duration-100">
                                <td class="px-3 py-2.5 font-semibold text-center text-gray-900">{{ $barang->kode_barang }}</td>
                                <td class="px-5 py-2.5 font-medium">
                                    <span class="line-through text-gray-400">{{ $barang->nama_barang }}</span>
                                </td>
                                <td class="px-5 py-2.5">{{ $barang->kategori->nama_kategori_barang ?? '-' }}</td>
                                <td class="px-5 py-2.5">{{ $barang->supplier->namaSupplier ?? '-' }}</td>
                                <td class="px-3 py-2.5 text-center">{{ $barang->satuan_jual }}</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-800">Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                                <td class="px-5 py-2.5 font-semibold text-gray-800">Rp{{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                                <td class="px-5 py-2.5 text-center text-gray-400">
                                    <div class="flex flex-col items-center">
                                        <span class="font-medium">{{ $barang->deleted_at->format('d M Y') }}</span>
                                        <span class="text-[10px]">{{ $barang->deleted_at->format('H:i') }} WIB</span>
                                    </div>
                                </td>
                                <td class="px-5 py-2.5 text-center whitespace-nowrap">
                                    <button type="button"
                                        onclick="confirmRestore('{{ $barang->kode_barang }}', '{{ $barang->nama_barang }}')"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 transition"
                                        title="Pulihkan">
                                        <i class="fa-solid fa-rotate-left text-xs"></i>
                                    </button>
                                    <button type="button"
                                        onclick="confirmForceDelete('{{ $barang->kode_barang }}', '{{ $barang->nama_barang }}')"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                        title="Hapus Permanen">
                                        <i class="fa-solid fa-trash-can text-xs"></i>
                                    </button>

                                    {{-- Restore Form --}}
                                    <form id="restore-form-{{ $barang->kode_barang }}"
                                        action="{{ route('barangs.restore', $barang->kode_barang) }}" method="POST" class="hidden">
                                        @csrf @method('PATCH')
                                    </form>

                                    {{-- Force Delete Form --}}
                                    <form id="force-delete-form-{{ $barang->kode_barang }}"
                                        action="{{ route('barangs.forceDelete', $barang->kode_barang) }}" method="POST" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
    @if($trashedBarangs->hasPages())
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50">
            {{ $trashedBarangs->appends(request()->query())->links() }}
        </div>
    @endif
</div>

@push('scripts')
    <script>
        function confirmRestore(kode, name) {
            Swal.fire({
                title: 'Pulihkan Barang?',
                text: `Anda yakin ingin memulihkan "${name}" (${kode})?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Pulihkan!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'swal-custom-popup' },
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`restore-form-${kode}`).submit();
                }
            });
        }

        function confirmForceDelete(kode, name) {
            Swal.fire({
                title: 'Hapus Permanen?',
                html: `<p>Anda yakin ingin menghapus <strong>"${name}"</strong> (${kode}) secara permanen?</p><p class="text-xs text-red-500 mt-2"><i class="fa-solid fa-triangle-exclamation"></i> Tindakan ini TIDAK BISA dibatalkan!</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Hapus Permanen!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'swal-custom-popup' },
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(`force-delete-form-${kode}`).submit();
                }
            });
        }
    </script>
@endpush

@endsection
