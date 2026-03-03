@extends('layouts.app')
@section('page-title', 'Verifikasi Barang Masuk')
@section('content')

    {{-- Error Validation Messages --}}
    @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded-xl shadow-sm mb-6" role="alert">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-circle-exclamation text-red-500 mt-0.5"></i>
                <div>
                    <p class="font-semibold">Input Tidak Valid!</p>
                    <ul class="mt-1 list-disc list-inside text-sm space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-1">
            <div class="w-10 h-10 rounded-xl bg-red-800 flex items-center justify-center shadow">
                <i class="fa-solid fa-clipboard-check text-white text-lg"></i>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Verifikasi Fisik Barang Masuk</h1>
                <p class="text-sm text-gray-500">Memastikan dan mengonfirmasi barang yang diterima secara fisik sesuai PO
                </p>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div x-data="poVerifikasi()" x-init="init()" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- ==================== LEFT COLUMN: PO Selection ==================== --}}
        <div class="lg:col-span-4">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden sticky top-6">
                {{-- Header --}}
                <div class="bg-gradient-to-r from-red-800 to-red-700 px-5 py-4">
                    <div class="flex items-center justify-between">
                        <h2 class="text-white font-semibold text-base">
                            <i class="fa-solid fa-file-invoice mr-2"></i>Surat Purchase Order
                        </h2>
                        <span class="bg-white/20 text-white text-xs font-bold px-2.5 py-1 rounded-full">
                            {{ $purchaseOrders->count() }} PO
                        </span>
                    </div>
                    {{-- Search Filter --}}
                    <div class="mt-3 relative">
                        <i
                            class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-red-300 text-xs"></i>
                        <input type="text" x-model="searchPO" placeholder="Cari No. PO atau Supplier..."
                            class="w-full bg-white/15 text-white placeholder-red-200 text-sm rounded-xl pl-9 pr-3 py-2 border border-white/20 focus:outline-none focus:ring-2 focus:ring-white/30 backdrop-blur">
                    </div>
                </div>

                {{-- PO List --}}
                <div class="p-3 space-y-2 max-h-[65vh] overflow-y-auto">
                    @forelse($purchaseOrders as $po)
                        <label x-show="
                                !searchPO ||
                                '{{ strtolower($po->po_id) }}'.includes(searchPO.toLowerCase()) ||
                                '{{ strtolower($po->supplier->namaSupplier ?? '') }}'.includes(searchPO.toLowerCase())
                            " x-transition class="block cursor-pointer group"
                            :class="selectedPO === '{{ $po->po_id }}' ? 'ring-2 ring-red-500 bg-red-50 rounded-xl' : ''">
                            <input type="radio" name="selectedPO" value="{{ $po->po_id }}" x-model="selectedPO" @change="fetchItems($event.target.value, {
                                    po_id: '{{ $po->po_id }}',
                                    supplier: '{{ $po->supplier->namaSupplier ?? '-' }}',
                                    tanggal: '{{ $po->tanggal_po ? $po->tanggal_po->format('d M Y') : '-' }}',
                                    total: '{{ number_format($po->total_harga ?? 0, 0, ',', '.') }}',
                                    user: '{{ $po->user->nama_lengkap ?? '-' }}',
                                    items_count: {{ $po->details_count }}
                                })" class="hidden">

                            <div class="p-3 rounded-xl border border-gray-200 group-hover:border-red-300 group-hover:bg-red-50/50 transition-all duration-200"
                                :class="selectedPO === '{{ $po->po_id }}' ? 'border-red-400 bg-red-50' : ''">

                                {{-- PO Number & Badge --}}
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-gray-900 text-sm">{{ $po->po_id }}</span>
                                    <span
                                        class="bg-emerald-100 text-emerald-700 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">
                                        Disetujui
                                    </span>
                                </div>

                                {{-- Supplier --}}
                                <div class="flex items-center gap-2 text-sm text-gray-600 mb-1.5">
                                    <i class="fa-solid fa-building text-gray-400 text-xs w-4"></i>
                                    <span class="truncate">{{ $po->supplier->namaSupplier ?? '-' }}</span>
                                </div>

                                {{-- Date --}}
                                <div class="flex items-center gap-2 text-sm text-gray-500 mb-1.5">
                                    <i class="fa-regular fa-calendar text-gray-400 text-xs w-4"></i>
                                    <span>{{ $po->tanggal_po ? $po->tanggal_po->format('d M Y') : '-' }}</span>
                                </div>

                                {{-- Bottom row: items count, total, user --}}
                                <div class="flex items-center justify-between mt-2 pt-2 border-t border-gray-100">
                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                        <span class="flex items-center gap-1">
                                            <i class="fa-solid fa-boxes-stacked text-gray-400"></i>
                                            {{ $po->details_count }} item
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <i class="fa-solid fa-user text-gray-400"></i>
                                            {{ $po->user->nama_lengkap ?? '-' }}
                                        </span>
                                    </div>
                                    <span class="text-xs font-semibold text-red-700">
                                        Rp {{ number_format($po->total_harga ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                        </label>
                    @empty
                        <div class="text-center py-12 text-gray-400">
                            <i class="fa-solid fa-inbox text-4xl mb-3 block"></i>
                            <p class="text-sm">Tidak ada PO yang menunggu verifikasi</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ==================== RIGHT COLUMN: Verification Table ==================== --}}
        <div class="lg:col-span-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Selected PO Info Header --}}
                <div class="border-b border-gray-100">
                    <template x-if="selectedPoInfo">
                        <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                            <div class="flex items-center justify-between mb-3">
                                <h2 class="text-lg font-bold text-gray-900">
                                    <i class="fa-solid fa-box-open text-red-600 mr-2"></i>Verifikasi Barang
                                </h2>
                                <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full"
                                    x-text="selectedPoInfo.po_id"></span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                <div class="bg-white rounded-xl border border-gray-100 p-3">
                                    <p class="text-[10px] uppercase font-semibold text-gray-400 mb-0.5">Supplier</p>
                                    <p class="text-sm font-semibold text-gray-800 truncate"
                                        x-text="selectedPoInfo.supplier"></p>
                                </div>
                                <div class="bg-white rounded-xl border border-gray-100 p-3">
                                    <p class="text-[10px] uppercase font-semibold text-gray-400 mb-0.5">Tanggal PO</p>
                                    <p class="text-sm font-semibold text-gray-800" x-text="selectedPoInfo.tanggal"></p>
                                </div>
                                <div class="bg-white rounded-xl border border-gray-100 p-3">
                                    <p class="text-[10px] uppercase font-semibold text-gray-400 mb-0.5">Total Harga</p>
                                    <p class="text-sm font-semibold text-red-700" x-text="'Rp ' + selectedPoInfo.total"></p>
                                </div>
                                <div class="bg-white rounded-xl border border-gray-100 p-3">
                                    <p class="text-[10px] uppercase font-semibold text-gray-400 mb-0.5">Dibuat Oleh</p>
                                    <p class="text-sm font-semibold text-gray-800 truncate" x-text="selectedPoInfo.user">
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template x-if="!selectedPoInfo">
                        <div class="px-6 py-4">
                            <h2 class="text-lg font-bold text-gray-900">
                                <i class="fa-solid fa-box-open text-red-600 mr-2"></i>Verifikasi Barang
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">Pilih Surat PO di sebelah kiri untuk memulai verifikasi
                            </p>
                        </div>
                    </template>
                </div>

                {{-- Form --}}
                <form x-ref="poForm" method="POST" action="{{ route('verifBarang.store') }}"
                    @submit.prevent="confirmSubmit()">
                    @csrf
                    <input type="hidden" name="po_id" x-model="selectedPO">

                    {{-- Table with Loading Overlay --}}
                    <div class="relative">
                        {{-- Loading Overlay --}}
                        <div x-show="isLoading" x-transition.opacity
                            class="absolute inset-0 bg-white/80 backdrop-blur-sm z-10 flex items-center justify-center">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-8 h-8 border-3 border-red-200 border-t-red-600 rounded-full animate-spin">
                                </div>
                                <span class="text-sm font-medium text-gray-500">Memuat data barang...</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-red-800 text-white text-xs uppercase tracking-wide">
                                    <tr>
                                        <th class="py-3 px-3 text-center w-10">#</th>
                                        <th class="py-3 px-3 text-center w-12">
                                            <i class="fa-solid fa-check"></i>
                                        </th>
                                        <th class="py-3 px-4 text-left">Kode Barang</th>
                                        <th class="py-3 px-4 text-left">Nama Barang</th>
                                        <th class="py-3 px-3 text-center">Qty PO</th>
                                        <th class="py-3 px-3 text-center">Qty Diterima</th>
                                        <th class="py-3 px-3 text-center">Qty Retur</th>
                                        <th class="py-3 px-3 text-center">Tgl Kadaluarsa</th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-100">
                                    {{-- Empty State --}}
                                    <template x-if="items.length === 0 && !isLoading">
                                        <tr>
                                            <td colspan="8" class="py-16 text-center">
                                                <div class="flex flex-col items-center gap-3 text-gray-400">
                                                    <div
                                                        class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center">
                                                        <i class="fa-solid fa-clipboard-list text-2xl text-gray-300"></i>
                                                    </div>
                                                    <p class="text-sm font-medium">Belum ada data barang</p>
                                                    <p class="text-xs">Pilih Surat PO di panel kiri untuk menampilkan daftar
                                                        barang</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>

                                    {{-- Data Rows --}}
                                    <template x-for="(item, index) in items" :key="item.kode_barang">
                                        <tr class="transition-colors duration-150"
                                            :class="item.selected ? 'bg-emerald-50/60' : 'hover:bg-gray-50'">

                                            {{-- Row Number --}}
                                            <td class="py-3 px-3 text-center text-xs font-bold text-gray-400"
                                                x-text="index + 1"></td>

                                            {{-- Checkbox --}}
                                            <td class="py-3 px-3 text-center">
                                                <input type="hidden" :name="`items[${index}][selected]`"
                                                    :value="item.selected ? 1 : 0">
                                                <label
                                                    class="inline-flex items-center justify-center w-6 h-6 rounded-lg border-2 transition-all duration-150 cursor-pointer"
                                                    :class="item.selected ? 'bg-emerald-500 border-emerald-500 shadow-sm' : 'bg-white border-gray-300 hover:border-emerald-400'">
                                                    <input type="checkbox" x-model="item.selected" class="sr-only">
                                                    <i class="fa-solid fa-check text-white text-[10px]"
                                                        x-show="item.selected" x-transition.scale></i>
                                                </label>
                                            </td>

                                            {{-- Kode Barang --}}
                                            <td class="py-3 px-4">
                                                <span
                                                    class="inline-flex items-center gap-1.5 px-2 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-mono font-semibold">
                                                    <i class="fa-solid fa-barcode text-gray-400 text-[10px]"></i>
                                                    <span x-text="item.kode_barang"></span>
                                                </span>
                                            </td>

                                            {{-- Nama Barang --}}
                                            <td class="py-3 px-4 font-medium text-gray-800" x-text="item.nama_barang"></td>

                                            {{-- Qty PO --}}
                                            <td class="py-3 px-3 text-center">
                                                <span class="font-bold text-gray-700" x-text="item.qty_po"></span>
                                                <span class="text-[10px] text-gray-400 block">karton</span>
                                            </td>

                                            {{-- Qty Diterima --}}
                                            <td class="py-3 px-3 text-center">
                                                <input type="number" min="0" :max="item.qty_po"
                                                    x-model.number="item.qty_diterima" @input="item.selected = true"
                                                    :name="`items[${index}][qty_diterima]`"
                                                    class="w-20 border border-gray-300 rounded-lg p-1.5 text-center text-sm font-semibold focus:ring-2 focus:ring-red-500 focus:border-red-500 transition"
                                                    :class="item.qty_diterima < item.qty_po ? 'bg-amber-50 border-amber-300 text-amber-700' : 'bg-white'">
                                            </td>

                                            {{-- Qty Retur (auto calculated) --}}
                                            <td class="py-3 px-3 text-center">
                                                <span
                                                    class="inline-flex items-center justify-center w-10 h-7 rounded-lg text-xs font-bold"
                                                    :class="Math.max(item.qty_po - (item.qty_diterima || 0), 0) > 0 ? 'bg-red-100 text-red-700' : 'bg-emerald-100 text-emerald-700'"
                                                    x-text="Math.max(item.qty_po - (item.qty_diterima || 0), 0)">
                                                </span>
                                                <input type="hidden" :name="`items[${index}][qty_retur]`"
                                                    :value="Math.max(item.qty_po - (item.qty_diterima || 0), 0)">
                                            </td>

                                            {{-- Tanggal Kadaluarsa --}}
                                            <td class="py-3 px-3 text-center">
                                                <input type="date" x-model="item.tgl_kadaluarsa"
                                                    :name="`items[${index}][tgl_kadaluarsa]`" required
                                                    class="border border-gray-300 rounded-lg px-2 py-1.5 w-36 text-xs font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">

                                                {{-- Hidden Inputs --}}
                                                <input type="hidden" :name="`items[${index}][kode_barang]`"
                                                    :value="item.kode_barang">
                                                <input type="hidden" :name="`items[${index}][qty_po]`" :value="item.qty_po">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Summary Footer --}}
                    <template x-if="items.length > 0">
                        <div class="border-t border-gray-100 bg-gray-50/80 px-6 py-4">
                            <div class="flex flex-wrap items-center justify-between gap-4">
                                {{-- Stats --}}
                                <div class="flex items-center gap-4">
                                    <div class="flex items-center gap-2 text-sm">
                                        <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <i class="fa-solid fa-boxes-stacked text-blue-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-gray-400 font-semibold uppercase">Total Item</p>
                                            <p class="text-sm font-bold text-gray-800" x-text="items.length"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                            <i class="fa-solid fa-circle-check text-emerald-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-gray-400 font-semibold uppercase">Diterima</p>
                                            <p class="text-sm font-bold text-emerald-700"
                                                x-text="items.reduce((s, i) => s + (Number(i.qty_diterima) || 0), 0)"></p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm">
                                        <div class="w-8 h-8 rounded-lg bg-red-100 flex items-center justify-center">
                                            <i class="fa-solid fa-rotate-left text-red-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-gray-400 font-semibold uppercase">Retur</p>
                                            <p class="text-sm font-bold text-red-700"
                                                x-text="items.reduce((s, i) => s + Math.max(i.qty_po - (Number(i.qty_diterima) || 0), 0), 0)">
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm">
                                        <div class="w-8 h-8 rounded-lg bg-amber-100 flex items-center justify-center">
                                            <i class="fa-solid fa-square-check text-amber-600 text-xs"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] text-gray-400 font-semibold uppercase">Dicentang</p>
                                            <p class="text-sm font-bold text-amber-700"
                                                x-text="items.filter(i => i.selected).length + '/' + items.length"></p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Submit Button --}}
                                <button type="submit"
                                    :disabled="!selectedPO || items.length === 0 || items.filter(i => i.selected).length === 0"
                                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-800 text-white font-semibold text-sm rounded-xl shadow-lg shadow-red-200 hover:bg-red-700 transition-all duration-200 disabled:opacity-40 disabled:cursor-not-allowed disabled:shadow-none focus:ring-4 focus:ring-red-200">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                    Konfirmasi Verifikasi
                                </button>
                            </div>
                        </div>
                    </template>

                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('alpine:init', () => {
                Alpine.data('poVerifikasi', () => ({
                    selectedPO: null,
                    selectedPoInfo: null,
                    items: [],
                    isLoading: false,
                    searchPO: '',

                    init() {
                        @if(old('po_id'))
                            this.selectedPO = '{{ old('po_id') }}';
                            this.fetchItems(this.selectedPO, null, true);
                        @endif
                    },

                    async fetchItems(poId, poInfo = null, isOldInput = false) {
                        this.items = [];
                        this.isLoading = true;
                        this.selectedPoInfo = poInfo;

                        try {
                            const res = await fetch(`/verifBarang/get-items/${poId}`);
                            if (!res.ok) throw new Error('Gagal memuat data detail PO');

                            const data = await res.json();
                            let fetchedItems = data.data || data;

                            this.items = fetchedItems.map(item => {
                                let qty_diterima_default = item.qty_po;
                                let tgl_kadaluarsa_default = '';
                                let selected_default = false;

                                @if(old('items'))
                                    const oldItems = @json(old('items'));
                                    const oldItem = oldItems.find(old => old.kode_barang === item.kode_barang);
                                    if (oldItem) {
                                        qty_diterima_default = oldItem.qty_diterima !== undefined ? parseInt(oldItem.qty_diterima) : item.qty_po;
                                        tgl_kadaluarsa_default = oldItem.tgl_kadaluarsa || '';
                                        selected_default = oldItem.selected === '1';
                                    }
                                @endif

                                return {
                                    ...item,
                                    qty_diterima: qty_diterima_default,
                                    tgl_kadaluarsa: tgl_kadaluarsa_default,
                                    selected: selected_default || isOldInput,
                                };
                            });

                        } catch (err) {
                            console.error('Error fetching PO items:', err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Memuat Data',
                                text: err.message,
                                customClass: { popup: 'swal-custom-popup' },
                            });
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    confirmSubmit() {
                        const selectedCount = this.items.filter(i => i.selected).length;
                        const totalRetur = this.items.reduce((s, i) => s + Math.max(i.qty_po - (Number(i.qty_diterima) || 0), 0), 0);

                        let warningText = `Anda akan memverifikasi ${selectedCount} item barang.`;
                        if (totalRetur > 0) {
                            warningText += ` Terdapat ${totalRetur} karton yang akan masuk retur.`;
                        }

                        Swal.fire({
                            title: 'Konfirmasi Verifikasi?',
                            text: warningText,
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Ya, Verifikasi',
                            cancelButtonText: 'Batal',
                            customClass: { popup: 'swal-custom-popup' },
                        }).then((result) => {
                            if (result.isConfirmed) {
                                this.$refs.poForm.submit();
                            }
                        });
                    }

                }));
            });
        </script>
    @endpush

@endsection