@extends('layouts.app')
@section('page-title', 'Surat Jalan dan Pengiriman Barang')
@section('content')

    <div class="space-y-6" x-data="suratJalanApp()" x-init="initLokasi(); fetchBarangs()"
        @tambah-barang.window="addItem($event.detail)">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-red-800 flex items-center justify-center shadow">
                    <i class="fa-solid fa-file-invoice text-white text-lg"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Buat Surat Jalan Baru</h1>
                    <p class="text-sm text-gray-500">Buat data pengiriman baru berdasarkan barang yang tersedia.</p>
                </div>
            </div>
            <a href="{{ route('surat_jalan.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <form action="{{ route('surat_jalan.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- ===== LEFT COLUMN: Main Form ===== --}}
                <div class="lg:col-span-2 space-y-6">

                    {{-- Section 1: Pelanggan & Tanggal --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-user-tag text-red-500"></i>
                                Data Pelanggan & Surat
                            </h3>
                        </div>
                        <div class="p-5 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label for="pelanggan_id"
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                        Pelanggan <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i
                                            class="fa-solid fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                        <select name="pelanggan_id" id="pelanggan_id"
                                            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition bg-white appearance-none"
                                            required @change="setDiskonPelanggan($event)">
                                            <option value="">-- Pilih Pelanggan --</option>
                                            @foreach($pelanggans as $p)
                                                <option value="{{ $p->pelanggan_id }}" data-diskon="{{ $p->jumlah_diskon }}">
                                                    {{ $p->nama_pelanggan }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label for="tanggal_surat"
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                        Tanggal Surat Jalan <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <i
                                            class="fa-regular fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                        <input type="date" name="tanggal_surat" id="tanggal_surat"
                                            value="{{ now()->format('Y-m-d') }}"
                                            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label for="nama_penerima"
                                    class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">
                                    Nama Penerima
                                </label>
                                <div class="relative">
                                    <i
                                        class="fa-solid fa-id-card absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                    <input name="nama_penerima" id="nama_penerima" placeholder="Masukkan Nama Penerima"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section 2: Alamat Penerima --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-location-dot text-red-500"></i>
                                Alamat Penerima
                            </h3>
                        </div>
                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Provinsi
                                        <span class="text-red-500">*</span></label>
                                    <select x-model="alamat.provinsi" @change="fetchKota()"
                                        class="w-full py-2.5 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition appearance-none"
                                        required>
                                        <option value="">-- Pilih Provinsi --</option>
                                        <template x-for="p in daftarProvinsi">
                                            <option :value="p.id" x-text="p.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Kota
                                        / Kabupaten <span class="text-red-500">*</span></label>
                                    <select x-model="alamat.kota" @change="fetchKecamatan()"
                                        class="w-full py-2.5 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition appearance-none"
                                        required>
                                        <option value="">-- Pilih Kota --</option>
                                        <template x-for="k in daftarKota">
                                            <option :value="k.id" x-text="k.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Kecamatan
                                        <span class="text-red-500">*</span></label>
                                    <select x-model="alamat.kecamatan"
                                        class="w-full py-2.5 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition appearance-none"
                                        required>
                                        <option value="">-- Pilih Kecamatan --</option>
                                        <template x-for="c in daftarKecamatan">
                                            <option :value="c.id" x-text="c.name"></option>
                                        </template>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Kode
                                        Pos <span class="text-red-500">*</span></label>
                                    <input type="text" x-model="alamat.kode_pos" placeholder="Kode Pos"
                                        class="w-full py-2.5 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                        required>
                                </div>
                                <div class="md:col-span-2">
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Detail
                                        Alamat <span class="text-red-500">*</span></label>
                                    <textarea x-model="alamat.detail" rows="2"
                                        placeholder="Nama jalan, No rumah, RT RW, patokan..."
                                        class="w-full py-2.5 px-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition resize-none"
                                        required></textarea>
                                </div>
                            </div>
                            <input type="hidden" name="alamat_penerima" :value="alamatGabungan">
                        </div>
                    </div>

                    {{-- Section 3: Katalog Barang --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                <i class="fa-solid fa-boxes-stacked text-red-500"></i>
                                Katalog Barang
                            </h3>
                        </div>
                        <div class="p-5">
                            {{-- Search --}}
                            <div class="flex items-end gap-3 mb-4">
                                <div class="flex-1">
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Cari
                                        Barang</label>
                                    <div class="relative">
                                        <i
                                            class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                        <input type="text" x-model="filters.search" placeholder="Nama / Kode Barang..."
                                            class="w-full pl-9 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                            @keydown.enter.prevent="fetchBarangs()">
                                    </div>
                                </div>
                                <button type="button" @click="fetchBarangs()"
                                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-800 text-white rounded-xl text-sm font-semibold hover:bg-red-700 transition-colors shadow-sm">
                                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Cari
                                </button>
                            </div>

                            {{-- Table --}}
                            <div class="overflow-y-auto border border-gray-200 rounded-xl" style="max-height: 380px;">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 sticky top-0 z-10">
                                        <tr class="text-gray-600">
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                                Kode</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                                Nama Barang</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">
                                                Stok</th>
                                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider">
                                                Harga / Satuan</th>
                                            <th
                                                class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody x-html="barangHtml" class="divide-y divide-gray-100"></tbody>
                                </table>
                            </div>
                            <div x-html="paginationHtml" class="mt-3" @click.prevent="handlePaginationClick($event)"></div>
                            <div x-show="loading" class="text-center text-red-500 py-3 text-sm font-medium">
                                <i class="fa-solid fa-spinner fa-spin mr-1"></i> Memuat data...
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ===== RIGHT COLUMN: Cart & Summary ===== --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-20 space-y-6">

                        {{-- Cart --}}
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-100 bg-red-800">
                                <h3 class="font-bold text-white text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                    Barang dalam Surat Jalan
                                    <span class="ml-auto bg-white/20 text-white text-xs font-bold px-2 py-0.5 rounded-full"
                                        x-text="Object.keys(cart).length">0</span>
                                </h3>
                            </div>
                            <div class="max-h-72 overflow-y-auto">
                                <table class="w-full text-sm">
                                    <thead class="bg-gray-50 sticky top-0">
                                        <tr class="text-gray-500">
                                            <th class="py-2.5 px-3 text-left text-xs font-semibold">Barang</th>
                                            <th class="py-2.5 px-3 text-center text-xs font-semibold">Qty</th>
                                            <th class="py-2.5 px-3 text-center text-xs font-semibold">Satuan</th>
                                            <th class="py-2.5 px-3 text-right text-xs font-semibold">Subtotal</th>
                                            <th class="py-2.5 px-3 w-8"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        <template x-if="Object.keys(cart).length === 0">
                                            <tr>
                                                <td colspan="5" class="text-center py-8 text-gray-400">
                                                    <i
                                                        class="fa-solid fa-cart-shopping text-2xl text-gray-300 mb-2 block"></i>
                                                    <span class="text-sm">Belum ada barang dipilih</span>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-for="item in cart" :key="item.kode_barang">
                                            <tr class="hover:bg-gray-50/50 transition">
                                                <td class="px-3 py-2.5">
                                                    <p class="font-medium text-gray-800 text-xs" x-text="item.nama"></p>
                                                    <p class="text-[10px] text-gray-400 font-mono"
                                                        x-text="item.kode_barang"></p>
                                                </td>
                                                <td class="px-3 py-2.5 text-center">
                                                    <input type="number" min="1" :max="item.stok_tersedia"
                                                        x-model.number="item.qty" @input="validateQty(item.kode_barang)"
                                                        :name="'items[' + item.kode_barang + '][quantity]'"
                                                        class="w-16 text-center border border-gray-200 rounded-lg py-1 text-sm focus:ring-2 focus:ring-red-500/20 focus:border-red-500">
                                                    <p class="text-[10px] text-gray-400 mt-0.5">Max: <span
                                                            x-text="item.stok_tersedia"></span></p>
                                                    <p x-show="item.qty > item.stok_tersedia"
                                                        class="text-red-500 text-[10px] font-semibold">Melebihi stok!</p>
                                                </td>
                                                <td class="px-3 py-2.5 text-center">
                                                    <input type="text" x-model="item.satuan"
                                                        :name="'items[' + item.kode_barang + '][satuan]'" readonly
                                                        class="w-16 border border-gray-200 rounded-lg text-xs bg-gray-50 text-center cursor-not-allowed py-1">
                                                </td>
                                                <td class="px-3 py-2.5 text-right text-xs font-semibold text-gray-800"
                                                    x-text="formatRupiah(item.subtotal)"></td>
                                                <td class="px-2 py-2.5 text-center">
                                                    <button type="button" @click="removeItem(item.kode_barang)"
                                                        class="w-6 h-6 flex items-center justify-center rounded-full text-red-400 hover:bg-red-50 hover:text-red-600 transition">
                                                        <i class="fa-solid fa-xmark text-xs"></i>
                                                    </button>
                                                </td>
                                                {{-- Hidden inputs --}}
                                                <input type="hidden" :name="'items[' + item.kode_barang + '][kode_barang]'"
                                                    :value="item.kode_barang">
                                                <input type="hidden" :name="'items[' + item.kode_barang + '][harga_satuan]'"
                                                    :value="item.harga">
                                                <input type="hidden" :name="'items[' + item.kode_barang + '][subtotal]'"
                                                    :value="item.subtotal">
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Biaya & Total --}}
                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                            <div class="px-5 py-3 border-b border-gray-100 bg-gray-50/50">
                                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                                    <i class="fa-solid fa-calculator text-red-500"></i>
                                    Rincian Biaya
                                </h3>
                            </div>
                            <div class="p-5 space-y-3">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-1.5">Biaya
                                        Pengiriman</label>
                                    <div class="relative">
                                        <span
                                            class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-semibold">Rp</span>
                                        <input type="number" min="0" name="biaya_pengiriman"
                                            x-model.number="biayaPengiriman" placeholder="0"
                                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                                    </div>
                                </div>

                                <div class="border-t border-gray-100 pt-3 space-y-2 text-sm">
                                    <div class="flex justify-between text-gray-600">
                                        <span>Subtotal Barang</span>
                                        <span class="font-semibold text-gray-800" x-text="formatRupiah(totalHarga)">Rp
                                            0</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Biaya Pengiriman</span>
                                        <span class="font-semibold text-gray-800" x-text="formatRupiah(biayaPengiriman)">Rp
                                            0</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>PPN ({{ $appSettings['ppn_persen'] ?? 11 }}%)</span>
                                        <span class="font-semibold text-gray-800" x-text="formatRupiah(ppn)">Rp 0</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600">
                                        <span>Diskon (<span x-text="diskonPelanggan"></span>%)</span>
                                        <span class="font-semibold text-red-600">- <span
                                                x-text="formatRupiah((totalHarga * diskonPelanggan) / 100)">Rp
                                                0</span></span>
                                    </div>
                                    <div class="flex justify-between border-t-2 border-gray-200 pt-3 mt-2">
                                        <span class="text-base font-bold text-gray-900">Total Keseluruhan</span>
                                        <span class="text-base font-bold text-red-700"
                                            x-text="formatRupiah(totalKeseluruhan)">Rp 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Hidden Inputs & Submit --}}
                        <input type="hidden" name="subtotal" :value="totalHarga.toFixed(2)">
                        <input type="hidden" name="diskon_pelanggan" :value="diskonPelanggan">
                        <input type="hidden" name="biaya_pengiriman" :value="biayaPengiriman">

                        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-3">
                            <button type="submit" :disabled="Object.keys(cart).length === 0"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 font-semibold text-sm rounded-xl transition shadow-lg focus:ring-4"
                                :class="Object.keys(cart).length > 0
                                        ? 'bg-red-800 text-white hover:bg-red-700 shadow-red-200 focus:ring-red-200'
                                        : 'bg-gray-300 text-gray-500 cursor-not-allowed shadow-none'">
                                <i class="fa-solid fa-floppy-disk"></i>
                                Simpan Surat Jalan
                            </button>
                            <a href="{{ route('surat_jalan.index') }}"
                                class="w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-100 text-gray-600 font-medium text-sm rounded-xl hover:bg-gray-200 transition">
                                <i class="fa-solid fa-xmark"></i> Batal
                            </a>
                        </div>

                    </div>
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
                diskonPelanggan: 0,

                alamat: {
                    provinsi: '',
                    kota: '',
                    kecamatan: '',
                    kode_pos: '',
                    detail: ''
                },

                daftarProvinsi: [],
                daftarKota: [],
                daftarKecamatan: [],

                async initLokasi() {
                    const res = await fetch("https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json");
                    this.daftarProvinsi = await res.json();
                },

                async fetchKota() {
                    this.daftarKota = [];
                    this.daftarKecamatan = [];
                    this.alamat.kota = '';
                    this.alamat.kecamatan = '';
                    const idProv = this.alamat.provinsi;
                    if (!idProv) return;
                    const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${idProv}.json`);
                    this.daftarKota = [...await res.json()];
                },

                async fetchKecamatan() {
                    this.daftarKecamatan = [];
                    this.alamat.kecamatan = '';
                    const idKota = this.alamat.kota;
                    if (!idKota) return;
                    const res = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${idKota}.json`);
                    this.daftarKecamatan = [...await res.json()];
                },

                get alamatGabungan() {
                    return [
                        this.getNamaById(this.daftarProvinsi, this.alamat.provinsi),
                        this.getNamaById(this.daftarKota, this.alamat.kota),
                        this.getNamaById(this.daftarKecamatan, this.alamat.kecamatan),
                        this.alamat.kode_pos,
                        this.alamat.detail
                    ].filter(Boolean).join(', ');
                },

                getNamaById(list, id) {
                    const item = list.find(l => l.id == id);
                    return item ? item.name : '';
                },

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

                setDiskonPelanggan(event) {
                    const selected = event.target.options[event.target.selectedIndex];
                    const diskon = parseFloat(selected.dataset.diskon || 0);
                    this.diskonPelanggan = diskon;
                },

                get ppn() {
                    const subtotalSetelahDiskon = this.totalHarga;
                    return subtotalSetelahDiskon * {{ floatval($appSettings['ppn_persen'] ?? 11) / 100 }};
                },

                get totalKeseluruhan() {
                    const potongan = (this.totalHarga * this.diskonPelanggan) / 100;
                    const subtotal = this.totalHarga - potongan;
                    return subtotal + this.ppn + this.biayaPengiriman;
                },

                formatRupiah(num) {
                    return 'Rp ' + (num || 0).toFixed(0).replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },

                handlePaginationClick(e) {
                    const link = e.target.closest('a');
                    if (!link) return;
                    const url = new URL(link.href);
                    const page = url.searchParams.get('page');
                    if (page) {
                        this.fetchBarangs(page);
                    }
                }
            }));
        });
    </script>
@endsection