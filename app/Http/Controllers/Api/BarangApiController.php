<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\StokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangApiController extends Controller
{
    // =====================================================
    // GET /api/barang
    // Daftar semua barang dengan stok & kategori
    // Query params: search, kategori_id, page, per_page
    // =====================================================
    public function index(Request $request)
    {
        $query = Barang::with(['kategori', 'supplier'])
            ->withSum('stoks as total_stok', 'jumlah_stok');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nama_barang', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('kategori_barang_id', $request->kategori_id);
        }

        $perPage = min((int) $request->get('per_page', 50), 500);
        $barangs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $barangs->map(fn($b) => $this->formatBarang($b)),
            'meta'    => [
                'current_page' => $barangs->currentPage(),
                'last_page'    => $barangs->lastPage(),
                'per_page'     => $barangs->perPage(),
                'total'        => $barangs->total(),
            ],
        ]);
    }

    // =====================================================
    // GET /api/barang/{kode}
    // Detail satu barang + semua batch stok
    // =====================================================
    public function show($kode)
    {
        $barang = Barang::with(['kategori', 'supplier', 'stoks'])
            ->where('kode_barang', $kode)
            ->first();

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => "Barang dengan kode '{$kode}' tidak ditemukan.",
            ], 404);
        }

        $today = now()->toDateString();

        return response()->json([
            'success' => true,
            'data'    => [
                'kode_barang'         => $barang->kode_barang,
                'nama_barang'         => $barang->nama_barang,
                'kategori'            => $barang->kategori?->nama_kategori_barang,
                'supplier'            => $barang->supplier?->namaSupplier,
                'satuan_jual'         => $barang->satuan_jual,
                'jml_barang_per_karton' => $barang->jml_barang_per_karton,
                'harga_jual'          => (float) $barang->harga_jual,
                'harga_beli'          => (float) $barang->harga_beli,
                'foto_produk'         => $barang->foto_produk
                    ? url('storage/' . $barang->foto_produk)
                    : null,
                'total_stok_karton'   => (float) $barang->stoks->sum('jumlah_stok'),
                'total_stok_pcs'      => (float) $barang->stoks->sum('jumlah_stok') * ($barang->jml_barang_per_karton ?? 1),
                'batch_stok'          => $barang->stoks
                    ->where('tgl_kadaluarsa', '>', $today)
                    ->sortBy('tgl_kadaluarsa')
                    ->values()
                    ->map(fn($s) => [
                        'po_id'          => $s->po_id,
                        'jumlah_karton'  => (float) $s->jumlah_stok,
                        'tgl_kadaluarsa' => $s->tgl_kadaluarsa,
                    ]),
            ],
        ]);
    }

    // =====================================================
    // GET /api/stok
    // Rekap stok per barang (FEFO order)
    // Query params: search, kategori_id, status (aman|menipis|habis)
    // =====================================================
    public function stok(Request $request)
    {
        $today = now()->toDateString();

        $query = DB::table('stok_barangs as sb')
            ->join('barangs as b', 'sb.kode_barang', '=', 'b.kode_barang')
            ->join('kategori_barangs as kb', 'b.kategori_barang_id', '=', 'kb.kategori_barang_id')
            ->select(
                'b.kode_barang',
                'b.nama_barang',
                'kb.nama_kategori_barang as kategori',
                'b.satuan_jual',
                'b.jml_barang_per_karton',
                DB::raw('SUM(sb.jumlah_stok) as total_karton'),
                DB::raw('MIN(sb.tgl_kadaluarsa) as earliest_expiry'),
            )
            ->where('sb.tgl_kadaluarsa', '>', $today)
            ->groupBy('b.kode_barang', 'b.nama_barang', 'kb.nama_kategori_barang', 'b.satuan_jual', 'b.jml_barang_per_karton');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('b.kode_barang', 'like', "%{$s}%")
                  ->orWhere('b.nama_barang', 'like', "%{$s}%");
            });
        }

        if ($request->filled('kategori_id')) {
            $query->where('b.kategori_barang_id', $request->kategori_id);
        }

        $items = $query->orderBy('b.kode_barang')->get();

        // Filter status client-side setelah aggregasi
        if ($request->filled('status')) {
            $items = $items->filter(function ($item) use ($request) {
                $stok = (float) $item->total_karton;
                return match ($request->status) {
                    'habis'   => $stok <= 0,
                    'menipis' => $stok > 0 && $stok <= 10,
                    'aman'    => $stok > 10,
                    default   => true,
                };
            })->values();
        }

        return response()->json([
            'success' => true,
            'total_items' => $items->count(),
            'data' => $items->map(fn($i) => [
                'kode_barang'       => $i->kode_barang,
                'nama_barang'       => $i->nama_barang,
                'kategori'          => $i->kategori,
                'satuan_jual'       => $i->satuan_jual,
                'total_karton'      => (float) $i->total_karton,
                'total_pcs'         => (float) $i->total_karton * ($i->jml_barang_per_karton ?? 1),
                'earliest_expiry'   => $i->earliest_expiry,
                'status'            => match(true) {
                    (float)$i->total_karton <= 0  => 'habis',
                    (float)$i->total_karton <= 10 => 'menipis',
                    default                        => 'aman',
                },
            ]),
        ]);
    }

    // =====================================================
    // GET /api/kategori
    // Daftar semua kategori barang
    // =====================================================
    public function kategori()
    {
        $kategoris = KategoriBarang::withCount('barangs')->get();

        return response()->json([
            'success' => true,
            'data'    => $kategoris->map(fn($k) => [
                'id'                  => $k->kategori_barang_id,
                'nama_kategori_barang' => $k->nama_kategori_barang,
                'jumlah_barang'       => $k->barangs_count,
            ]),
        ]);
    }

    // =====================================================
    // Helper: format satu barang untuk index
    // =====================================================
    private function formatBarang(Barang $b): array
    {
        return [
            'kode_barang'          => $b->kode_barang,
            'nama_barang'          => $b->nama_barang,
            'kategori'             => $b->kategori?->nama_kategori_barang,
            'supplier'             => $b->supplier?->namaSupplier,
            'satuan_jual'          => $b->satuan_jual,
            'jml_barang_per_karton' => $b->jml_barang_per_karton,
            'harga_jual'           => (float) $b->harga_jual,
            'harga_beli'           => (float) $b->harga_beli,
            'total_stok_karton'    => (float) ($b->total_stok ?? 0),
            'total_stok_pcs'       => (float) ($b->total_stok ?? 0) * ($b->jml_barang_per_karton ?? 1),
            'foto_produk'          => $b->foto_produk ? url('storage/' . $b->foto_produk) : null,
        ];
    }
}
