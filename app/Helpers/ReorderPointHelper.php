<?php

namespace App\Helpers;

use App\Models\Barang;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReorderPointHelper
{
    /**
     * Dapatkan daftar barang yang perlu di reorder berdasarkan perhitungan Reorder Point.
     * Mengembalikan collection barang yang di dalamnya terdapat atribut 'rop_value'.
     */
    public static function getItemsToReorder()
    {
        // 1. Ambil semua barang
        $barangs = Barang::with(['kategori', 'supplier'])->select('barangs.*')->get();

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
        $leadTimeData = DB::table('detail_lap_barang_masuk as dlm')
            ->join('lap_barang_masuk as lbm', 'dlm.barang_masuk_id', '=', 'lbm.barang_masuk_id')
            ->join('Purchase_Orders as po', 'lbm.po_id', '=', 'po.po_id')
            ->select(
                'dlm.kode_barang',
                DB::raw('ROUND(AVG(DATEDIFF(lbm.tanggal_masuk, po.tanggal_po)), 0) as l_avg'),
                DB::raw('MAX(DATEDIFF(lbm.tanggal_masuk, po.tanggal_po)) as l_max')
            )
            ->groupBy('dlm.kode_barang')
            ->get()
            ->keyBy('kode_barang');

        $itemsToReorder = collect();

        foreach ($barangs as $barang) {
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

            // Safety Stock
            $ss = $dAvg * ($lMax - $lAvg);
            
            // ROP
            $rop = ($dAvg * $lAvg) + $ss;

            // Jika butuh reorder
            if ($rop > 0 && $totalStokKarton <= $rop) {
                $barang->rop_value = $rop;
                $barang->stok_saat_ini = $totalStokKarton;
                $itemsToReorder->push($barang);
            }
        }

        return $itemsToReorder;
    }
}
