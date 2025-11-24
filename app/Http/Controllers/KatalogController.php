<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use Illuminate\Http\Request;

class KatalogController extends Controller
{
    /**
     * Display a listing of the inventory items (catalog).
     */
    public function index(Request $request)
    {
        // 1. Get all categories for filter options
        $kategoris = KategoriBarang::all();

        // 2. Start the query for Barangs (Items)
        $barangsQuery = Barang::with(['kategori', 'supplier', 'stok'])
            ->orderBy('nama_barang', 'asc');

        // 3. Handle search/filter (Example: Search by product name)
        if ($request->has('search') && $request->search != '') {
            $barangsQuery->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        // 4. Handle category filter
        if ($request->has('kategori') && $request->kategori != '') {
            $barangsQuery->where('kategori_barang_id', $request->kategori);
        }
        
        // 5. Paginate the results
        $barangs = $barangsQuery->paginate(12); // 10 items per page

        return view('katalog.index', compact('barangs', 'kategoris'));
    }
}
