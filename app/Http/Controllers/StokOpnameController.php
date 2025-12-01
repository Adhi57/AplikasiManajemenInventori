<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StokBarang;
use Illuminate\Support\Facades\DB;

class StokOpnameController extends Controller
{
    public function index()
    {
        $stok = StokBarang::with('barang')->get();
        return view('stok_opname.index', compact('stok'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'stok_id' => 'required|exists:stok_barangs,id',
            'jumlah_stok' => 'required|numeric|min:0',
            'jumlah_stok_rusak' => 'required|numeric|min:0',
            'tgl_kadaluarsa' => 'nullable|date',
            'alasan' => 'required|string'
        ]);
    
        $stok = StokBarang::findOrFail($request->stok_id);
    
        // Data sebelum
        $before = [
            'baik' => $stok->jumlah_stok,
            'rusak' => $stok->jumlah_stok_rusak,
            'exp'   => $stok->tgl_kadaluarsa,
        ];
    
        // Update stok
        $stok->jumlah_stok = $request->jumlah_stok;
        $stok->jumlah_stok_rusak = $request->jumlah_stok_rusak;
        $stok->tgl_kadaluarsa = $request->tgl_kadaluarsa;
        $stok->save();
    
        // LOG
        DB::table('stok_opname_logs')->insert([
            'stok_id' => $stok->id,
    
            'stok_baik_sebelum' => $before['baik'],
            'stok_rusak_sebelum' => $before['rusak'],
            'tgl_kadaluarsa_sebelum' => $before['exp'],
    
            'stok_baik_sesudah' => $request->jumlah_stok,
            'stok_rusak_sesudah' => $request->jumlah_stok_rusak,
            'tgl_kadaluarsa_sesudah' => $request->tgl_kadaluarsa,
    
            'alasan_update' => $request->alasan,
            'user_id' => auth()->id(),
            'created_at' => now()
        ]);
    
        return back()->with('success', 'Stock opname berhasil diperbarui & dicatat!');
    }
}    