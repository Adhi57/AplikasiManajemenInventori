@extends('layouts.app')

@section('page-title', 'Data Master / Supplier')

@section('content')

<div class="p-4 sm:p-6 bg-gray-50 rounded-xl shadow-inner min-h-[80vh]">

    <!-- Top Bar: Search and Filters -->
    <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        
        <!-- Search Bar -->
        <div class="relative w-full md:w-1/2 lg:w-1/3 text-gray-600">
            <input
                type="search"
                name="search"
                placeholder="Cari Nama, Kota, atau No. Telepon Supplier..."
                class="w-full h-10 px-5 pr-10 text-sm border-2 border-gray-300 outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 rounded-xl transition duration-150 shadow-sm">
            <button type="submit" class="absolute right-0 top-0 mt-3 mr-4 text-gray-500 hover:text-red-700 transition">
                <i class="fa-solid fa-magnifying-glass h-4 w-4"></i>
            </button>
        </div>

        <!-- Add Supplier Button -->
        <a href="{{ route('suppliers.create') }}"
            class="bg-red-700 text-white px-4 py-2 rounded-xl shadow-lg shadow-red-300 hover:bg-red-800 transition duration-150 font-semibold flex items-center gap-2">
            <i class="fa-solid fa-plus"></i>
            Tambah Supplier
        </a>
    </div>


    <!-- TABEL -->
    @if(session('success'))
    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg shadow-sm font-medium" role="alert">
        {{ session('success') }}
    </div>
    @endif
    
    <div class="bg-white rounded-xl shadow-xl overflow-hidden border border-gray-200">
        <div class="p-6">
            
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800 border-b pb-2">Daftar Supplier</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700 rounded-lg">
                    <thead class="text-xs text-white uppercase bg-red-700 border-b-4 border-red-500">
                        <tr>
                            <th scope="col" class="px-6 py-3 rounded-tl-xl">ID Supplier</th>
                            <th scope="col" class="px-6 py-3">Nama Supplier</th>
                            <th scope="col" class="px-6 py-3">Alamat</th>
                            <th scope="col" class="px-6 py-3">Kota</th>
                            <th scope="col" class="px-6 py-3">No Telp</th>
                            <th scope="col" class="px-6 py-3">Rata Waktu Pengiriman</th>
                            <th scope="col" class="px-6 py-3 text-center rounded-tr-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                        <tr class="text-left text-xs border-b even:bg-red-50 odd:bg-white hover:bg-red-100 transition duration-100">
                            <td scope="row" class="px-6 py-2 font-semibold">{{ $supplier->id_supplier }}</td>
                            <td class="px-6 py-2">{{ $supplier->namaSupplier }}</td>
                            <td class="px-6 py-2">{{ $supplier->alamatSupplier }}</td>
                            <td class="px-6 py-2">{{ $supplier->Kota }}</td>
                            <td class="px-6 py-2 font-mono">{{ $supplier->noTelepon }}</td>
                            <td class="px-6 py-2 font-medium text-red-600">{{ $supplier->waktuPengiriman }} hari</td>

                            <!-- Action Column -->
                            <td class="px-6 py-2 text-center whitespace-nowrap flex justify-center items-center gap-3">
                                <!-- SHOW -->
                                <a href="{{ route('suppliers.show', $supplier->id_supplier) }}"
                                    class="text-gray-500 hover:text-blue-600 p-1" title="Lihat Detail">
                                    <i class="fa-solid fa-eye cursor-pointer text-sm"></i>
                                </a>

                                <!-- EDIT -->
                                <a href="{{ route('suppliers.edit', $supplier->id_supplier) }}"
                                    class="text-gray-500 hover:text-amber-600 p-1" title="Edit Supplier">
                                    <i class="fa-solid fa-pen-to-square cursor-pointer text-sm"></i>
                                </a>

                                <!-- Delete -->
                                <form action="{{ route('suppliers.destroy', $supplier->id_supplier) }}"
                                    class="inline-block"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus supplier {{ $supplier->namaSupplier }}? Aksi ini tidak dapat dibatalkan.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-gray-500 hover:text-red-700 p-1" title="Hapus Supplier">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
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

@endsection