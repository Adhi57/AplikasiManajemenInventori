<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReorderPointController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategori = $request->input('kategori');

        // 1. Ambil semua barang beserta total stok (karton)
        $barangs = Barang::with(['kategori', 'supplier'])
            ->select('barangs.*')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('kode_barang', 'like', "%{$search}%")
                      ->orWhere('nama_barang', 'like', "%{$search}%");
                });
            })
            ->when($kategori, function ($query, $kategori) {
                $query->where('kategori_barang_id', $kategori);
            })
            ->orderBy('nama_barang')
            ->get();

        // 2. Hitung D_avg (rata-rata pemakaian per hari dalam karton) per barang
        $periodDays = 4;
        $startDate = Carbon::now()->subDays($periodDays);

        $avgUsageData = DB::table('detail_lap_barang_keluar as dlk')
            ->join('lap_barang_keluar as lbk', 'dlk.lap_keluar_id', '=', 'lbk.lap_keluar_id')
            ->join('barangs as b', 'dlk.kode_barang', '=', 'b.kode_barang')
            ->where('lbk.tanggal_keluar', '>=', $startDate)
            ->groupBy('dlk.kode_barang')
            ->select(
                'dlk.kode_barang',
                DB::raw('SUM(dlk.jumlah_keluar) as total_keluar'),
                DB::raw('MAX(b.jml_barang_per_karton) as jml_per_karton')
            )
            ->get()
            ->keyBy('kode_barang');

        // 3. Hitung Lead Time dari data PO yang sudah diterima
        $leadTimeData = DB::table('detail_lap_barang_masuk as dlm')
            ->join('lap_barang_masuk as lbm', 'dlm.barang_masuk_id', '=', 'lbm.barang_masuk_id')
            ->join('purchase_orders as po', 'lbm.po_id', '=', 'po.po_id')
            ->select(
                'dlm.kode_barang',
                DB::raw('ROUND(AVG(DATEDIFF(lbm.tanggal_masuk, po.tanggal_po)), 0) as l_avg'),
                DB::raw('MAX(DATEDIFF(lbm.tanggal_masuk, po.tanggal_po)) as l_max'),
                DB::raw('COUNT(*) as jumlah_po')
            )
            ->groupBy('dlm.kode_barang')
            ->get()
            ->keyBy('kode_barang');

        // 4. Monthly usage trend (last 6 months) per kode_barang
        $sixMonthsAgo = Carbon::now()->subMonths(6)->startOfMonth();
        $monthlyUsage = DB::table('detail_lap_barang_keluar as dlk')
            ->join('lap_barang_keluar as lbk', 'dlk.lap_keluar_id', '=', 'lbk.lap_keluar_id')
            ->where('lbk.tanggal_keluar', '>=', $sixMonthsAgo)
            ->groupBy('dlk.kode_barang', 'bulan')
            ->select(
                'dlk.kode_barang',
                DB::raw('DATE_FORMAT(lbk.tanggal_keluar, "%Y-%m") as bulan'),
                DB::raw('SUM(dlk.jumlah_keluar) as total_keluar')
            )
            ->get()
            ->groupBy('kode_barang');

        // Build 6-month labels
        $trendLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $trendLabels[] = Carbon::now()->subMonths($i)->format('Y-m');
        }
        $trendLabelNames = array_map(fn($l) => Carbon::parse($l . '-01')->translatedFormat('M'), $trendLabels);

        // 5. Build data array for view
        $items = $barangs->map(function ($barang) use ($avgUsageData, $periodDays, $leadTimeData, $monthlyUsage, $trendLabels) {
            $totalStokKarton = $barang->total_stok ?? 0;
            
            // D_avg
            $usageInfo = $avgUsageData->get($barang->kode_barang);
            $dAvg = 0;
            if ($usageInfo) {
                $jmlPerKarton = $usageInfo->jml_per_karton ?: 1;
                $totalKeluarKarton = $usageInfo->total_keluar / $jmlPerKarton;
                $dAvg = round($totalKeluarKarton / $periodDays, 4);
            }

            // Lead Time dari PO
            $ltInfo = $leadTimeData->get($barang->kode_barang);
            $lAvg = $ltInfo ? (int) $ltInfo->l_avg : 0;
            $lMax = $ltInfo ? (int) $ltInfo->l_max : 0;
            $jumlahPo = $ltInfo ? (int) $ltInfo->jumlah_po : 0;

            // Monthly trend data
            $itemUsage = $monthlyUsage->get($barang->kode_barang, collect());
            $usageByMonth = $itemUsage->keyBy('bulan');
            $trendData = array_map(fn($m) => (float)($usageByMonth[$m]->total_keluar ?? 0), $trendLabels);

            return [
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'kategori' => $barang->kategori->nama_kategori_barang ?? '-',
                'supplier' => $barang->supplier->namaSupplier ?? '-',
                'foto_produk' => $barang->foto_produk,
                'stok_karton' => round($totalStokKarton, 2),
                'd_avg' => $dAvg,
                'l_max' => $lMax,
                'l_avg' => $lAvg,
                'jumlah_po' => $jumlahPo,
                'trend' => $trendData,
            ];
        })->values();

        // Kategori untuk filter
        $kategoris = \App\Models\KategoriBarang::orderBy('nama_kategori_barang')->get();

        // Global trend for chart (aggregate masuk vs keluar 6 months)
        $globalTrendMasuk = DB::table('lap_barang_masuk as lbm')
            ->join('detail_lap_barang_masuk as dlm', 'lbm.barang_masuk_id', '=', 'dlm.barang_masuk_id')
            ->where('lbm.tanggal_masuk', '>=', $sixMonthsAgo)
            ->select(DB::raw('DATE_FORMAT(lbm.tanggal_masuk, "%Y-%m") as bulan'), DB::raw('SUM(dlm.quantity_diterima) as total'))
            ->groupBy('bulan')->orderBy('bulan')->pluck('total', 'bulan');

        $globalTrendKeluar = DB::table('lap_barang_keluar as lbk')
            ->join('detail_lap_barang_keluar as dlk', 'lbk.lap_keluar_id', '=', 'dlk.lap_keluar_id')
            ->where('lbk.tanggal_keluar', '>=', $sixMonthsAgo)
            ->select(DB::raw('DATE_FORMAT(lbk.tanggal_keluar, "%Y-%m") as bulan'), DB::raw('SUM(dlk.jumlah_keluar) as total'))
            ->groupBy('bulan')->orderBy('bulan')->pluck('total', 'bulan');

        $globalMasukData = array_map(fn($m) => (float)($globalTrendMasuk[$m] ?? 0), $trendLabels);
        $globalKeluarData = array_map(fn($m) => (float)($globalTrendKeluar[$m] ?? 0), $trendLabels);

        return view('reorder_point.index', compact(
            'items', 'search', 'kategori', 'kategoris',
            'trendLabelNames', 'globalMasukData', 'globalKeluarData'
        ));
    }
}
