@extends('layouts.app')
@section('page-title',  'Surat Jalan dan Pengiriman Barang')
@section('content')

<div 
    class="max-w-7xl mx-auto bg-white shadow-lg rounded-lg p-6" 
    x-data="suratJalanApp()" 
    x-init="fetchBarangs()"
    @tambah-barang.window="addItem($event.detail)"
>

    <h1 class="text-2xl font-bold text-gray-800 mb-6">Buat Surat Jalan Baru</h1>

    <form action="{{ route('surat_jalan.store') }}" method="POST">
        @csrf

        {{-- HEADER --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Pelanggan</label>
                <select name="pelanggan_id" class="w-full border rounded-md py-2 px-3" required>
                    <option value="">-- Pilih Pelanggan --</option>
                    @foreach($pelanggans as $p)
                        <option value="{{ $p->pelanggan_id }}">{{ $p->nama_pelanggan }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Tanggal Surat Jalan</label>
                <input type="date" name="tanggal_surat" 
                       value="{{ now()->format('Y-m-d') }}" 
                       class="w-full border rounded-md py-2 px-3" required>
            </div>
        </div>

        {{-- BODY --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- KATALOG BARANG --}}
            <div class="bg-gray-50 p-4 rounded-lg shadow-inner">
                <div class="flex items-end gap-3 mb-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700">Cari Barang</label>
                        <input type="text" x-model="filters.search"
                               placeholder="Nama / Kode Barang..."
                               class="w-full border-gray-300 rounded-md py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <button type="button" 
                            @click="fetchBarangs()" 
                            class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                        Cari
                    </button>
                </div>

                <div class="overflow-y-auto border rounded-lg" style="max-height: 400px;">
                    <table class="w-full text-sm text-left text-gray-700">
                        <thead class="bg-gray-100 text-xs uppercase sticky top-0">
                            <tr>
                                <th class="px-3 py-2">Kode</th>
                                <th class="px-3 py-2">Nama</th>
                                <th class="px-3 py-2 text-center">Stok</th>
                                <th class="px-3 py-2 text-right">Harga / Satuan</th>
                                <th class="px-3 py-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody x-html="barangHtml"></tbody>
                    </table>
                </div>
                <div x-html="paginationHtml" class="mt-3"></div>
                <div x-show="loading" class="text-center text-indigo-500 py-2">Memuat data...</div>
            </div>

            {{-- KERANJANG --}}
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">
                    Barang dalam Surat Jalan
                    <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2 py-0.5 rounded" 
                          x-text="Object.keys(cart).length">0</span>
                </h2>

                <div class="max-h-80 overflow-y-auto mb-4 border rounded-lg">
                    <table class="w-full text-sm text-gray-700">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-2 px-2 text-left">Barang</th>
                                <th class="py-2 px-2 text-center">Qty</th>
                                <th class="py-2 px-2 text-center">Satuan</th>
                                <th class="py-2 px-2 text-right">Subtotal</th>
                                <th class="py-2 px-2 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-if="Object.keys(cart).length === 0">
                                <tr><td colspan="5" class="text-center py-4 text-gray-400">Belum ada barang dipilih</td></tr>
                            </template>

                            <template x-for="item in cart" :key="item.kode_barang">
                                <tr class="border-b">
                                    <td class="px-2 py-2 font-medium text-gray-800" x-text="item.nama"></td>
                                    <td class="px-2 py-2 text-center">
                                        <input 
                                            type="number" 
                                            min="1"
                                            :max="item.stok_tersedia"
                                            x-model.number="item.qty"
                                            @input="validateQty(item.kode_barang)"
                                            :name="'items[' + item.kode_barang + '][quantity]'"
                                            class="w-20 text-center border rounded-md py-1 text-sm"
                                        >
                                        <small class="text-xs">Max: <span x-text="item.stok_tersedia"></span></small> 
                                        <small x-show="item.qty > item.stok_tersedia" class="text-red-500 text-xs block">
                                            Kuantitas melebihi stok!
                                        </small>
                                    </td>

                                    <td class="px-2 py-2 text-center">
                                        <input type="text"
                                            x-model="item.satuan"
                                            :name="'items[' + item.kode_barang + '][satuan]'"
                                            readonly
                                            class="border rounded-md text-sm bg-gray-100 text-center cursor-not-allowed">
                                    </td>

                                    <td class="px-2 py-2 text-right" x-text="formatRupiah(item.subtotal)"></td>
                                    <td class="px-2 py-2 text-center">
                                        <button type="button" 
                                                @click="removeItem(item.kode_barang)" 
                                                class="text-red-600 hover:text-red-800">
                                            ✕
                                        </button>
                                    </td>

                                    {{-- hidden inputs --}}
                                    <input type="hidden" :name="'items[' + item.kode_barang + '][kode_barang]'" :value="item.kode_barang">
                                    <input type="hidden" :name="'items[' + item.kode_barang + '][harga_satuan]'" :value="item.harga">
                                    <input type="hidden" :name="'items[' + item.kode_barang + '][subtotal]'" :value="item.subtotal">
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- INPUT BIAYA PENGIRIMAN --}}
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Pengiriman</label>
                    <input 
                        type="number" 
                        min="0" 
                        name="biaya_pengiriman" 
                        x-model.number="biayaPengiriman"
                        placeholder="Masukkan biaya pengiriman"
                        class="w-full border rounded-md py-2 px-3 focus:ring-indigo-500 focus:border-indigo-500"
                    >
                </div>

                {{-- TOTAL --}}
                <div class="font-bold text-gray-800 border-t pt-2">
                    <div class="flex justify-between">
                        <span>Subtotal Barang:</span>
                        <span x-text="formatRupiah(totalHarga)">Rp 0</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Pengiriman:</span>
                        <span x-text="formatRupiah(biayaPengiriman)">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-lg mt-2 border-t pt-2">
                        <span>Total Keseluruhan:</span>
                        <span x-text="formatRupiah(totalKeseluruhan)">Rp 0</span>
                    </div>
                </div>

                <button type="submit"
                        :disabled="Object.keys(cart).length === 0"
                        class="w-full mt-4 py-3 px-4 text-white font-semibold rounded-lg shadow-md transition"
                        :class="Object.keys(cart).length > 0 
                            ? 'bg-green-600 hover:bg-green-700' 
                            : 'bg-gray-400 cursor-not-allowed'">
                    Simpan Surat Jalan
                </button>
            </div>
        </div>
    </form>
</div>

{{-- SCRIPT ALPINE.JS --}}
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('suratJalanApp', () => ({
        cart: {},
        filters: { search: '', page: 1 },
        barangHtml: '',
        paginationHtml: '',
        loading: false,
        biayaPengiriman: 0,

        async fetchBarangs(page = 1) {
            this.loading = true;
            this.filters.page = page;
            const query = new URLSearchParams(this.filters).toString();
            const res = await fetch('{{ route('surat_jalan.fetch-barangs') }}?' + query);
            const data = await res.json();
            this.barangHtml = data.html;
            this.paginationHtml = data.pagination;
            this.loading = false;
        },

        addItem(barang) {
            const kode = barang.kode_barang;
            const stokTerkecil = barang.stok_tersedia;
            if (this.cart[kode]) {
                if (this.cart[kode].qty < stokTerkecil) {
                    this.cart[kode].qty++;
                }
            } else {
                this.cart[kode] = { 
                    ...barang,
                    stok_tersedia: stokTerkecil,
                    qty: 1,
                    subtotal: barang.harga 
                };
            }
            this.updateSubtotal(kode);
            this.cart = { ...this.cart };
        },

        removeItem(kode) {
            delete this.cart[kode];
            this.cart = { ...this.cart };
        },

        updateSubtotal(kode) {
            const item = this.cart[kode];
            item.subtotal = item.qty * item.harga;
        },

        validateQty(kode) {
            const item = this.cart[kode];
            if (item.qty > item.stok_tersedia) {
                item.qty = item.stok_tersedia;
            } else if (item.qty < 1 || isNaN(item.qty)) {
                item.qty = 1;
            }
            this.updateSubtotal(kode);
            this.cart = { ...this.cart };
        },

        get totalHarga() {
            return Object.values(this.cart).reduce((sum, i) => sum + i.subtotal, 0);
        },

        get totalKeseluruhan() {
            return this.totalHarga + this.biayaPengiriman;
        },

        formatRupiah(num) {
            return 'Rp ' + (num || 0).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    }));
});
</script>
@endsection
