<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use App\Models\StokBarang;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                ->where('stok_barangs.jumlah_stok', '>', 0) // 🔹 hanya stok > 0
                ->when($search, function ($query, $search) {
                    $query->where('stok_barangs.kode_barang', 'like', "%{$search}%")
                          ->orWhere('barangs.nama_barang', 'like', "%{$search}%");
                })
                ->when($kategori, function ($query, $kategori) {
                    $query->where('barangs.kategori_id', $kategori);
                })
                ->groupBy('stok_barangs.kode_barang')
                ->havingRaw('SUM(stok_barangs.jumlah_stok) > 0') // 🔹 pastikan hasil group juga ada stok
                ->orderBy('stok_barangs.kode_barang')
                ->get();

            $stokBarangs->map(function ($stok) {
                $stok->barang = Barang::with('kategori')->where('kode_barang', $stok->kode_barang)->first();
                return $stok;
            });
        } else {
            // === DETAIL PER BATCH ===
            $stokBarangs = StokBarang::with(['barang.kategori'])
                ->where('jumlah_stok', '>', 0) // 🔹 hanya stok > 0
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
        $kapasitasMaks = 10000;
        $totalStok = $group
            ? $stokBarangs->sum('total_karton')
            : $stokBarangs->sum('jumlah_stok');
        $persentase = $kapasitasMaks > 0 ? round(($totalStok / $kapasitasMaks) * 100, 2) : 0;

        return view('stok.index', compact(
            'stokBarangs',
            'search',
            'kategori',
            'kategoris',
            'group',
            'kapasitasMaks',
            'totalStok',
            'persentase'
        ));
    }
}
