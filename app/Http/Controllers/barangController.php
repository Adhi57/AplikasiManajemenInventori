<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use App\Models\Supplier;
use App\Models\Barang;
use App\Models\HargaBarang;
use App\Models\stokBarang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    // Menampilkan semua barang
    public function index()
    {
        $barangs = Barang::with(['supplier', 'kategori', 'hargaBarang', 'stok'])->get();

        return view('barangs.index', compact('barangs'));
    }

    // Menampilkan Form Create
    public function create()
    {
        $kategoriBarangs = KategoriBarang::all();
        $suppliers = Supplier::all();

        return view('barangs.create', compact('kategoriBarangs', 'suppliers'));
    }

    public function store(Request $request)
    {
        // 1. Validasi data input
        $validatedData = $request->validate([
            'nama_barang'        => 'required|string|max:255',
            'nama_satuan'        => 'required|string|max:50',
            'kategori_barang_id' => 'required|string|exists:kategori_barangs,kategori_barang_id',
            'id_supplier'        => 'required|integer|exists:suppliers,id_supplier',

            // Harga barang
            'tipe_harga_barang'  => 'required|in:Eceran,Grosir,Diskon',
            'harga_beli'         => 'required|numeric|min:0',
            'harga_jual'         => 'required|numeric|min:0',
            'berlaku_mulai'      => 'required|date',

            // Stok
            'jumlah_stok'        => 'nullable|integer|min:0',
            'jumlah_stok_rusak'  => 'nullable|integer|min:0',
            'tgl_kadaluarsa'     => 'required|date',
        ]);


        $barang = Barang::create([
            'nama_barang'        => $validatedData['nama_barang'],
            'nama_satuan'        => $validatedData['nama_satuan'],
            'kategori_barang_id' => $validatedData['kategori_barang_id'],
            'id_supplier'        => $validatedData['id_supplier'],
        ]);


        $hargaBarang = HargaBarang::create([
            'kode_barang'       => $barang->kode_barang, 
            'tipe_harga_barang' => $validatedData['tipe_harga_barang'],
            'harga_beli'        => $validatedData['harga_beli'],
            'harga_jual'        => $validatedData['harga_jual'],
            'berlaku_mulai'     => $validatedData['berlaku_mulai'],
            'berlaku_sampai'    => null,
        ]);


        stokBarang::create([
            'kode_barang'       => $barang->kode_barang,
            'jumlah_stok'       => $validatedData['jumlah_stok'] ?? 0,
            'jumlah_stok_rusak' => $validatedData['jumlah_stok_rusak'] ?? 0,
            'tgl_kadaluarsa'    => $validatedData['tgl_kadaluarsa'],
            'updated_at'        => now(),
        ]);

        return redirect()->route('barangs.index')
            ->with('success', 'Data Barang berhasil ditambahkan dengan kode: ' . $barang->kode_barang);
    }

    public function destroy($kode_barang)
    {
        // cari barang berdasarkan primary key
        $barang = Barang::where('kode_barang', $kode_barang)->firstOrFail();

        // hapus record
        $barang->delete();

        return redirect()->route('barangs.index')
            ->with('success', 'Data barang berhasil dihapus.');
    }
}
