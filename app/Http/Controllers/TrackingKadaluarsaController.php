<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokBarang;
use App\Models\Barang;

class TrackingKadaluarsaController extends Controller
{
    // Menampilkan seluruh barang yang punya expiry
    public function index(Request $request)
    {
        $search = $request->search ?? null;

        $stok = StokBarang::with('barang')
            ->when($search, fn($q) =>
                $q->where('kode_barang', 'like', "%$search%")
                  ->orWhereHas('barang', fn($qb) =>
                    $qb->where('nama_barang', 'like', "%$search%")
                  )
            )
            ->orderBy('tgl_kadaluarsa', 'asc')
            ->get();

        return view('tracking_kadaluarsa.index', compact('stok', 'search'));
    }

    // Detail satu barang berdasarkan kode 
    public function detail($kode_barang)
    {
        $stok = StokBarang::where('kode_barang', $kode_barang)
            ->with('barang')
            ->orderBy('tgl_kadaluarsa', 'asc')
            ->get();

        if ($stok->isEmpty()) {
            return redirect()->route('tracking_kadaluarsa.index')
                ->with('error', 'Data tidak ditemukan.');
        }

        return view('tracking_kadaluarsa.detail', compact('stok'));
    }

    // Menghapus stok tertentu (ID stok)
    public function destroy($id)
    {
        $stok = StokBarang::findOrFail($id);
        $stok->delete();

        return back()->with('success', 'Stok berhasil dihapus.');
    }
}
