<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseOrderDetail;
use Exception;

class PO_ApprovalController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $PO_List = PurchaseOrder::with(['details'])
            ->when($status, function ($query, $status) {
                $query->where('status_po', $status);
            })
            ->orderByRaw("
            CASE 
                WHEN status_po = 'Pending' THEN 1
                WHEN status_po = 'Disetujui' THEN 2
                WHEN status_po = 'Diterima' THEN 3
                WHEN status_po = 'Ditolak' THEN 4
                ELSE 5
            END
        ")
            ->orderBy('created_at', 'desc')
            ->get();


        return view('approval.approval_po', compact('PO_List'));
    }

    public function show($po_id)
    {
        $purchaseOrder = PurchaseOrder::with(['details.barang', 'supplier', 'user'])
            ->where('po_id', $po_id)
            ->firstOrFail();

        return view('approval.show_po', compact('purchaseOrder'));
    }

    public function approve($po_id)
    {
        // Menggunakan transaksi database untuk memastikan konsistensi data
        DB::beginTransaction();

        try {
            $purchaseOrder = PurchaseOrder::where('po_id', $po_id)->firstOrFail();

            // 1. Cek status PO, hanya PO 'Pending' yang bisa disetujui
            if ($purchaseOrder->status_po !== 'Pending') {
                return back()->with('error', 'Gagal menyetujui. Purchase Order ini sudah tidak dalam status Pending.');
            }

            // 2. Update status PO menjadi 'Disetujui'
            $purchaseOrder->status_po = 'Disetujui';
            $purchaseOrder->save();

            DB::commit();

            return redirect()->route('approval.approval_po')->with('success', "Purchase Order {$po_id} berhasil disetujui dan stok barang telah ditambahkan!");
        } catch (Exception $e) {
            DB::rollBack();
            // Log error
            return back()->with('error', 'Terjadi kesalahan saat menyetujui PO: ' . $e->getMessage());
        }
    }

    /**
     * Menolak Purchase Order.
     *
     * @param string $po_id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject($po_id)
    {
        try {
            $purchaseOrder = PurchaseOrder::where('po_id', $po_id)->firstOrFail();

            // 1. Cek status PO, hanya PO 'Pending' yang bisa ditolak
            if ($purchaseOrder->status_po !== 'Pending') {
                return back()->with('error', 'Gagal menolak. Purchase Order ini sudah tidak dalam status Pending.');
            }

            // 2. Update status PO menjadi 'Ditolak'
            $purchaseOrder->status_po = 'Ditolak';
            // Tambahkan kolom lain seperti alasan_penolakan jika ada
            $purchaseOrder->save();

            return redirect()->route('approval.approval_po')->with('warning', "Purchase Order {$po_id} berhasil ditolak.");
        } catch (Exception $e) {
            // Log error
            return back()->with('error', 'Terjadi kesalahan saat menolak PO: ' . $e->getMessage());
        }
    }

    public function print_po($po_id)
    {
        $purchaseOrder = PurchaseOrder::with(['user', 'supplier', 'details.barang'])
            ->where('po_id', $po_id)
            ->firstOrFail();
    
        return Pdf::loadView('approval.show_po', [
            'purchaseOrder' => $purchaseOrder,
            'pdf' => true
        ])
        ->setPaper('a4', 'portrait')
        ->stream('PurchaseOrder_' . $po_id . '.pdf');
    }
}
