<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Pengiriman;
use App\Models\PurchaseOrder;
use App\Models\ReturBarang;
use App\Models\StokBarang;
use App\Models\Supplier;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View; 
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Data Ringkasan Utama
        $jumlahProduk = Cache::remember('dashboard_jumlah_produk', now()->addMinutes(30), fn() => Barang::count('kode_barang'));
        $jumlahKarton = Cache::remember('dashboard_jumlah_karton', now()->addMinutes(30), fn() => StokBarang::sum('jumlah_stok'));
        $jumlahKartonRusak = Cache::remember('dashboard_jumlah_karton_rusak', now()->addMinutes(30), fn() => StokBarang::sum('jumlah_stok_rusak'));
        $totalSupplier = Cache::remember('dashboard_total_supplier', now()->addMinutes(30), fn() => Supplier::count());
        $totalPelanggan = Cache::remember('dashboard_total_pelanggan', now()->addMinutes(30), fn() => Pelanggan::count());

        // =============================
        // 2. HITUNG BARANG MASUK BULAN INI
        // =============================
        $pageMasuk = request()->get('page', 1);
        $barangMasukBulanIni = Cache::remember('dashboard_barang_masuk_bulan_ini_page_' . $pageMasuk, now()->addMinutes(30), function() {
            return DB::table('lap_barang_masuk as lbm')
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
        });

        $totalMasukBulanIni = Cache::remember('dashboard_total_masuk_bulan_ini', now()->addMinutes(30), function() {
            return DB::table('lap_barang_masuk as lbm')
                ->join('detail_lap_barang_masuk as d', 'lbm.barang_masuk_id', '=', 'd.barang_masuk_id')
                ->whereBetween('lbm.tanggal_masuk', [
                    Carbon::now()->startOfMonth(), 
                    Carbon::now()->endOfMonth()
                ])
                ->sum('d.quantity_diterima');
        });

        $totalRusakBulanIni = Cache::remember('dashboard_total_rusak_bulan_ini', now()->addMinutes(30), function() {
            return DB::table('lap_barang_masuk as lbm')
                ->join('detail_lap_barang_masuk as d', 'lbm.barang_masuk_id', '=', 'd.barang_masuk_id')
                ->whereBetween('lbm.tanggal_masuk', [
                    Carbon::now()->startOfMonth(), 
                    Carbon::now()->endOfMonth()
                ])
                ->sum('d.quantity_rusak');
        });

        // =============================
        // 3. HITUNG BARANG KELUAR BULAN INI
        // =============================
        $totalKeluarBulanIni = Cache::remember('dashboard_total_keluar_bulan_ini', now()->addMinutes(30), function() {
            return DB::table('lap_barang_keluar as lbk')
                ->join('detail_lap_barang_keluar as dlk', 'lbk.lap_keluar_id', '=', 'dlk.lap_keluar_id')
                ->join('barangs as b', 'dlk.kode_barang', '=', 'b.kode_barang')
                ->whereBetween('lbk.tanggal_keluar', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ])
                ->sum(DB::raw('dlk.jumlah_keluar / b.jml_barang_per_karton'));
        });
    
        // =============================
        // 4. BARANG Terlaris
        // =============================
        // Note: pagination cannot be cached directly. Get the data then paginate manually or cache the whole collection
        // Since it's a dashboard (page 1 is mostly what we care about), we will cache the top 5 limit instead of paginating if possible,
        // but to keep compatibility with paginate(5), we will not cache the pagination object, but the base query results instead.
        // Actually, for simplicity and caching, we can just fetch top 5 items. If pagination is needed, caching is trickier.
        // I will keep paginate(5) but caching it requires caution. The easiest way is to cache the current page.
        $page = request()->get('page', 1);
        $barangTerlaris = Cache::remember('dashboard_barang_terlaris_page_' . $page, now()->addMinutes(30), function() {
            return DB::table('lap_barang_keluar as lbk')
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
                ->paginate(5);
        });

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
    
        $statusPengiriman = Cache::remember('dashboard_status_pengiriman', now()->addMinutes(30), function() use ($startOfWeek, $endOfWeek) {
            return Pengiriman::select('status_pengiriman', DB::raw('COUNT(*) as total'))
                ->whereBetween('tanggal_pengiriman', [$startOfWeek, $endOfWeek])
                ->groupBy('status_pengiriman')
                ->pluck('total','status_pengiriman');
        });

        $barangMasuk = Cache::remember('dashboard_barang_masuk', now()->addMinutes(30), function() {
            return DB::table('lap_barang_masuk')
                ->select(DB::raw('MONTH(tanggal_masuk) as bulan'), DB::raw('COUNT(*) as total'))
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->pluck('total', 'bulan');
        });

        $barangKeluar = Cache::remember('dashboard_barang_keluar', now()->addMinutes(30), function() {
            return DB::table('lap_barang_keluar')
                ->select(DB::raw('MONTH(tanggal_keluar) as bulan'), DB::raw('COUNT(*) as total'))
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->pluck('total', 'bulan');
        });

        // Omset per bulan (subtotal barang keluar)
        $omsetBulanan = Cache::remember('dashboard_omset_bulanan', now()->addMinutes(30), function() {
            return DB::table('lap_barang_keluar')
                ->select(DB::raw('MONTH(tanggal_keluar) as bulan'), DB::raw('SUM(total_akhir) as total'))
                ->groupBy('bulan')
                ->orderBy('bulan')
                ->pluck('total', 'bulan');
        });

        // =============================
        // 5. OMSET BULAN INI
        // =============================
        $omsetBulanIni = Cache::remember('dashboard_omset_bulan_ini', now()->addMinutes(30), function() {
            return DB::table('lap_barang_keluar')
                ->whereBetween('tanggal_keluar', [
                    Carbon::now()->startOfMonth(),
                    Carbon::now()->endOfMonth()
                ])
                ->sum('total_akhir');
        });

        // =============================
        // 6. LOW STOCK ITEMS (stok <= 10 karton)
        // =============================
        $lowStockItems = Cache::remember('dashboard_low_stock_items', now()->addMinutes(30), function() {
            return DB::table('stok_barangs as sb')
                ->join('barangs as b', 'sb.kode_barang', '=', 'b.kode_barang')
                ->select('b.kode_barang', 'b.nama_barang', 'b.foto_produk', DB::raw('SUM(sb.jumlah_stok) as total_stok'))
                ->groupBy('b.kode_barang', 'b.nama_barang', 'b.foto_produk')
                ->having('total_stok', '<=', 10)
                ->having('total_stok', '>', 0)
                ->orderBy('total_stok', 'asc')
                ->limit(5)
                ->get();
        });

        // =============================
        // 7. RETUR BARANG TERBARU
        // =============================
        $recentReturs = ReturBarang::with(['barang', 'purchaseOrder'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // =============================
        // 8. STOK PER KATEGORI (for chart)
        // =============================
        $stokPerKategori = Cache::remember('dashboard_stok_per_kategori', now()->addMinutes(30), function() {
            return DB::table('stok_barangs as sb')
                ->join('barangs as b', 'sb.kode_barang', '=', 'b.kode_barang')
                ->join('kategori_barangs as kb', 'b.kategori_barang_id', '=', 'kb.kategori_barang_id')
                ->select('kb.nama_kategori_barang as kategori', DB::raw('SUM(sb.jumlah_stok) as total_stok'))
                ->groupBy('kb.nama_kategori_barang')
                ->orderBy('total_stok', 'desc')
                ->get();
        });

        return view('dashboard', compact(
            'jumlahProduk',
            'jumlahKarton',
            'jumlahKartonRusak',
            'barangMasukBulanIni',
            'totalMasukBulanIni',
            'totalRusakBulanIni',
            'totalKeluarBulanIni',
            'barangTerlaris',
            'poPending',
            'sjPending',
            'barangExpired',
            'statusPengiriman',
            'barangMasuk',
            'barangKeluar',
            'omsetBulanan',
            'totalSupplier',
            'totalPelanggan',
            'omsetBulanIni',
            'lowStockItems',
            'recentReturs',
            'stokPerKategori'
        ));
    }
}