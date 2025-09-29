@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-12 gap-4">
    
    <!-- Pilih PO -->
    <div class="col-span-4 bg-white rounded-xl shadow p-5">
        <h2 class="text-lg font-semibold mb-3">1. Pilih Surat PO</h2>
        <div class="space-y-2">
            @foreach($po_letters as $po)
                <label class="flex items-center space-x-2 p-2 border rounded hover:bg-gray-50 cursor-pointer">
                    <input type="radio" name="selected_po" value="{{ $po->id }}" class="po-radio">
                    <span>{{ $po->number }} - <span class="text-gray-600">{{ $po->supplier }}</span></span>
                </label>
            @endforeach
        </div>
    </div>

    <!-- Daftar Barang -->
    <div class="col-span-8 bg-white rounded-xl shadow p-5">
        <h2 class="text-lg font-semibold mb-3">2. Daftar Barang (Dari PO)</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border rounded" id="items-table">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2"><input type="checkbox" id="select-all-items"></th>
                        <th class="p-2">ID</th>
                        <th class="p-2">Nama Barang</th>
                        <th class="p-2">Satuan</th>
                        <th class="p-2">Qty PO</th>
                        <th class="p-2">Diterima</th>
                    </tr>
                </thead>
                <tbody id="items-body">
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-3">Pilih PO terlebih dahulu</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex justify-end mt-4 space-x-3">
            <button type="button" id="add-to-return" class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                Tambah ke Retur
            </button>
            <button type="button" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                Konfirmasi Sesuai
            </button>
        </div>
    </div>
</div>

<!-- Barang Retur -->
<div class="bg-white rounded-xl shadow p-5 mt-5">
    <h2 class="text-lg font-semibold mb-3">3. Daftar Barang Retur</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm border rounded" id="return-table">
            <thead class="bg-red-50">
                <tr>
                    <th class="p-2">ID</th>
                    <th class="p-2">Nama Barang</th>
                    <th class="p-2">Satuan</th>
                    <th class="p-2">Qty Retur</th>
                    <th class="p-2">Alasan</th>
                    <th class="p-2">Aksi</th>
                </tr>
            </thead>
            <tbody id="return-body">
                <tr>
                    <td colspan="6" class="text-center text-gray-500 py-3 italic">Belum ada barang retur</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- Script -->
<script>
document.querySelectorAll('.po-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        let poId = this.value;

        fetch(`/get-items/${poId}`)
            .then(res => res.json())
            .then(items => {
                let tbody = document.getElementById('items-body');
                tbody.innerHTML = "";

                if (items.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="6" class="text-center text-gray-500 py-3">Tidak ada barang untuk PO ini</td></tr>`;
                } else {
                    items.forEach(item => {
                        tbody.innerHTML += `
                            <tr>
                                <td class="text-center"><input type="checkbox" class="item-checkbox" data-id="${item.id}" data-name="${item.name}" data-unit="${item.unit}" data-qty="${item.qty}"></td>
                                <td>${item.id}</td>
                                <td>${item.name}</td>
                                <td>${item.unit}</td>
                                <td class="font-bold">${item.qty}</td>
                                <td><input type="number" min="0" max="${item.qty}" value="${item.qty}" class="received-input w-20 border rounded text-center"></td>
                            </tr>
                        `;
                    });
                }
            })
            .catch(err => console.error(err));
    });
});

// Tambah ke retur
document.getElementById('add-to-return').addEventListener('click', () => {
    let selectedItems = document.querySelectorAll('.item-checkbox:checked');
    let returnBody = document.getElementById('return-body');

    if (selectedItems.length === 0) {
        alert("Pilih barang terlebih dahulu!");
        return;
    }

    if (returnBody.querySelector('td') && returnBody.querySelector('td').colSpan === 6) {
        returnBody.innerHTML = "";
    }

    selectedItems.forEach(cb => {
        let id = cb.dataset.id;
        let name = cb.dataset.name;
        let unit = cb.dataset.unit;
        let qty = cb.dataset.qty;
        let receivedInput = cb.closest('tr').querySelector('.received-input');
        let returQty = receivedInput.value;

        returnBody.innerHTML += `
            <tr>
                <td>${id}</td>
                <td>${name}</td>
                <td>${unit}</td>
                <td class="text-red-600 font-bold">${returQty}</td>
                <td><input type="text" placeholder="Alasan retur" class="w-full border rounded px-2 py-1"></td>
                <td><button type="button" class="remove-row text-red-600 hover:text-red-800">Hapus</button></td>
            </tr>
        `;
    });

    selectedItems.forEach(cb => cb.checked = false);
});

// Hapus retur
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-row')) {
        e.target.closest('tr').remove();

        let returnBody = document.getElementById('return-body');
        if (returnBody.children.length === 0) {
            returnBody.innerHTML = `<tr><td colspan="6" class="text-center text-gray-500 py-3 italic">Belum ada barang retur</td></tr>`;
        }
    }
});

// Select all
document.getElementById('select-all-items').addEventListener('change', function() {
    let checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});
</script>
@endsection
