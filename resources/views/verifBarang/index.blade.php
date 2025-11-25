@extends('layouts.app')
@section('page-title', 'Verifikasi Barang Masuk')
@section('content')

<!-- Notifikasi & Error Messages -->
@if ($errors->any())
<div class="bg-red-50 border border-red-300 text-red-800 p-4 rounded-xl shadow-md mb-6" role="alert">
    <strong class="font-bold text-lg">Input Tidak Valid!</strong>
    <span class="block sm:inline"> Mohon periksa kembali input barang yang Anda masukkan:</span>
    <ul class="mt-2 list-disc list-inside text-sm">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (session('success'))
<div class="bg-green-50 border border-green-300 text-green-800 p-4 rounded-xl shadow-md mb-6" role="alert">
    {{ session('success') }}
</div>
@endif

@if (session('error'))
<div class="bg-red-50 border border-red-300 text-red-800 p-4 rounded-xl shadow-md mb-6" role="alert">
    {{ session('error') }}
</div>
@endif

<!-- Main Content Grid -->
    <div class="mb-8 p-4">
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">
            Verifikasi Fisik Barang Masuk
        </h1>
        <p class="text-base text-gray-600 border-b pb-4">
            Memastikan dan Mengonfirmasi barang yang diterima secara fisik
        </p>
    </div>

