{{-- resources/views/purchase_orders/_barang_list.blade.php --}}
@forelse($barangs as $barang)
    <tr class="hover:bg-blue-50/30 transition-colors">
        <td class="px-4 py-3">
            <span class="font-mono text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $barang->kode_barang }}</span>
        </td>
        <td class="px-4 py-3 font-medium text-gray-800">{{ $barang->nama_barang }}</td>
        <td class="px-4 py-3">
            <span
                class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs font-medium rounded-md">{{ $barang->supplier->namaSupplier ?? 'N/A' }}</span>
        </td>
        <td class="px-4 py-3 font-semibold text-gray-700">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
        <td class="px-4 py-3 text-center">
            <button type="button" @click="addItem({
                            kode_barang: '{{ $barang->kode_barang }}',
                            nama: '{{ addslashes($barang->nama_barang) }}',
                            harga: {{ $barang->harga_beli }},
                            satuan: '{{ $barang->satuan_jual }}'
                        })"
                class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-red-800 text-white rounded-lg text-xs font-semibold hover:bg-red-700 transition-colors shadow-sm">
                <i class="fa-solid fa-plus text-[10px]"></i> Pilih
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-4 py-12 text-center">
            <div class="flex flex-col items-center gap-2">
                <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-box-open text-gray-400"></i>
                </div>
                <p class="text-sm text-gray-500">Tidak ada barang ditemukan.</p>
            </div>
        </td>
    </tr>
@endforelse