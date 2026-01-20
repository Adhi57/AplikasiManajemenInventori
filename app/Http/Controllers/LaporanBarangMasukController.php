<?php

namespace App\Http\Controllers;

use App\Models\LapBarangMasuk;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanBarangMasukController extends Controller
{
    public function index(Request $request)
    {
        $query = LapBarangMasuk::with([
                'supplier',
                'details.barang'
            ])
            ->orderByDesc('tanggal_masuk');

        // 🔍 Filter tanggal (dari - sampai)
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_masuk', [$request->dari, $request->sampai]);
        } elseif ($request->filled('dari')) {
            $query->whereDate('tanggal_masuk', '>=', $request->dari);
        } elseif ($request->filled('sampai')) {
            $query->whereDate('tanggal_masuk', '<=', $request->sampai);
        }
        
        // 🔍 Filter No PO atau Supplier
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('po_id', 'like', "%{$search}%")
                ->orWhereHas('supplier', function ($qs) use ($search) {
                    $qs->where('namaSupplier', 'like', "%{$search}%");
                });
            });
        }


        $laporans = $query->get();

        return view('laporan.barang_masuk.index', compact('laporans'));
    }

    public function cetak(Request $request)
    {
        $query = LapBarangMasuk::with([
                'supplier',
                'details.barang'
            ])
            ->orderBy('tanggal_masuk', 'asc');

        //  Filter tanggal (sama seperti index)
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_masuk', [$request->dari, $request->sampai]);
            $start = $request->dari;
            $end   = $request->sampai;
        } elseif ($request->filled('dari')) {
            $query->whereDate('tanggal_masuk', '>=', $request->dari);
            $start = $request->dari;
            $end   = '-';
        } elseif ($request->filled('sampai')) {
            $query->whereDate('tanggal_masuk', '<=', $request->sampai);
            $start = '-';
            $end   = $request->sampai;
        } else {
            $start = '-';
            $end   = '-';
        }

        $data = $query->get();

        $pdf = Pdf::loadView('laporan.barang_masuk.cetak', [
            'data'  => $data,
            'start' => $start,
            'end'   => $end,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Barang_Masuk_' . now()->format('d-m-Y') . '.pdf');
    }
}
