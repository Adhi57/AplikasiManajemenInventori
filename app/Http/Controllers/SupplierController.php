<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /**
     * Menampilkan daftar semua supplier.
     */
    public function index()
    {
        // Mengambil semua data supplier
        $suppliers = Supplier::all();

        // Mengirim data ke view
        return view('suppliers.index', compact('suppliers'));
    }

    /**
     * Menampilkan form untuk membuat supplier baru.
     */
    public function create()
    {
        $supplier = new Supplier();
        return view('suppliers.form', compact('supplier'));
    }

    /**
     * Menyimpan data supplier baru ke database.
     */
    public function store(Request $request)
    {
        
        // 1. Validasi data
        $validatedData = $request->validate([
            'namaSupplier' => 'required|string|max:40',
            'alamatSupplier' => 'required|string|max:100',
            'Kota' => 'required|string|max:30',
            'noTelepon' => 'required|string|max:12',
            'waktuPengiriman' => 'required|integer|min:0',
        ]);
        
        // 2. Simpan data 
        Supplier::create($validatedData);
        return redirect()->route('suppliers.index')
            ->with('success', 'Data Supplier berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail satu supplier tertentu.
     */
    public function show($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.show', compact('supplier'));
    }


    /**
     * Update data supplier di database.
     */
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.form', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'namaSupplier' => 'required|string|max:255',
            'alamatSupplier' => 'required|string',
            'Kota' => 'required|string|max:100',
            'noTelepon' => 'required|string|max:20',
            'waktuPengiriman' => 'required|integer|min:1',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Data supplier berhasil diperbarui.');
    }

    /**
     * Menghapus supplier dari database.
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()->route('suppliers.index')
            ->with('success', 'Data Supplier berhasil dihapus.');
    }
}
