@extends('layouts.app')
@section('page-title', 'Data Master / Kategori Barang')
@section('content')

    <x-page-header title="Kategori Barang" description="Kelola kategori untuk pengelompokan barang" icon="fa-layer-group">
</x-page-header>

    @if(session('success'))
        <div class="mb-4 bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-emerald-50 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span class="text-sm font-medium text-emerald-700">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT: Table --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-table-list text-red-500"></i>
                        Daftar Kategori Barang
                    </h3>
                </div>
                <div class="p-5">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-700" id="kategori-table">
                            <thead class="text-xs text-white uppercase bg-red-800">
                                <tr>
                                    <th class="px-5 py-3 rounded-tl-xl">ID</th>
                                    <th class="px-5 py-3">Nama Kategori</th>
                                    <th class="px-5 py-3 text-center rounded-tr-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($kategori_barang as $kat)
                                    <tr
                                        class="text-xs border-b border-gray-100 even:bg-gray-50/50 hover:bg-red-50 transition duration-100">
                                        <td class="px-5 py-3 font-semibold text-gray-900">{{ $kat->kategori_barang_id }}</td>
                                        <td class="px-5 py-3 font-medium">{{ $kat->nama_kategori_barang }}</td>
                                        <td class="px-5 py-3 text-center whitespace-nowrap">
                                            <a href="{{ route('kategori_barang.edit', $kat->kategori_barang_id) }}"
                                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition"
                                                title="Edit">
                                                <i class="fa-solid fa-pen-to-square text-xs"></i>
                                            </a>
                                            <form action="{{ route('kategori_barang.destroy', $kat->kategori_barang_id) }}"
                                                method="POST" class="inline-block"
                                                onsubmit="return confirm('Yakin ingin menghapus kategori {{ $kat->nama_kategori_barang }}?');">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                                    title="Hapus">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Inline Form --}}
        <div class="lg:col-span-1">
            <div class="sticky top-6">
                @php
                    $isEdit = isset($editKategori);
                    $kategori = $isEdit ? $editKategori : new \App\Models\KategoriBarang();
                    $actionRoute = $isEdit
                        ? route('kategori_barang.update', $kategori->kategori_barang_id)
                        : route('kategori_barang.store');
                @endphp

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-red-800">
                        <h3 class="font-bold text-white text-sm flex items-center gap-2">
                            <i class="fa-solid {{ $isEdit ? 'fa-pen-to-square' : 'fa-plus' }}"></i>
                            {{ $isEdit ? 'Edit Kategori' : 'Tambah Kategori Baru' }}
                        </h3>
                    </div>
                    <div class="p-5">
                        <form action="{{ $actionRoute }}" method="POST">
                            @csrf
                            @if($isEdit) @method('PUT') @endif

                            <div class="mb-5">
                                <label for="nama_kategori_barang"
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Nama Kategori <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i
                                        class="fa-solid fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" name="nama_kategori_barang" id="nama_kategori_barang"
                                        value="{{ old('nama_kategori_barang', $kategori->nama_kategori_barang) }}"
                                        placeholder="Nama kategori"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                        required>
                                </div>
                            </div>

                            <div class="flex gap-2">
                                @if($isEdit)
                                    <a href="{{ route('kategori_barang.index') }}"
                                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
                                        <i class="fa-solid fa-xmark"></i> Batal
                                    </a>
                                @endif
                                <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-red-800 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200">
                                    <i class="fa-solid {{ $isEdit ? 'fa-floppy-disk' : 'fa-plus' }}"></i>
                                    {{ $isEdit ? 'Simpan' : 'Tambah' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection