@extends('layouts.app')
@section('page-title', 'Data Master / Pelanggan')
@section('content')

{{-- Page Header --}}
<div class="mb-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-800 flex items-center justify-center shadow">
                <i class="fa-solid fa-user-plus text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Tambah Pelanggan Baru</h1>
                <p class="text-sm text-gray-500">Lengkapi data untuk mendaftarkan pelanggan baru</p>
            </div>
        </div>
        <a href="{{ route('pelanggans.index') }}"
            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
            <i class="fa-solid fa-arrow-left"></i>
            Kembali
        </a>
    </div>
</div>

{{-- Error Alert --}}
@if ($errors->any())
<div class="mb-6 bg-white rounded-2xl border border-red-200 shadow-sm overflow-hidden">
    <div class="px-5 py-3 bg-red-50 border-b border-red-200">
        <h3 class="font-bold text-red-800 text-sm flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i> Terdapat Kesalahan
        </h3>
    </div>
    <div class="p-5">
        <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
            @foreach ($errors->all() as $error) <li>{{ $error }}</li> @endforeach
        </ul>
    </div>
</div>
@endif

<form action="{{ route('pelanggans.store') }}" method="POST" class="space-y-6">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">

            {{-- Section 1: Identitas Pelanggan --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-id-card text-red-500"></i>
                        Identitas Pelanggan
                    </h3>
                </div>
                <div class="p-5 space-y-5">
                    <div>
                        <label for="nama_pelanggan" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                            Nama Pelanggan <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" name="nama_pelanggan" id="nama_pelanggan"
                                value="{{ old('nama_pelanggan') }}" placeholder="Nama pelanggan"
                                class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition" required>
                        </div>
                    </div>
                    <div>
                        <label for="alamat" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                            Alamat <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-location-dot absolute left-3 top-3 text-gray-400 text-sm"></i>
                            <textarea name="alamat" id="alamat" rows="3" placeholder="Alamat lengkap"
                                class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition resize-none"
                                required>{{ old('alamat') }}</textarea>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="NPWP" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">NPWP</label>
                            <div class="relative">
                                <i class="fa-solid fa-file-invoice absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="NPWP" id="NPWP" value="{{ old('NPWP') }}" maxlength="20"
                                    placeholder="xx.xxx.xxx.x-xxx.xxx"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                            </div>
                        </div>
                        <div>
                            <label for="PIC" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">PIC</label>
                            <div class="relative">
                                <i class="fa-solid fa-user-tie absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="PIC" id="PIC" value="{{ old('PIC') }}" maxlength="50"
                                    placeholder="Person in charge"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 2: Kategori & Tipe Harga --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                        <i class="fa-solid fa-tags text-red-500"></i>
                        Kategori & Tipe Harga
                    </h3>
                </div>
                <div class="p-5 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="kategori_pelanggan_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Kategori Pelanggan <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <select name="kategori_pelanggan_id" id="kategori_pelanggan_id"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition bg-white appearance-none" required>
                                    <option value="">-- Pilih Kategori --</option>
                                    @foreach($kategori_pelanggans as $kategori)
                                    <option value="{{ $kategori->kategori_pelanggan_id }}"
                                        {{ old('kategori_pelanggan_id') == $kategori->kategori_pelanggan_id ? 'selected' : '' }}>
                                        {{ $kategori->kategori_pelanggan }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div>
                            <label for="tipe_harga" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Tipe Harga
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-coins absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <select name="tipe_harga" id="tipe_harga"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition bg-white appearance-none">
                                    <option value="Eceran" {{ old('tipe_harga') == 'Eceran' ? 'selected' : '' }}>Eceran</option>
                                    <option value="Grosir" {{ old('tipe_harga') == 'Grosir' ? 'selected' : '' }}>Grosir</option>
                                    <option value="Diskon" {{ old('tipe_harga') == 'Diskon' ? 'selected' : '' }}>Diskon</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== RIGHT COLUMN: Actions ===== --}}
        <div class="lg:col-span-1">
            <div class="sticky top-6 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-red-800 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200 focus:ring-4 focus:ring-red-200">
                        <i class="fa-solid fa-floppy-disk"></i>
                        Simpan Pelanggan
                    </button>
                    <a href="{{ route('pelanggans.index') }}"
                        class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
                        <i class="fa-solid fa-xmark"></i>
                        Batal
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection