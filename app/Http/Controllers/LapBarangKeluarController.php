<?php

namespace App\Http\Controllers;

use App\Models\LapBarangKeluar;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class LapBarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = LapBarangKeluar::with(['details.barang', 'pengiriman.suratJalan.pelanggan', 'pengiriman.suratJalan.details'])
            ->orderByDesc('tanggal_keluar');
    
        // 🔍 Filter tanggal (dari - sampai)
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_keluar', [$request->dari, $request->sampai]);
        } elseif ($request->filled('dari')) {
            $query->whereDate('tanggal_keluar', '>=', $request->dari);
        } elseif ($request->filled('sampai')) {
            $query->whereDate('tanggal_keluar', '<=', $request->sampai);
        }
    
        // 🔍 Filter nama barang atau pelanggan
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('details.barang', function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            })->orWhereHas('pengiriman.suratJalan.pelanggan', function ($q) use ($search) {
                $q->where('nama_pelanggan', 'like', "%{$search}%");
            });
        }
    
        $laporans = $query->get();
    
        return view('laporan.barang_keluar.index', compact('laporans'));
    }

    public function cetak(Request $request)
    {
        $query = LapBarangKeluar::with(['details.barang', 'pengiriman.suratJalan.pelanggan', 'pengiriman.suratJalan.details'])
            ->orderBy('tanggal_keluar', 'asc');

        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal_keluar', [$request->dari, $request->sampai]);
            $start = $request->dari;
            $end   = $request->sampai;
        } elseif ($request->filled('dari')) {
            $query->whereDate('tanggal_keluar', '>=', $request->dari);
            $start = $request->dari;
            $end   = '-';
        } elseif ($request->filled('sampai')) {
            $query->whereDate('tanggal_keluar', '<=', $request->sampai);
            $start = '-';
            $end   = $request->sampai;
        } else {
            $start = '-';
            $end   = '-';
        }

        $data = $query->get();

        $pdf = Pdf::loadView('laporan.barang_keluar.cetak', [
            'data'  => $data,
            'start' => $start,
            'end'   => $end,
        ])->setPaper('a4', 'portrait');

        return $pdf->download('Laporan_Barang_Keluar_' . now()->format('d-m-Y') . '.pdf');
    }
}
