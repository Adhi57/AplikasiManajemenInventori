<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarcodeScannerController extends Controller
{
    /**
     * Lookup a barang by its barcode (kode_barang).
     * Returns JSON with barang data if found.
     */
    public function lookup(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255',
        ]);

        $barang = Barang::with(['kategori', 'supplier'])
            ->where('kode_barang', $request->code)
            ->first();

        if ($barang) {
            // Calculate available stock from non-expired batches
            $today = now()->toDateString();
            $totalKarton = DB::table('stok_barangs')
                ->where('kode_barang', $barang->kode_barang)
                ->where('tgl_kadaluarsa', '>', $today)
                ->sum('jumlah_stok');

            $stokTersedia = $totalKarton * ($barang->jml_barang_per_karton ?? 1);

            return response()->json([
                'found' => true,
                'barang' => [
                    'kode_barang'   => $barang->kode_barang,
                    'nama_barang'   => $barang->nama_barang,
                    'harga_beli'    => $barang->harga_beli,
                    'harga_jual'    => $barang->harga_jual,
                    'satuan_jual'   => $barang->satuan_jual,
                    'kategori'      => $barang->kategori->nama_kategori_barang ?? null,
                    'supplier'      => $barang->supplier->namaSupplier ?? null,
                    'id_supplier'   => $barang->id_supplier,
                    'kategori_barang_id' => $barang->kategori_barang_id,
                    'tipe_harga_barang'  => $barang->tipe_harga_barang,
                    'jml_barang_per_karton' => $barang->jml_barang_per_karton,
                    'stok_tersedia' => $stokTersedia,
                ],
            ]);
        }

        return response()->json(['found' => false]);
    }
}
