@extends('layouts.app')

@section('page-title', 'Data Master / Barang')

@section('content')

    @php
        $isEdit = $barang->exists;
        $actionRoute = $isEdit
            ? route('barangs.update', $barang->kode_barang)
            : route('barangs.store');
    @endphp

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-800 flex items-center justify-center shadow">
                    <i class="fa-solid {{ $isEdit ? 'fa-pen-to-square' : 'fa-box' }} text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $isEdit ? 'Edit Barang' : 'Tambah Barang Baru' }}</h1>
                    <p class="text-sm text-gray-500">
                        {{ $isEdit ? 'Perbarui informasi barang: ' . $barang->nama_barang : 'Lengkapi data untuk mendaftarkan barang baru' }}
                    </p>
                </div>
            </div>
            <a href="{{ route('barangs.index') }}"
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
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Terdapat Kesalahan
                </h3>
            </div>
            <div class="p-5">
                <ul class="list-disc list-inside text-sm text-red-600 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <form action="{{ $actionRoute }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ===== LEFT COLUMN: Main Form ===== --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Section 1: Kode Barang & Info Dasar --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-box-open text-red-500"></i>
                            Informasi Dasar Barang
                        </h3>
                    </div>
                    <div class="p-5 space-y-5">

                        {{-- Kode Barang --}}
                        <div>
                            <label for="kode_barang" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Kode Barang (Barcode) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="kode_barang" id="kode_barang"
                                    value="{{ old('kode_barang', $barang->kode_barang) }}"
                                    placeholder="Masukkan kode barcode"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                    required>
                            </div>
                            @error('kode_barang')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Nama Barang --}}
                        <div>
                            <label for="nama_barang" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Nama Barang <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-tag absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="nama_barang" id="nama_barang"
                                    value="{{ old('nama_barang', $barang->nama_barang) }}"
                                    placeholder="Nama produk"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                    required>
                            </div>
                            @error('nama_barang')
                                <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Kategori & Supplier --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="kategori_barang_id" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Kategori <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-layer-group absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <select name="kategori_barang_id" id="kategori_barang_id"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition bg-white appearance-none"
                                        required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach($kategoriBarangs as $kategoriBarang)
                                            <option value="{{ $kategoriBarang->kategori_barang_id }}"
                                                {{ old('kategori_barang_id', $barang->kategori_barang_id) == $kategoriBarang->kategori_barang_id ? 'selected' : '' }}>
                                                {{ $kategoriBarang->nama_kategori_barang }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="id_supplier" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Supplier <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-truck-field absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <select name="id_supplier" id="id_supplier"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition bg-white appearance-none"
                                        required>
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id_supplier }}"
                                                {{ old('id_supplier', $barang->id_supplier) == $supplier->id_supplier ? 'selected' : '' }}>
                                                {{ $supplier->namaSupplier }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Harga & Satuan --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-coins text-red-500"></i>
                            Informasi Harga & Satuan
                        </h3>
                    </div>
                    <div class="p-5 space-y-5">

                        {{-- Harga Beli & Harga Jual --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="harga_beli" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Harga Beli per Karton <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-semibold">Rp</span>
                                    <input type="number" step="0.01" name="harga_beli" id="harga_beli"
                                        value="{{ old('harga_beli', $barang->harga_beli) }}"
                                        placeholder="0"
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                        required>
                                </div>
                            </div>
                            <div>
                                <label for="harga_jual" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Harga Jual per Satuan <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-semibold">Rp</span>
                                    <input type="number" step="0.01" name="harga_jual" id="harga_jual"
                                        value="{{ old('harga_jual', $barang->harga_jual) }}"
                                        placeholder="0"
                                        class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                        required>
                                </div>
                            </div>
                        </div>

                        {{-- Tipe Harga --}}
                        <div>
                            <label for="tipe_harga_barang" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Tipe Harga Jual <span class="text-red-500">*</span>
                            </label>
                            @php $selectedTipe = old('tipe_harga_barang', $barang->tipe_harga_barang); @endphp
                            <div class="flex flex-wrap gap-2" x-data="{ tipe: '{{ $selectedTipe ?: 'Eceran' }}' }">
                                @foreach(['Eceran' => 'fa-store', 'Grosir' => 'fa-boxes-stacked', 'Diskon' => 'fa-percent'] as $val => $icon)
                                    <label class="cursor-pointer" @click="tipe = '{{ $val }}'">
                                        <input type="radio" name="tipe_harga_barang" value="{{ $val }}" class="hidden" :checked="tipe === '{{ $val }}'">
                                        <span class="inline-flex items-center gap-1.5 px-4 py-2 border-2 rounded-xl text-sm font-semibold transition-all"
                                            :class="tipe === '{{ $val }}' ? 'border-red-500 bg-red-50 text-red-700' : 'border-gray-200 text-gray-500 hover:border-gray-300'">
                                            <i class="fa-solid {{ $icon }} text-xs"></i>
                                            {{ $val }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Satuan & Jml/Karton --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="satuan_jual" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Satuan Jual <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-ruler absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <select name="satuan_jual" id="satuan_jual"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition bg-white appearance-none"
                                        required>
                                        @php $selectedSatuan = old('satuan_jual', $barang->satuan_jual); @endphp
                                        <option value="pcs" {{ $selectedSatuan == 'pcs' ? 'selected' : '' }}>pcs</option>
                                        <option value="renteng" {{ $selectedSatuan == 'renteng' ? 'selected' : '' }}>renteng</option>
                                        <option value="pack" {{ $selectedSatuan == 'pack' ? 'selected' : '' }}>pack</option>
                                        <option value="karton" {{ $selectedSatuan == 'karton' ? 'selected' : '' }}>karton</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label for="jml_barang_per_karton" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Jumlah per Karton <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fa-solid fa-cubes absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="number" name="jml_barang_per_karton" id="jml_barang_per_karton"
                                        value="{{ old('jml_barang_per_karton', $barang->jml_barang_per_karton) }}"
                                        placeholder="0"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 3: Periode Harga --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-calendar-days text-red-500"></i>
                            Periode Harga
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="berlaku_mulai" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Berlaku Mulai <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fa-regular fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="date" name="berlaku_mulai" id="berlaku_mulai"
                                        value="{{ old('berlaku_mulai', $barang->berlaku_mulai ? \Carbon\Carbon::parse($barang->berlaku_mulai)->format('Y-m-d') : date('Y-m-d')) }}"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                        required>
                                </div>
                            </div>
                            <div>
                                <label for="berlaku_sampai" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Berlaku Sampai <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fa-regular fa-calendar-check absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="date" name="berlaku_sampai" id="berlaku_sampai"
                                        value="{{ old('berlaku_sampai', $barang->berlaku_sampai ? \Carbon\Carbon::parse($barang->berlaku_sampai)->format('Y-m-d') : date('Y-m-d')) }}"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                        required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 4: Foto Produk --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-image text-red-500"></i>
                            Foto Produk
                        </h3>
                    </div>
                    <div class="p-5">
                        @if ($isEdit && $barang->foto_produk)
                            <div class="mb-4 flex items-center gap-4 p-3 bg-gray-50 rounded-xl">
                                <img src="{{ asset('storage/' . $barang->foto_produk) }}" alt="Foto Produk"
                                    class="h-16 w-16 object-cover rounded-lg border border-gray-200 shadow-sm">
                                <div>
                                    <p class="text-xs font-bold text-gray-500 uppercase">Foto Saat Ini</p>
                                    <p class="text-sm text-gray-700">Upload baru untuk mengganti</p>
                                </div>
                            </div>
                        @endif
                        <label for="foto_produk" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                            Upload Foto <span class="text-gray-400 font-normal normal-case">(Opsional)</span>
                        </label>
                        <input type="file" name="foto_produk" id="foto_produk"
                            class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2.5 file:px-4
                            file:rounded-xl file:border-0
                            file:text-sm file:font-semibold
                            file:bg-red-50 file:text-red-700
                            hover:file:bg-red-100
                            focus:outline-none cursor-pointer">
                        @error('foto_produk')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation text-[10px]"></i> {{ $message }}
                            </p>
                        @enderror
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
                                    <i class="fa-solid fa-barcode text-red-500 text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] uppercase font-bold text-gray-400">Kode Barang</p>
                                    <p class="text-sm font-bold text-gray-800 truncate" id="summary-kode">
                                        {{ $barang->kode_barang ?: 'Belum diisi' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-tag text-blue-500 text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] uppercase font-bold text-gray-400">Nama Barang</p>
                                    <p class="text-sm font-bold text-gray-800 truncate" id="summary-nama">
                                        {{ $barang->nama_barang ?: 'Belum diisi' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-coins text-emerald-500 text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] uppercase font-bold text-gray-400">Harga Jual</p>
                                    <p class="text-sm font-bold text-gray-800 truncate" id="summary-harga">
                                        {{ $barang->harga_jual ? 'Rp ' . number_format($barang->harga_jual, 0, ',', '.') : 'Belum diisi' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                                <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-layer-group text-amber-500 text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] uppercase font-bold text-gray-400">Kategori</p>
                                    <p class="text-sm font-bold text-gray-800 truncate" id="summary-kategori">
                                        {{ $barang->kategori ? $barang->kategori->nama_kategori_barang : 'Belum dipilih' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-red-800 text-white font-semibold text-sm rounded-xl hover:bg-red-700 transition shadow-lg shadow-red-200 focus:ring-4 focus:ring-red-200">
                            <i class="fa-solid fa-floppy-disk"></i>
                            {{ $isEdit ? 'Perbarui Barang' : 'Simpan Barang' }}
                        </button>
                        <a href="{{ route('barangs.index') }}"
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
        document.addEventListener('DOMContentLoaded', function () {
            const satuanSelect = document.getElementById('satuan_jual');
            const jumlahInput = document.getElementById('jml_barang_per_karton');

            function updateJumlahKarton() {
                if (satuanSelect.value === 'karton') {
                    jumlahInput.value = 1;
                    jumlahInput.readOnly = true;
                    jumlahInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                } else {
                    jumlahInput.readOnly = false;
                    jumlahInput.classList.remove('bg-gray-100', 'cursor-not-allowed');
                }
            }

            updateJumlahKarton();
            satuanSelect.addEventListener('change', updateJumlahKarton);

            // ── Live Summary Updates ──────────────────────────────────
            document.getElementById('kode_barang')?.addEventListener('input', function() {
                document.getElementById('summary-kode').textContent = this.value || 'Belum diisi';
            });
            document.getElementById('nama_barang')?.addEventListener('input', function() {
                document.getElementById('summary-nama').textContent = this.value || 'Belum diisi';
            });
            document.getElementById('harga_jual')?.addEventListener('input', function() {
                const val = parseFloat(this.value);
                document.getElementById('summary-harga').textContent = val ? 'Rp ' + val.toLocaleString('id-ID') : 'Belum diisi';
            });
            document.getElementById('kategori_barang_id')?.addEventListener('change', function() {
                const text = this.options[this.selectedIndex]?.text || 'Belum dipilih';
                document.getElementById('summary-kategori').textContent = text === '-- Pilih Kategori --' ? 'Belum dipilih' : text;
            });
        });
        </script>
    @endpush

@endsection