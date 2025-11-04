@extends('layouts.app')
@section('page-title', 'Data Master / Kategori Pelanggan')
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
            <h2 class="text-lg font-semibold">Daftar Kategori Pelanggan</h2>
        </div>

        <table class="w-full text-sm text-left text-gray-900 rounded-lg">
            <thead class="text-xs text-gray-900 border-b bg-neutral">
                <tr>
                    <th class="px-6 py-2">ID</th>
                    <th class="px-6 py-2">Nama Kategori</th>
                    <th class="px-6 py-2">Jumlah Diskon</th>
                    <th class="px-6 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($kategori_pelanggan as $kat)
                <tr class="text-left text-xs even:bg-violet-50 odd:bg-white">
                    <td class="px-6 py-2">{{ $kat->kategori_pelanggan_id }}</td>
                    <td class="px-6 py-2">{{ $kat->kategori_pelanggan }}</td>
                    <td class="px-6 py-2">{{ $kat->jumlah_diskon }}%</td>
                    <td class="px-6 py-2 text-center flex justify-center gap-2">
                        <a href="{{ route('kategori_pelanggan.edit', $kat->kategori_pelanggan_id) }}" class="text-blue-600">Edit</a>

                        <form action="{{ route('kategori_pelanggan.destroy', $kat->kategori_pelanggan_id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?');">
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
            $kategori = $isEdit ? $editKategori : new \App\Models\Kategori_Pelanggan();
            $actionRoute = $isEdit
                ? route('kategori_pelanggan.update', $kategori->kategori_pelanggan_id)
                : route('kategori_pelanggan.store');

            // contoh enum (sesuaikan dengan enum di migration)
            $enumValues = ['Retail', 'Grosir', 'Biasa'];
        @endphp

        <h2 class="text-xl font-bold mb-4">{{ $isEdit ? 'Edit Kategori Pelanggan' : 'Tambah Kategori Pelanggan' }}</h2>

        <form action="{{ $actionRoute }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <!-- Nama Kategori (Enum) -->
            <div class="mb-4">
                <label class="block mb-2 font-medium">Nama Kategori</label>
                <select name="kategori_pelanggan" class="w-full border rounded p-2" required>
                    <option value="">-- Pilih Kategori Pelanggan --</option>
                    @foreach($enumValues as $value)
                        <option value="{{ $value }}" {{ $kategori->kategori_pelanggan == $value ? 'selected' : '' }}>
                            {{ $value }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Jumlah Diskon -->
            <div class="mb-4">
                <label class="block mb-2 font-medium">Jumlah Diskon (%)</label>
                <input type="number" name="jumlah_diskon" class="w-full border rounded p-2"
                    value="{{ old('jumlah_diskon', $kategori->jumlah_diskon) }}"
                    min="0" max="100" required>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('kategori_pelanggan.index') }}" class="px-4 py-2 bg-gray-200 rounded">Batal</a>
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
                    {{ $isEdit ? 'Simpan' : 'Tambah' }}
                </button>
            </div>
        </form>
    </div>

</div>

@endsection
