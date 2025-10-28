<?php

namespace App\Http\Controllers;

use App\Models\LaporanBarangMasuk;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\stokBarang;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VerifikasiBarangController extends Controller
{
    public function submitPenerimaan(Request $request)
    {
        $request->validate([
            'po_id' => 'required|exists:purchase_orders,po_id',
            'items_diterima' => 'required|array',
            'items_diterima.*.kode_barang' => 'required|exists:barangs,kode_barang',
            'items_diterima.*.qty_diterima' => 'required|integer|min:0',
            'items_diterima.*.tgl_kadaluarsa' => 'nullable|date|after_or_equal:today', 
            'items_retur' => 'nullable|array',
            'items_retur.*.kode_barang' => 'required|string',
            'items_retur.*.qty_retur' => 'required|integer|min:1',
            'items_retur.*.alasan' => 'nullable|string',
        ]);

        $poId = $request->input('po_id');
        $itemsDiterima = $request->input('items_diterima');
        $itemsRetur = $request->input('items_retur', []);
        $userIdPenerima = Auth::user()->user_id;

        DB::beginTransaction();

        try {
            // 1. Catat ke Laporan Barang Masuk
            $statusPenerimaan = empty($itemsRetur) ? 'Sesuai' : 'Retur';
            $catatanRetur = '';
            if ($statusPenerimaan == 'Retur') {
                $catatanRetur = "Terdapat Retur: " . collect($itemsRetur)
                    ->map(fn($item) => "Barang {$item['kode_barang']} ({$item['qty_retur']} unit) - Alasan: {$item['alasan']}")
                    ->implode('; ');
            }

            $laporan = LaporanBarangMasuk::create([
                'po_id' => $poId,
                'tanggal_terima' => Carbon::now(),
                'user_id_penerima' => $userIdPenerima,
                'status_penerimaan' => $statusPenerimaan,
                'catatan' => $catatanRetur,
            ]);

            // 2. Update Stok Barang dan Purchase Order Detail
            foreach ($itemsDiterima as $item) {
                $kodeBarang = $item['kode_barang'];
                $qtyDiterima = $item['qty_diterima'];
                $tglKadaluarsa = $item['tgl_kadaluarsa'];

                if ($qtyDiterima > 0) {
                    // Validasi kadaluarsa
                    if (!$tglKadaluarsa) {
                        DB::rollBack();
                        return response()->json([
                            'message' => "Tanggal kadaluarsa wajib diisi untuk barang {$kodeBarang} yang diterima."
                        ], 422);
                    }

                    // Update tgl_kadaluarsa_batch di detail PO
                    PurchaseOrderDetail::where('po_id', $poId)
                        ->where('kode_barang', $kodeBarang)
                        ->update([
                            'tgl_kadaluarsa_batch' => $tglKadaluarsa
                        ]);

                    // Insert / Update stok barang
                    stokBarang::updateOrCreate(
                        [
                            'po_id' => $poId,
                            'kode_barang' => $kodeBarang,
                        ],
                        [
                            'jumlah_stok' => DB::raw("jumlah_stok + {$qtyDiterima}"),
                            'tgl_kadaluarsa' => $tglKadaluarsa,
                        ]
                    );
                }
            }

            // 3. Catat retur ke tabel retur (jika ada)
            foreach ($itemsRetur as $retur) {
                // Misal tabel retur_pembelian dengan fields: po_id, kode_barang, qty_retur, alasan, created_at
                DB::table('retur_pembelian')->insert([
                    'po_id' => $poId,
                    'kode_barang' => $retur['kode_barang'],
                    'qty_retur' => $retur['qty_retur'],
                    'alasan' => $retur['alasan'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }

            // 4. Update status PO
            PurchaseOrder::where('po_id', $poId)->update(['status_po' => 'Diterima']);

            DB::commit();

            return response()->json([
                'message' => 'Penerimaan PO berhasil dikonfirmasi dan stok telah diperbarui!',
                'laporan_id' => $laporan->id_laporan
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Gagal submit penerimaan PO: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal memproses penerimaan.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
