@extends('layouts.app')
@section('page-title', 'Data Master / Kategori Barang')
@section('content')


@php
    $isEdit = $kategori_barang->exists;
    $actionRoute = $isEdit 
        ? route('kategori_barang.update', $kategori_barang->kategori_barang_id) 
        : route('kategori_barang.store');

        $header = $isEdit 
        ? 'Edit Kategori Barang'
        : 'Tambah Kategori Barang';
@endphp


<div class="container mx-auto p-4">

    <x-page-header title="{{ $header }}" description="{{ $isEdit ? 'Perbarui informasi kategori barang' : 'Lengkapi data untuk mendaftarkan kategori barang baru' }}" icon="fa-layer-group">
        <x-slot name="actions">
            <a href="{{ route('kategori_barang.index') }}"
                class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-white/10 bg-white/5 hover:bg-white/15 backdrop-blur-sm shadow-sm transition-all text-sm font-medium hover:scale-105 hover:border-amber-400/30 duration-200">
                <i class="fa-solid fa-arrow-left text-amber-400"></i>
                <span>Batal</span>
            </a>
        </x-slot>
    </x-page-header>

    <div class="bg-white rounded-xl shadow-xs p-6 max-w-xl mx-auto mt-6">

        <!-- FORM -->
        <form action="{{$actionRoute }}" method="POST">
            @csrf
            @if ($isEdit)
            @method('PUT')
            @endif

            <!-- ALERT -->
            @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
            @endif

            @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                <strong class="font-bold">Oops!</strong>
                <span class="block sm:inline">Ada masalah dengan input Anda.</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- INPUTS -->
            <div class="mb-4">
                <label for="nama_kategori_barang" class="block text-sm font-medium text-gray-700 mb-1">Nama kategori Barang</label>
                <input
                    type="text"
                    name="nama_kategori_barang"
                    id="nama_kategori_barang"
                    value="{{ old('nama_kategori_barang', $kategori_barang->nama_kategori_barang) }}"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                    required>
            </div>

            <!-- BUTTONS -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('kategori_barang.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg
                          hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Batal
                </a>

                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow-md
                           hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan
                </button>
            </div>
        </form>

    </div>

</div>

@endsection