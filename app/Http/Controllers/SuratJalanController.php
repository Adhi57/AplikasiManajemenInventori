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
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class SuratJalanController extends Controller
{
    /**
     * INDEX — Menampilkan daftar surat jalan
     */
    public function index(Request $request)
    {
        $query = SuratJalan::with(['pelanggan', 'user', 'pengiriman'])
            ->withCount('details')
            ->orderBy('sj_id','desc')
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
            $query->where(function($q) use ($request) {
                $q->where('sj_id', 'like', "%{$request->search}%")
                  ->orWhereHas('pelanggan', function($sub) use ($request) {
                      $sub->where('nama_pelanggan', 'like', "%{$request->search}%");
                  });
            });
        }

        $suratJalans = $query->paginate(10);

        // Status counts for summary cards
        $statusCounts = SuratJalan::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'Disetujui' THEN 1 ELSE 0 END) as disetujui,
            SUM(CASE WHEN status = 'Ditolak' THEN 1 ELSE 0 END) as ditolak,
            SUM(CASE WHEN status = 'Dikirim' OR status = 'Selesai' THEN 1 ELSE 0 END) as dikirim
        ")->first();

        return view('surat_jalan.index', compact('suratJalans', 'statusCounts'));
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
    Log::info('Masuk ke store surat jalan', $request->only(['pelanggan_id', 'biaya_pengiriman', 'diskon_pelanggan']));

    // VALIDASI INPUT
    $request->validate([
        'pelanggan_id' => 'required',
        'nama_penerima' => 'required|string|max:40',
        'alamat_penerima' => 'required|string|max:255',
        'tanggal_surat' => 'required|date',
        'subtotal' => 'nullable|numeric|min:0',
        'biaya_pengiriman' => 'nullable|numeric|min:0',
        'diskon_pelanggan' => 'nullable|numeric|min:0|max:100',
        'items' => 'required|array|min:1',
        'items.*.kode_barang' => 'required',
        'items.*.quantity' => 'required|numeric|min:1',
        'items.*.harga_satuan' => 'required|numeric|min:0',
        'items.*.satuan' => 'required',
    ]);

    DB::beginTransaction();
    try {
        $sj_id = 'SJ-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));
        Log::info('Generated SJ ID', ['sj_id' => $sj_id]);

        $sj = SuratJalan::create([
            'sj_id' => $sj_id,
            'user_id' => Auth::user()->user_id ?? Auth::id(),
            'pelanggan_id' => $request->pelanggan_id,
            'nama_penerima' => $request->nama_penerima,
            'alamat_penerima' => $request->alamat_penerima,
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

        Log::info('Surat Jalan Created', $sj->toArray());

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
        Log::info('Surat Jalan Committed', ['sj_id' => $sj_id]);

        return redirect()->route('surat_jalan.index')->with('success', 'Surat jalan berhasil dibuat!');
    } catch (\Throwable $e) {
        DB::rollBack();
        Log::error('Gagal menyimpan surat jalan', ['error' => $e->getMessage()]);
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
        Log::error('Gagal menghapus Surat Jalan: ' . $e->getMessage());

        return back()->with('error', 'Gagal menghapus surat jalan: ' . $e->getMessage());
    }
}
    public function edit($sj_id)
    {
        $suratJalan = SuratJalan::with(['details.barang', 'pelanggan'])->where('sj_id', $sj_id)->firstOrFail();

        // Hanya boleh edit jika status Pending
        if ($suratJalan->status !== 'Pending') {
            return redirect()->route('surat_jalan.index')->with('error', 'Hanya Surat Jalan berstatus Pending yang dapat diedit.');
        }

        $pelanggans = DB::table('pelanggans')
            ->join('kategori_pelanggans', 'pelanggans.kategori_pelanggan_id', '=', 'kategori_pelanggans.kategori_pelanggan_id')
            ->select('pelanggans.*', 'kategori_pelanggans.jumlah_diskon')
            ->get();
        $kategoris = KategoriBarang::all();

        // Convert existing details to json structure for alpine cart
        $cartData = [];
        foreach ($suratJalan->details as $d) {
            $b = $d->barang;
            // Dapatkan stok tersedia untuk validasi
            $stokTersedia = DB::table('stok_barangs')
                ->where('kode_barang', $b->kode_barang)
                ->where('tgl_kadaluarsa', '>', now()->toDateString())
                ->sum('jumlah_stok');
                
            $stokTerkecil = $stokTersedia * ($b->jml_barang_per_karton ?? 1);

            $cartData[$b->kode_barang] = [
                'kode_barang' => $b->kode_barang,
                'nama' => $b->nama_barang,
                'harga' => (float)$d->harga_satuan,
                'satuan' => $d->satuan,
                'qty' => $d->quantity,
                'stok_tersedia' => $stokTerkecil,
                'subtotal' => (float)$d->harga_satuan * $d->quantity
            ];
        }

        return view('surat_jalan.edit', compact('suratJalan', 'pelanggans', 'kategoris', 'cartData'));
    }

    public function update(Request $request, $sj_id)
    {
        $request->validate([
            'pelanggan_id' => 'required',
            'nama_penerima' => 'required|string|max:40',
            'alamat_penerima' => 'required|string|max:255',
            'tanggal_surat' => 'required|date',
            'subtotal' => 'nullable|numeric|min:0',
            'biaya_pengiriman' => 'nullable|numeric|min:0',
            'diskon_pelanggan' => 'nullable|numeric|min:0|max:100',
            'items' => 'required|array|min:1',
            'items.*.kode_barang' => 'required',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.harga_satuan' => 'required|numeric|min:0',
            'items.*.satuan' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $sj = SuratJalan::where('sj_id', $sj_id)->firstOrFail();

            if ($sj->status !== 'Pending') {
                return redirect()->route('surat_jalan.index')->with('error', 'Hanya Surat Jalan berstatus Pending yang dapat diedit.');
            }

            $sj->update([
                'pelanggan_id' => $request->pelanggan_id,
                'nama_penerima' => $request->nama_penerima,
                'alamat_penerima' => $request->alamat_penerima,
                'tanggal_surat' => $request->tanggal_surat,
                'biaya_pengiriman' => $request->biaya_pengiriman ?? 0,
                'diskon_pelanggan' => $request->diskon_pelanggan ?? 0,
                'subtotal' => $request->subtotal ?? 0,
            ]);

            // Hapus detail lama
            \App\Models\SuratJalanDetail::where('sj_id', $sj_id)->delete();

            // Insert detail baru
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
            Log::info('Surat Jalan Updated', ['sj_id' => $sj_id]);

            return redirect()->route('surat_jalan.index')->with('success', 'Surat jalan berhasil diperbarui!');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal mengupdate surat jalan', ['error' => $e->getMessage()]);
            return back()->with('error', 'Gagal mengupdate surat jalan: ' . $e->getMessage());
        }
    }

    /**
     * Print Surat Jalan as PDF
     */
    public function print_sj($sj_id)
    {
        $suratJalan = SuratJalan::with(['details.barang', 'pelanggan', 'user', 'approver'])
            ->where('sj_id', $sj_id)
            ->firstOrFail();

        return Pdf::loadView('approval.print_sj', compact('suratJalan'))
            ->setPaper('a4', 'portrait')
            ->stream('SuratJalan_' . $sj_id . '.pdf');
    }
}

