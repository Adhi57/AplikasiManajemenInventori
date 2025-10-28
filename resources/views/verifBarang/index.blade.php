@extends('layouts.app')

@section('content')

@if ($errors->any())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Ada Kesalahan Input!</strong>
        <span class="block sm:inline">Periksa daftar barang Anda:</span>
        <ul class="mt-2 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        {{ session('success') }}
    </div>
@endif

@if (session('error'))
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
        {{ session('error') }}
    </div>
@endif

<div x-data="poRetur()" x-init="init()" class="grid grid-cols-1 md:grid-cols-12 gap-4">

    <!-- Pilih PO -->
    <div class="col-span-4 bg-white rounded-xl shadow p-5">
        <h2 class="text-lg font-semibold mb-3">1. Pilih Surat PO</h2>
        <div class="space-y-2">
            @foreach($purchaseOrders as $po)
            <label class="flex items-start space-x-3 p-2 border rounded-md hover:bg-gray-50 cursor-pointer mb-2">
                <input
                    type="radio"
                    name="selectedPO"
                    value="{{ $po->po_id }}"
                    x-model="selectedPO"
                    @change="fetchItems($event.target.value)"
                    class="mt-1 accent-blue-600">
                <div>
                    <div class="font-semibold text-gray-700">{{ $po->po_id }}</div>
                    <div class="text-sm text-gray-500">Supplier: {{ $po->supplier->namaSupplier }}</div>
                </div>
            </label>
            @endforeach
        </div>
    </div>

    <!-- Daftar Barang (Form) -->
    <div class="col-span-8 bg-white rounded-xl shadow p-5">
        <h2 class="text-lg font-semibold mb-3">2. Daftar Barang (Dari PO)</h2>

        <form x-ref="poForm" method="POST" action="{{ route('verifBarang.store') }}">
            @csrf
            <input type="hidden" name="po_id" x-model="selectedPO">

            <div class="overflow-x-auto">
    <table class="min-w-full border border-gray-300 text-sm text-left">
        <thead class="bg-gray-100 text-gray-700 font-semibold">
            <tr>
                <th class="p-2 text-center w-10">Pilih</th>
                <th class="p-2">Kode Barang</th>
                <th class="p-2">Nama Barang</th>
                <th class="p-2 text-center">Qty PO</th>
                <th class="p-2 text-center">Qty Diterima</th>
                <th class="p-2 text-center">Qty Retur</th>
                <th class="p-2 text-center">Tgl Kadaluarsa</th>
            </tr>
        </thead>

        <tbody>
            <template x-for="(item, index) in items" :key="item.kode_barang">
                <tr class="border-b hover:bg-gray-50 transition">
                    <!-- Checkbox -->
                    <td class="p-2 text-center">
                        <input type="hidden" :name="`items[${index}][selected]`" :value="item.selected ? 1 : 0">

                        <input type="checkbox"
                            x-model="item.selected"
                            value="1"
                            class="accent-blue-600 w-4 h-4">
                    </td>

                    <!-- Kode Barang -->
                    <td class="p-2 font-medium text-gray-700" x-text="item.kode_barang"></td>

                    <!-- Nama Barang -->
                    <td class="p-2 text-gray-600" x-text="item.nama_barang"></td>

                    <!-- Qty PO -->
                    <td class="p-2 text-center text-gray-700" x-text="item.qty_po"></td>

                    <!-- Qty Diterima -->
                    <td class="p-2 text-center">
                        <input type="number"
                            min="0"
                            :max="item.qty_po"
                            x-model.number="item.qty_diterima"
                            :name="`items[${index}][qty_diterima]`"
                            class="border rounded w-20 text-center focus:ring focus:ring-blue-200">
                    </td>

                    <!-- Qty Retur otomatis -->
                    <td class="p-2 text-center text-red-500 font-semibold">
                        <span x-text="Math.max(item.qty_po - (item.qty_diterima || 0), 0)"></span>
                    </td>

                    <!-- Tanggal Kadaluarsa -->
                    <td class="p-2 text-center">
                        <input type="date"
                            x-model="item.tgl_kadaluarsa"
                            :name="`items[${index}][tgl_kadaluarsa]`"
                            class="border rounded px-2 py-1 w-40 text-sm focus:ring focus:ring-blue-200">
                    </td>

                    <!-- Hidden Inputs -->
                    <input type="hidden" :name="`items[${index}][kode_barang]`" :value="item.kode_barang">
                    <input type="hidden" :name="`items[${index}][qty_po]`" :value="item.qty_po">
                </tr>
            </template>
        </tbody>
    </table>
</div>


            <button type="submit">Konfirmasi Sesuai</button>
        </form>

    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('poRetur', () => ({
                selectedPO: null,
                items: [],

                init() {
                    console.log('Component initialized');
                },

                async fetchItems(poId) {
                    this.items = []; // reset dulu
                    try {
                        const res = await fetch(`/verifBarang/get-items/${poId}`);
                        if (!res.ok) throw new Error('Gagal fetch data');

                        const data = await res.json();
                        // Cek apakah data array
                        if (Array.isArray(data)) {
                            this.items = data;
                        } else if (Array.isArray(data.data)) {
                            this.items = data.data;
                        } else {
                            throw new Error('Format data tidak sesuai');
                        }

                        console.log(this.items); // debug
                    } catch (err) {
                        console.error(err);
                        alert('Gagal memuat data detail PO');
                    }
                }

            }))
        });
    </script>

    @endsection