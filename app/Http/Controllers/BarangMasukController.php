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

            
                $rules = [
                    'po_id' => 'required|string|exists:purchase_orders,po_id',
                    'items' => [
                        'required', 
                        'array', 
                        'min:1',
                        function ($attribute, $value, $fail) {
                            $is_any_item_selected = collect($value)->contains(function ($item) {
                                return !empty($item['selected']);
                            });
    
                            if (!$is_any_item_selected) {
                                $fail('Minimal satu item barang harus dicentang (dipilih) untuk diverifikasi.');
                            }
                        },
                    ],
                    'items.*.qty_diterima' => 'required|numeric|min:0',
                    
                    'items.*.tgl_kadaluarsa' => [
                        'nullable',
                        'date',
                        function ($attribute, $value, $fail) use ($request) {
                            // Mendapatkan indeks item (misal: 0, 1, 2)
                            $index = explode('.', $attribute)[1];
                            $item = $request->items[$index];
    
                            // Hanya validasi jika item dicentang DAN qty_diterima > 0
                            if (!empty($item['selected']) && (int)($item['qty_diterima'] ?? 0) > 0) {
                                if (empty($value)) {
                                    $fail('Tanggal Kadaluarsa wajib diisi untuk barang yang diterima di baris ' . ((int)$index + 1) . '.');
                                }
                            }
                        },
                    ],
                ];
    
                $request->validate($rules);

            // Ambil supplier dari PO
            $supplierId = DB::table('purchase_orders')
                ->where('po_id', $request->po_id)
                ->value('id_supplier');

            // Simpan ke tabel lap_barang_masuk
            $barangMasukId = DB::table('lap_barang_masuk')->insertGetId([
                'po_id'         => $request->po_id,
                'user_id'       => Auth::id(),
                'id_supplier'   => $supplierId,
                'tanggal_masuk' => Carbon::now(),
            ]);
            Log::info("Barang Masuk ID berhasil dibuat: " . $barangMasukId);

            // Loop data items
            foreach ($request->items as $index => $item) {
                if (empty($item['selected'])) continue; // hanya simpan yg dicentang

                $qtyPo        = $item['qty_po'] ?? 0;
                $qtyDiterima  = $item['qty_diterima'] ?? 0;
                $qtyRusak     = max($qtyPo - $qtyDiterima, 0);
                $hargaSatuan  = DB::table('barangs')->where('kode_barang', $item['kode_barang'])->value('harga_beli');
                $subtotal     = $hargaSatuan * $qtyDiterima;
                $tglKadaluarsa = $item['tgl_kadaluarsa'] ?? null;
                Log::info("Memproses item: {$index}", ['kode' => $item['kode_barang'], 'qty_diterima' => $item['qty_diterima']]);

                // Simpan detail barang masuk
                DB::table('detail_lap_barang_masuk')->insert([
                    'barang_masuk_id'   => $barangMasukId,
                    'kode_barang'       => $item['kode_barang'],
                    'quantity_po'       => $qtyPo,
                    'quantity_diterima' => $qtyDiterima,
                    'quantity_rusak'    => $qtyRusak, 
                    'satuan'            => 'karton',
                    'kondisi'           => $qtyRusak > 0 ? 'Rusak' : 'Baik',
                    'harga_satuan'      => $hargaSatuan,
                    'subtotal'          => $subtotal,
                ]);

                // Update / Insert ke stok_barangs
                $existing = DB::table('stok_barangs')
                    ->where('kode_barang', $item['kode_barang'])
                    ->where('po_id', $request->po_id)
                    ->first();

                if ($existing) {
                    // Jika stok sudah ada → update jumlah stok dan stok rusak
                    DB::table('stok_barangs')
                        ->where('id', $existing->id)
                        ->update([
                            'jumlah_stok'       => $existing->jumlah_stok + $qtyDiterima,
                            'jumlah_stok_rusak' => $existing->jumlah_stok_rusak + $qtyRusak,
                            'tgl_kadaluarsa'    => $tglKadaluarsa ?? $existing->tgl_kadaluarsa,
                            'updated_at'        => now(),
                        ]);
                } else {
                    // Jika belum ada → buat record baru
                    DB::table('stok_barangs')->insert([
                        'po_id'              => $request->po_id,
                        'kode_barang'        => $item['kode_barang'],
                        'jumlah_stok'        => $qtyDiterima,
                        'jumlah_stok_rusak'  => $qtyRusak,
                        'tgl_kadaluarsa'     => $tglKadaluarsa,
                        'created_at'         => now(),
                        'updated_at'         => now(),
                    ]);
                }

                DB::table('purchase_orders')
                ->where('po_id', $request->po_id)
                ->update(['status_po' => 'Diterima', 'updated_at' => now()]);
            }
        DB::commit();
        Log::info("Transaksi BERHASIL TERSIMPAN SEMUA.");
        return redirect()->back()->with('success', 'Data barang masuk & stok berhasil diperbarui.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Kegagalan Transaksi Barang Masuk", ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage()); 
        }
    }
}