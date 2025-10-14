<?php

namespace App\Http\Controllers;

use App\Models\Kategori_Pelanggan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    public function index()
    {
        $pelanggans = Pelanggan::with(['Kategori_Pelanggan'])->get();

        return view('pelanggans.index', compact('pelanggans'));
    }

    public function create()
    {
        $kategori_pelanggans = Kategori_Pelanggan::all();
        $pelanggans = Pelanggan::all();

        return view('pelanggans.create', compact('kategori_pelanggans'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:255',
            'alamat' => 'required|string',
            'NPWP' => 'nullable|string|max:50',
            'PIC' => 'nullable|string|max:100',
            'kategori_pelanggan_id' => 'required|exists:kategori_pelanggans,kategori_pelanggan_id',
            'tipe_harga' => 'required|string',
        ]);

        Pelanggan::create($request->all());

        return redirect()->route('pelanggans.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }
}
