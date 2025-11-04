<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BarangController extends Controller
{
    /**
     * Menampilkan daftar semua Barang.
     */
    public function index()
    {
        // Mengambil semua data barang dengan relasi kategori dan supplier
        $barangs = Barang::with(['kategori', 'supplier', 'stok'])->paginate(10);
        
        return view('barangs.index', compact('barangs'));
    }

    /**
     * Menampilkan form untuk membuat Barang baru.
     */
    public function create()
    {
        // Variabel $barang diinisialisasi sebagai model kosong untuk digunakan di form.blade.php (mode CREATE)
        $barang = new Barang();
        $kategoriBarangs = KategoriBarang::all();
        $suppliers = Supplier::all();

        return view('barangs.form', compact('barang', 'kategoriBarangs', 'suppliers'));
    }

    /**
     * Menyimpan Barang yang baru dibuat ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'kategori_barang_id' => 'required|exists:kategori_barangs,kategori_barang_id',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'tipe_harga_barang' => 'required|in:Eceran,Grosir,Diskon', 
            'satuan_terkecil' => 'required|string|max:50',
            'jml_barang_per_karton' => 'required|integer|min:1',
            'berlaku_mulai' => 'required|date',
            
        ]);

        DB::beginTransaction();

        try {
            // 1. Handle File Upload (menggunakan disk 'public' secara eksplisit)
            $foto_path = null;
            if ($request->hasFile('foto_produk')) {
                // Simpan file ke folder 'images/foto_produk' di dalam disk 'public'
                $foto_path = $request->file('foto_produk')->store('images/foto_produk', 'public');
            }

            // 2. Buat Kode Barang Otomatis (contoh sederhana: B-Timestamp)
            $kode_barang = 'B-' . Carbon::now()->format('ymdHis');

            // 3. Simpan data Barang
            $barang = Barang::create([
                'kode_barang' => $kode_barang,
                'nama_barang' => $validated['nama_barang'],
                'foto_produk' => $foto_path, 
                'kategori_barang_id' => $validated['kategori_barang_id'],
                'id_supplier' => $validated['id_supplier'],
                'harga_beli' => $validated['harga_beli'],
                'harga_jual' => $validated['harga_jual'],
                'tipe_harga_barang' => $validated['tipe_harga_barang'],
                'satuan_jual' => $validated['satuan_jual'],
                'jml_barang_per_karton' => $validated['jml_barang_per_karton'],
                'berlaku_mulai' => $validated['berlaku_mulai'],
            ]);

            DB::commit();

            return redirect()->route('barangs.index')->with('success', 'Barang baru berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            // Hapus file jika terjadi error setelah upload (menggunakan disk 'public')
            if ($foto_path) {
                Storage::disk('public')->delete($foto_path);
            }
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menyimpan barang: ' . $e->getMessage()]);
        }
        
    }

    /**
     * Menampilkan form untuk mengedit Barang.
     */
    public function edit($kode_barang)
    {
        $barang = Barang::with(['kategori', 'supplier', 'stok'])->where('kode_barang', $kode_barang)->firstOrFail();
        $kategoriBarangs = KategoriBarang::all();
        $suppliers = Supplier::all();

        return view('barangs.form', compact('barang', 'kategoriBarangs', 'suppliers'));
    }

    /**
     * Memperbarui Barang di database.
     */
    public function update(Request $request, $kode_barang)
    {
        $barang = Barang::where('kode_barang', $kode_barang)->firstOrFail();

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:255',
            'foto_produk' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'kategori_barang_id' => 'required|exists:kategori_barangs,kategori_barang_id',
            'id_supplier' => 'required|exists:suppliers,id_supplier',
            'harga_beli' => 'required|numeric|min:0',
            'harga_jual' => 'required|numeric|min:0',
            'tipe_harga_barang' => 'required|in:Eceran,Grosir,Diskon', // Perbaiki spasi
            'satuan_terkecil' => 'required|string|max:50',
            'jml_barang_per_karton' => 'required|integer|min:1',
            'berlaku_mulai' => 'required|date',
            'tgl_kadaluarsa' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            $old_foto_path = $barang->foto_produk;
            $foto_path = $old_foto_path;

            // 1. Handle File Upload (jika ada file baru)
            if ($request->hasFile('foto_produk')) {
                // Hapus foto lama jika ada, menggunakan disk 'public'
                if ($old_foto_path) {
                    Storage::disk('public')->delete($old_foto_path);
                }
                
                // Simpan file baru ke disk 'public' di folder 'images/foto_produk' (konsisten dengan store)
                $foto_path = $request->file('foto_produk')->store('images/foto_produk', 'public');
            }

            // 2. Update data Barang
            $barang->update([
                'nama_barang' => $validated['nama_barang'],
                'foto_produk' => $foto_path, // Path relatif terhadap disk 'public'
                'kategori_barang_id' => $validated['kategori_barang_id'],
                'id_supplier' => $validated['id_supplier'],
                'harga_beli' => $validated['harga_beli'],
                'harga_jual' => $validated['harga_jual'],
                'tipe_harga_barang' => $validated['tipe_harga_barang'],
                'satuan_terkecil' => $validated['satuan_terkecil'],
                'jml_barang_per_karton' => $validated['jml_barang_per_karton'],
                'berlaku_mulai' => $validated['berlaku_mulai'],
            ]);


            DB::commit();

            return redirect()->route('barangs.index')->with('success', 'Barang berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal memperbarui barang: ' . $e->getMessage()]);
        }
    }

    /**
     * Menghapus Barang dari database.
     */
    public function destroy($kode_barang)
    {
        $barang = Barang::where('kode_barang', $kode_barang)->firstOrFail();

        DB::beginTransaction();
        try {
            // Hapus foto produk jika ada (menggunakan disk 'public')
            if ($barang->foto_produk) {
                Storage::disk('public')->delete($barang->foto_produk);
            }
            
            // Relasi stok akan terhapus jika Anda menggunakan `onDelete('cascade')` pada foreign key
            // Jika tidak, Anda harus menghapus stok secara eksplisit: $barang->stok()->delete();

            $barang->delete();

            DB::commit();

            return redirect()->route('barangs.index')->with('success', 'Barang berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['error' => 'Gagal menghapus barang: ' . $e->getMessage()]);
        }
    }

    
    public function show(Barang $barang)
    {
        $barang->load(['kategori', 'supplier','stok']);

        // Mengembalikan view 'barangs.show' dan menyertakan data barang
        return view('barangs.show', compact('barang'));
    }
    
}
