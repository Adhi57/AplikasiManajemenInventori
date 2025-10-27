@extends('layouts.app') 

@section('content')
<div class="container mx-auto p-4" x-data="purchaseRequest()" x-init="fetchBarangs()">

    <h1 class="text-3xl font-bold mb-6 text-gray-800">Form Permintaan Pembelian Baru</h1>

    <div class="flex flex-wrap -mx-4">
        
        <div class="w-full lg:w-7/12 px-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-xl">
                <h2 class="text-2xl font-semibold mb-4 border-b pb-2">Katalog Barang Master</h2>

                <div class="flex flex-wrap gap-4 mb-4 items-end">
                    <div class="flex-1">
                        <label for="search_input" class="block text-sm font-medium text-gray-700">Cari Barang</label>
                        {{-- Hapus @input dan debounce --}}
                        <input type="text" id="search_input" x-model="filters.search"
                               placeholder="Nama/Kode Barang..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    
                    <div>
                        <label for="kategori_select" class="block text-sm font-medium text-gray-700">Kategori</label>
                        {{-- Hapus @change --}}
                        <select id="kategori_select" x-model="filters.kategori"
                                class="px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            <option value="">Semua</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->kategori_barang_id }}">{{ $kategori->nama_kategori_barang }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="supplier_select" class="block text-sm font-medium text-gray-700">Supplier</label>
                        {{-- Hapus @change --}}
                        <select id="supplier_select" x-model="filters.supplier"
                                class="px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                            <option value="">Semua</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id_supplier }}">{{ $supplier->namaSupplier }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    {{-- TOMBOL FILTER BARU --}}
                    <button type="button" @click="fetchBarangs()"
                            class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md shadow-sm hover:bg-blue-700 transition duration-150 ease-in-out">
                        Filter
                    </button>
                </div>
                
                <div class="overflow-x-auto relative shadow-md sm:rounded-lg" style="max-height: 60vh;">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 sticky top-0">
                            <tr>
                                <th scope="col" class="py-3 px-6">Kode</th>
                                <th scope="col" class="py-3 px-6">Nama Barang</th>
                                <th scope="col" class="py-3 px-6">Supplier Default</th>
                                <th scope="col" class="py-3 px-6">Harga Beli</th>
                                <th scope="col" class="py-3 px-6">Aksi</th>
                            </tr>
                        </thead>
                        <tbody x-html="barangHtml">
                            {{-- Data awal dimuat oleh x-init --}}
                        </tbody>
                    </table>
                </div>
                
                {{-- Pagination Placeholder --}}
                <div x-html="paginationHtml" class="mt-4">
                    {{-- Pagination links akan diisi di sini oleh AJAX --}}
                </div>
                
                <div x-show="loading" class="text-center text-blue-500 py-2">Memuat barang...</div>
            </div>
        </div>
        
        <div class="w-full lg:w-5/12 px-4">
            <div class="bg-white p-6 rounded-lg shadow-xl sticky top-6">
                <h2 class="text-2xl font-semibold mb-4 border-b pb-2">Keranjang Permintaan 
                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2 py-0.5 rounded" x-text="Object.keys(cart).length">0</span> Item
                </h2>
                
                <form id="purchase-request-form" action="{{ route('purchase_orders.buat_permintaan') }}" method="POST">
                    @csrf
                    
                    {{-- Detail Header PO --}}
                    <div class="space-y-4 mb-6">
                        <div>
                            <label for="id_supplier" class="block text-sm font-medium text-gray-700">Supplier Utama (Wajib)</label>
                            <select name="id_supplier" id="id_supplier" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">-- Pilih Supplier --</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id_supplier }}">{{ $supplier->namaSupplier }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="tanggal_po" class="block text-sm font-medium text-gray-700">Tanggal Permintaan</label>
                            <input type="date" name="tanggal_po" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:ring-blue-500 focus:border-blue-500" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    
                    {{-- Detail Item Permintaan (Keranjang) --}}
                    <div class="max-h-80 overflow-y-auto mb-6 border rounded-lg p-2">
                        <table class="min-w-full text-sm text-gray-600">
                            <thead>
                                <tr class="border-b">
                                    <th class="py-2 text-left">Barang</th>
                                    <th class="py-2 text-center" style="width: 30%;">Qty</th>
                                    <th scope="col" class="py-3 px-6">Harga</th>
                                    <th class="py-2 text-center" style="width: 10%;">Hapus</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-if="Object.keys(cart).length === 0">
                                    <tr>
                                        <td colspan="3" class="text-center py-4 text-gray-500">Keranjang kosong. Tambahkan barang dari katalog.</td>
                                    </tr>
                                </template>
                                <template x-for="item in cart" :key="item.kode_barang">
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="py-2 px-1 text-sm font-medium text-gray-900" x-text="item.nama"></td>
                                        <td class="py-2 px-1">
                                            <input type="number" 
                                                   :name="'items[' + item.kode_barang + '][quantity]'" 
                                                   x-model.number="item.qty" 
                                                   @input="validateQty(item.kode_barang)"
                                                   min="1" 
                                                   class="w-full text-center border-gray-300 rounded-md text-sm py-1 focus:ring-blue-500 focus:border-blue-500" 
                                                   required>
                                            
                                            {{-- Hidden Fields untuk Detail PO --}}
                                            <input type="hidden" :name="'items[' + item.kode_barang + '][kode_barang]'" :value="item.kode_barang">
                                            <input type="hidden" :name="'items[' + item.kode_barang + '][satuan]'" :value="item.satuan">
                                        </td>
                                        <td class="py-2 px-1">
                                            <input type="number" :name="'items[' + item.kode_barang + '][harga_satuan]'" :value="item.harga" class="w-full text-center border-gray-300 rounded-md text-sm py-1 focus:ring-blue-500 focus:border-blue-500" readonly>
                                        </td>
                                        <td class="py-2 px-1 text-center">
                                            <button type="button" @click="removeItem(item.kode_barang)" class="text-red-600 hover:text-red-900 focus:outline-none">
                                                <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Total Belanjaan -->
                    <div class="flex justify-between items-center py-3 px-2 bg-gray-50 border-t font-bold text-gray-800 rounded-b-lg mb-6">
                        <span>HARGA PERMINTAAN:</span>
                        <span x-text="formatRupiah(totalHargaPermintaan)">Rp 0</span>
                    </div>
                    <div class="flex justify-between items-center py-3 px-2 bg-gray-50 border-t font-bold text-gray-800 rounded-b-lg mb-6">
                        <span>PPN:</span>
                        <span text="PPN">11%</span>
                    </div>

                    <div class="flex justify-between items-center py-3 px-2 bg-gray-50 border-t font-bold text-gray-800 rounded-b-lg mb-6">
                        <span>TOTAL HARGA PERMINTAAN:</span>
                        <span x-text="formatRupiah(totalHargaPermintaanPPN)">Rp 0</span>
                    </div>

                    {{-- Tombol Submit --}}
                    <button type="submit" 
                            :disabled="Object.keys(cart).length === 0"
                            class="w-full py-3 px-4 text-white font-semibold rounded-lg shadow-md transition duration-150 ease-in-out"
                            :class="{'bg-green-600 hover:bg-green-700': Object.keys(cart).length > 0, 'bg-gray-400 cursor-not-allowed': Object.keys(cart).length === 0}">
                        <i class="fas fa-file-invoice"></i> Buat Permintaan Pembelian
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('purchaseRequest', () => ({
            cart: {}, 
            filters: {
                search: '',
                kategori: '',
                supplier: '',
                page: 1,
            },
            barangHtml: @json(view('purchase_orders._barang_list', ['barangs' => $barangs])->render()),
            paginationHtml: @json((string) $barangs->links()),
            loading: false,

            bindPaginationEvents() {
                this.$nextTick(() => {
                    document.querySelectorAll('[x-html="paginationHtml"] a').forEach(link => {
                        link.removeEventListener('click', this.handlePaginationClick); 
                        link.addEventListener('click', this.handlePaginationClick.bind(this)); 
                    });
                });
            },
            
            handlePaginationClick(e) {
                e.preventDefault();
                const url = new URL(e.target.href);
                const page = url.searchParams.get('page');
                
                if (page) {
                    this.fetchBarangs(page);
                }
            },
            
            get totalHargaPermintaan() {
                return Object.values(this.cart).reduce((total, item) => {
                    // Pastikan harga dan qty adalah angka
                    const harga = parseFloat(item.harga) || 0;
                    const qty = parseFloat(item.qty) || 0;
                    return total + (harga * qty);
                }, 0);
            },

            get totalHargaPermintaanPPN() {
                return Object.values(this.cart).reduce((total, item) => {
                    const harga = parseFloat(item.harga) || 0;
                    const qty = parseFloat(item.qty) || 0;
                    return total + ((harga * qty) + (harga * qty * 11/100));
                }, 0);
            },

            formatRupiah(number) {
                return 'Rp ' + number.toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            },
            async fetchBarangs(page = 1) {
                this.loading = true;
                this.filters.page = page;

                const urlParams = new URLSearchParams(this.filters);
                const url = '{{ route('purchase_orders.fetch_barangs') }}?' + urlParams.toString();

                try {
                    const response = await fetch(url);
                    if (!response.ok) throw new Error('Network response was not ok');
                    
                    const data = await response.json();
                    
                    this.barangHtml = data.html;
                    this.paginationHtml = data.pagination;
                } catch (error) {
                    console.error('Fetch error:', error);
                } finally {
                    this.loading = false;
                    this.bindPaginationEvents();
                }
            },

            addItem(barang) {
                const kode = barang.kode_barang;
                if (this.cart[kode]) {
                    this.cart[kode].qty++;
                } else {
                    this.cart[kode] = {...barang, qty: 1};
                }
                this.cart = {...this.cart}; 
            },

            removeItem(kode) {
                delete this.cart[kode];
                this.cart = {...this.cart};
            },

            validateQty(kode) {
                if (this.cart[kode].qty < 1 || isNaN(this.cart[kode].qty)) {
                    this.cart[kode].qty = 1;
                }
            }
        }));
    });
</script>
@endsection