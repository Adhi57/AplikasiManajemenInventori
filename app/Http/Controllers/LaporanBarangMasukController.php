<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanBarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'mingguan');

        $query = DB::table('lap_barang_masuk as lbm')
            ->join('suppliers as s', 'lbm.id_supplier', '=', 's.id_supplier')
            ->join('detail_lap_barang_masuk as d', 'lbm.barang_masuk_id', '=', 'd.barang_masuk_id')
            ->join('barangs as b', 'd.kode_barang', '=', 'b.kode_barang')
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
            );

        // 🔹 Filter berdasarkan pilihan
        if ($filter === 'mingguan') {
            $query->whereBetween('lbm.tanggal_masuk', [
                Carbon::now()->startOfWeek(),
                Carbon::now()->endOfWeek(),
            ]);
        } elseif ($filter === 'bulanan') {
            $query->whereMonth('lbm.tanggal_masuk', Carbon::now()->month)
                ->whereYear('lbm.tanggal_masuk', Carbon::now()->year);
        } elseif ($filter === 'rentang' && $request->filled(['start_date', 'end_date'])) {
            $query->whereBetween('lbm.tanggal_masuk', [$request->start_date, $request->end_date]);
        }
        // 🔸 jika "semua", tidak perlu filter tanggal

        $data = $query->orderBy('lbm.tanggal_masuk', 'desc')->get();

        return view('laporan.barang_masuk.index', compact('data', 'filter'));
    }

    public function cetak(Request $request)
    {
        $filter = $request->get('filter', 'mingguan');

        $query = DB::table('lap_barang_masuk as lbm')
            ->join('suppliers as s', 'lbm.id_supplier', '=', 's.id_supplier')
            ->join('detail_lap_barang_masuk as d', 'lbm.barang_masuk_id', '=', 'd.barang_masuk_id')
            ->join('barangs as b', 'd.kode_barang', '=', 'b.kode_barang')
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
            );

        // 🔹 Tentukan periode default
        $start = null;
        $end = null;

        if ($filter === 'mingguan') {
            $start = Carbon::now()->startOfWeek()->format('Y-m-d');
            $end   = Carbon::now()->endOfWeek()->format('Y-m-d');
            $query->whereBetween('lbm.tanggal_masuk', [$start, $end]);
        } elseif ($filter === 'bulanan') {
            $start = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end   = Carbon::now()->endOfMonth()->format('Y-m-d');
            $query->whereMonth('lbm.tanggal_masuk', Carbon::now()->month)
                ->whereYear('lbm.tanggal_masuk', Carbon::now()->year);
        } elseif ($filter === 'rentang' && $request->filled(['start_date', 'end_date'])) {
            $start = $request->start_date;
            $end   = $request->end_date;
            $query->whereBetween('lbm.tanggal_masuk', [$start, $end]);
        } elseif ($filter === 'semua') {
            // Ambil rentang tanggal berdasarkan data pertama dan terakhir
            $first = DB::table('lap_barang_masuk')->orderBy('tanggal_masuk', 'asc')->value('tanggal_masuk');
            $last  = DB::table('lap_barang_masuk')->orderBy('tanggal_masuk', 'desc')->value('tanggal_masuk');
            $start = $first ? Carbon::parse($first)->format('Y-m-d') : '-';
            $end   = $last ? Carbon::parse($last)->format('Y-m-d') : '-';
        }

        $data = $query->orderBy('lbm.tanggal_masuk', 'asc')->get();

        $pdf = Pdf::loadView('laporan.barang_masuk.cetak', [
            'data' => $data,
            'start' => $start,
            'end' => $end,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Barang_Masuk_' . now()->format('d-m-Y') . '.pdf');
    }
}
