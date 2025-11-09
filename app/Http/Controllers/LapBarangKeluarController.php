<?php

namespace App\Http\Controllers;

use App\Models\LapBarangKeluar;
use Illuminate\Http\Request;

class LapBarangKeluarController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\LapBarangKeluar::with(['details.barang', 'pengiriman.suratJalan.pelanggan'])
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
    
}
