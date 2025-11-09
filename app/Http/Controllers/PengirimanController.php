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

class PengirimanController extends Controller
{
    public function index()
    {
        $pengirimans = Pengiriman::with(['suratJalan.pelanggan'])
            ->latest()
            ->paginate(10);
        return view('pengiriman.index', compact('pengirimans'));
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

        Pengiriman::create($request->all());
        return redirect()->route('pengiriman.index')->with('success', 'Data pengiriman berhasil disimpan!');
    }

    public function show(Pengiriman $pengiriman)
    {
        $pengiriman->load('suratJalan.pelanggan');
        return view('pengiriman.show', compact('pengiriman'));
    }

    public function update(Request $request, Pengiriman $pengiriman)
    {
        $request->validate([
            'no_polisi' => [
                'required',
                'string',
                'max:15',
                Rule::unique('pengirimans', 'no_polisi')->ignore($pengiriman->pengiriman_id, 'pengiriman_id'),
            ],
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
        \Log::info('Masuk updateStatus controller', ['id' => $id, 'input' => $request->all()]);

        DB::beginTransaction();
        try {
            $pengiriman = Pengiriman::with('suratJalan.details.barang')->findOrFail($id);
            $status = $request->input('status_pengiriman');

            $pengiriman->status_pengiriman = $status;
            $pengiriman->tanggal_sampai = now();
            $pengiriman->save();

            if ($status === 'Terkirim') {
                \Log::info("Memanggil kurangiStokFEFO...");
                $this->kurangiStokFEFO($pengiriman);
                $this->buatLaporanBarangKeluar($pengiriman);
            }

            DB::commit();
            return back()->with('success', 'Status diperbarui dan laporan keluar berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal updateStatus: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', $e->getMessage());
        }
    }

    // ================= FEFO (stok keluar berdasarkan expired paling cepat) =====================
    public function kurangiStokFEFO(Pengiriman $pengiriman)
    {
        \Log::info('=== FEFO DIJALANKAN ===', [
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
            \Log::info("Laporan keluar sudah ada untuk pengiriman {$pengiriman->pengiriman_id}");
            return;
        }

        $lap = LapBarangKeluar::create([
            'pengiriman_id' => $pengiriman->pengiriman_id,
            'sj_id' => $pengiriman->sj_id,
            'tanggal_keluar' => now(),
        ]);

        foreach ($pengiriman->suratJalan->details as $detail) {
            DetailLapBarangKeluar::create([
                'lap_keluar_id' => $lap->lap_keluar_id,
                'kode_barang' => $detail->kode_barang,
                'jumlah_keluar' => $detail->quantity,
                'harga_jual' => $detail->barang->harga_jual ?? 0,
                'subtotal' => $detail->quantity * ($detail->barang->harga_jual ?? 0),
            ]);
        }

        \Log::info("Laporan barang keluar dibuat otomatis untuk pengiriman {$pengiriman->pengiriman_id}");
    }
}
