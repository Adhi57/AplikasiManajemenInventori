@extends('layouts.app')
@section('page-title', 'Stock Opname')

@section('content')

<div class="max-w-screen mx-auto p-6">

    <h1 class="text-2xl font-bold mb-6 text-gray-800">Stock Opname</h1>

    @if(session('success'))
    <div class="p-3 mb-4 bg-green-100 text-green-700 rounded-md">
        {{ session('success') }}
    </div>
    @endif

    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-md">
        <table class="w-full text-sm text-gray-700">
            <thead>
                <tr class="bg-red-600 text-white uppercase text-xs font-semibold tracking-wider">
                    <th class="p-4 text-left">Kode Barang</th>
                    <th class="p-4 text-left">Nama Barang</th>
                    <th class="p-4 text-center">Stok Baik</th>
                    <th class="p-4 text-center">Stok Rusak</th>
                    <th class="p-4 text-center">Kadaluarsa</th>
                    <th class="p-4 text-left">Alasan Update</th>
                    <th class="p-4 text-center">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @foreach($stok as $item)
                <tr class="border-b border-gray-100 hover:bg-red-50 transition duration-100">

                    <form action="{{ route('stock.opname.update') }}" method="POST" class="contents">
                        @csrf

                        {{-- Hidden ID --}}
                        <input type="hidden" name="stok_id" value="{{ $item->id }}">

                        {{-- KODE --}}
                        <td class="p-4 font-medium">
                            {{ $item->kode_barang }}
                        </td>

                        {{-- NAMA --}}
                        <td class="p-4">
                            {{ $item->barang->nama_barang ?? '-' }}
                        </td>

                        {{-- STOK BAIK --}}
                        <td class="p-4 text-center">
                            <input type="number" name="jumlah_stok"
                                class="w-24 border border-gray-300 rounded-lg px-2 py-1 text-sm 
                                       focus:ring-red-600 focus:border-red-600"
                                value="{{ $item->jumlah_stok }}">
                        </td>

                        {{-- STOK RUSAK --}}
                        <td class="p-4 text-center">
                            <input type="number" name="jumlah_stok_rusak"
                                class="w-24 border border-gray-300 rounded-lg px-2 py-1 text-sm 
                                       focus:ring-red-600 focus:border-red-600"
                                value="{{ $item->jumlah_stok_rusak }}">
                        </td>

                        {{-- KADALUARSA --}}
                        <td class="p-4 text-center">
                            <input type="date" name="tgl_kadaluarsa"
                                class="border border-gray-300 rounded-lg px-2 py-1 text-sm 
                                       focus:ring-red-600 focus:border-red-600"
                                value="{{ $item->tgl_kadaluarsa }}">
                        </td>

                        {{-- ALASAN --}}
                        <td class="p-4">
                            <input type="text" name="alasan"
                                placeholder="Alasan update..."
                                class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm 
                                       focus:ring-red-600 focus:border-red-600"
                                required>
                        </td>

                        {{-- AKSI --}}
                        <td class="p-4 text-center">
                            <button
                                class="px-4 py-1.5 bg-red-700 text-white rounded-lg text-xs font-semibold 
                                       shadow hover:bg-red-800 transition">
                                Update
                            </button>
                        </td>

                    </form>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
