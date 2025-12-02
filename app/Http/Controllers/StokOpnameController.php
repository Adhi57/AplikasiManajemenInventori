<?php

namespace App\Http\Controllers;

use App\Models\stokOpnameLogs;
use Illuminate\Http\Request;
use App\Models\StokBarang;

class StokOpnameController extends Controller
{
    public function index()
    {
        $stok = StokBarang::with('barang')->get();
        $logs = stokOpnameLogs::with(['stok.barang', 'user'])
                    ->latest()
                    ->take(20) 
                    ->get();

        return view('stok_opname.index', compact('stok', 'logs'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'stok_id' => 'required|exists:stok_barangs,id',
            'jumlah_stok' => 'required|integer|min:0',
            'jumlah_stok_rusak' => 'required|integer|min:0',
            'tgl_kadaluarsa' => 'nullable|date',
            'alasan' => 'required|string|max:255'
        ]);

        $stok = StokBarang::findOrFail($validated['stok_id']);

        // Data sebelum
        $before = [
            'baik' => $stok->jumlah_stok,
            'rusak' => $stok->jumlah_stok_rusak,
            'exp'   => $stok->tgl_kadaluarsa,
        ];

        // Update stok
        $stok->update([
            'jumlah_stok' => $validated['jumlah_stok'],
            'jumlah_stok_rusak' => $validated['jumlah_stok_rusak'],
            'tgl_kadaluarsa' => $validated['tgl_kadaluarsa'],
        ]);

        // Catat log
        stokOpnameLogs::create([
            'stok_id' => $stok->id,
            'stok_baik_sebelum' => $before['baik'],
            'stok_rusak_sebelum' => $before['rusak'],
            'tgl_kadaluarsa_sebelum' => $before['exp'],
            'stok_baik_sesudah' => $validated['jumlah_stok'],
            'stok_rusak_sesudah' => $validated['jumlah_stok_rusak'],
            'tgl_kadaluarsa_sesudah' => $validated['tgl_kadaluarsa'],
            'alasan_update' => $validated['alasan'],
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Stock opname berhasil diperbarui & dicatat!');
    }
}

