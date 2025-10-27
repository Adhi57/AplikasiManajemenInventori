<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Barang;
use App\Models\PurchaseOrderDetail;
use Illuminate\Http\Request;

class VerifikasiBarangController extends Controller
{
    /**
     * Menampilkan halaman verifikasi barang (daftar PO dengan status Disetujui)
     */
    public function index()
    {
        $po_list = PurchaseOrder::with('supplier')
            ->where('status_po', 'Disetujui')
            ->latest()
            ->get()
            ->map(function ($po) {
                return [
                    'id' => $po->po_id,
                    'number' => $po->po_id,
                    'supplier' => $po->supplier->namaSupplier ?? 'N/A',
                ];
            });

        return view('verifBarang.index', compact('po_list'));
    }

    /**
     * API: Ambil daftar barang berdasarkan PO ID (JSON)
     */
    public function getItems(string $poId)
    {
        $details = PurchaseOrderDetail::where('po_id', $poId)
            ->with('barang')
            ->get()
            ->map(function ($detail) {
                return [
                    'id' => $detail->detail_po_id,
                    'kode_barang' => $detail->kode_barang,
                    'name' => $detail->barang->nama_barang ?? 'Tidak Ditemukan',
                    'unit' => $detail->satuan,
                    'qty_po' => $detail->quantity,
                    'qty_received' => 0,
                ];
            });

        return response()->json($details);
    }
}
