<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View; 
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $jumlahProduk = Barang::count('kode_barang');
        $jumlahKarton = StokBarang::sum('jumlah_stok');

        // =============================
        // HITUNG BARANG MASUK MINGGU INI
        // =============================

        $barangMasukBulanIni = DB::table('lap_barang_masuk as lbm')
            ->join('suppliers as s', 'lbm.id_supplier', '=', 's.id_supplier')
            ->join('detail_lap_barang_masuk as d', 'lbm.barang_masuk_id', '=', 'd.barang_masuk_id')
            ->join('barangs as b', 'd.kode_barang', '=', 'b.kode_barang')
            ->whereBetween('lbm.tanggal_masuk', [
                Carbon::now()->startOfMonth(), 
                Carbon::now()->endOfMonth()
            ])
            ->select(
                'lbm.barang_masuk_id',
                'lbm.po_id',
                'lbm.tanggal_masuk',
                's.namaSupplier',
                'b.nama_barang',
                'd.quantity_diterima',
                'd.quantity_rusak',
                'd.satuan',
                'd.harga_satuan',
                'd.subtotal'
            )
            ->orderBy('lbm.tanggal_masuk', 'desc')
            ->get();

        // Total barang masuk Bulan ini
        $totalMasukBulanIni = $barangMasukBulanIni->sum('quantity_diterima');

        return view('dashboard', compact(
            'jumlahProduk',
            'jumlahKarton',
            'barangMasukBulanIni',
            'totalMasukBulanIni'
        ));
    }
}
