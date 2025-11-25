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
     * INDEX — Menampilkan daftar surat jalan
     */
    public function index(Request $request)
    {
        $query = SuratJalan::with(['pelanggan', 'user'])
        ->orderByRaw("
        CASE 
            WHEN status = 'Pending' THEN 1
            WHEN status = 'Disetujui' THEN 2
            WHEN status = 'Ditolak' THEN 3
            WHEN status = 'Dikirim' THEN 4
            WHEN status = 'Selesai' THEN 5
            ELSE 6
        END
    ");

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
     * CREATE — Form tambah surat jalan (barang belum kadaluarsa)
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

        $pelanggans = DB::table('pelanggans')
            ->join('kategori_pelanggans', 'pelanggans.kategori_pelanggan_id', '=', 'kategori_pelanggans.kategori_pelanggan_id')
            ->select('pelanggans.*', 'kategori_pelanggans.jumlah_diskon')
            ->get();
        $kategoris = KategoriBarang::all();

        return view('surat_jalan.create', compact('barangs', 'pelanggans', 'kategoris'));
    }

    /**
     * 📨 STORE — Simpan surat jalan baru
     */
    public function store(Request $request)
    {
        \Log::info('Masuk ke store surat jalan', $request->only(['pelanggan_id', 'biaya_pengiriman', 'diskon_pelanggan']));
    
        DB::beginTransaction();
        try {
            $sj_id = 'SJ-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
            \Log::info('Generated SJ ID', ['sj_id' => $sj_id]);
    
            $sj = SuratJalan::create([
                'sj_id' => $sj_id,
                'user_id' => Auth::user()->user_id ?? Auth::id(),
                'pelanggan_id' => $request->pelanggan_id,
                'tanggal_surat' => $request->tanggal_surat,
                'status' => 'Pending',
                'biaya_pengiriman' => $request->biaya_pengiriman ?? 0,
                'diskon_pelanggan' => $request->diskon_pelanggan ?? 0,
                'subtotal' => $request->subtotal ?? 0,
            ]);

            \App\Models\Pengiriman::create([
                'sj_id' => $sj_id,
                'status_pengiriman' => 'Menunggu',
            ]);
            
    
            \Log::info('Surat Jalan Created', $sj->toArray());
    
            foreach ($request->items as $item) {
                \App\Models\SuratJalanDetail::create([
                    'detail_sj_id' => 'SJD-' . strtoupper(Str::random(8)),
                    'sj_id' => $sj_id,
                    'kode_barang' => $item['kode_barang'],
                    'quantity' => $item['quantity'],
                    'harga_satuan' => $item['harga_satuan'],
                    'satuan' => $item['satuan'],
                ]);
            }
    
            DB::commit();
            \Log::info('Surat Jalan Committed', ['sj_id' => $sj_id]);
    
            return redirect()->route('surat_jalan.index')->with('success', 'Surat jalan berhasil dibuat!');
        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Gagal menyimpan surat jalan', ['error' => $e->getMessage()]);
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

public function destroy($sj_id)
{
    DB::beginTransaction();
    try {
        // Ambil data surat jalan
        $suratJalan = SuratJalan::with('details')->where('sj_id', $sj_id)->firstOrFail();

        // Hapus detail surat jalan
        \App\Models\SuratJalanDetail::where('sj_id', $sj_id)->delete();

        // Hapus data pengiriman terkait
        \App\Models\Pengiriman::where('sj_id', $sj_id)->delete();

        // Hapus surat jalan
        $suratJalan->delete();

        DB::commit();

        return redirect()->route('surat_jalan.index')
            ->with('success', 'Surat jalan berhasil dihapus.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Gagal menghapus Surat Jalan: ' . $e->getMessage());

        return back()->with('error', 'Gagal menghapus surat jalan: ' . $e->getMessage());
    }
}


}
