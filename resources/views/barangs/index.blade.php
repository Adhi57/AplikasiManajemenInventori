@extends('layouts.app')
@section('page-title', 'Data Master / Barang')
@section('content')

<div class="p-4 sm:p-6 bg-gray-50 rounded-xl shadow-inner min-h-[80vh]">

    <div class="grid grid-cols-12 gap-4 items-center mb-6">
        
        <div class="relative col-span-12 md:col-span-8 text-gray-600">
            {{-- Form untuk menampung filter dan search. Method GET digunakan untuk filtering. --}}
            <form method="GET" action="{{ route('barangs.index') }}" class="flex flex-wrap items-center gap-3">
                
                {{-- Input Pencarian Teks --}}
                <input
                    type="search"
                    name="search"
                    id="search-input"
                    placeholder="Cari Kode atau Nama Barang..."
                    value="{{ request('search') }}"
                    class="flex-1 min-w-[200px] h-10 px-5 text-sm border border-gray-300 outline-none focus:ring-2 focus:ring-red-600 focus:border-red-600 rounded-xl transition duration-150 shadow-sm"
                >

                {{-- Dropdown Kategori --}}
                <select name="kategori_id" class="h-10 border border-gray-300 rounded-xl px-3 text-sm focus:ring-red-600 focus:border-red-600 shadow-sm outline-none">
                    <option value="">Semua Kategori</option>
                    @foreach ($kategoriBarangs as $kategori)
                    <option value="{{ $kategori->kategori_barang_id }}"
                        {{ request('kategori_id') == $kategori->kategori_barang_id ? 'selected' : '' }}>
                        {{ $kategori->nama_kategori_barang }}
                    </option>
                    @endforeach
                </select>

                {{-- Tombol Filter --}}
                <button type="submit" class="bg-red-700 text-white px-4 py-2 rounded-xl shadow-lg hover:bg-red-800 transition duration-150 ease-in-out">
                    <i class="fa-solid fa-magnifying-glass"></i> Filter
                </button>
            </form>
        </div>
        <div class="col-span-12 md:col-span-4 flex justify-end">
            <a href="{{ route('barangs.create') }}"
                class="bg-red-700 text-white font-semibold px-4 py-2 rounded-xl shadow-lg hover:bg-red-800 transition duration-150 ease-in-out flex items-center gap-2">
                <i class="fa-solid fa-plus text-sm"></i>
                Tambah Barang Baru
            </a>
        </div>
    </div>


    <div class="my-3">
        @if(session('success'))
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded-lg shadow-sm font-medium" role="alert">
            {{ session('success') }}
        </div>
        @endif
        
        <div class="bg-white rounded-xl overflow-hidden shadow-2xl border border-gray-200">
            <div class="p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">Daftar Barang</h2>

                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table id="barang-table" class="w-full text-sm text-left rtl:text-right text-gray-700">
                        <thead class="text-xs text-white uppercase bg-red-700 border-b-4 border-red-500 sticky top-0 z-10">
                            <tr>
                                <th scope="col" class="px-3 py-3 text-center rounded-tl-xl">Kode</th>
                                <th scope="col" class="px-6 py-3">Nama Barang</th>
                                <th scope="col" class="px-3 py-3 text-center">Satuan Jual</th>
                                <th scope="col" class="px-6 py-3">Kategori</th>
                                <th scope="col" class="px-6 py-3">Supplier</th>
                                <th scope="col" class="px-3 py-3 text-center">Jml/Karton</th>
                                <th scope="col" class="px-3 py-3 text-center">Tipe Harga Jual</th>
                                <th scope="col" class="px-6 py-3 text-left">H. Jual (Unit)</th>
                                <th scope="col" class="px-6 py-3 text-left">H. Beli (Karton)</th>
                                <th scope="col" class="px-6 py-3 text-center rounded-tr-xl">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($barangs as $barang)
                            <tr class=" text-left text-xs even:bg-red-50 odd:bg-white border-b hover:bg-red-100 transition duration-100">
                                <td scope="row" class="px-3 py-2 font-semibold text-center text-gray-900">{{ $barang->kode_barang }}</td>
                                <td class="px-6 py-2">{{ $barang->nama_barang }}</td>
                                <td class="px-3 py-2 text-center">{{ $barang->satuan_jual }}</td>
                                <td class="px-6 py-2">{{ $barang->kategori->nama_kategori_barang ?? '-' }}</td>
                                <td class="px-6 py-2">{{ $barang->supplier->namaSupplier ?? '-'}}</td>
                                <td class="px-3 py-2 text-center">{{ $barang->jml_barang_per_karton }}</td>
                                
                                <td class="px-3 py-2 text-center">
                                    <span class="font-medium px-2 py-0.5 rounded-full text-white text-[10px] uppercase tracking-wider
                                        @if($barang->tipe_harga_barang == 'Eceran') bg-blue-600 shadow-md shadow-blue-300
                                        @elseif($barang->tipe_harga_barang == 'Grosir') bg-green-600 shadow-md shadow-green-300
                                        @else bg-red-600 shadow-md shadow-red-300 @endif">
                                        {{ $barang->tipe_harga_barang }}
                                    </span>
                                </td>
                                <td class="px-6 py-2 font-medium text-gray-800">Rp{{ number_format($barang->harga_jual, 0, ',', '.')}}</td>
                                <td class="px-6 py-2 font-medium text-gray-800">Rp{{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                                
                                <td class="px-6 py-2 text-center whitespace-nowrap">
                                    <a href="{{ route('barangs.show', $barang->kode_barang) }}"
                                        class="inline-block text-gray-500 hover:text-blue-700 transition duration-150 p-1">
                                        <i class="fa-solid fa-eye cursor-pointer text-sm"></i>
                                    </a>

                                    <a href="{{ route('barangs.edit', $barang->kode_barang) }}"
                                        class="inline-block text-gray-500 hover:text-amber-600 transition duration-150 p-1">
                                        <i class="fa-solid fa-pen-to-square cursor-pointer text-sm"></i>
                                    </a>
                                    
                                    <form action="{{ route('barangs.destroy', $barang->kode_barang) }}" method="POST" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            onclick="return confirm('Yakin ingin menghapus data barang {{ $barang->nama_barang }}? Aksi ini tidak dapat dibatalkan.')"
                                            class="text-gray-500 hover:text-red-700 transition duration-150 p-1">
                                            <i class="fa-solid fa-trash-can cursor-pointer text-sm"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="p-4 border-t bg-gray-50">
                {{ $barangs->appends(request()->query())->links() }}
            </div>
            
        </div>

    </div>
</div>

@endsection