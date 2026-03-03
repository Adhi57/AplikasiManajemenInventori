<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokBarang;
use App\Models\Barang;
use App\Models\RiwayatHapusStok;

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

    // Menghapus stok tertentu (ID stok) & menyimpan riwayat
    public function destroy($id)
    {
        $stok = StokBarang::findOrFail($id);

        // Simpan riwayat sebelum menghapus
        RiwayatHapusStok::create([
            'po_id'            => $stok->po_id,
            'kode_barang'      => $stok->kode_barang,
            'nama_barang'      => $stok->barang->nama_barang ?? 'Barang Dihapus',
            'jumlah_stok'      => $stok->jumlah_stok,
            'jumlah_stok_rusak'=> $stok->jumlah_stok_rusak ?? 0,
            'tgl_kadaluarsa'   => $stok->tgl_kadaluarsa,
            'alasan'           => 'Kadaluarsa',
            'dihapus_oleh'     => auth()->user()->nama_lengkap ?? auth()->user()->username,
            'created_at'       => now(),
        ]);

        $stok->delete();

        return back()->with('success', 'Stok berhasil dihapus dan dicatat ke riwayat.');
    }

    // Menampilkan riwayat penghapusan stok
    public function riwayat(Request $request)
    {
        $search = $request->search ?? null;

        $riwayat = RiwayatHapusStok::query()
            ->when($search, fn($q) =>
                $q->where('kode_barang', 'like', "%$search%")
                  ->orWhere('nama_barang', 'like', "%$search%")
                  ->orWhere('dihapus_oleh', 'like', "%$search%")
            )
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('tracking_kadaluarsa.riwayat', compact('riwayat', 'search'));
    }
}
