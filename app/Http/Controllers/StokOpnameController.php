<?php

namespace App\Http\Controllers;

use App\Models\stokOpnameLogs;
use Illuminate\Http\Request;
use App\Models\StokBarang;

class StokOpnameController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $stok = StokBarang::with('barang')
            ->when($search, function ($query, $search) {
                $query->where('kode_barang', 'like', "%{$search}%")
                    ->orWhereHas('barang', function ($q) use ($search) {
                        $q->where('nama_barang', 'like', "%{$search}%");
                    });
            })
            ->orderBy('kode_barang')
            ->get();

        $logs = stokOpnameLogs::with(['stok.barang', 'user'])
                    ->latest()
                    ->take(5)
                    ->get();

        return view('stok_opname.index', compact('stok', 'logs', 'search'));
    }

    public function update(Request $request)
    {
        // DEBUG 1: Log semua data yang diterima dari form
        \Log::info('=== STOCK OPNAME UPDATE START ===');
        \Log::info('All request data:', $request->all());

        $validated = $request->validate([
            'stok_id' => 'required|exists:stok_barangs,id',
            'jumlah_stok' => 'required|numeric|min:0',
            'jumlah_stok_rusak' => 'required|numeric|min:0',
            'tgl_kadaluarsa' => 'nullable|date',
            'alasan' => 'required|string|max:255'
        ]);

        // Cast ke integer untuk disimpan ke database
        $validated['jumlah_stok'] = (int) $validated['jumlah_stok'];
        $validated['jumlah_stok_rusak'] = (int) $validated['jumlah_stok_rusak'];

        // DEBUG 2: Log validated data
        \Log::info('Validated data:', $validated);

        $stok = StokBarang::findOrFail($validated['stok_id']);

        // DEBUG 3: Log record yang ditemukan (sebelum update)
        \Log::info('Stok SEBELUM update:', [
            'id' => $stok->id,
            'kode_barang' => $stok->kode_barang,
            'jumlah_stok' => $stok->jumlah_stok,
            'jumlah_stok_rusak' => $stok->jumlah_stok_rusak,
            'tgl_kadaluarsa' => $stok->tgl_kadaluarsa,
            'incrementing' => $stok->getIncrementing(),
            'keyType' => $stok->getKeyType(),
            'key' => $stok->getKey(),
        ]);

        // Data sebelum
        $before = [
            'baik' => $stok->jumlah_stok,
            'rusak' => $stok->jumlah_stok_rusak,
            'exp'   => $stok->tgl_kadaluarsa,
        ];

        // Update stok
        $updateResult = $stok->update([
            'jumlah_stok' => $validated['jumlah_stok'],
            'jumlah_stok_rusak' => $validated['jumlah_stok_rusak'],
            'tgl_kadaluarsa' => $validated['tgl_kadaluarsa'],
        ]);

        // DEBUG 4: Log hasil update
        \Log::info('Update result:', [
            'success' => $updateResult,
            'dirty' => $stok->getDirty(),
            'changes' => $stok->getChanges(),
        ]);

        // DEBUG 5: Reload dari database dan cek
        $stokAfter = StokBarang::find($stok->id);
        \Log::info('Stok SESUDAH update (reload dari DB):', [
            'id' => $stokAfter->id,
            'jumlah_stok' => $stokAfter->jumlah_stok,
            'jumlah_stok_rusak' => $stokAfter->jumlah_stok_rusak,
            'tgl_kadaluarsa' => $stokAfter->tgl_kadaluarsa,
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

        \Log::info('=== STOCK OPNAME UPDATE END ===');

        return back()->with('success', 'Stock opname berhasil diperbarui & dicatat!');
    }

    public function riwayat(Request $request)
    {
        $search = $request->input('search');

        $logs = stokOpnameLogs::with(['stok.barang', 'user'])
            ->when($search, function ($query, $search) {
                $query->where('alasan_update', 'like', "%{$search}%")
                    ->orWhereHas('stok', function ($q) use ($search) {
                        $q->where('kode_barang', 'like', "%{$search}%");
                    })
                    ->orWhereHas('stok.barang', function ($q) use ($search) {
                        $q->where('nama_barang', 'like', "%{$search}%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('nama_lengkap', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(15);

        return view('stok_opname.riwayat', compact('logs', 'search'));
    }
}

