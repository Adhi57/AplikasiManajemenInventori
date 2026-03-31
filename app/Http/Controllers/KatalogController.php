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
        // 1. Get all categories with product count
        $kategoris = KategoriBarang::withCount('barangs')->orderBy('nama_kategori_barang')->get();

        // 2. Start the query for Barangs (Items)
        $barangsQuery = Barang::with(['kategori', 'supplier', 'stok']);

        // 3. Handle search/filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $barangsQuery->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', '%' . $search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $search . '%');
            });
        }

        // 4. Handle category filter
        if ($request->has('kategori') && $request->kategori != '') {
            $barangsQuery->where('kategori_barang_id', $request->kategori);
        }

        // 5. Handle sorting
        $sort = $request->get('sort', 'nama');
        switch ($sort) {
            case 'harga_asc':
                $barangsQuery->orderBy('harga_jual', 'asc');
                break;
            case 'harga_desc':
                $barangsQuery->orderBy('harga_jual', 'desc');
                break;
            case 'terbaru':
                $barangsQuery->latest('kode_barang');
                break;
            default:
                $barangsQuery->orderBy('nama_barang', 'asc');
        }

        // 6. Paginate the results
        $barangs = $barangsQuery->paginate(12);

        // 7. Stats for the header
        $totalProduk = Barang::count();
        $totalKategori = KategoriBarang::count();
        $totalTersedia = Barang::whereHas('stoks', function ($q) {
            $q->where('jumlah_stok', '>', 0);
        })->count();

        // Active category name for display
        $activeKategori = null;
        if ($request->kategori) {
            $activeKategori = KategoriBarang::find($request->kategori);
        }

        return view('katalog.index', compact(
            'barangs', 'kategoris',
            'totalProduk', 'totalKategori', 'totalTersedia',
            'activeKategori', 'sort'
        ));
    }
}
