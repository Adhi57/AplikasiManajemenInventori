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
        $search = $request->get('search');

        $PO_List = PurchaseOrder::with(['details', 'supplier', 'user'])
            ->when($status, function ($query, $status) {
                $query->where('status_po', $status);
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('po_id', 'like', "%{$search}%")
                        ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                            $supplierQuery->where('namaSupplier', 'like', "%{$search}%");
                        })
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('nama_lengkap', 'like', "%{$search}%");
                        });
                });
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

        return view('approval.approval_po', compact('PO_List', 'status', 'search'));
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
        DB::beginTransaction();

        try {
            $purchaseOrder = PurchaseOrder::where('po_id', $po_id)->firstOrFail();

            if ($purchaseOrder->status_po !== 'Pending') {
                return redirect()
                    ->route('approval.show_po', $po_id)
                    ->with('error', 'PO ini sudah tidak berstatus Pending.');
            }

            $purchaseOrder->status_po = 'Disetujui';
            $purchaseOrder->save();

            DB::commit();

            return redirect()
                ->route('approval.show_po', $po_id)
                ->with('success', "Purchase Order {$po_id} berhasil disetujui.");

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('approval.show_po', $po_id)
                ->with('error', 'Terjadi kesalahan saat menyetujui PO.');
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

            if ($purchaseOrder->status_po !== 'Pending') {
                return redirect()
                    ->route('approval.show_po', $po_id)
                    ->with('error', 'PO ini sudah tidak berstatus Pending.');
            }

            $purchaseOrder->status_po = 'Ditolak';
            $purchaseOrder->save();

            return redirect()
                ->route('approval.show_po', $po_id)
                ->with('warning', "Purchase Order {$po_id} berhasil ditolak.");

        } catch (Exception $e) {
            return redirect()
                ->route('approval.show_po', $po_id)
                ->with('error', 'Terjadi kesalahan saat menolak PO.');
        }
    }


    public function print_po($po_id)
    {
        $purchaseOrder = PurchaseOrder::with(['user', 'supplier', 'details.barang'])
            ->where('po_id', $po_id)
            ->firstOrFail();

        return Pdf::loadView('approval.print_po', compact('purchaseOrder'))
            ->setPaper('a4', 'portrait')
            ->stream('PurchaseOrder_' . $po_id . '.pdf');
    }
}
