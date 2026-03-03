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
                DB::raw('ANY_VALUE(b.jml_barang_per_karton) as jml_per_karton')
            )
            ->get()
            ->keyBy('kode_barang');

        // 3. Hitung Lead Time dari data PO yang sudah diterima (Diterima)
        //    Lead Time = DATEDIFF(tanggal_masuk, tanggal_po) per kode_barang
        //    L_avg = rata-rata lead time, L_max = lead time terlama
        $leadTimeData = DB::table('detail_lap_barang_masuk as dlm')
            ->join('lap_barang_masuk as lbm', 'dlm.barang_masuk_id', '=', 'lbm.barang_masuk_id')
            ->join('Purchase_Orders as po', 'lbm.po_id', '=', 'po.po_id')
            ->select(
                'dlm.kode_barang',
                DB::raw('ROUND(AVG(DATEDIFF(lbm.tanggal_masuk, po.tanggal_po)), 0) as l_avg'),
                DB::raw('MAX(DATEDIFF(lbm.tanggal_masuk, po.tanggal_po)) as l_max'),
                DB::raw('COUNT(*) as jumlah_po')
            )
            ->groupBy('dlm.kode_barang')
            ->get()
            ->keyBy('kode_barang');

        // 4. Build data array untuk view (sudah siap untuk Alpine.js)
        $items = $barangs->map(function ($barang) use ($avgUsageData, $periodDays, $leadTimeData) {
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

            return [
                'kode_barang' => $barang->kode_barang,
                'nama_barang' => $barang->nama_barang,
                'kategori' => $barang->kategori->nama_kategori_barang ?? '-',
                'stok_karton' => round($totalStokKarton, 2),
                'd_avg' => $dAvg,
                'l_max' => $lMax,
                'l_avg' => $lAvg,
                'jumlah_po' => $jumlahPo,
            ];
        })->values();

        // Kategori untuk filter
        $kategoris = \App\Models\KategoriBarang::orderBy('nama_kategori_barang')->get();

        return view('reorder_point.index', compact('items', 'search', 'kategori', 'kategoris'));
    }
}
