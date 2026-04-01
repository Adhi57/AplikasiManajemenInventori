@extends('layouts.app')

@section('page-title', 'Data Master / Supplier')

@section('content')

    <x-page-header title="Data Supplier" description="Kelola informasi supplier dan pemasok" icon="fa-truck-field">
    <x-slot name="actions">
        <a href="{{ route('suppliers.create') }}" class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-red-950 shadow-md transition-all text-sm font-bold hover:scale-105 duration-200">
            <i class="fa-solid fa-plus"></i>
            <span>Tambah Supplier</span>
        </a>
    </x-slot>
</x-page-header>

    {{-- Search --}}
    <div class="mb-6">
        <div class="relative max-w-md">
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="search" placeholder="Cari nama, kota, atau no. telepon supplier..."
                class="w-full h-10 pl-9 pr-4 text-sm border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-red-500 transition shadow-sm">
        </div>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="mb-4 bg-white rounded-2xl border border-emerald-200 shadow-sm overflow-hidden">
            <div class="px-5 py-3 bg-emerald-50 flex items-center gap-2">
                <i class="fa-solid fa-circle-check text-emerald-600"></i>
                <span class="text-sm font-medium text-emerald-700">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                <i class="fa-solid fa-table-list text-red-500"></i>
                Daftar Supplier
            </h3>
        </div>
        <div class="p-5">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-700">
                    <thead class="text-xs text-white uppercase bg-red-800">
                        <tr>
                            <th class="px-5 py-3 rounded-tl-xl">ID</th>
                            <th class="px-5 py-3">Nama Supplier</th>
                            <th class="px-5 py-3">Alamat</th>
                            <th class="px-5 py-3">Kota</th>
                            <th class="px-5 py-3">No Telp</th>
                            <th class="px-5 py-3">Waktu Kirim</th>
                            <th class="px-5 py-3 text-center rounded-tr-xl">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr
                                class="text-xs border-b border-gray-100 even:bg-gray-50/50 hover:bg-red-50 transition duration-100">
                                <td class="px-5 py-2.5 font-semibold text-gray-900">{{ $supplier->id_supplier }}</td>
                                <td class="px-5 py-2.5 font-medium">{{ $supplier->namaSupplier }}</td>
                                <td class="px-5 py-2.5">{{ $supplier->alamatSupplier }}</td>
                                <td class="px-5 py-2.5">{{ $supplier->Kota }}</td>
                                <td class="px-5 py-2.5 font-mono text-gray-600">{{ $supplier->noTelepon }}</td>
                                <td class="px-5 py-2.5">
                                    <span
                                        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                                        <i class="fa-solid fa-clock text-[8px]"></i>
                                        {{ $supplier->waktuPengiriman }} hari
                                    </span>
                                </td>
                                <td class="px-5 py-2.5 text-center whitespace-nowrap">
                                    <a href="{{ route('suppliers.show', $supplier->id_supplier) }}"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition"
                                        title="Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('suppliers.edit', $supplier->id_supplier) }}"
                                        class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-amber-600 hover:bg-amber-50 transition"
                                        title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('suppliers.destroy', $supplier->id_supplier) }}" method="POST"
                                        class="inline-block"
                                        onsubmit="return confirm('Yakin ingin menghapus supplier {{ $supplier->namaSupplier }}?');">
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

@endsection