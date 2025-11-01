<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class BarangMasukController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            // 🔹 Validasi
            $rules = [
                'po_id' => 'required|string|exists:purchase_orders,po_id',
                'items' => [
                    'required',
                    'array',
                    'min:1',
                    function ($attribute, $value, $fail) {
                        $is_any_item_selected = collect($value)->contains(fn($item) => !empty($item['selected']));
                        if (!$is_any_item_selected) {
                            $fail('Minimal satu item barang harus dicentang untuk diverifikasi.');
                        }
                    },
                ],
                'items.*.qty_diterima' => 'required|numeric|min:0',
                'items.*.tgl_kadaluarsa' => [
                    'nullable',
                    'date',
                    function ($attribute, $value, $fail) use ($request) {
                        $index = explode('.', $attribute)[1];
                        $item = $request->items[$index];

                        if (!empty($item['selected']) && (int)($item['qty_diterima'] ?? 0) > 0 && empty($value)) {
                            $fail('Tanggal Kadaluarsa wajib diisi untuk barang diterima di baris ' . ((int)$index + 1) . '.');
                        }
                    },
                ],
            ];

            $request->validate($rules);

            // 🔹 Ambil supplier dari PO
            $supplierId = DB::table('purchase_orders')
                ->where('po_id', $request->po_id)
                ->value('id_supplier');

            // 🔹 Simpan ke tabel laporan barang masuk
            $barangMasukId = DB::table('lap_barang_masuk')->insertGetId([
                'po_id'         => $request->po_id,
                'user_id'       => Auth::id(),
                'id_supplier'   => $supplierId,
                'tanggal_masuk' => Carbon::now(),
            ]);
            Log::info("Barang Masuk ID berhasil dibuat: {$barangMasukId}");

            // 🔹 Proses tiap item
            foreach ($request->items as $index => $item) {
                if (empty($item['selected'])) continue;

                $kodeBarang = $item['kode_barang'];
                $qtyPo = (int)($item['qty_po'] ?? 0);
                $qtyDiterima = (int)($item['qty_diterima'] ?? 0);
                $qtyRetur = max($qtyPo - $qtyDiterima, 0); // sekarang dianggap retur, bukan rusak
                $tglKadaluarsa = $item['tgl_kadaluarsa'] ?? null;
                $hargaSatuan = DB::table('barangs')->where('kode_barang', $kodeBarang)->value('harga_beli');
                $subtotal = $hargaSatuan * $qtyDiterima;

                Log::info("Memproses item: {$index}", [
                    'kode' => $kodeBarang,
                    'qty_po' => $qtyPo,
                    'qty_diterima' => $qtyDiterima,
                    'qty_retur' => $qtyRetur
                ]);

                // 🔸 Simpan detail barang masuk
                DB::table('detail_lap_barang_masuk')->insert([
                    'barang_masuk_id'   => $barangMasukId,
                    'kode_barang'       => $kodeBarang,
                    'quantity_po'       => $qtyPo,
                    'quantity_diterima' => $qtyDiterima,
                    'quantity_rusak'    => 0, // tidak lagi pakai stok rusak
                    'satuan'            => 'karton',
                    'kondisi'           => 'Baik',
                    'harga_satuan'      => $hargaSatuan,
                    'subtotal'          => $subtotal,
                ]);

                // 🔸 Update / Insert stok barang hanya untuk qty diterima
                if ($qtyDiterima > 0) {
                    $existing = DB::table('stok_barangs')
                        ->where('kode_barang', $kodeBarang)
                        ->where('po_id', $request->po_id)
                        ->first();

                    if ($existing) {
                        DB::table('stok_barangs')
                            ->where('id', $existing->id)
                            ->update([
                                'jumlah_stok' => $existing->jumlah_stok + $qtyDiterima,
                                'tgl_kadaluarsa' => $tglKadaluarsa ?? $existing->tgl_kadaluarsa,
                                'updated_at' => now(),
                            ]);
                    } else {
                        DB::table('stok_barangs')->insert([
                            'po_id' => $request->po_id,
                            'kode_barang' => $kodeBarang,
                            'jumlah_stok' => $qtyDiterima,
                            'jumlah_stok_rusak' => 0,
                            'tgl_kadaluarsa' => $tglKadaluarsa,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // 🔸 Jika ada selisih (retur)
                if ($qtyRetur > 0) {
                    DB::table('retur_barangs')->insert([
                        'po_id' => $request->po_id,
                        'kode_barang' => $kodeBarang,
                        'qty_retur' => $qtyRetur,
                        'alasan' => 'Kuantitas diterima lebih sedikit dari PO',
                        'status_retur' => 'Pending',
                        'tanggal_retur' => Carbon::now(),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    Log::info("Barang {$kodeBarang} masuk retur sebanyak {$qtyRetur}");
                }

                // 🔸 Update status PO
                DB::table('purchase_orders')
                    ->where('po_id', $request->po_id)
                    ->update(['status_po' => 'Diterima', 'updated_at' => now()]);
            }

            DB::commit();
            Log::info("Transaksi BERHASIL TERSIMPAN SEMUA.");

            return redirect()->back()->with('success', 'Data barang masuk & retur berhasil disimpan.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Kegagalan Transaksi Barang Masuk", ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }
}
