@extends('layouts.app')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-11 gap-4">
    <div class="relative col-span-5 text-gray-600 bg-white rounded-xl">
        <input type="search" name="search" placeholder="Search" class=" w-xl h-10 px-5 text-sm focus:outline-none">
        <button type="submit" class="absolute right-0 top-0 mt-3 mr-4">
            <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 56.966 56.966" style="enable-background:new 0 0 56.966 56.966;" xml:space="preserve" width="512px" height="512px">
                <path d="M55.146,51.887L41.588,37.786c3.486-4.144,5.396-9.358,5.396-14.786c0-12.682-10.318-23-23-23s-23,10.318-23,23  s10.318,23,23,23c4.761,0,9.298-1.436,13.177-4.162l13.661,14.208c0.571,0.593,1.339,0.92,2.162,0.92  c0.779,0,1.518-0.297,2.079-0.837C56.255,54.982,56.293,53.08,55.146,51.887z M23.984,6c9.374,0,17,7.626,17,17s-7.626,17-17,17  s-17-7.626-17-17S14.61,6,23.984,6z" />
            </svg>
        </button>
    </div>

</div>

<!-- TABEL -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white col-span-2">
    <table class="w-full text-sm text-left rtl:text-right text-gray-900 ">
            <thead class="text-xs text-gray-900 uppercase bg-gray-200">
                <tr>
                    <th scope="col" class="px-6 py-2">Kode Barang</th>
                    <th scope="col" class="px-6 py-2">Nama Barang</th>
                    <th scope="col" class="px-6 py-2">Qty Diterima</th>
                    <th scope="col" class="px-6 py-2">Satuan</th>
                </tr>
            </thead>
            <tbody>
                <tr class=" border-b border-gray-300">
                    <td class="px-6 py-2">10281</td>
                    <td class="px-6 py-2">Dummy Product</td>
                    <td class="px-6 py-2">1961</td>
                    <td class="px-6 py-2">Pcs</td>
                </tr>
                <tr class=" border-b border-gray-300">
                    <td class="px-6 py-2">10281</td>
                    <td class="px-6 py-2">Dummy Product</td>
                    <td class="px-6 py-2">1961</td>
                    <td class="px-6 py-2">Pcs</td>
                </tr>
                <tr class=" border-b border-gray-300">
                    <td class="px-6 py-2">10281</td>
                    <td class="px-6 py-2">Dummy Product</td>
                    <td class="px-6 py-2">1961</td>
                    <td class="px-6 py-2">Pcs</td>
                </tr>
                <tr class=" border-b border-gray-300">
                    <td class="px-6 py-2">10281</td>
                    <td class="px-6 py-2">Dummy Product</td>
                    <td class="px-6 py-2">1961</td>
                    <td class="px-6 py-2">Pcs</td>
                </tr>
                <tr class=" border-b border-gray-300">
                    <td class="px-6 py-2">10281</td>
                    <td class="px-6 py-2">Dummy Product</td>
                    <td class="px-6 py-2">1961</td>
                    <td class="px-6 py-2">Pcs</td>
                </tr>
            </tbody>
        </table>
    </div>

</div>


@endsection