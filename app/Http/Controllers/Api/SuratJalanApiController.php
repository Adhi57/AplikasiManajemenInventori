<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuratJalan;
use App\Models\SuratJalanDetail;
use App\Models\Pelanggan;
use App\Models\Barang;
use App\Models\Pengiriman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Events\TransactionUpdated;

class SuratJalanApiController extends Controller
{
    // =====================================================
    // GET /api/v1/surat-jalan
    // Daftar surat jalan
    // Query params: status, search, per_page, page
    // =====================================================
    public function index(Request $request)
    {
        $query = SuratJalan::with(['pelanggan', 'details'])
            ->withCount('details')
            ->orderBy('sj_id', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('sj_id', 'like', "%{$s}%")
                  ->orWhereHas('pelanggan', fn($sub) => $sub->where('nama_pelanggan', 'like', "%{$s}%"));
            });
        }

        $perPage = min((int) $request->get('per_page', 20), 100);
        $items   = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $items->map(fn($sj) => $this->formatSuratJalan($sj)),
            'meta'    => [
                'current_page' => $items->currentPage(),
                'last_page'    => $items->lastPage(),
                'per_page'     => $items->perPage(),
                'total'        => $items->total(),
            ],
        ]);
    }

    // =====================================================
    // GET /api/v1/surat-jalan/{sj_id}
    // Detail surat jalan beserta item-itemnya
    // =====================================================
    public function show($sj_id)
    {
        $sj = SuratJalan::with(['pelanggan', 'details.barang'])->where('sj_id', $sj_id)->first();

        if (!$sj) {
            return response()->json([
                'success' => false,
                'message' => "Surat Jalan '{$sj_id}' tidak ditemukan.",
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatSuratJalanDetail($sj),
        ]);
    }

    // =====================================================
    // POST /api/v1/surat-jalan
    // Buat surat jalan baru dari web lain
    //
    // Body JSON:
    // {
    //   "pelanggan_id"      : "PLG-001",         // required
    //   "nama_penerima"     : "Budi Santoso",    // required, max:40
    //   "alamat_penerima"   : "Jl. Merdeka 1",  // required, max:255
    //   "tanggal_surat"     : "2026-04-21",      // required, format: Y-m-d
    //   "biaya_pengiriman"  : 50000,             // optional, default 0
    //   "diskon_pelanggan"  : 5,                 // optional (persen), default 0
    //   "subtotal"          : 1000000,           // optional, default 0
    //   "items"             : [                   // required, min 1 item
    //     {
    //       "kode_barang"  : "BRG001",           // required
    //       "quantity"     : 10,                 // required, min:1
    //       "harga_satuan" : 50000,              // required, min:0
    //       "satuan"       : "pcs"               // required
    //     }
    //   ]
    // }
    // =====================================================
    public function store(Request $request)
    {
        // Validasi input
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'pelanggan_id'           => 'required', // Boleh belum exist di db gudang
            'nama_penerima'          => 'required|string|max:40',
            'alamat_penerima'        => 'required|string|max:255',
            'tanggal_surat'          => 'required|date',
            'biaya_pengiriman'       => 'nullable|numeric|min:0',
            'diskon_pelanggan'       => 'nullable|numeric|min:0|max:100',
            'subtotal'               => 'nullable|numeric|min:0',
            'items'                  => 'required|array|min:1',
            'items.*.kode_barang'    => 'required|exists:barangs,kode_barang',
            'items.*.quantity'       => 'required|numeric|min:1',
            'items.*.harga_satuan'   => 'required|numeric|min:0',
            'items.*.satuan'         => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        DB::beginTransaction();
        try {
            $sj_id = $request->sj_id ?? 'SJ-' . now()->format('ymd') . '-' . strtoupper(Str::random(5));

            // Hitung subtotal otomatis jika tidak dikirim
            $subtotal = $request->subtotal;
            if (is_null($subtotal)) {
                $subtotal = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['harga_satuan']);
            }

            // OTOMATIS BUAT PELANGGAN JIKA BELUM ADA
            $pelanggan = Pelanggan::find($request->pelanggan_id);
            if (!$pelanggan) {
                $defaultKategori = \App\Models\Kategori_Pelanggan::first();
                $kategoriId = $defaultKategori ? $defaultKategori->kategori_pelanggan_id : 1;

                Pelanggan::create([
                    'pelanggan_id'          => $request->pelanggan_id,
                    'nama_pelanggan'        => $request->nama_penerima,
                    'alamat'                => $request->alamat_penerima,
                    'tipe_harga'            => 'eceran', // default untuk ecommerce
                    'NPWP'                  => '-',
                    'PIC'                   => $request->telepon ?? '0000',
                    'kategori_pelanggan_id' => $kategoriId,
                ]);
                Log::info('[API] Data Pelanggan baru otomatis dibuat dari Ecommerce', ['pelanggan_id' => $request->pelanggan_id]);
            }

            $sj = SuratJalan::create([
                'sj_id'            => $sj_id,
                'user_id'          => $request->user_id ?? 'USR-M364PC', // Terima user_id dari Ecommerce
                'pelanggan_id'     => $request->pelanggan_id,
                'nama_penerima'    => $request->nama_penerima,
                'alamat_penerima'  => $request->alamat_penerima,
                'tanggal_surat'    => $request->tanggal_surat,
                'status'           => 'Pending',
                'biaya_pengiriman' => $request->biaya_pengiriman ?? 0,
                'diskon_pelanggan' => $request->diskon_pelanggan ?? 0,
                'subtotal'         => $subtotal,
            ]);

            // Buat record pengiriman awal
            Pengiriman::create([
                'sj_id'             => $sj_id,
                'status_pengiriman' => 'Menunggu',
            ]);

            // Simpan semua item
            foreach ($request->items as $item) {
                SuratJalanDetail::create([
                    'detail_sj_id' => 'SJD-' . strtoupper(Str::random(8)),
                    'sj_id'        => $sj_id,
                    'kode_barang'  => $item['kode_barang'],
                    'quantity'     => $item['quantity'],
                    'harga_satuan' => $item['harga_satuan'],
                    'satuan'       => $item['satuan'],
                ]);
            }

            DB::commit();
            Log::info('[API] Surat Jalan dibuat via API', ['sj_id' => $sj_id]);

            // Load relasi untuk response
            $sj->load(['pelanggan', 'details.barang']);

            // Broadcast Event
            broadcast(new TransactionUpdated('Surat Jalan baru ('.$sj_id.') masuk dari Ecommerce!', 'success'));

            return response()->json([
                'success' => true,
                'message' => 'Surat Jalan berhasil dibuat.',
                'data'    => $this->formatSuratJalanDetail($sj),
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('[API] Gagal membuat Surat Jalan', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat surat jalan: ' . $e->getMessage(),
            ], 500);
        }
    }

    // =====================================================
    // GET /api/v1/pelanggan
    // Daftar pelanggan (untuk kebutuhan form di web lain)
    // =====================================================
    public function pelanggan()
    {
        $pelanggans = Pelanggan::select('pelanggan_id', 'nama_pelanggan', 'alamat', 'tipe_harga')->get();

        return response()->json([
            'success' => true,
            'data'    => $pelanggans,
        ]);
    }

    // =====================================================
    // Helper: format list surat jalan
    // =====================================================
    private function formatSuratJalan(SuratJalan $sj): array
    {
        return [
            'sj_id'            => $sj->sj_id,
            'pelanggan'        => $sj->pelanggan?->nama_pelanggan,
            'nama_penerima'    => $sj->nama_penerima,
            'alamat_penerima'  => $sj->alamat_penerima,
            'tanggal_surat'    => $sj->tanggal_surat,
            'status'           => $sj->status,
            'jumlah_item'      => $sj->details_count,
            'subtotal'         => (float) $sj->subtotal,
            'biaya_pengiriman' => (float) $sj->biaya_pengiriman,
            'diskon_pelanggan' => (float) $sj->diskon_pelanggan,
            'total_bayar'      => (float) $sj->subtotal
                                    - ((float) $sj->subtotal * ($sj->diskon_pelanggan / 100))
                                    + (float) $sj->biaya_pengiriman,
        ];
    }

    // =====================================================
    // Helper: format detail surat jalan + items
    // =====================================================
    private function formatSuratJalanDetail(SuratJalan $sj): array
    {
        return [
            'sj_id'            => $sj->sj_id,
            'pelanggan_id'     => $sj->pelanggan_id,
            'pelanggan'        => $sj->pelanggan?->nama_pelanggan,
            'nama_penerima'    => $sj->nama_penerima,
            'alamat_penerima'  => $sj->alamat_penerima,
            'tanggal_surat'    => $sj->tanggal_surat,
            'status'           => $sj->status,
            'subtotal'         => (float) $sj->subtotal,
            'biaya_pengiriman' => (float) $sj->biaya_pengiriman,
            'diskon_pelanggan' => (float) $sj->diskon_pelanggan,
            'total_bayar'      => (float) $sj->subtotal
                                    - ((float) $sj->subtotal * ($sj->diskon_pelanggan / 100))
                                    + (float) $sj->biaya_pengiriman,
            'items'            => $sj->details->map(fn($d) => [
                'kode_barang'  => $d->kode_barang,
                'nama_barang'  => $d->barang?->nama_barang,
                'quantity'     => (float) $d->quantity,
                'harga_satuan' => (float) $d->harga_satuan,
                'satuan'       => $d->satuan,
                'subtotal_item'=> (float) $d->quantity * (float) $d->harga_satuan,
            ]),
        ];
    }
    // =====================================================
    // POST /api/v1/surat-jalan/{sj_id}/cancel-request
    // Terima notifikasi pengajuan pembatalan dari E-Commerce
    // =====================================================
    public function cancelRequest($sj_id)
    {
        $sj = SuratJalan::where('sj_id', $sj_id)->first();

        if (!$sj) {
            return response()->json([
                'success' => false,
                'message' => "Surat Jalan '{$sj_id}' tidak ditemukan.",
            ], 404);
        }

        // Ubah status ke Pengajuan Batal
        $sj->status = 'Pengajuan Batal';
        $sj->save();

        Log::info('[API] Permintaan Batal untuk Surat Jalan', ['sj_id' => $sj_id]);

        // Broadcast Event
        broadcast(new TransactionUpdated('Pengajuan batal untuk Surat Jalan ('.$sj_id.') dari Ecommerce!', 'warning'));

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diubah menjadi Pengajuan Batal',
        ]);
    }
}
