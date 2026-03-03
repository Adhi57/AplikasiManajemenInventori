<?php

namespace App\Http\Controllers;

use App\Models\ReturBarang;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReturBarangController extends Controller
{
    /**
     * Menampilkan daftar retur barang
     */
    public function index(Request $request)
    {
        $search = $request->get('search', null);
        $status = $request->get('status', null);
        $query = ReturBarang::with(['purchaseOrder', 'barang']);

        // Filter pencarian berdasarkan kode barang atau po_id
        if ($search) {
            $query->where('kode_barang', 'like', "%{$search}%")
                  ->orWhere('po_id', 'like', "%{$search}%");
        }

        // Filter berdasarkan status retur
        if ($status) {
            $query->where('status_retur', $status);
        }

        // Urutkan: pending dulu, baru yang lain
        $query->orderByRaw("
            CASE 
                WHEN status_retur = 'Pending' THEN 1
                ELSE 2
            END
        ")->orderBy('tanggal_retur', 'desc');

        $returs = $query->paginate(10);

        return view('returBarang.index', compact('returs', 'search', 'status'));
    }

    /**
     * Menampilkan detail satu retur barang
     */
    public function show($id)
    {
        $retur = ReturBarang::with(['purchaseOrder', 'barang'])->findOrFail($id);
        return view('returBarang.show', compact('retur'));
    }

    public function updateAlasan(Request $request, $id)
{
    $request->validate([
        'alasan' => 'nullable|string|max:255',
    ]);

    $retur = ReturBarang::findOrFail($id);
    $retur->alasan = $request->alasan;
    $retur->save();

    return response()->json([
        'success' => true,
        'message' => 'Alasan berhasil diperbarui.',
    ]);
}

public function konfirmasiSesuai($retur_id)
{
    try {
        $retur = ReturBarang::findOrFail($retur_id);

        if ($retur->status_retur === 'Selesai') {
            return response()->json([
                'success' => false,
                'message' => 'Retur ini sudah dikonfirmasi sebelumnya.'
            ]);
        }

        $retur->update([
            'status_retur' => 'Disetujui',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Retur berhasil dikonfirmasi sebagai Disetujui.'
        ]);
    } catch (\Exception $e) {
        Log::error('Gagal konfirmasi retur: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat mengonfirmasi retur.'
        ], 500);
    }
}

public function batalkanRetur($id)
{
    try {
        $retur = ReturBarang::findOrFail($id);

        if ($retur->status_retur === 'Ditolak') {
            return response()->json([
                'success' => false,
                'message' => 'Retur ini sudah dibatalkan sebelumnya.'
            ]);
        }

        $retur->update([
            'status_retur' => 'Ditolak',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Retur berhasil dibatalkan.'
        ]);

    } catch (\Exception $e) {
        Log::error('Gagal batalkan retur: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan saat membatalkan retur.'
        ], 500);
    }
}

public function updateTanggal(Request $request, $id)
{
    $request->validate([
        'tanggal_retur' => 'required|date',
    ]);

    $retur = ReturBarang::findOrFail($id);
    $retur->tanggal_retur = $request->tanggal_retur;
    $retur->save();

    return response()->json([
        'success' => true,
        'message' => 'Tanggal retur berhasil diperbarui.'
    ]);
}



}
