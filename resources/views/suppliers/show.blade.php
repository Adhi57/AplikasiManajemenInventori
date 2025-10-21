@extends('layouts.app')

@section('content')

<div class="container mx-auto p-6">

    <div class="bg-white shadow rounded-xl max-w-2xl mx-auto p-6">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-2xl font-bold text-gray-800">Detail Supplier</h1>
            <a href="{{ route('suppliers.index') }}" 
               class="text-sm bg-gray-200 hover:bg-gray-300 text-gray-700 px-3 py-2 rounded-lg">
               ← Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-800">
            <div>
                <p class="font-semibold text-gray-600">Kode Supplier:</p>
                <p class="mb-2">{{ $supplier->id_supplier }}</p>
            </div>

            <div>
                <p class="font-semibold text-gray-600">Nama Supplier:</p>
                <p class="mb-2">{{ $supplier->namaSupplier }}</p>
            </div>

            <div class="md:col-span-2">
                <p class="font-semibold text-gray-600">Alamat:</p>
                <p class="mb-2">{{ $supplier->alamatSupplier }}</p>
            </div>

            <div>
                <p class="font-semibold text-gray-600">Kota:</p>
                <p class="mb-2">{{ $supplier->Kota }}</p>
            </div>

            <div>
                <p class="font-semibold text-gray-600">No. Telepon</p>
                <p class="mb-2">{{ $supplier->noTelepon }}</p>
            </div>

            <div>
                <p class="font-semibold text-gray-600">Lama Pengiriman:</p>
                <p class="mb-2">{{ $supplier->waktuPengiriman}}</p>
            </div>

        </div>

        <div class="flex justify-end mt-6 space-x-3">
            <a href="{{ route('suppliers.edit', $supplier->id_supplier) }}"
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow">
                Edit
            </a>
            <form action="{{ route('suppliers.destroy', $supplier->id_supplier) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus supplier ini?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg shadow">
                    Hapus
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
