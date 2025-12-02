@foreach($barangs as $b)
<tr class="border-b hover:bg-gray-50">
    <td class="px-3 py-2">{{ $b->kode_barang }}</td>
    <td class="px-3 py-2">{{ $b->nama_barang }}</td>
    <td class="px-3 py-2 text-center">
        {{-- tampilkan stok dalam satuan terkecil --}}
        {{ number_format($b->stok_tersedia, 0, ',', '.') }}
        <span class="text-gray-500 text-xs">({{ $b->satuan_jual }})</span>
    </td>
    <td class="px-3 py-2 text-right">
        Rp {{ number_format($b->harga_jual, 0, ',', '.') }}
        <span class="text-gray-500 text-xs">/ {{ $b->satuan_jual }}</span>
    </td>
    <td class="px-3 py-2 text-center">
        <button 
            type="button"
            @click="$dispatch('tambah-barang', {
                kode_barang: '{{ $b->kode_barang }}',
                nama: '{{ $b->nama_barang }}',
                harga: {{ $b->harga_jual }},
                satuan: '{{ $b->satuan_jual }}',
                stok_tersedia: {{ $b->stok_tersedia }},
                jml_barang_per_karton: {{ $b->jml_barang_per_karton ?? 1 }}
            })"
            class="bg-green-600 text-white text-xs px-3 py-1 rounded hover:bg-green-700 transition">
            Tambah
        </button>
    </td>
    <tr>
    <td colspan="5" class="text-center text-xs border-b text-gray-500 py-1">
        1 karton = {{ $b->jml_barang_per_karton }} {{ $b->satuan_jual }}
    </td>
    </tr>

</tr>
@endforeach
