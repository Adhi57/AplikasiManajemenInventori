<?php

namespace App\Http\Controllers;
use App\Models\Kategori_Pelanggan;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class KategoriPelangganController extends Controller
{
//Menampilkan kategori 
    public function index()
    {
        $kategori_pelanggan = Kategori_Pelanggan::all();
        return view('kategori_pelanggan.index', compact('kategori_pelanggan'));
    }


    public function store(Request $request)
    {
        // 1. Validasi Input
        $validated = $request->validate([
            // Validasi ENUM: harus salah satu dari nilai yang diperbolehkan di database
            'kategori_pelanggan' => [
                'required', 
                'string', 
                Rule::in(['Retail', 'Grosir', 'Biasa'])
            ],
            // Validasi DECIMAL(5,2): Angka desimal, maksimal 999.99
            'jumlah_diskon' => 'nullable|numeric|between:0.00,999.99', 
        ]);

        // 2. Simpan ke Database
        Kategori_Pelanggan::create($validated);

        return redirect()->route('kategori_pelanggan.index')
                         ->with('success', 'Kategori pelanggan berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $editKategori = Kategori_Pelanggan::findOrFail($id);
        
        $kategori_pelanggan = Kategori_Pelanggan::all();

        return view('kategori_pelanggan.index', compact('kategori_pelanggan', 'editKategori'));
    }

    public function update(Request $request, $id)
    {
        $kategori = Kategori_Pelanggan::findOrFail($id);

        $validated = $request->validate([
            'kategori_pelanggan' => [
                'required', 
                'string', 
                Rule::in(['Retail', 'Grosir', 'Biasa'])
            ],
            'jumlah_diskon' => 'nullable|numeric|between:0.00,999.99', 
        ]);

        $kategori->update($validated);

        return redirect()->route('kategori_pelanggan.index')
                         ->with('success', 'Kategori pelanggan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $kategori = Kategori_Pelanggan::findOrFail($id);
        
        try {
            $kategori->delete();
            return redirect()->route('kategori_pelanggan.index')
                             ->with('success', 'Kategori pelanggan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('kategori_pelanggan.index')
                             ->with('error', 'Gagal menghapus! Kategori ini masih digunakan oleh data pelanggan.');
        }
    }
}
