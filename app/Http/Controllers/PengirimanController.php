<?php

namespace App\Http\Controllers;

use App\Models\Pengiriman;
use App\Models\SuratJalan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\SuratJalanDetail;
use App\Models\Barang;
use App\Models\StokBarang;

class PengirimanController extends Controller
{
    public function index()
    {
        $pengirimans = \App\Models\Pengiriman::with(['suratJalan.pelanggan'])
            ->latest()
            ->paginate(10);
    
        return view('pengiriman.index', compact('pengirimans'));
    }
    

    // CREATE
    public function create()
    {

        $existing_sj_ids = \App\Models\Pengiriman::pluck('sj_id')->toArray();

        $surat_jalans = SuratJalan::with('pelanggan')->get();
        return view('pengiriman.form', compact( 'surat_jalans', 'existing_sj_ids'));
    }

    // EDIT
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
        // Mengambil relasi yang dibutuhkan untuk tampilan detail
        $pengiriman->load('suratJalan.pelanggan');
        
        return view('pengiriman.show', compact('pengiriman'));
    }

    public function update(Request $request, Pengiriman $pengiriman)
    {
        // 1. Validasi Input
        $request->validate([
            // Abaikan no_polisi saat ini dari pengecekan unique
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

        // 2. Perbarui Data
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

        // Untuk pengecekan cepat, kamu bisa pakai dd() (akan stop proses dan tampil di browser)
        // dd('updateStatus called', $id, $request->input('status_pengiriman'));

        DB::beginTransaction();
        try {
            $pengiriman = Pengiriman::findOrFail($id);
            $status = $request->input('status_pengiriman');

            \Log::info('Update status ->', ['pengiriman_id' => $pengiriman->pengiriman_id, 'status' => $status]);

            $pengiriman->status_pengiriman = $status;
            $pengiriman->tanggal_sampai = now();
            $pengiriman->save();

            if ($status === 'Terkirim') {
                \Log::info("Memanggil kurangiStokFEFO dari updateStatus...");
                $this->kurangiStokFEFO($pengiriman);
            }

            DB::commit();
            return back()->with('success', 'Status diperbarui');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal updateStatus: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', $e->getMessage());
        }
    }

    public function kurangiStokFEFO(\App\Models\Pengiriman $pengiriman)
{
    \Log::info('=== FEFO DIJALANKAN ===', [
        'pengiriman_id' => $pengiriman->pengiriman_id,
        'sj_id' => $pengiriman->sj_id
    ]);

    $details = \App\Models\SuratJalanDetail::where('sj_id', $pengiriman->sj_id)->get();

    foreach ($details as $detail) {
        $barang = \App\Models\Barang::where('kode_barang', $detail->kode_barang)->first();
        if (!$barang) {
            \Log::warning('Barang tidak ditemukan', ['kode_barang' => $detail->kode_barang]);
            continue;
        }

        // Hitung jumlah dalam satuan karton (stok disimpan dalam karton)
        $jumlahKarton = $detail->quantity / max($barang->jml_barang_per_karton, 1);

        \Log::info('Proses barang FEFO', [
            'kode_barang' => $barang->kode_barang,
            'nama_barang' => $barang->nama_barang,
            'qty_surat_jalan' => $detail->quantity,
            'konversi_karton' => $jumlahKarton,
        ]);

        // Ambil stok berdasarkan FEFO (First Expired First Out)
        $stokList = \App\Models\StokBarang::where('kode_barang', $barang->kode_barang)
            ->where('jumlah_stok', '>', 0)
            ->orderBy('tgl_kadaluarsa', 'asc')
            ->get();

        if ($stokList->isEmpty()) {
            throw new \Exception("Tidak ada stok untuk barang {$barang->nama_barang}");
        }

        $sisa = $jumlahKarton;

        foreach ($stokList as $stok) {
            if ($sisa <= 0) break;

            $ambil = min($stok->jumlah_stok, $sisa);
            $stok->jumlah_stok -= $ambil;
            $stok->save();

            $sisa -= $ambil;

            \Log::info('Kurangi stok batch FEFO', [
                'barang' => $barang->nama_barang,
                'stok_id' => $stok->id,
                'ambil' => $ambil,
                'stok_sisa' => $stok->jumlah_stok,
                'tgl_kadaluarsa' => $stok->tgl_kadaluarsa,
            ]);
        }

        // Periksa kalau stok total masih kurang setelah looping semua batch
        if ($sisa > 0) {
            throw new \Exception("Stok barang {$barang->nama_barang} tidak mencukupi untuk pengiriman!");
        }
    }
}

}
