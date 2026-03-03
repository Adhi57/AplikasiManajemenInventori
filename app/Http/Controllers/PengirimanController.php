<?php

namespace App\Http\Controllers;

use App\Models\Pengiriman;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use App\Models\Barang;
use App\Models\StokBarang;
use App\Models\LapBarangKeluar;
use App\Models\DetailLapBarangKeluar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PengirimanController extends Controller
{
    public function index(Request $request)
    {
        $query = Pengiriman::with(['suratJalan.pelanggan'])
            ->latest();

        // Filter by status
        if ($request->status) {
            $query->where('status_pengiriman', $request->status);
        }

        // Search by SJ ID or pelanggan name
        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('sj_id', 'like', "%{$request->search}%")
                  ->orWhere('nama_driver', 'like', "%{$request->search}%")
                  ->orWhere('no_polisi', 'like', "%{$request->search}%")
                  ->orWhereHas('suratJalan.pelanggan', function($sub) use ($request) {
                      $sub->where('nama_pelanggan', 'like', "%{$request->search}%");
                  });
            });
        }

        $pengirimans = $query->paginate(10);

        // Status counts for summary cards
        $statusCounts = Pengiriman::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status_pengiriman = 'Menunggu' THEN 1 ELSE 0 END) as menunggu,
            SUM(CASE WHEN status_pengiriman = 'Dalam Perjalanan' THEN 1 ELSE 0 END) as dalam_perjalanan,
            SUM(CASE WHEN status_pengiriman = 'Terkirim' THEN 1 ELSE 0 END) as terkirim,
            SUM(CASE WHEN status_pengiriman = 'Dibatalkan' THEN 1 ELSE 0 END) as dibatalkan
        ")->first();

        return view('pengiriman.index', compact('pengirimans', 'statusCounts'));
    }

    public function create()
    {
        $existing_sj_ids = Pengiriman::pluck('sj_id')->toArray();
        $surat_jalans = SuratJalan::with('pelanggan')->get();
        return view('pengiriman.form', compact('surat_jalans', 'existing_sj_ids'));
    }

    public function edit($id)
    {
        $pengiriman = Pengiriman::findOrFail($id);
        $surat_jalans = SuratJalan::with('pelanggan')->get();
        return view('pengiriman.form', compact('pengiriman', 'surat_jalans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'sj_id' => 'required|exists:surat_jalans,sj_id',
            'nama_driver' => 'nullable|string|max:100',
            'no_polisi' => 'nullable|string|max:50',
            'tanggal_pengiriman' => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_pengiriman',
        ]);

        // Verify the SJ is approved before creating pengiriman
        $suratJalan = SuratJalan::where('sj_id', $request->sj_id)->first();
        if (!$suratJalan || $suratJalan->status !== 'Disetujui') {
            return redirect()->back()->with('error', 'Surat Jalan harus berstatus "Disetujui" sebelum membuat pengiriman.');
        }

        Pengiriman::create($request->all());
        return redirect()->route('pengiriman.index')->with('success', 'Data pengiriman berhasil disimpan!');
    }

    public function show(Pengiriman $pengiriman)
    {
        $pengiriman->load(['suratJalan.pelanggan', 'suratJalan.details.barang', 'suratJalan.user']);
        return view('pengiriman.show', compact('pengiriman'));
    }

    public function update(Request $request, Pengiriman $pengiriman)
    {
        // Verify the linked SJ is still approved
        $suratJalan = SuratJalan::where('sj_id', $pengiriman->sj_id)->first();
        if (!$suratJalan || $suratJalan->status !== 'Disetujui') {
            return redirect()->back()->with('error', 'Surat Jalan terkait harus berstatus "Disetujui" untuk memperbarui pengiriman.');
        }

        $request->validate([
            'no_polisi' => 'required|string|max:15',
            'nama_kendaraan' => 'required|string|max:100',
            'nama_driver' => 'required|string|max:100',
            'tanggal_pengiriman' => 'required|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_pengiriman',
            'status_pengiriman' => 'required|in:Menunggu,Dalam Perjalanan,Terkirim,Dibatalkan',
            'catatan' => 'nullable|string',
        ]);

        $pengiriman->update($request->only([
            'nama_kendaraan',
            'nama_driver',
            'no_polisi',
            'tanggal_pengiriman',
            'tanggal_sampai',
            'status_pengiriman',
            'catatan',
        ]));

        return redirect()->route('pengiriman.index')->with('success', 'Data pengiriman berhasil diperbarui.');
    }

    public function updateStatus(Request $request, $id)
    {
        Log::info('Masuk updateStatus controller', ['id' => $id, 'input' => $request->all()]);

        DB::beginTransaction();
        try {
            $pengiriman = Pengiriman::with('suratJalan.details.barang')->findOrFail($id);
            $status = $request->input('status_pengiriman');

            $pengiriman->status_pengiriman = $status;
            $pengiriman->tanggal_sampai = now();
            $pengiriman->save();

            if ($status === 'Terkirim') {
                Log::info("Memanggil kurangiStokFEFO...");
                $this->kurangiStokFEFO($pengiriman);
                $this->buatLaporanBarangKeluar($pengiriman);
            }

            DB::commit();
            return back()->with('success', 'Status diperbarui dan laporan keluar berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal updateStatus: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', $e->getMessage());
        }
    }

    // ================= FEFO (stok keluar berdasarkan expired paling cepat) =====================
    public function kurangiStokFEFO(Pengiriman $pengiriman)
    {
        Log::info('=== FEFO DIJALANKAN ===', [
            'pengiriman_id' => $pengiriman->pengiriman_id,
            'sj_id' => $pengiriman->sj_id
        ]);

        $details = SuratJalanDetail::where('sj_id', $pengiriman->sj_id)->get();

        foreach ($details as $detail) {
            $barang = Barang::where('kode_barang', $detail->kode_barang)->first();
            if (!$barang) continue;

            $jumlahKarton = $detail->quantity / max($barang->jml_barang_per_karton, 1);

            $stokList = StokBarang::where('kode_barang', $barang->kode_barang)
                ->where('jumlah_stok', '>', 0)
                ->orderBy('tgl_kadaluarsa', 'asc')
                ->get();

            if ($stokList->isEmpty()) {
                throw new \Exception("Stok barang {$barang->nama_barang} kosong!");
            }

            $sisa = $jumlahKarton;
            foreach ($stokList as $stok) {
                if ($sisa <= 0) break;

                $ambil = min($stok->jumlah_stok, $sisa);
                $stok->jumlah_stok -= $ambil;
                $stok->save();
                $sisa -= $ambil;
            }

            if ($sisa > 0) {
                throw new \Exception("Stok barang {$barang->nama_barang} tidak mencukupi untuk pengiriman!");
            }
        }
    }

    // ================= Buat laporan otomatis setelah terkirim =====================
    protected function buatLaporanBarangKeluar(Pengiriman $pengiriman)
    {
        if (LapBarangKeluar::where('pengiriman_id', $pengiriman->pengiriman_id)->exists()) {
            Log::info("Laporan keluar sudah ada untuk pengiriman {$pengiriman->pengiriman_id}");
            return;
        }

        $sj = $pengiriman->suratJalan;

        $totalBarang = 0;
        foreach ($sj->details as $detail) {
            $harga = $detail->harga_satuan ?? 0;
            $totalBarang += ($detail->quantity * $harga);
        }    

        $biayaKirim = $sj->biaya_pengiriman ?? 0;
        $diskon     = ($totalBarang * $sj->diskon_pelanggan / 100 );

        $totalAkhir = ($totalBarang + $biayaKirim) - $diskon;

        $lap = LapBarangKeluar::create([
            'pengiriman_id' => $pengiriman->pengiriman_id,
            'sj_id' => $pengiriman->sj_id,
            'tanggal_keluar' => now(),
            'biaya_kirim' => $sj->biaya_pengiriman,
            'diskon' => $diskon,
            'total_akhir' => $totalAkhir,
        ]);

        foreach ($pengiriman->suratJalan->details as $detail) {
            $hargaSatuan = $detail->harga_satuan ?? 0;
            DetailLapBarangKeluar::create([
                'lap_keluar_id' => $lap->lap_keluar_id,
                'kode_barang' => $detail->kode_barang,
                'jumlah_keluar' => $detail->quantity,
                'harga_jual' => $hargaSatuan,
                'subtotal' => $detail->quantity * $hargaSatuan,
            ]);
        }

        Log::info("Laporan barang keluar dibuat otomatis untuk pengiriman {$pengiriman->pengiriman_id}");
    }
}
