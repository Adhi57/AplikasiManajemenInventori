@extends('layouts.app')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <div class=" bg-linear-65 from-red-700 to-red-500 p-3 pl-6 rounded-lg shadow-md">
        <h2 class="text-sm font-medium text-gray-200 mb-2">Total Products</h2>
        <p class="text-4xl font-semibold text-gray-200">450</p>
    </div>
    <div class=" bg-linear-65 from-red-700 to-red-500 p-3 pl-6 rounded-lg shadow-md">
        <h2 class="text-sm font-medium text-gray-200 mb-2">Total Products</h2>
        <p class="text-3xl font-semibold text-gray-200">450</p>
    </div>
    <div class=" bg-linear-65 from-red-700 to-red-500 p-3 pl-6 rounded-lg shadow-md">
        <h2 class="text-sm font-medium text-gray-200 mb-2">Total Products</h2>
        <p class="text-3xl font-semibold text-gray-200">450</p>
    </div>
    <div class=" bg-linear-65 from-red-700 to-red-500 p-3 pl-6 rounded-lg shadow-md">
        <h2 class="text-sm font-medium text-gray-200 mb-2">Total Products</h2>
        <p class="text-3xl font-semibold text-gray-200">450</p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-4 lg:grid-cols-12 gap-3 my-8">

    <div class="bg-white p-2 px-4 rounded-lg col-span-3">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Ringkasan</h2>

            <el-dropdown class="inline-block" x-data="{ selectedFilter: 'Minggu ini' }">
                <button class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white/10 px-3 py-2 text-xs font-semibold text-gray-500 ring-1 ring-inset ring-gray-300 hover:bg-gray-100 items-center">
                    <span x-text="selectedFilter">Minggu ini</span>
                    <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="-mr-1 size-5 text-gray-400">
                        <path d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                    </svg>
                </button>
                <el-menu anchor="bottom end" popover class="w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none transition data-closed:scale-95 data-closed:opacity-0">
                    <div class="py-1">
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" x-on:click.prevent="selectedFilter = 'Minggu ini'">Minggu ini</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" x-on:click.prevent="selectedFilter = 'Tahun ini'">Bulan ini</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" x-on:click.prevent="selectedFilter = 'Setiap Waktu'">Tahun ini</a>
                    </div>
                </el-menu>
            </el-dropdown>
        </div>

        <h3 class=" text-sm text-gray-900 font-semibold">Jumlah Total Barang dalam Stok</h3>
        <div class=" flex items-baseline">
            <p class="text-4xl font-semibold text-gray-900 mr-2">450</p>
            <p class="text-sm text-gray-700">pcs</p>
        </div>

        <h3 class=" mt-7 text-sm text-gray-900 font-semibold">Barang Masuk</h3>
        <div class=" flex items-baseline">
            <p class="text-4xl font-semibold text-gray-900 mr-2">450</p>
            <p class="text-sm text-gray-700">pcs</p>
        </div>

        <h3 class=" mt-7 text-sm text-gray-900 font-semibold">Barang Keluar</h3>
        <div class=" flex items-baseline mb-7">
            <p class="text-4xl font-semibold text-gray-900 mr-2">450</p>
            <p class="text-sm text-gray-700">pcs</p>
        </div>
    </div>

    <div class="bg-white p-2 px-4 rounded-lg col-span-5">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Informasi Stok</h2>
        </div>

        <div class="flex flex-col justify-center items-center p-4">
            <div id="hs-doughnut-chart"></div>

            <div class="flex justify-center sm:justify-end items-center gap-x-4 mt-3 sm:mt-6">
                <div class="inline-flex items-center">
                    <span class="size-2.5 inline-block bg-green-600 rounded-sm me-2"></span>
                    <span class="text-[13px] text-gray-600 dark:text-neutral-400">Stok Baik</span>
                </div>
                <div class="inline-flex items-center">
                    <span class="size-2.5 inline-block bg-red-700 rounded-sm me-2"></span>
                    <span class="text-[13px] text-gray-600 dark:text-neutral-400">Stok Rusak</span>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white p-2 px-4 rounded-lg col-span-4">
        <h2 class=" text-lg font-semibold text-gray-900 mb-4">Barang Terlaris</h2>

        <div class="bg-neutral-100 flex min-w-0 items-center p-2 rounded-md mb-2">
            <div class="flex-shrink-0 w-16 h-16 mr-4">
                <img src="https://via.placeholder.com/64x64.png?text=Santan" alt="Santan Bubuk Tabura" class="object-cover w-full h-full rounded-md">
            </div>

            <div class="flex-grow min-w-0">
                <p class=" text-base font-semibold text-gray-900 truncate"> Santan Bubuk Taburaaaaaaaaaahhh</p>
                <p class="text-sm text-gray-500">Bumbu</p>
            </div>

            <div class="flex flex-col items-end text-right ml-4">
                <p class="text-sm text-gray-600 mb-1">Tersedia</p>
                <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-green-200 text-green-800 rounded-full">
                    180karton
                </span>
            </div>
        </div>

        <div class="bg-neutral-100 flex min-w-0 items-center p-2 rounded-md mb-2">
            <div class="flex-shrink-0 w-16 h-16 mr-4">
                <img src="https://via.placeholder.com/64x64.png?text=Santan" alt="Santan Bubuk Tabura" class="object-cover w-full h-full rounded-md">
            </div>

            <div class="flex-grow min-w-0">
                <p class=" text-base font-semibold text-gray-900 truncate"> Santan Bubuk Taburhhhhhhaaaaaaaaaaaaaa</p>
                <p class="text-sm text-gray-500">Bumbu</p>
            </div>

            <div class="flex flex-col items-end text-right ml-4">
                <p class="text-sm text-gray-600 mb-1">Tersedia</p>
                <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-green-200 text-green-800 rounded-full">
                    180karton
                </span>
            </div>
        </div>

        <div class="bg-neutral-100 flex min-w-0 items-center p-2 rounded-md mb-2">
            <div class="flex-shrink-0 w-16 h-16 mr-4">
                <img src="https://via.placeholder.com/64x64.png?text=Santan" alt="Santan Bubuk Tabura" class="object-cover w-full h-full rounded-md">
            </div>

            <div class="flex-grow min-w-0">
                <p class=" text-base font-semibold text-gray-900 truncate"> Santan Bubuk Taburhhhhhhaaaaaaaaaaaaaa</p>
                <p class="text-sm text-gray-500">Bumbu</p>
            </div>

            <div class="flex flex-col items-end text-right ml-4">
                <p class="text-sm text-gray-600 mb-1">Tersedia</p>
                <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-green-200 text-green-800 rounded-full">
                    180karton
                </span>
            </div>
        </div>

        <div class="bg-neutral-100 flex min-w-0 items-center p-2 rounded-md mb-2">
            <div class="flex-shrink-0 w-16 h-16 mr-4">
                <img src="https://via.placeholder.com/64x64.png?text=Santan" alt="Santan Bubuk Tabura" class="object-cover w-full h-full rounded-md">
            </div>

            <div class="flex-grow min-w-0">
                <p class=" text-base font-semibold text-gray-900 truncate"> Santan Bubuk Taburhhhhhhaaaaaaaaaaaaaa</p>
                <p class="text-sm text-gray-500">Bumbu</p>
            </div>

            <div class="flex flex-col items-end text-right ml-4">
                <p class="text-sm text-gray-600 mb-1">Tersedia</p>
                <span class="inline-flex items-center px-3 py-1 text-xs font-medium bg-green-200 text-green-800 rounded-full">
                    180karton
                </span>
            </div>
        </div>
    </div>

    <div class="bg-white col-span-7 p-4 rounded-xl overflow-x-auto">
        <h2 class="text-lg rounded-lg font-semibold text-gray-900 mb-4">Barang Masuk Minggu ini</h2>

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

        <div class="grid grid-cols-6">
            <button type="button" class=" col-span-4 col-start-2 focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm my-5 px-5 py-1 ml-4">Lihat Selengkapnya</button>
        </div>
    </div>

    <div class="bg-white col-span-5 rounded-xl py-6">
        <h2 class="text-lg px-4 rounded-lg font-semibold text-gray-900 mb-4">Surat Jalan Menunggu Approval</h2>

        <div class="bg-neutral-100 flex min-w-0 justify-between items-center mx-4 p-2 rounded-md mb-2">
            <div class="bg-red-700 rounded-2xl text-white text-2xl p-2 min-w-12 text-center mr-4">
                <i class="fa-solid fa-file"></i>
            </div>
            <div class="flex-grow min-w-0">
                <p class=" text-base font-semibold text-gray-900 truncate"> SJ-20252904-025-0023</p>
                <p class="text-sm text-gray-500">Admin Gudang</p>
            </div>
            <button type="button" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-1 ml-4">Lihat</button>
        </div>
        <div class="bg-neutral-100 flex min-w-0 justify-between items-center mx-4 p-2 rounded-md mb-2">
            <div class="bg-red-700 rounded-2xl text-white text-2xl p-2 min-w-12 text-center mr-4">
                <i class="fa-solid fa-file"></i>
            </div>
            <div class="flex-grow min-w-0">
                <p class=" text-base font-semibold text-gray-900 truncate"> SJ-20252904-025-0023</p>
                <p class="text-sm text-gray-500">Admin Gudang</p>
            </div>
            <button type="button" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-1 ml-4">Lihat</button>
        </div>
        <div class="bg-neutral-100 flex min-w-0 justify-between items-center mx-4 p-2 rounded-md mb-2">
            <div class="bg-red-700 rounded-2xl text-white text-2xl p-2 min-w-12 text-center mr-4">
                <i class="fa-solid fa-file"></i>
            </div>
            <div class="flex-grow min-w-0">
                <p class=" text-base font-semibold text-gray-900 truncate"> SJ-20252904-025-0023</p>
                <p class="text-sm text-gray-500">Admin Gudang</p>
            </div>
            <button type="button" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-1 ml-4">Lihat</button>
        </div>
        <div class="bg-neutral-100 flex min-w-0 justify-between items-center mx-4 p-2 rounded-md mb-2">
            <div class="bg-red-700 rounded-2xl text-white text-2xl p-2 min-w-12 text-center mr-4">
                <i class="fa-solid fa-file"></i>
            </div>
            <div class="flex-grow min-w-0">
                <p class=" text-base font-semibold text-gray-900 truncate"> SJ-20252904-025-0023</p>
                <p class="text-sm text-gray-500">Admin Gudang</p>
            </div>
            <button type="button" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-1 ml-4">Lihat</button>
        </div>
    </div>

    <div class="bg-white col-span-5 rounded-xl py-6">
        <h2 class="text-lg px-4 rounded-lg font-semibold text-gray-900 mb-4">Purchase Order Menunggu Approval</h2>

        <div class="bg-neutral-100 flex min-w-0 justify-between items-center mx-4 p-2 rounded-md mb-2">
            <div class="bg-red-700 rounded-2xl text-white text-2xl p-2 min-w-12 text-center mr-4">
                <i class="fa-solid fa-file"></i>
            </div>
            <div class="flex-grow min-w-0">
                <p class=" text-base font-semibold text-gray-900 truncate"> SJ-20252904-025-0023</p>
                <p class="text-sm text-gray-500">Admin Gudang</p>
            </div>
            <button type="button" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-1 ml-4">Lihat</button>
        </div>
        <div class="bg-neutral-100 flex min-w-0 justify-between items-center mx-4 p-2 rounded-md mb-2">
            <div class="bg-red-700 rounded-2xl text-white text-2xl p-2 min-w-12 text-center mr-4">
                <i class="fa-solid fa-file"></i>
            </div>
            <div class="flex-grow min-w-0">
                <p class=" text-base font-semibold text-gray-900 truncate"> SJ-20252904-025-0023</p>
                <p class="text-sm text-gray-500">Admin Gudang</p>
            </div>
            <button type="button" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-1 ml-4">Lihat</button>
        </div>
        <div class="bg-neutral-100 flex min-w-0 justify-between items-center mx-4 p-2 rounded-md mb-2">
            <div class="bg-red-700 rounded-2xl text-white text-2xl p-2 min-w-12 text-center mr-4">
                <i class="fa-solid fa-file"></i>
            </div>
            <div class="flex-grow min-w-0">
                <p class=" text-base font-semibold text-gray-900 truncate"> SJ-20252904-025-0023</p>
                <p class="text-sm text-gray-500">Admin Gudang</p>
            </div>
            <button type="button" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-1 ml-4">Lihat</button>
        </div>
        <div class="bg-neutral-100 flex min-w-0 justify-between items-center mx-4 p-2 rounded-md mb-2">
            <div class="bg-red-700 rounded-2xl text-white text-2xl p-2 min-w-12 text-center mr-4">
                <i class="fa-solid fa-file"></i>
            </div>
            <div class="flex-grow min-w-0">
                <p class=" text-base font-semibold text-gray-900 truncate"> SJ-20252904-025-0023</p>
                <p class="text-sm text-gray-500">Admin Gudang</p>
            </div>
            <button type="button" class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-1 ml-4">Lihat</button>
        </div>
    </div>

