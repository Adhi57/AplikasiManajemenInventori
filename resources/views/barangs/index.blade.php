@extends('layouts.app')

@section('content')


<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-12 gap-4">
    <div class="relative col-span-5 text-gray-600">
        <input
            type="search"
            name="search"
            placeholder="Cari Kode atau Nama Barang..."
            class="w-full h-10 px-5 text-sm border-0.5 outline-none focus:ring-0 rounded-xl">
        <button type="submit" class="absolute right-0 top-0 mt-3 mr-4">
            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 56.966 56.966" width="16" height="16">
                <path
                    d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23 	
			 			s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92 	
			 			c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z 
			 			M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17 	
			 			s-17-7.626-17-17S14.61,6,23.984,6z" />
            </svg>
        </button>
    </div>
</div>


<!-- TABEL -->

<div class="my-3">

    <div class="bg-white rounded-lg grid-cols-10 overflow-x-auto shadow-md">
        @if(session('success'))
        <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif
        <div class="p-6">
            <!-- Header + Button -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Daftar Barang</h2>
                <a href="{{ route('barangs.create') }}"
                    class="bg-indigo-500 text-white px-4 py-2 rounded-lg shadow hover:bg-indigo-600 transition duration-150 ease-in-out">
                    + Tambah Barang Baru
                </a>
            </div>

            <table class="w-full text-sm text-left rtl:text-right text-gray-700 rounded-lg">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b-2">
                    <tr>
                        <th scope="col" class="px-3 py-3 text-center">Kode</th>
                        <th scope="col" class="px-6 py-3">Nama Barang</th>
                        <th scope="col" class="px-3 py-3 text-center">Satuan</th>
                        <th scope="col" class="px-6 py-3">Kategori</th>
                        <th scope="col" class="px-6 py-3">Supplier</th>
                        <th scope="col" class="px-3 py-3 text-center">Jml/Karton</th>
                        <!-- KOLOM HARGA BARU -->
                        <th scope="col" class="px-3 py-3 text-center">Tipe Harga</th>
                        <th scope="col" class="px-6 py-3 text-right">H. Beli</th>
                        <th scope="col" class="px-6 py-3 text-right">H. Jual</th>
                        <th scope="col" class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($barangs as $barang)
                    <tr class=" text-left text-xs even:bg-indigo-50 odd:bg-white border-b hover:bg-indigo-100 transition duration-100">
                        <td scope="row" class="px-3 py-2 font-medium text-center text-gray-900">{{ $barang->kode_barang }}</td>
                        <td class="px-6 py-2">{{ $barang->nama_barang }}</td>
                        <td class="px-3 py-2 text-center">{{ $barang->satuan_terkecil }}</td>
                        <td class="px-6 py-2">{{ $barang->kategori->nama_kategori_barang ?? '-' }}</td>
                        <td class="px-6 py-2">{{ $barang->supplier->namaSupplier ?? '-'}}</td>
                        <td class="px-3 py-2 text-center">{{ $barang->jml_barang_per_karton }}</td>
                        
                        <!-- DATA KOLOM HARGA BARU -->
                        <td class="px-3 py-2 text-center">
                            <span class="font-semibold px-2 py-0.5 rounded-full text-white 
                                @if($barang->tipe_harga_barang == 'Eceran') bg-blue-500
                                @elseif($barang->tipe_harga_barang == 'Grosir') bg-green-500
                                @else bg-red-500 @endif">
                                {{ $barang->tipe_harga_barang }}
                            </span>
                        </td>
                        <td class="px-6 py-2 text-right">Rp{{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
                        <td class="px-6 py-2 text-right">Rp{{ number_format($barang->harga_jual, 0, ',', '.') }}</td>
                        
                        <td class="px-6 py-2 text-center whitespace-nowrap">
                        <a href="{{ route('barangs.show', $barang->kode_barang) }}"
                          class="text-indigo-600 hover:text-indigo-900">
                          <i class="fa-solid fa-eye cursor-pointer text-blue-700 mx-1"></i>
                        </a>

                        <!-- EDIT -->
                        <a href="{{ route('barangs.edit', $barang->kode_barang) }}"
                          class="text-indigo-600 hover:text-indigo-900">
                          <i class="fa-solid fa-pen cursor-pointer text-amber-300 mx-1"></i>
                        </a>
                            <form action="{{ route('barangs.destroy', $barang->kode_barang) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Yakin ingin menghapus data barang {{ $barang->nama_barang }}?')">
                                    <i class="fa-solid fa-trash cursor-pointer text-red-600 hover:text-red-800 transition mx-1"></i>
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
