<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengiriman;
use App\Models\SuratJalanDetail;
use App\Models\Barang;
use App\Models\StokBarang;
use App\Models\LapBarangKeluar;
use App\Models\DetailLapBarangKeluar;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\StokKeluarLog;

class PengirimanApiController extends Controller
{
    // =====================================================
    // GET /api/v1/pengiriman
    // Mengambil daftar pengiriman
    // Query params: status (optional)
    // =====================================================
    public function index(Request $request)
    {
        $query = Pengiriman::with(['suratJalan.pelanggan'])->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status_pengiriman', $request->status);
        }

        $perPage = min((int) $request->get('per_page', 20), 100);
        $items = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $items->map(fn($p) => $this->formatPengiriman($p)),
            'meta'    => [
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
            ],
        ]);
    }

    // =====================================================
    // GET /api/v1/pengiriman/{sj_id}
    // Lacak status spesifik dari satu surat jalan
    // =====================================================
    public function show($sj_id)
    {
        $pengiriman = Pengiriman::with(['suratJalan.pelanggan', 'suratJalan.details.barang'])
                    ->where('sj_id', $sj_id)
                    ->first();

        // Jika surat jalan ada tapi belum masuk proses pengiriman
        if (!$pengiriman) {
            return response()->json([
                'success' => false,
                'message' => "Data pengiriman untuk Surat Jalan '{$sj_id}' tidak ditemukan atau sedang disiapkan.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatPengirimanDetail($pengiriman),
        ]);
    }

    // =====================================================
    // POST /api/v1/pengiriman/{sj_id}/terkirim
    // Tandai pengiriman sebagai selesai/terkirim
    // =====================================================
    public function markTerkirim($sj_id)
    {
        $pengiriman = Pengiriman::where('sj_id', $sj_id)->first();

        if (!$pengiriman) {
            return response()->json([
                'success' => false,
                'message' => "Data pengiriman untuk Surat Jalan '{$sj_id}' tidak ditemukan.",
            ], 404);
        }

        DB::beginTransaction();
        try {
            $pengiriman->status_pengiriman = 'Terkirim';
            $pengiriman->tanggal_sampai = now();
            $pengiriman->save();

            Log::info("[API] Memanggil kurangiStokFEFO untuk SJ: {$sj_id}");
            $this->kurangiStokFEFO($pengiriman);
            $this->buatLaporanBarangKeluar($pengiriman);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Status pengiriman untuk '{$sj_id}' berhasil diupdate menjadi Terkirim.",
                'data'    => $this->formatPengirimanDetail($pengiriman),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[API] Gagal update status ke Terkirim: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate status: ' . $e->getMessage(),
            ], 500);
        }
    }

    // ================= FEFO (stok keluar berdasarkan expired paling cepat) =====================
    private function kurangiStokFEFO(Pengiriman $pengiriman)
    {
        Log::info('[API] === FEFO DIJALANKAN ===', [
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
                
                StokKeluarLog::create([
                    'kode_barang' => $barang->kode_barang,
                    'po_id' => $stok->po_id,
                    'jumlah' => $ambil,
                    'tgl_kadaluarsa' => $stok->tgl_kadaluarsa,
                    'sumber' => 'Pengiriman API (SJ: ' . $pengiriman->sj_id . ')',
                    'dieksekusi_oleh' => 'Sistem / API',
                ]);
                
                Log::info("[API] STOK KELUAR (FEFO): SJ {$pengiriman->sj_id} | Barang {$barang->kode_barang} | PO: {$stok->po_id} | Diambil: {$ambil} Karton | Sisa Stok Ini: {$stok->jumlah_stok} Karton | Exp: {$stok->tgl_kadaluarsa}");

                $sisa -= $ambil;
            }

            if ($sisa > 0) {
                throw new \Exception("Stok barang {$barang->nama_barang} tidak mencukupi untuk pengiriman!");
            }
        }
    }

    // ================= Buat laporan otomatis setelah terkirim =====================
    private function buatLaporanBarangKeluar(Pengiriman $pengiriman)
    {
        if (LapBarangKeluar::where('pengiriman_id', $pengiriman->pengiriman_id)->exists()) {
            Log::info("[API] Laporan keluar sudah ada untuk pengiriman {$pengiriman->pengiriman_id}");
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

        Log::info("[API] Laporan barang keluar dibuat otomatis untuk pengiriman {$pengiriman->pengiriman_id}");
    }

    // =====================================================
    // Helper: format list pengiriman
    // =====================================================
    private function formatPengiriman(Pengiriman $p): array
    {
        return [
            'pengiriman_id'      => $p->pengiriman_id,
            'sj_id'              => $p->sj_id,
            'pelanggan'          => $p->suratJalan?->pelanggan?->nama_pelanggan,
            'nama_driver'        => $p->nama_driver,
            'nama_kendaraan'     => $p->nama_kendaraan,
            'no_polisi'          => $p->no_polisi,
            'status_pengiriman'  => $p->status_pengiriman,
            'tanggal_pengiriman' => $p->tanggal_pengiriman,
            'tanggal_sampai'     => $p->tanggal_sampai,
        ];
    }

    // =====================================================
    // Helper: format detail pengiriman
    // =====================================================
    private function formatPengirimanDetail(Pengiriman $p): array
    {
        return [
            'pengiriman_id'      => $p->pengiriman_id,
            'sj_id'              => $p->sj_id,
            'pelanggan'          => $p->suratJalan?->pelanggan?->nama_pelanggan,
            'penerima'           => $p->suratJalan?->nama_penerima,
            'alamat_tujuan'      => $p->suratJalan?->alamat_penerima,
            'nama_driver'        => $p->nama_driver,
            'nama_kendaraan'     => $p->nama_kendaraan,
            'no_polisi'          => $p->no_polisi,
            'status_pengiriman'  => $p->status_pengiriman,
            'tanggal_pengiriman' => $p->tanggal_pengiriman,
            'tanggal_sampai'     => $p->tanggal_sampai,
            'catatan'            => $p->catatan ?? '-',
            'items_dikirim'      => $p->suratJalan?->details->map(fn($d) => [
                'kode_barang'    => $d->kode_barang,
                'nama_barang'    => $d->barang?->nama_barang,
                'quantity'       => (float) $d->quantity,
                'satuan'         => $d->satuan,
            ])
        ];
    }
}
