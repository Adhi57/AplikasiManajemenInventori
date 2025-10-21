@extends('layouts.app')

@section('content')

<div class="flex gap-6">

    <!-- TABEL -->
    <div class="flex-1 bg-white rounded-xl p-4">
        @if(session('success'))
            <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex justify-between mb-4">
            <h2 class="text-lg font-semibold">Daftar Kategori Barang</h2>
        </div>

        <table class="w-full text-sm text-left rtl:text-right text-gray-900 rounded-lg">
            <thead class="text-xs text-gray-900 border-b-1 rounded-lg bg-neutral">
                <tr>
                    <th class="px-6 py-2">ID</th>
                    <th class="px-6 py-2">Nama Kategori</th>
                    <th class="px-6 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kategori_barang as $kat)
                <tr class="text-left text-xs even:bg-violet-50 odd:bg-white">
                    <td class="px-6 py-2">{{ $kat->kategori_barang_id }}</td>
                    <td class="px-6 py-2">{{ $kat->nama_kategori_barang }}</td>
                    <td class="px-6 py-2 text-center flex justify-center gap-2">
                        <a href="{{ route('kategori_barang.edit', $kat->kategori_barang_id) }}" class="text-blue-600">Edit</a>

                        <form action="{{ route('kategori_barang.destroy', $kat->kategori_barang_id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- FORM -->
    <div class="w-96 bg-white p-6 rounded shadow">
        @php
            $isEdit = isset($editKategori);
            $kategori = $isEdit ? $editKategori : new \App\Models\KategoriBarang();
            $actionRoute = $isEdit 
                ? route('kategori_barang.update', $kategori->kategori_barang_id)
                : route('kategori_barang.store');
        @endphp

        <h2 class="text-xl font-bold mb-4">{{ $isEdit ? 'Edit Kategori Barang' : 'Tambah Kategori Barang' }}</h2>

        <form action="{{ $actionRoute }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <label class="block mb-2 font-medium">Nama Kategori</label>
            <input type="text" name="nama_kategori_barang" value="{{ old('nama_kategori_barang', $kategori->nama_kategori_barang) }}"
                class="w-full px-3 py-2 border rounded mb-4" required>

            <div class="flex justify-end gap-2">
                <a href="{{ route('kategori_barang.index') }}" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">{{ $isEdit ? 'Simpan' : 'Tambah' }}</button>
            </div>
        </form>
    </div>

</div>

@endsection
