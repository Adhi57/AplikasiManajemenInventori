<?php

namespace App\Http\Controllers;

use App\Models\Kategori_Pelanggan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::with(['kategori_pelanggan'])->get();

        return view('pelanggans.index', compact('pelanggans'));
    }

    public function create()
    {
        $kategori_pelanggans = Kategori_Pelanggan::all();
        $pelanggans = Pelanggan::all();

        return view('pelanggans.create', compact('kategori_pelanggans'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'alamat' => 'required|string|max:255',
            'NPWP' => 'nullable|string|max:20',
            'PIC' => 'nullable|string|max:50',
            'kategori_pelanggan_id' => 'required|exists:kategori_pelanggans,kategori_pelanggan_id',
            'tipe_harga' => 'required|string',
        ]);
    
        // Simpan data
        Pelanggan::create($request->all());
    
        return redirect()->route('pelanggans.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    public function show($id)
    {
        $pelanggans = Pelanggan::findOrFail($id);
        return view('pelanggans.show', compact('pelanggans'));
    }

// EDIT
    public function edit($id)
    {
        $pelanggan = Pelanggan::findOrFail($id);
        $kategori_pelanggans = Kategori_Pelanggan::all();
        return view('pelanggans.edit', compact('pelanggan', 'kategori_pelanggans'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'alamat' => 'required|string',
            'NPWP' => 'nullable|string|max:20',
            'PIC' => 'nullable|string|max:50',
            'kategori_pelanggan_id' => 'required|exists:kategori_pelanggans,kategori_pelanggan_id',
            'tipe_harga' => 'required|string',
        ]);

        $pelanggan = Pelanggan::findOrFail($id);
        $pelanggan->update($validated);

        return redirect()->route('pelanggans.index')->with('success', 'Data pelanggan berhasil diperbarui!');
    }

// DELETE
    public function destroy($id)
    {
        // cari barang berdasarkan primary key
        $pelanggans = Pelanggan::where('pelanggan_id', $id)->firstOrFail();

        // hapus record
        $pelanggans->delete();

        return redirect()->route('pelanggans.index')
            ->with('success', 'Data Pelanggan berhasil dihapus.');
    }

    
}
