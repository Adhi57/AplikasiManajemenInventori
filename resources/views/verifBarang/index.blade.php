@extends('layouts.app')

@section('content')
@section('page-title', 'Verifikasi Barang')

<div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-12 gap-3 my-8">
    <div class="bg-white col-span-4 p-4 rounded-xl overflow-x-auto">
        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
                        <div class="py-3 px-4">
                            <div class="relative max-w-xs">
                                <label class="sr-only">Search</label>
                                <input type="text" name="hs-table-with-pagination-search" id="hs-table-with-pagination-search" class="py-1.5 sm:py-2 px-3 ps-9 block w-full border-gray-200 shadow-2xs rounded-lg sm:text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="Cari Surat PO">
                                <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                    <svg class="size-4 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="m21 21-4.3-4.3"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 ">
                                    <tr>
                                        <th scope="col" class="py-3 px-4 pe-0">
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium ">Pilih Salah Satu Surat PO</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="py-3 ps-4">
                                            <div class="flex items-center h-5">
                                                <input id="hs-table-pagination-checkbox-1" type="checkbox" class="border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 ">
                                                <label for="hs-table-pagination-checkbox-1" class="sr-only">Checkbox</label>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 ">632</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="py-1 px-4">
                            <nav class="flex items-center space-x-1" aria-label="Pagination">
                                <button type="button" class="p-2.5 min-w-10 inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none " aria-label="Previous">
                                    <span aria-hidden="true">«</span>
                                    <span class="sr-only">Previous</span>
                                </button>
                                <button type="button" class="min-w-10 flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none " aria-current="page">1</button>
                                <button type="button" class="min-w-10 flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none ">2</button>
                                <button type="button" class="min-w-10 flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none ">3</button>
                                <button type="button" class="p-2.5 min-w-10 inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none " aria-label="Next">
                                    <span class="sr-only">Next</span>
                                    <span aria-hidden="true">»</span>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-6 gap-4">
            <button type="button" class=" col-span-2 focus:outline-none text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:ring-gray-500 font-medium rounded-lg text-sm my-5 px-5 py-1 ml-4">Clear</button>
            <button type="button" class=" col-span-2 col-start-5 focus:outline-none text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm my-5 px-5 py-1 ml-4">Pilih</button>
        </div>
    </div>
    
    <div class="bg-white col-span-8 p-4 rounded-xl overflow-x-auto">
        <h2 class="text-lg rounded-lg font-semibold text-gray-900 mb-4">Daftar Barang</h2>

        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
                        <div class="py-3 px-4">
                            <div class="relative max-w-xs">
                                <label class="sr-only">Search</label>
                                <input type="text" name="hs-table-with-pagination-search" id="hs-table-with-pagination-search" class="py-1.5 sm:py-2 px-3 ps-9 block w-full border-gray-200 shadow-2xs rounded-lg sm:text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="Search for items">
                                <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                    <svg class="size-4 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="m21 21-4.3-4.3"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 ">
                                    <tr>
                                        <th scope="col" class="py-3 px-4 pe-0">
                                            <div class="flex items-center h-5">
                                                <input id="hs-table-pagination-checkbox-all" type="checkbox" class="border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 ">
                                                <label for="hs-table-pagination-checkbox-all" class="sr-only">Checkbox</label>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium ">ID Barang</th>
                                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium ">Nama Barang</th>
                                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium ">Satuan</th>
                                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium ">Quantity</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="py-3 ps-4">
                                            <div class="flex items-center h-5">
                                                <input id="hs-table-pagination-checkbox-1" type="checkbox" class="border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 ">
                                                <label for="hs-table-pagination-checkbox-1" class="sr-only">Checkbox</label>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 ">632</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 ">Dummy Product</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 ">Pcs</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 ">15</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="py-1 px-4">
                            <nav class="flex items-center space-x-1" aria-label="Pagination">
                                <button type="button" class="p-2.5 min-w-10 inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none " aria-label="Previous">
                                    <span aria-hidden="true">«</span>
                                    <span class="sr-only">Previous</span>
                                </button>
                                <button type="button" class="min-w-10 flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none " aria-current="page">1</button>
                                <button type="button" class="min-w-10 flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none ">2</button>
                                <button type="button" class="min-w-10 flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none ">3</button>
                                <button type="button" class="p-2.5 min-w-10 inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none " aria-label="Next">
                                    <span class="sr-only">Next</span>
                                    <span aria-hidden="true">»</span>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-6">
            <button type="button" class=" col-start-4 focus:outline-none text-gray-100 bg-green-600 hover:bg-green-800 focus:ring-4 focus:ring-green-800 font-medium rounded-lg text-sm my-5 px-5 py-1 ml-4">Konfirmasi Sesuai</button>
            <button type="button" class=" col-span-2 col-start-5 focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm my-5 px-5 py-1 ml-4">Tambah ke Daftar Retur</button>
        </div>
    </div>

    <div class="bg-white col-span-12 p-4 rounded-xl overflow-x-auto">
        <h2 class="text-lg rounded-lg font-semibold text-gray-900 mb-4">Daftar Barang Akan Diretur</h2>

        <div class="flex flex-col">
            <div class="-m-1.5 overflow-x-auto">
                <div class="p-1.5 min-w-full inline-block align-middle">
                    <div class="border border-gray-200 rounded-lg divide-y divide-gray-200">
                        <div class="py-3 px-4">
                            <div class="relative max-w-xs">
                                <label class="sr-only">Search</label>
                                <input type="text" name="hs-table-with-pagination-search" id="hs-table-with-pagination-search" class="py-1.5 sm:py-2 px-3 ps-9 block w-full border-gray-200 shadow-2xs rounded-lg sm:text-sm focus:z-10 focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none " placeholder="Search for items">
                                <div class="absolute inset-y-0 start-0 flex items-center pointer-events-none ps-3">
                                    <svg class="size-4 text-gray-400 dark:text-neutral-500" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="11" cy="11" r="8"></circle>
                                        <path d="m21 21-4.3-4.3"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50 ">
                                    <tr>
                                        <th scope="col" class="py-3 px-4 pe-0">
                                            <div class="flex items-center h-5">
                                                <input id="hs-table-pagination-checkbox-all" type="checkbox" class="border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 ">
                                                <label for="hs-table-pagination-checkbox-all" class="sr-only">Checkbox</label>
                                            </div>
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium ">ID Barang</th>
                                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium ">Nama Barang</th>
                                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium ">Satuan</th>
                                        <th scope="col" class="px-6 py-3 text-start text-xs font-medium ">Quantity</th>
                                        <th scope="col" class="px-6 py-3 text-end text-xs font-medium ">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <tr>
                                        <td class="py-3 ps-4">
                                            <div class="flex items-center h-5">
                                                <input id="hs-table-pagination-checkbox-1" type="checkbox" class="border-gray-200 rounded-sm text-blue-600 focus:ring-blue-500 ">
                                                <label for="hs-table-pagination-checkbox-1" class="sr-only">Checkbox</label>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-800 ">632</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 ">Dummy Product</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 ">Pcs</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800 ">15</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                                            <button type="button" class="inline-flex items-center gap-x-2 text-sm font-semibold rounded-lg border border-transparent text-red-600 hover:text-red-800 focus:outline-hidden focus:text-red-800 disabled:opacity-50 disabled:pointer-events-none ">Hapus</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="py-1 px-4">
                            <nav class="flex items-center space-x-1" aria-label="Pagination">
                                <button type="button" class="p-2.5 min-w-10 inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none " aria-label="Previous">
                                    <span aria-hidden="true">«</span>
                                    <span class="sr-only">Previous</span>
                                </button>
                                <button type="button" class="min-w-10 flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none " aria-current="page">1</button>
                                <button type="button" class="min-w-10 flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none ">2</button>
                                <button type="button" class="min-w-10 flex justify-center items-center text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 py-2.5 text-sm rounded-full disabled:opacity-50 disabled:pointer-events-none ">3</button>
                                <button type="button" class="p-2.5 min-w-10 inline-flex justify-center items-center gap-x-2 text-sm rounded-full text-gray-800 hover:bg-gray-100 focus:outline-hidden focus:bg-gray-100 disabled:opacity-50 disabled:pointer-events-none " aria-label="Next">
                                    <span class="sr-only">Next</span>
                                    <span aria-hidden="true">»</span>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-6">
            <button type="button" class=" col-start-4 focus:outline-none text-gray-700 bg-gray-200 hover:bg-gray-300 focus:ring-4 focus:ring-gray-500 font-medium rounded-lg text-sm my-5 px-5 py-1 ml-4">Clear</button>
            <button type="button" class=" col-span-2 col-start-5 focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm my-5 px-5 py-1 ml-4">Konfirmasi Retur</button>
        </div>
    </div>
</div>

@endsection