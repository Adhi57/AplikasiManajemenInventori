<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pengiriman;
use App\Models\PurchaseOrder;
use App\Models\StokBarang;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View; 
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Data Ringkasan Utama
        $jumlahProduk = Barang::count('kode_barang'); // Total varian produk
        $jumlahKarton = StokBarang::sum('jumlah_stok');
        $jumlahKartonRusak = StokBarang::sum('jumlah_stok_rusak');

        // =============================
        // 2. HITUNG BARANG MASUK BULAN INI
        // =============================

        // Mengambil detail barang masuk bulan ini (Digunakan untuk tabel atau grafik)
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
            ->paginate(5);
        

        
        // Total quantity barang masuk Bulan ini (untuk kartu statistik)
        $totalMasukBulanIni = $barangMasukBulanIni->sum('quantity_diterima');

        // =============================
        // 3. HITUNG BARANG KELUAR BULAN INI
        // =============================

        $totalKeluarBulanIni = DB::table('lap_barang_keluar as lbk')
        ->join('detail_lap_barang_keluar as dlk', 'lbk.lap_keluar_id', '=', 'dlk.lap_keluar_id')
        ->join('barangs as b', 'dlk.kode_barang', '=', 'b.kode_barang')
        ->whereBetween('lbk.tanggal_keluar', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth()
        ])
        ->sum(DB::raw('dlk.jumlah_keluar / b.jml_barang_per_karton'));
    
        // =============================
        // 4. BARANG Terlaris
        // =============================

        $barangTerlaris = DB::table('lap_barang_keluar as lbk')
        ->join('detail_lap_barang_keluar as dlk', 'lbk.lap_keluar_id', '=', 'dlk.lap_keluar_id')
        ->join('barangs as b', 'dlk.kode_barang', '=', 'b.kode_barang')
        ->join('kategori_barangs as kb', 'b.kategori_barang_id', '=', 'kb.kategori_barang_id')
        ->selectRaw('ANY_VALUE(lbk.lap_keluar_id) as lap_keluar_id,
            dlk.kode_barang,
            b.satuan_jual,
            b.foto_produk,
            ANY_VALUE(b.nama_barang) as nama_barang,
            ANY_VALUE(kb.nama_kategori_barang) as nama_kategori,
            COUNT(dlk.kode_barang) as kali_terjual')
        ->groupBy('dlk.kode_barang')
        ->orderBy('kali_terjual', 'desc')
        ->paginate(perPage:5);

        $poPending = PurchaseOrder::where('status_po', 'Pending')
        ->orderBy('created_at', 'desc')
        ->paginate(3);
    
        $sjPending = SuratJalan::where('status', 'Pending')
        ->orderBy('created_at', 'desc')
        ->paginate(3);

        $barangExpired = StokBarang::with('barang')
        ->where('tgl_kadaluarsa', '>=', Carbon::now())
        ->where('tgl_kadaluarsa', '<=', Carbon::now()->addDays(60))             
        ->orderBy('tgl_kadaluarsa', 'asc')
        ->limit(3)
        ->get();

        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek   = Carbon::now()->endOfWeek();
    
        $statusPengiriman = Pengiriman::select('status_pengiriman', DB::raw('COUNT(*) as total'))
            ->whereBetween('tanggal_pengiriman', [$startOfWeek, $endOfWeek])
            ->groupBy('status_pengiriman')
            ->pluck('total','status_pengiriman');

        $barangMasuk = DB::table('lap_barang_masuk')
            ->select(DB::raw('MONTH(tanggal_masuk) as bulan'), DB::raw('COUNT(*) as total'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        return view('dashboard', compact(
            'jumlahProduk',
            'jumlahKarton',
            'jumlahKartonRusak',
            'barangMasukBulanIni',
            'totalMasukBulanIni',
            'totalKeluarBulanIni',
            'barangTerlaris',
            'poPending',
            'sjPending',
            'barangExpired',
            'statusPengiriman',
            'barangMasuk'
        ));
    }
}