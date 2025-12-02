@extends('layouts.app')
@section('page-title',  'Purchase Orders')
@section('content')

<!-- SUCCESS ALERT MODAL -->
@if (session('success'))
<div 
    x-data="{ show: true }" 
    x-show="show"
    x-init="setTimeout(() => show = false, 5000)"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-40 z-50 p-4"
>
    <div class="bg-white rounded-xl p-6 max-w-md w-full shadow-lg">
        <div class="flex flex-col items-center gap-3">
            <svg class="w-14 h-14 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4"></path>
                <circle cx="12" cy="12" r="9" opacity="0.08" fill="currentColor"></circle>
            </svg>
            <h2 class="text-2xl font-semibold text-gray-800">Permintaan Berhasil!</h2>
            <p class="text-sm text-gray-600 text-center">{{ session('success') }}</p>
            <button 
                @click="show = false"
                class="mt-3 inline-flex items-center justify-center px-5 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium shadow-sm transition"
            >
                Tutup
            </button>
        </div>
    </div>
</div>
@endif

<!-- MAIN CONTENT -->
<div class="max-w-screen mx-auto p-6" x-data="purchaseRequest()" x-init="fetchBarangs()">

    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Form Permintaan Pembelian</h1>
        <p class="text-sm text-gray-600 mt-1">Buat Surat Permintaan Pembelian (Purchase Order) baru — pilih barang dari katalog, atur kuantitas, lalu kirim permintaan.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- KATALOG (LEFT: 7/12) -->
        <div class="lg:col-span-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-5 border-b">
                    <div class="mb-2">
                        <h2 class="text-lg font-semibold text-gray-800">Katalog Barang Master</h2>
                        <p class="text-sm text-gray-500 mt-1">Telusuri dan tambahkan barang ke keranjang permintaan.</p>
                    </div>
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                        <div class="flex items-center gap-3">
                            <div class="relative">
                                <input id="search_input" type="text" x-model.debounce.500ms="filters.search"
                                    placeholder="Cari nama / kode (contoh: B200, Kertas A4)"
                                    class="w-72 pl-10 pr-4 py-2 rounded-lg border border-gray-200 text-sm focus:ring-0 focus:border-sky-500 focus:shadow-outline">
                                <svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>

                            <select x-model="filters.kategori" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500">
                                <option value="">Semua Kategori</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->kategori_barang_id }}">{{ $kategori->nama_kategori_barang }}</option>
                                @endforeach
                            </select>

                            <select x-model="filters.supplier" class="px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500">
                                <option value="">Semua Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id_supplier }}">{{ $supplier->namaSupplier }}</option>
                                @endforeach
                            </select>

                            <button @click="fetchBarangs()" class="px-4 py-2 rounded-lg bg-sky-600 hover:bg-sky-700 text-white text-sm font-medium shadow-sm transition">
                                Cari
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="p-4">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm text-left text-gray-700">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3 font-medium text-xs text-gray-500">Kode</th>
                                    <th class="px-4 py-3 font-medium text-xs text-gray-500">Nama Barang</th>
                                    <th class="px-4 py-3 font-medium text-xs text-gray-500">Supplier Default</th>
                                    <th class="px-4 py-3 font-medium text-xs text-gray-500">Harga Beli</th>
                                    <th class="px-4 py-3 font-medium text-xs text-gray-500 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody x-html="barangHtml" class="divide-y divide-gray-100">
                                <!-- AJAX loaded rows -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Loading Overlay -->
                    <div x-show="loading" class="absolute inset-0 flex items-center justify-center bg-white/70">
                        <svg class="animate-spin h-8 w-8 text-sky-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                    </div>

                    <div class="mt-4" x-html="paginationHtml"></div>
                </div>
            </div>
        </div>

        <!-- KERANJANG (RIGHT: 5/12) -->
        <div class="lg:col-span-4">
            <div class="sticky top-6">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Keranjang Permintaan</h3>
                            <p class="text-xs text-gray-500">Ringkasan item yang akan diajukan.</p>
                        </div>
                        <div class="text-sm font-semibold text-sky-700 bg-sky-100 px-3 py-1 rounded-full" x-text="Object.keys(cart).length">0</div>
                    </div>

                    <form id="purchase-request-form" action="{{ route('purchase_orders.buat_permintaan') }}" method="POST" class="mt-4">
                        @csrf

                        <div class="space-y-3">
                            <div>
                                <label class="text-xs text-gray-600">Supplier Utama (Wajib)</label>
                                <select name="id_supplier" id="id_supplier" required class="w-full mt-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500">
                                    <option value="">-- Pilih Supplier --</option>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id_supplier }}">{{ $supplier->namaSupplier }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="text-xs text-gray-600">Tanggal Permintaan</label>
                                <input type="date" name="tanggal_po" value="{{ now()->format('Y-m-d') }}" required
                                    class="w-full mt-1 px-3 py-2 rounded-lg border border-gray-200 text-sm focus:border-sky-500">
                            </div>
                        </div>

                        <!-- Item list -->
                        <div class="mt-4 max-h-64 overflow-y-auto border border-gray-100 rounded-lg">
                            <table class="w-full text-sm text-gray-700">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left text-xs text-gray-500">Barang</th>
                                        <th class="px-2 py-2 text-center text-xs text-gray-500">Qty (Karton)</th>
                                        <th class="px-2 py-2 text-center text-xs text-gray-500">Harga</th>
                                        <th class="px-2 py-2 text-xs"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-if="Object.keys(cart).length === 0">
                                        <tr>
                                            <td colspan="4" class="py-6 text-center text-gray-400 italic">Keranjang kosong. Tambahkan barang dari katalog.</td>
                                        </tr>
                                    </template>

                                    <template x-for="item in cart" :key="item.kode_barang">
                                        <tr class="border-b last:border-b-0">
                                            <td class="px-3 py-2 text-sm font-medium" x-text="item.nama"></td>
                                            <td class="px-2 py-2 text-center">
                                                <input type="number" min="1"
                                                    :name="'items[' + item.kode_barang + '][quantity]'"
                                                    x-model.number="item.qty"
                                                    @input="validateQty(item.kode_barang)"
                                                    class="w-20 text-center px-2 py-1 rounded-md border border-gray-200 text-sm">
                                            </td>
                                            <td class="px-2 py-2 text-center text-xs" x-text="formatRupiah(item.harga)"></td>
                                            <td class="px-2 py-2 text-center">
                                                <button type="button" @click="removeItem(item.kode_barang)" class="text-red-500 hover:text-red-700">
                                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </td>

                                            <!-- Hidden fields -->
                                            <input type="hidden" :name="'items[' + item.kode_barang + '][kode_barang]'" :value="item.kode_barang">
                                            <input type="hidden" :name="'items[' + item.kode_barang + '][satuan]'" :value="item.satuan">
                                            <input type="hidden" :name="'items[' + item.kode_barang + '][harga_satuan]'" :value="item.harga">
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- Summary -->
                        <div class="mt-4 bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-medium" x-text="formatRupiah(totalHargaPermintaan)">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600 mt-2">
                                <span>PPN (11%)</span>
                                <span class="font-medium" x-text="formatRupiah(totalPPN)">Rp 0</span>
                            </div>
                            <div class="flex justify-between items-center mt-3 pt-2 border-t border-gray-100">
                                <span class="text-sm font-semibold text-gray-800">TOTAL AKHIR</span>
                                <span class="text-base font-bold text-sky-600" x-text="formatRupiah(totalHargaPermintaanPPN)">Rp 0</span>
                            </div>
                        </div>

                        <!-- Submit -->
                        <button type="submit"
                                :disabled="Object.keys(cart).length === 0"
                                class="w-full mt-4 py-3 rounded-lg text-white font-semibold shadow-sm transition"
                                :class="Object.keys(cart).length > 0 ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-300 cursor-not-allowed'">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"></path></svg>
                                Buat Permintaan Pembelian
                            </div>
                        </button>

                    </form>
                </div>
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
