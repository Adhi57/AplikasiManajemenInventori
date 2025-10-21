<?php

namespace App\Http\Controllers;

use App\Models\KategoriBarang;
use Illuminate\Http\Request;

class KategoriBarangController extends Controller
{
    public function index()
    {
        $kategori_barang = KategoriBarang::all();
        return view('kategori_barang.index', compact('kategori_barang'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_kategori_barang' => 'required|string|max:100',
        ]);

        KategoriBarang::create($validated);

        return redirect()->route('kategori_barang.index')
            ->with('success', 'Kategori barang berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori_barang = KategoriBarang::all();
        $editKategori = KategoriBarang::findOrFail($id);

        return view('kategori_barang.index', compact('kategori_barang', 'editKategori'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_kategori_barang' => 'required|string|max:100',
        ]);

        $kategori = KategoriBarang::findOrFail($id);
        $kategori->update($validated);

        return redirect()->route('kategori_barang.index')
            ->with('success', 'Kategori barang berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = KategoriBarang::findOrFail($id);
        $kategori->delete();

        return redirect()->route('kategori_barang.index')
            ->with('success', 'Kategori barang berhasil dihapus.');
    }
}
