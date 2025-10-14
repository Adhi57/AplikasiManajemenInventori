@extends('layouts.app')

@section('content')

<div class="container mx-auto p-4">

    <div class="bg-white rounded-xl shadow-xs p-6 max-w-xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl text-center font-bold text-gray-800">Edit Supplier</h1>
        </div>

        <!-- FORM -->
        <form action="{{ route('suppliers.update', $supplier->id_supplier) }}" method="POST">
            @csrf
            @method('PUT')

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
                <label for="namaSupplier" class="block text-sm font-medium text-gray-700 mb-1">Nama Supplier</label>
                <input
                    type="text"
                    name="namaSupplier"
                    id="namaSupplier"
                    value="{{ old('namaSupplier', $supplier->namaSupplier) }}"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                    required>
            </div>

            <div class="mb-4">
                <label for="alamatSupplier" class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea
                    name="alamatSupplier"
                    id="alamatSupplier"
                    rows="3"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                    required>{{ old('alamatSupplier', $supplier->alamatSupplier) }}</textarea>
            </div>

            <div class="mb-4">
                <label for="Kota" class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                <input
                    type="text"
                    name="Kota"
                    id="Kota"
                    value="{{ old('Kota', $supplier->Kota) }}"
                    class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                    required>
            </div>

            <div class="mb-6 flex gap-7">
                <div>
                    <label for="noTelepon" class="block text-sm font-medium text-gray-700 mb-1">No. Telepon</label>
                    <input
                        type="text"
                        name="noTelepon"
                        id="noTelepon"
                        value="{{ old('noTelepon', $supplier->noTelepon) }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm
                               focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>

                <div>
                    <label for="waktuPengiriman" class="block text-sm font-medium text-gray-700 mb-1">
                        Rata-rata Waktu Pengiriman (hari)
                    </label>
                    <input
                        type="number"
                        name="waktuPengiriman"
                        id="waktuPengiriman"
                        min="1"
                        value="{{ old('waktuPengiriman', $supplier->waktuPengiriman) }}"
                        class="py-2.5 sm:py-3 px-4 block w-full border-gray-200 rounded-lg sm:text-sm
                               focus:border-blue-500 focus:ring-blue-500"
                        required>
                </div>
            </div>

            <!-- BUTTONS -->
            <div class="flex justify-end space-x-3">
                <a href="{{ route('suppliers.index') }}"
                   class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-lg
                          hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Batal
                </a>

                <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg shadow-md
                           hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

@endsection
