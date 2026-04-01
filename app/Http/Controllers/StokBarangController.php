<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use App\Models\StokBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Setting;
use Carbon\Carbon;

class StokBarangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategori = $request->input('kategori');
        $group = $request->input('group', false);

        // Ambil semua kategori dari tabel kategori_barangs
        $kategoris = KategoriBarang::orderBy('nama_kategori_barang')->get();

        if ($group) {
            // === GROUP BY BARANG ===
            $stokBarangs = StokBarang::select(
                    'stok_barangs.kode_barang',
                    DB::raw('SUM(stok_barangs.jumlah_stok) as total_karton'),
                    DB::raw('MAX(stok_barangs.tgl_kadaluarsa) as terakhir_kadaluarsa')
                )
                ->join('barangs', 'barangs.kode_barang', '=', 'stok_barangs.kode_barang')
                ->when($search, function ($query, $search) {
                    $query->where('stok_barangs.kode_barang', 'like', "%{$search}%")
                          ->orWhere('barangs.nama_barang', 'like', "%{$search}%");
                })
                ->when($kategori, function ($query, $kategori) {
                    $query->where('barangs.kategori_id', $kategori);
                })
                ->groupBy('stok_barangs.kode_barang')
                ->havingRaw('SUM(stok_barangs.jumlah_stok)') 
                ->orderBy('stok_barangs.kode_barang')
                ->get();

            $stokBarangs->map(function ($stok) {
                $stok->barang = Barang::with('kategori')->where('kode_barang', $stok->kode_barang)->first();
                return $stok;
            });
        } else {
            // === DETAIL PER BATCH ===
            $stokBarangs = StokBarang::with(['barang.kategori'])
                ->when($search, function ($query, $search) {
                    $query->where('kode_barang', 'like', "%{$search}%")
                          ->orWhereHas('barang', function ($q) use ($search) {
                              $q->where('nama_barang', 'like', "%{$search}%");
                          });
                })
                ->when($kategori, function ($query, $kategori) {
                    $query->whereHas('barang', function ($q) use ($kategori) {
                        $q->where('kategori_id', $kategori);
                    });
                })
                ->orderBy('kode_barang')
                ->orderBy('tgl_kadaluarsa')
                ->get();
        }

        // === Hitung kapasitas gudang ===
        $kapasitasMaks = floatval(Setting::get('kapasitas_gudang', 1000));
        $totalStok = $group
            ? $stokBarangs->sum('total_karton')
            : $stokBarangs->sum('jumlah_stok');
        $persentase = $kapasitasMaks > 0 ? round(($totalStok / $kapasitasMaks) * 100, 2) : 0;

        // =============================================
        // DATA DASHBOARD BARU
        // =============================================

        // 1. KPI Summary Data - Aggregate stok per barang
        $stokPerBarang = DB::table('stok_barangs')
            ->join('barangs', 'barangs.kode_barang', '=', 'stok_barangs.kode_barang')
            ->select(
                'stok_barangs.kode_barang',
                'barangs.nama_barang',
                'barangs.foto_produk',
                DB::raw('SUM(stok_barangs.jumlah_stok) as total_stok'),
                DB::raw('MIN(stok_barangs.tgl_kadaluarsa) as earliest_expiry')
            )
            ->groupBy('stok_barangs.kode_barang', 'barangs.nama_barang', 'barangs.foto_produk')
            ->get();

        $totalItems = $stokPerBarang->count();
        $totalKarton = $stokPerBarang->sum('total_stok');
        $stokMenipis = $stokPerBarang->where('total_stok', '<=', 10)->where('total_stok', '>', 0)->count();
        $stokHabis = $stokPerBarang->where('total_stok', '<=', 0)->count();

        // Near expiry count (within 60 days)
        $nearExpiry = DB::table('stok_barangs')
            ->where('jumlah_stok', '>', 0)
            ->where('tgl_kadaluarsa', '<=', Carbon::now()->addDays(60))
            ->where('tgl_kadaluarsa', '>', Carbon::now())
            ->distinct('kode_barang')
            ->count('kode_barang');

        // Already expired count
        $expiredCount = DB::table('stok_barangs')
            ->where('jumlah_stok', '>', 0)
            ->where('tgl_kadaluarsa', '<', Carbon::now())
            ->distinct('kode_barang')
            ->count('kode_barang');

        // 2. Stok per Kategori (for donut chart)
        $stokPerKategori = DB::table('stok_barangs as sb')
            ->join('barangs as b', 'sb.kode_barang', '=', 'b.kode_barang')
            ->join('kategori_barangs as kb', 'b.kategori_barang_id', '=', 'kb.kategori_barang_id')
            ->select('kb.nama_kategori_barang as kategori', DB::raw('SUM(sb.jumlah_stok) as total_stok'))
            ->groupBy('kb.nama_kategori_barang')
            ->orderBy('total_stok', 'desc')
            ->get();

        // 3. Top 8 Stok Items (for bar chart)
        $topStokItems = DB::table('stok_barangs as sb')
            ->join('barangs as b', 'sb.kode_barang', '=', 'b.kode_barang')
            ->select('b.nama_barang', DB::raw('SUM(sb.jumlah_stok) as total_stok'))
            ->groupBy('b.kode_barang', 'b.nama_barang')
            ->orderBy('total_stok', 'desc')
            ->limit(8)
            ->get();

        // 4. Tren Pergerakan Stok (6 bulan terakhir)
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();

        $trendMasuk = DB::table('lap_barang_masuk as lbm')
            ->join('detail_lap_barang_masuk as dlm', 'lbm.barang_masuk_id', '=', 'dlm.barang_masuk_id')
            ->where('lbm.tanggal_masuk', '>=', $sixMonthsAgo)
            ->select(
                DB::raw('DATE_FORMAT(lbm.tanggal_masuk, "%Y-%m") as bulan'),
                DB::raw('SUM(dlm.quantity_diterima) as total')
            )
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        $trendKeluar = DB::table('lap_barang_keluar as lbk')
            ->join('detail_lap_barang_keluar as dlk', 'lbk.lap_keluar_id', '=', 'dlk.lap_keluar_id')
            ->where('lbk.tanggal_keluar', '>=', $sixMonthsAgo)
            ->select(
                DB::raw('DATE_FORMAT(lbk.tanggal_keluar, "%Y-%m") as bulan'),
                DB::raw('SUM(dlk.jumlah_keluar) as total')
            )
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->pluck('total', 'bulan');

        // Build 6-month labels
        $trendLabels = [];
        $trendMasukData = [];
        $trendKeluarData = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthKey = Carbon::now()->subMonths($i)->format('Y-m');
            $monthLabel = Carbon::now()->subMonths($i)->translatedFormat('M Y');
            $trendLabels[] = $monthLabel;
            $trendMasukData[] = $trendMasuk[$monthKey] ?? 0;
            $trendKeluarData[] = $trendKeluar[$monthKey] ?? 0;
        }

        // 5. Alert Items - stok menipis + hampir kadaluarsa
        $alertStokMenipis = DB::table('stok_barangs as sb')
            ->join('barangs as b', 'sb.kode_barang', '=', 'b.kode_barang')
            ->select('b.kode_barang', 'b.nama_barang', 'b.foto_produk', DB::raw('SUM(sb.jumlah_stok) as total_stok'))
            ->groupBy('b.kode_barang', 'b.nama_barang', 'b.foto_produk')
            ->having('total_stok', '<=', 10)
            ->having('total_stok', '>', 0)
            ->orderBy('total_stok', 'asc')
            ->limit(10)
            ->get();

        $alertNearExpiry = DB::table('stok_barangs as sb')
            ->join('barangs as b', 'sb.kode_barang', '=', 'b.kode_barang')
            ->where('sb.jumlah_stok', '>', 0)
            ->where('sb.tgl_kadaluarsa', '<=', Carbon::now()->addDays(60))
            ->select('b.kode_barang', 'b.nama_barang', 'sb.jumlah_stok', 'sb.tgl_kadaluarsa')
            ->orderBy('sb.tgl_kadaluarsa', 'asc')
            ->limit(10)
            ->get();

        return view('stok.index', compact(
            'stokBarangs',
            'search',
            'kategori',
            'kategoris',
            'group',
            'kapasitasMaks',
            'totalStok',
            'persentase',
            // New dashboard data
            'totalItems',
            'totalKarton',
            'stokMenipis',
            'stokHabis',
            'nearExpiry',
            'expiredCount',
            'stokPerKategori',
            'topStokItems',
            'trendLabels',
            'trendMasukData',
            'trendKeluarData',
            'alertStokMenipis',
            'alertNearExpiry'
        ));
    }
}
