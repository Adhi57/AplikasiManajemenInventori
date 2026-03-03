<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetail;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class PurchaseOrdersController extends Controller
{
    
    /**
     * Menampilkan form katalog barang dan keranjang permintaan.
     */
    public function create()
    {
        // Ambil data yang dibutuhkan untuk katalog dan filter
        $barangs = Barang::with(['supplier', 'kategori'])->latest()->paginate(15);
        $suppliers = Supplier::all();
        $kategoris = KategoriBarang::all();

        return view('purchase_orders.buat_permintaan', compact('barangs', 'suppliers', 'kategoris'));
    }

    /**
     * Mengambil data barang dengan filter (untuk AJAX/Alpine.js)
     */
    public function fetchBarangs(Request $request)
    {
        $query = Barang::with(['supplier', 'kategori']);

        if ($request->filled('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kategori')) {
            $query->where('kategori_barang_id', $request->kategori);
        }

        if ($request->filled('supplier')) {
            $query->where('id_supplier', $request->supplier);
        }

        $barangs = $query->latest()->paginate(15);
        
        // Kembalikan hanya HTML dari partial view
        return response()->json([
            'html' => view('purchase_orders._barang_list', compact('barangs'))->render(),
            'pagination' => (string) $barangs->links()
        ]);
    }
    
    /**
     * Menyimpan data permintaan pembelian baru.
     */
    public function store(Request $request)
    {
        // 1. VALIDASI DATA
        try {
            $validated = $request->validate([
                'id_supplier' => 'required|exists:suppliers,id_supplier',
                'tanggal_po' => 'required|date',
                'items' => 'required|array|min:1', // Harus ada minimal 1 item
                'items.*.kode_barang' => 'required|string|exists:barangs,kode_barang',
                'items.*.quantity' => 'required|numeric|min:1',
                'items.*.harga_satuan' => 'required|numeric|min:0',
                'items.*.satuan' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return redirect()->back()->withInput()->withErrors($e->errors())->with('error', 'Validasi gagal. Pastikan semua kolom item terisi dengan benar (Qty minimal 1).');
        }

        // 2. HITUNG TOTAL HARGA & SIAPKAN DETAIL
        $totalHarga = 0;
        $poDetails = [];
        
        foreach ($validated['items'] as $item) {
            $harga_dasar = $item['quantity'] * $item['harga_satuan'];
            $ppn = $harga_dasar * 0.11;
            $subtotal = $harga_dasar + $ppn;

            $poDetails[] = [
                // po_id akan ditambahkan setelah header dibuat
                'kode_barang' => $item['kode_barang'],
                'quantity' => $item['quantity'],
                'harga_satuan' => $item['harga_satuan'],
                'satuan' => $item['satuan'],
                'created_at' => now(), 
                'updated_at' => now(), 
            ];
        }

        // 3. MULAI TRANSAKSI
        DB::beginTransaction();

        try {
            // 4. BUAT HEADER PURCHASE ORDER
            // po_id akan otomatis terisi oleh fungsi boot() di Model PurchaseOrder
            $poHeader = PurchaseOrder::create([
                'id_supplier' => $validated['id_supplier'],
                'tanggal_po' => $validated['tanggal_po'],
                'user_id' => Auth::id(), 
                'status_po' => 'Pending', 
                'total_harga' => $subtotal,
            ]);
            
            // Ambil PO ID yang baru dibuat untuk prefix detail
            $newPoId = $poHeader->po_id;

            // 5. SIAPKAN DETAIL UNTUK INSERT MASSAL
            $finalPoDetails = [];
            $itemCount = 1;

            foreach ($poDetails as $detail) {
                // Generate detail_po_id
                $detailPoId = 'POD/' . $newPoId . '/' . str_pad($itemCount, 3, '0', STR_PAD_LEFT);
                
                $finalPoDetails[] = array_merge($detail, [
                    'po_id' => $newPoId, 
                    'detail_po_id' => $detailPoId, 
                ]);

                $itemCount++;
            }
            
            // Menggunakan insert() dari Model PurchaseOrderDetail
            PurchaseOrderDetail::insert($finalPoDetails);

            // 6. COMMIT TRANSAKSI
            DB::commit();

            // REDIRECT SUKSES
            // Mengembalikan po_id yang sudah digenerate
            return redirect()->back()->with('success', 'Permintaan Pembelian berhasil dibuat dan menunggu approval dengan nomor PO: ' . $poHeader->po_id);

        } catch (\Exception $e) {
            // 7. ROLLBACK JIKA ADA KEGAGALAN
            DB::rollBack();
            
            Log::error('Gagal Menyimpan Purchase Order:', ['error' => $e->getMessage(), 'request' => $request->all()]);

            return redirect()->back()->withInput()->with('error', 'Gagal membuat Permintaan Pembelian. Silakan coba lagi. ' . $e->getMessage());
        }
    }public function getItems($po_id)
    {
        $details = PurchaseOrderDetail::with('barang')
            ->where('po_id', $po_id)
            ->get()
            ->map(function ($d) {
                return [
                    'kode_barang'   => $d->kode_barang,
                    'nama_barang'   => $d->barang->nama_barang ?? '-',
                    'qty_po'        => $d->quantity,
                    'qty_diterima'  => 0, // default 0, bisa diubah nanti di UI
                ];
            });
    
        return response()->json($details);
    }
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with(['supplier', 'user'])
            ->withCount('details')
            ->where('status_po', 'Disetujui')
            ->latest()
            ->get();

        return view('verifBarang.index', compact('purchaseOrders'));
    }

}
