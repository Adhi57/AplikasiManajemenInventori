{{-- resources/views/purchase_orders/_barang_list.blade.php --}}
@forelse($barangs as $barang)
    <tr class="bg-white border-b hover:bg-gray-50">
        <td class="py-3 px-6 font-medium text-gray-900 whitespace-nowrap">{{ $barang->kode_barang }}</td>
        <td class="py-3 px-6">{{ $barang->nama_barang }}</td>
        <td class="py-3 px-6">{{ $barang->supplier->namaSupplier ?? 'N/A' }}</td>
        <td class="py-3 px-6">Rp. {{ number_format($barang->harga_beli, 0, ',', '.') }}</td>
        <td class="py-3 px-6">
            <button type="button" 
                    @click="addItem({
                        kode_barang: '{{ $barang->kode_barang }}',
                        nama: '{{ $barang->nama_barang }}',
                        harga: {{ $barang->harga_beli }},
                        satuan: '{{ $barang->satuan_terkecil }}'
                    })" 
                    class="text-sm px-3 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Pilih
            </button>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="py-4 text-center text-gray-500">Tidak ada barang yang ditemukan.</td>
    </tr>
@endforelse