<div x-data="poRetur()" x-init="init()" class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    <!-- Pilih PO (Sidebar - Col 1) -->
    <div class="lg:col-span-4 bg-white rounded-xl shadow-sm p-6 h-fit sticky top-6">
        <h2 class="text-xl font-extrabold mb-4 text-black border-b pb-2">1. Pilih Surat PO</h2>
        <div class="space-y-3 max-h-[70vh] overflow-y-auto pr-2">
            @foreach($purchaseOrders as $po)
            <label class="flex items-start space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-indigo-50 transition duration-150 cursor-pointer">
                <input
                    type="radio"
                    name="selectedPO"
                    value="{{ $po->po_id }}"
                    x-model="selectedPO"
                    @change="fetchItems($event.target.value)"
                    class="mt-1.5 w-5 h-5 accent-indigo-600 focus:ring-indigo-500">
                <div class="flex-grow">
                    <div class="font-semibold text-gray-800">PO: {{ $po->po_id }}</div>
                    <div class="text-sm text-gray-500 truncate">Supplier: {{ $po->supplier->namaSupplier }}</div>
                </div>
            </label>
            @endforeach
        </div>
    </div>

    <!-- Daftar Barang (Form - Col 2) -->
    <div class="lg:col-span-8 bg-white rounded-xl shadow-sm p-6">
        <h2 class="text-xl font-extrabold mb-4 border-b pb-2">2. Daftar Barang (Verifikasi & Retur)</h2>

        <form x-ref="poForm" method="POST" action="{{ route('verifBarang.store') }}">
            @csrf
            <input type="hidden" name="po_id" x-model="selectedPO">

            <div class="overflow-x-auto rounded-lg border border-gray-200">
                <table class="min-w-full divide-y divide-gray-200 text-sm text-left">
                    <thead class="text-xs text-white uppercase bg-red-700 border-b-4 border-red-500">
                        <tr>
                            <th class="p-3 text-center w-10">Verif</th>
                            <th class="p-3">Kode Barang</th>
                            <th class="p-3">Nama Barang</th>
                            <th class="p-3 text-center">Qty PO (Karton)</th>
                            <th class="p-3 text-center">Qty Diterima</th>
                            <th class="p-3 text-center">Qty Retur</th>
                            <th class="p-3 text-center">Tgl Kadaluarsa</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        <template x-if="items.length === 0">
                            <tr>
                                <td colspan="7" class="p-5 text-center text-gray-500 italic">
                                    Pilih Surat PO di sebelah kiri untuk menampilkan daftar barang.
                                </td>
                            </tr>
                        </template>

                        <template x-for="(item, index) in items" :key="item.kode_barang">
                            <tr class="hover:bg-gray-50 transition">
                                <!-- Checkbox -->
                                <td class="p-3 text-center">
                                    <!-- Hidden input to ensure 'selected' status is always submitted, even if unchecked (value 0) -->
                                    <input type="hidden" :name="`items[${index}][selected]`" :value="item.selected ? 1 : 0">

                                    <input type="checkbox"
                                        x-model="item.selected"
                                        value="1"
                                        class="accent-green-600 w-4 h-4 focus:ring-green-500 rounded">
                                </td>

                                <!-- Kode Barang -->
                                <td class="p-3 font-medium text-gray-800" x-text="item.kode_barang"></td>

                                <!-- Nama Barang -->
                                <td class="p-3 text-gray-600" x-text="item.nama_barang"></td>

                                <!-- Qty PO -->
                                <td class="p-3 text-center font-bold text-gray-700" x-text="item.qty_po"></td>

                                <!-- Qty Diterima -->
                                <td class="p-3 text-center">
                                    <input type="number"
                                        min="0"
                                        :max="item.qty_po"
                                        x-model.number="item.qty_diterima"
                                        @input="item.selected = true" {{-- Auto-select when quantity is entered --}}
                                        :name="`items[${index}][qty_diterima]`"
                                        class="border border-gray-300 rounded-md w-24 p-1.5 text-center transition duration-150 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                                </td>

                                <!-- Qty Retur otomatis -->
                                <td class="p-3 text-center text-red-600 font-extrabold">
                                    <span x-text="Math.max(item.qty_po - (item.qty_diterima || 0), 0)"></span>
                                    {{-- Hidden input for Retur Qty (calculated on backend, but good to submit PO Qty for verification) --}}
                                    <input type="hidden" :name="`items[${index}][qty_retur]`" :value="Math.max(item.qty_po - (item.qty_diterima || 0), 0)">
                                </td>

                                <!-- Tanggal Kadaluarsa -->
                                <td class="p-3 text-center">
                                <input type="date"
                                    x-model="item.tgl_kadaluarsa"
                                    :name="`items[${index}][tgl_kadaluarsa]`"
                                    required
                                    class="border border-gray-300 rounded-md px-2 py-1.5 w-40 text-sm transition duration-150 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    >

                                <!-- Hidden Inputs -->
                                <input type="hidden" :name="`items[${index}][kode_barang]`" :value="item.kode_barang">
                                <input type="hidden" :name="`items[${index}][qty_po]`" :value="item.qty_po">
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <button type="submit"
                :disabled="!selectedPO || items.length === 0"
                class="flex items-center justify-center gap-3 mt-6 px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed focus:ring-4 focus:ring-indigo-300 focus:outline-none">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2l4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Konfirmasi Verifikasi Barang Masuk
            </button>

        </form>

    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('poRetur', () => ({
            selectedPO: null,
            items: [],

            init() {
                // Check if there was old input (in case of validation error)
                @if(old('po_id'))
                    this.selectedPO = '{{ old('po_id') }}';
                    // Re-fetch items if selectedPO is set from old input
                    this.fetchItems(this.selectedPO, true);
                @endif
            },

            async fetchItems(poId, isOldInput = false) {
                this.items = []; // reset
                try {
                    // Endpoint might need to be adjusted based on your Laravel route
                    const res = await fetch(`/verifBarang/get-items/${poId}`);
                    if (!res.ok) throw new Error('Gagal memuat data detail PO');

                    const data = await res.json();
                    
                    let fetchedItems = data.data || data; // Handle both direct array and {data: array}
                    
                    // Map items to include reactive properties for Alpine and apply old input if available
                    this.items = fetchedItems.map(item => {
                        let qty_diterima_default = item.qty_po;
                        let tgl_kadaluarsa_default = '';
                        let selected_default = false;

                        // Check for old input to persist user changes after validation failure
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
                            // Ensure qty_diterima is reactive and initialized, defaulting to qty_po
                            qty_diterima: qty_diterima_default, 
                            // Ensure tgl_kadaluarsa is reactive
                            tgl_kadaluarsa: tgl_kadaluarsa_default,
                            // Ensure selected is reactive
                            selected: selected_default || isOldInput, // Auto-select when loaded if using old input
                        };
                    });

                } catch (err) {
                    console.error('Error fetching PO items:', err);
                    alert('Gagal memuat data detail PO: ' + err.message);
                }
            }

        }))
    });
</script>

@endsection