</div>

@endsection

@push('scripts')

<script src="{{ asset('assets/js/hs-apexcharts-helpers.js') }}"></script>

<script>
    window.addEventListener('load', () => {
        // Apex Doughnut Chart
        (function() {
            if (typeof buildChart === 'function') {
                buildChart('#hs-doughnut-chart', (mode) => ({
                    chart: {
                        height: 230,
                        width: 230,
                        type: 'donut',
                        zoom: {
                            enabled: false
                        }
                    },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '76%'
                            }
                        }
                    },
                    series: [47, 23],
                    labels: ['Stok Baik', 'Stok Rusak'],
                    legend: {
                        show: false
                    },
                    dataLabels: {
                        enabled: false
                    },
                    stroke: {
                        width: 5
                    },
                    grid: {
                        padding: {
                            top: -12,
                            bottom: -11,
                            left: -12,
                            right: -12
                        }
                    },
                    states: {
                        hover: {
                            filter: {
                                type: 'none'
                            }
                        }
                    },
                    tooltip: {
                        enabled: true,
                        custom: function(props) {
                            return buildTooltipForDonut(
                                props,
                                mode === 'dark' ? ['#fff', '#fff', '#000'] : ['#fff', '#fff', '#000']
                            );
                        }
                    }
                }), {
                    colors: ['#54BC1C', '#F90000'],
                    stroke: {
                        colors: ['rgb(255, 255, 255)']
                    }
                }, {
                    colors: ['#54BC1C', '#F90000'],
                    stroke: {
                        colors: ['rgb(38, 38, 38)']
                    }
                });
            } else {
                console.error("Fungsi buildChart tidak ditemukan. Pastikan hs-apexcharts-helpers.js dimuat dengan benar.");
            }
        })();
    });
</script>
@endpush