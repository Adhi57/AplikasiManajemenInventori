@extends('layouts.app')

@section('page-title', 'Data Master / Supplier')

@section('content')

    @php
        $isEdit = $supplier->exists;
        $actionRoute = $isEdit
            ? route('suppliers.update', $supplier->id_supplier)
            : route('suppliers.store');
    @endphp

    {{-- Page Header --}}
    <x-page-header title="{{ $isEdit ? 'Edit Supplier' : 'Tambah Supplier Baru' }}" description="{{ $isEdit ? 'Perbarui informasi supplier: ' . $supplier->namaSupplier : 'Lengkapi data untuk mendaftarkan supplier baru' }}" icon="{{ $isEdit ? 'fa-pen-to-square' : 'fa-truck-field' }}">
        <x-slot name="actions">
            <a href="{{ route('suppliers.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                <i class="fa-solid fa-arrow-left text-amber-400"></i>
                <span>Batal</span>
            </a>
        </x-slot>
    </x-page-header>

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

    <form action="{{ $actionRoute }}" method="POST" class="space-y-6">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- ===== LEFT COLUMN: Main Form ===== --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Section 1: Informasi Supplier --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-building text-red-500"></i>
                            Informasi Supplier
                        </h3>
                    </div>
                    <div class="p-5 space-y-5">
                        {{-- Nama Supplier --}}
                        <div>
                            <label for="namaSupplier"
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Nama Supplier <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i
                                    class="fa-solid fa-building absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="namaSupplier" id="namaSupplier"
                                    value="{{ old('namaSupplier', $supplier->namaSupplier) }}" placeholder="Nama supplier"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                    required>
                            </div>
                        </div>

                        {{-- Alamat --}}
                        <div>
                            <label for="alamatSupplier"
                                class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Alamat <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i class="fa-solid fa-location-dot absolute left-3 top-3 text-gray-400 text-sm"></i>
                                <textarea name="alamatSupplier" id="alamatSupplier" rows="3"
                                    placeholder="Alamat lengkap supplier"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition resize-none"
                                    required>{{ old('alamatSupplier', $supplier->alamatSupplier) }}</textarea>
                            </div>
                        </div>

                        {{-- Kota --}}
                        <div>
                            <label for="Kota" class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                Kota <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <i
                                    class="fa-solid fa-city absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" name="Kota" id="Kota" value="{{ old('Kota', $supplier->Kota) }}"
                                    placeholder="Kota"
                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                    required>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Section 2: Kontak & Pengiriman --}}
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                            <i class="fa-solid fa-phone text-red-500"></i>
                            Kontak & Pengiriman
                        </h3>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="noTelepon"
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    No. Telepon <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i
                                        class="fa-solid fa-phone absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="text" name="noTelepon" id="noTelepon"
                                        value="{{ old('noTelepon', $supplier->noTelepon) }}" placeholder="08xxxxxxxxxx"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                        required>
                                </div>
                            </div>
                            <div>
                                <label for="waktuPengiriman"
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Rata-rata Waktu Pengiriman <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <i
                                        class="fa-solid fa-clock absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input type="number" name="waktuPengiriman" id="waktuPengiriman" min="1"
                                        value="{{ old('waktuPengiriman', $supplier->waktuPengiriman) }}" placeholder="0"
                                        class="w-full pl-9 pr-16 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                        required>
                                    <span
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs font-semibold">hari</span>
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
                            {{ $isEdit ? 'Perbarui Supplier' : 'Simpan Supplier' }}
                        </button>
                        <a href="{{ route('suppliers.index') }}"
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