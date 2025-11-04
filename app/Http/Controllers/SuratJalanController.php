<?php

namespace App\Http\Controllers;

use App\Models\SuratJalan;
use App\Models\Pelanggan;
use App\Models\Barang;
use App\Models\KategoriBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SuratJalanController extends Controller
{
    /**
     * 📄 INDEX — Menampilkan daftar surat jalan
     */
    public function index(Request $request)
    {
        $query = SuratJalan::with(['pelanggan', 'user'])
            ->orderBy('tanggal_surat', 'desc');

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('sj_id', 'like', "%{$request->search}%")
                  ->orWhereHas('pelanggan', function($q) use ($request) {
                      $q->where('nama_pelanggan', 'like', "%{$request->search}%");
                  });
        }

        $suratJalans = $query->paginate(10);

        return view('surat_jalan.index', compact('suratJalans'));
    }

    /**
     * 🧾 CREATE — Form tambah surat jalan (barang belum kadaluarsa)
     */
    public function create()
    {
        $today = now()->toDateString();

        // Barang yang masih memiliki stok dan belum kadaluarsa
        $barangs = DB::table('stok_barangs')
            ->join('barangs', 'stok_barangs.kode_barang', '=', 'barangs.kode_barang')
            ->select(
                'barangs.kode_barang',
                'barangs.nama_barang',
                'barangs.harga_jual',
                'stok_barangs.tgl_kadaluarsa',
                DB::raw('SUM(stok_barangs.jumlah_stok) as stok_tersedia')
            )
            ->where('stok_barangs.tgl_kadaluarsa', '>', $today)
            ->groupBy('barangs.kode_barang', 'barangs.nama_barang', 'barangs.harga_jual', 'stok_barangs.tgl_kadaluarsa')
            ->havingRaw('stok_tersedia > 0')
            ->paginate(10);

        $pelanggans = Pelanggan::all();
        $kategoris = KategoriBarang::all();

        return view('surat_jalan.create', compact('barangs', 'pelanggans', 'kategoris'));
    }

    /**
     * 📨 STORE — Simpan surat jalan baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'pelanggan_id' => 'required|exists:pelanggans,pelanggan_id',
            'tanggal_surat' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.kode_barang' => 'required|exists:barangs,kode_barang',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.satuan' => 'required|in:pcs,renteng,pack,karton',
        ]);

        DB::beginTransaction();
        try {
            $sj_id = 'SJ-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));

            $sj = SuratJalan::create([
                'sj_id' => $sj_id,
                'user_id' => Auth::user()->user_id ?? Auth::id(),
                'pelanggan_id' => $request->pelanggan_id,
                'tanggal_surat' => $request->tanggal_surat,
                'status' => 'Pending',
            ]);

            foreach ($request->items as $item) {
                $subtotal = $item['quantity'] * $item['harga_satuan'];

                \App\Models\SuratJalanDetail::create([
                    'detail_sj_id' => 'SJD-' . strtoupper(Str::random(8)),
                    'sj_id' => $sj_id,
                    'kode_barang' => $item['kode_barang'],
                    'harga_barang_id' => $item['harga_barang_id'] ?? null,
                    'quantity' => $item['quantity'],
                    'harga_satuan' => $item['harga_satuan'],
                    'satuan' => $item['satuan'],
                    'subtotal' => $subtotal,
                ]);
            }

            DB::commit();
            return redirect()->route('surat_jalan.index')->with('success', 'Surat jalan berhasil dibuat!');
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan surat jalan: ' . $e->getMessage());
        }
    }

    public function fetchBarangs(Request $request)
{
    $today = now()->toDateString();

    $query = DB::table('stok_barangs')
        ->join('barangs', 'stok_barangs.kode_barang', '=', 'barangs.kode_barang')
        ->select(
            'barangs.kode_barang',
            'barangs.nama_barang',
            'barangs.harga_jual',
            'barangs.satuan_jual',
            'barangs.jml_barang_per_karton',
            DB::raw('SUM(stok_barangs.jumlah_stok) as total_karton'),
            DB::raw('MIN(stok_barangs.tgl_kadaluarsa) as tgl_kadaluarsa')
        )
        ->where('stok_barangs.tgl_kadaluarsa', '>', $today)
        ->groupBy(
            'barangs.kode_barang',
            'barangs.nama_barang',
            'barangs.harga_jual',
            'barangs.satuan_jual',
            'barangs.jml_barang_per_karton'
        )
        ->havingRaw('SUM(stok_barangs.jumlah_stok) > 0');

    if ($request->search) {
        $query->where(function ($q) use ($request) {
            $q->where('barangs.nama_barang', 'like', "%{$request->search}%")
              ->orWhere('barangs.kode_barang', 'like', "%{$request->search}%");
        });
    }

    $barangs = $query->paginate(8);

    //  Konversi stok ke satuan terkecil (pcs)
    foreach ($barangs as $b) {
        $b->stok_tersedia = $b->total_karton * $b->jml_barang_per_karton;
    }

    $html = view('surat_jalan._barang_list', compact('barangs'))->render();
    $pagination = (string) $barangs->links();

    return response()->json([
        'html' => $html,
        'pagination' => $pagination,
    ]);
}

public function show($sj_id)
{
    $suratJalan = SuratJalan::with(['details.barang', 'pelanggan', 'user'])
        ->where('sj_id', $sj_id)
        ->firstOrFail();

    return view('surat_jalan.show', compact('suratJalan'));
}


}
