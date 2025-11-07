<?php

namespace App\Http\Controllers;

use App\Models\Pengiriman;
use App\Models\SuratJalan;
use Illuminate\Http\Request;

class PengirimanController extends Controller
{
    public function index()
    {
        $pengirimans = \App\Models\Pengiriman::with(['suratJalan.pelanggan'])
            ->latest()
            ->paginate(10);
    
        return view('pengiriman.index', compact('pengirimans'));
    }
    

    // CREATE
    public function create()
    {
        $surat_jalans = SuratJalan::with('pelanggan')->get();
        return view('pengiriman.form', compact('surat_jalans'));
    }

    // EDIT
    public function edit($id)
    {
        $pengiriman = Pengiriman::findOrFail($id);
        $surat_jalans = SuratJalan::with('pelanggan')->get();
        return view('pengiriman.form', compact('pengiriman', 'surat_jalans'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'sj_id' => 'required|exists:surat_jalans,sj_id',
            'nama_driver' => 'nullable|string|max:100',
            'no_polisi' => 'nullable|string|max:50',
            'tanggal_pengiriman' => 'nullable|date',
            'tanggal_sampai' => 'nullable|date|after_or_equal:tanggal_pengiriman',
        ]);

        Pengiriman::create($request->all());

        return redirect()->route('pengiriman.index')->with('success', 'Data pengiriman berhasil disimpan!');
    }
}
