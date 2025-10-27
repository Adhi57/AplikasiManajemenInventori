@extends('layouts.app')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-12 gap-4">
    
    <!-- Pilih PO -->
    <div class="col-span-4 bg-white rounded-xl shadow p-5">
        <h2 class="text-lg font-semibold mb-3">1. Pilih Surat PO</h2>
        <div class="space-y-2">
            @forelse($po_list as $po)
                <label class="flex flex-col space-y-1 p-3 border border-gray-200 rounded-lg hover:bg-blue-50 cursor-pointer transition duration-150 ease-in-out has-[:checked]:border-blue-500 has-[:checked]:bg-blue-100">
                    <div class="flex items-center space-x-2">
                        <input type="radio" name="selected_po" value="{{ $po['id'] }}" class="po-radio text-blue-600 focus:ring-blue-500" required>
                        <span class="font-medium text-gray-800">{{ $po['number'] }}</span>
                    </div>
                    <span class="text-xs text-gray-500 ml-6">Supplier: {{ $po['supplier'] }}</span>
                </label>
            @empty
                <p class="text-gray-500 italic text-center py-5">Tidak ada PO yang siap diterima saat ini.</p>
            @endforelse
        </div>
    </div>

    <!-- Daftar Barang -->
    <div class="col-span-8 bg-white rounded-xl shadow p-5">
        <h2 class="text-lg font-semibold mb-3">2. Daftar Barang (Dari PO)</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse rounded-lg overflow-hidden" id="items-table">
                <thead class="bg-gray-100 border-b-2 border-gray-200">
                    <tr>
                        <th class="p-3 text-center w-10">
                            <input type="checkbox" id="select-all-items" class="rounded text-blue-600">
                        </th>
                        <th class="p-3 text-left">Kode Barang</th>
                        <th class="p-3 text-left">Nama Barang</th>
                        <th class="p-3 text-center">Qty PO</th>
                        <th class="p-3 text-center">Qty Diterima</th>
                        <th class="p-3 text-center text-red-600">Qty Retur</th>
                    </tr>
                </thead>
                <tbody id="items-body" class="divide-y divide-gray-100">
                    <tr>
                        <td colspan="6" class="text-center text-gray-500 py-6 italic">Pilih PO terlebih dahulu untuk memuat detail barang.</td>
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
function updateItemsEmptyState(message) {
    document.getElementById('items-body').innerHTML = `
        <tr><td colspan="6" class="text-center text-gray-500 py-6 italic">${message}</td></tr>`;
}

function renderItemsTable(items) {
    let tbody = document.getElementById('items-body');
    tbody.innerHTML = '';

    if (!items.length) {
        updateItemsEmptyState("Tidak ada barang pada PO ini.");
        return;
    }

    items.forEach(item => {
        tbody.innerHTML += `
            <tr>
                <td class="text-center">
                    <input type="checkbox" class="item-checkbox" 
                        data-id="${item.id}" 
                        data-name="${item.name}" 
                        data-unit="${item.unit}" 
                        data-qty="${item.qty_po}">
                </td>
                <td class="p-2">${item.kode_barang}</td>
                <td class="p-2">${item.name}</td>
                <td class="p-2 text-center">${item.qty_po}</td>
                <td class="p-2 text-center">
                    <input type="number" class="received-input border rounded w-20 text-center" 
                        value="${item.qty_po}" min="0" max="${item.qty_po}">
                </td>
                <td class="p-2 text-center text-red-600 font-bold">0</td>
            </tr>
        `;
    });
}

// Event ketika PO dipilih
document.querySelectorAll('.po-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        let poId = this.value;
        updateItemsEmptyState("Memuat detail barang...");


        fetch(url)
            .then(res => {
                if (!res.ok) throw new Error('Gagal memuat data PO.');
                return res.json();
            })
            .then(items => {
                renderItemsTable(items);
            })
            .catch(err => {
                console.error(err);
                updateItemsEmptyState("Gagal memuat detail barang. Silakan coba lagi.");
            });
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
