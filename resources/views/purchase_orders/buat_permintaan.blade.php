@extends('layouts.app')
@section('page-title', 'Permintaan Pembelian')
@section('content')

    {{-- MAIN CONTENT --}}
    <div class="space-y-6" x-data="purchaseRequest()" x-init="fetchBarangs()">

        {{-- HEADER --}}
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Form Permintaan Pembelian</h1>
            <p class="text-sm text-gray-500 mt-1">Buat Surat Permintaan Pembelian (Purchase Order) baru — pilih barang dari
                katalog, atur kuantitas, lalu kirim permintaan.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

            {{-- ================================ --}}
            {{-- LEFT: KATALOG (8/12) --}}
            {{-- ================================ --}}
            <div class="lg:col-span-8">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                    {{-- Catalog Header + Filters --}}
                    <div class="p-5 border-b border-gray-100">
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-2 h-5 bg-blue-500 rounded-full"></div>
                            <h2 class="font-semibold text-gray-800">Katalog Barang Master</h2>
                        </div>

                        <div class="flex flex-col md:flex-row gap-3">
                            {{-- Search --}}
                            <div class="flex-1 relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-sm"></i>
                                </div>
                                <input type="text" x-model.debounce.500ms="filters.search" @input="fetchBarangs()"
                                    placeholder="Cari nama / kode barang..."
                                    class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                            </div>

                            {{-- Kategori --}}
                            <select x-model="filters.kategori" @change="fetchBarangs()"
                                class="py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm bg-white">
                                <option value="">Semua Kategori</option>
                                @foreach($kategoris as $kategori)
                                    <option value="{{ $kategori->kategori_barang_id }}">{{ $kategori->nama_kategori_barang }}
                                    </option>
                                @endforeach
                            </select>

                            {{-- Supplier --}}
                            <select x-model="filters.supplier" @change="fetchBarangs()"
                                class="py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm bg-white">
                                <option value="">Semua Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id_supplier }}">{{ $supplier->namaSupplier }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="relative">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-50">
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Kode</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Nama Barang</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Supplier Utama</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Harga Beli</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody x-html="barangHtml" class="divide-y divide-gray-50">
                                    {{-- AJAX loaded rows --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Loading Overlay --}}
                        <div x-show="loading" x-transition
                            class="absolute inset-0 flex items-center justify-center bg-white/80 backdrop-blur-sm z-10">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="animate-spin h-8 w-8 text-red-600" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                <span class="text-xs text-gray-500 font-medium">Memuat data...</span>
                            </div>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    <div class="p-4 border-t border-gray-100" x-html="paginationHtml"></div>
                </div>
            </div>

            {{-- ================================ --}}
            {{-- RIGHT: KERANJANG (4/12) --}}
            {{-- ================================ --}}
            <div class="lg:col-span-4">
                <div class="sticky top-6 space-y-4">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">

                        {{-- Cart Header --}}
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-2 h-5 bg-red-500 rounded-full"></div>
                                <h3 class="font-semibold text-gray-800">Keranjang Permintaan</h3>
                            </div>
                            <span class="px-2.5 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full"
                                x-text="Object.keys(cart).length + ' item'">0 item</span>
                        </div>

                        <form id="purchase-request-form" action="{{ route('purchase_orders.buat_permintaan') }}"
                            method="POST">
                            @csrf

                            {{-- Supplier + Date --}}
                            <div class="space-y-3 mb-4">
                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Supplier</label>
                                    <select name="id_supplier" required
                                        class="w-full py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm bg-white">
                                        <option value="">-- Pilih Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id_supplier }}">{{ $supplier->namaSupplier }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label
                                        class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1.5">Tanggal
                                        Permintaan</label>
                                    <input type="date" name="tanggal_po" value="{{ now()->format('Y-m-d') }}" required
                                        class="w-full py-2.5 px-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition shadow-sm">
                                </div>
                            </div>

                            {{-- Item List --}}
                            <div class="max-h-72 overflow-y-auto rounded-xl border border-gray-100">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500 uppercase">
                                                Barang</th>
                                            <th class="px-2 py-2 text-center text-xs font-semibold text-gray-500 uppercase">
                                                QTY (Karton)</th>
                                            <th class="px-2 py-2 text-center text-xs font-semibold text-gray-500 uppercase">
                                                Harga</th>
                                            <th class="px-2 py-2 w-8"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <template x-if="Object.keys(cart).length === 0">
                                            <tr>
                                                <td colspan="4" class="py-8 text-center">
                                                    <div class="flex flex-col items-center gap-2">
                                                        <div
                                                            class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                                                            <i class="fa-solid fa-cart-shopping text-gray-400"></i>
                                                        </div>
                                                        <p class="text-xs text-gray-400">Keranjang kosong</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>

                                        <template x-for="item in cart" :key="item.kode_barang">
                                            <tr class="hover:bg-gray-50/50 transition-colors">
                                                <td class="px-3 py-2.5">
                                                    <p class="font-medium text-gray-800 text-sm truncate max-w-[120px]"
                                                        x-text="item.nama"></p>
                                                    <p class="text-[10px] text-gray-400 font-mono"
                                                        x-text="item.kode_barang"></p>
                                                </td>
                                                <td class="px-2 py-2.5 text-center">
                                                    <input type="number" min="1"
                                                        :name="'items[' + item.kode_barang + '][quantity]'"
                                                        x-model.number="item.qty" @input="validateQty(item.kode_barang)"
                                                        class="w-16 text-center px-1.5 py-1 rounded-lg border border-gray-200 text-sm font-semibold focus:ring-2 focus:ring-red-500/20 focus:border-red-500 transition">
                                                </td>
                                                <td class="px-2 py-2.5 text-center text-xs text-gray-600"
                                                    x-text="formatRupiah(item.harga)"></td>
                                                <td class="px-2 py-2.5 text-center">
                                                    <button type="button" @click="removeItem(item.kode_barang)"
                                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-red-400 hover:bg-red-50 hover:text-red-600 transition">
                                                        <i class="fa-solid fa-trash text-xs"></i>
                                                    </button>
                                                </td>

                                                {{-- Hidden fields --}}
                                                <input type="hidden" :name="'items[' + item.kode_barang + '][kode_barang]'"
                                                    :value="item.kode_barang">
                                                <input type="hidden" :name="'items[' + item.kode_barang + '][satuan]'"
                                                    :value="item.satuan">
                                                <input type="hidden" :name="'items[' + item.kode_barang + '][harga_satuan]'"
                                                    :value="item.harga">
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Summary --}}
                            <div class="mt-4 bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2">
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>Subtotal</span>
                                    <span class="font-medium" x-text="formatRupiah(totalHargaPermintaan)">Rp 0</span>
                                </div>
                                <div class="flex justify-between text-sm text-gray-600">
                                    <span>PPN (11%)</span>
                                    <span class="font-medium" x-text="formatRupiah(totalPPN)">Rp 0</span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                    <span class="text-sm font-bold text-gray-800">TOTAL AKHIR</span>
                                    <span class="text-lg font-extrabold text-red-700"
                                        x-text="formatRupiah(totalHargaPermintaanPPN)">Rp 0</span>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <button type="submit" :disabled="Object.keys(cart).length === 0"
                                class="w-full mt-4 py-3 rounded-xl text-white font-semibold shadow-sm transition-all"
                                :class="Object.keys(cart).length > 0 ? 'bg-red-800 hover:bg-red-700 hover:shadow-md' : 'bg-gray-300 cursor-not-allowed'">
                                <div class="flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-paper-plane text-sm"></i>
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
                        const harga = parseFloat(item.harga) || 0;
                        const qty = parseFloat(item.qty) || 0;
                        return total + (harga * qty);
                    }, 0);
                },

                get totalPPN() {
                    return this.totalHargaPermintaan * 0.11;
                },

                get totalHargaPermintaanPPN() {
                    return this.totalHargaPermintaan + this.totalPPN;
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
                        this.cart[kode] = { ...barang, qty: 1 };
                    }
                    this.cart = { ...this.cart };
                },

                removeItem(kode) {
                    delete this.cart[kode];
                    this.cart = { ...this.cart };
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