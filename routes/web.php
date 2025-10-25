<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\KategoriPelangganController;
use App\Http\Controllers\PelangganController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VerifikasiBarangController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/barangs', function () {
    return view('barangs.index');
});

Route::get('/suppliers', function () {
    return view('suppliers.index');
});

Route::get('/pelanggans', function () {
    return view('pelanggans.index');
});

Route::get('/kategori_pelanggan', function () {
    return view('kategori_pelanggan.index');
});

Route::get('/kategori_barang', function () {
    return view('kategori_barang.index');
});


Route::get('/verifBarang', function () {
    return view('verifBarang.index');
});

Route::get('/katalog_barang', [KatalogController::class, 'index'])->name('katalog.index');

// Halaman Verifikasi Barang
Route::get('/verifBarang', [VerifikasiBarangController::class, 'index'])->name('verif.barang.index');
    
// // API untuk mengambil data
// Route::get('/api/verifBarang/pos', [VerifikasiBarangController::class, 'getPurchaseOrders'])->name('api.verif.pos');
// Route::get('/api/verifBarang/details/{po_id}', [VerifikasiBarangController::class, 'getPoDetails'])->name('api.verif.details');

// // API untuk aksi
// Route::post('/api/verifBarang/confirm', [VerifikasiBarangController::class, 'confirmGoods'])->name('api.verif.confirm');
// Route::post('/api/verifBarang/return', [VerifikasiBarangController::class, 'processReturn'])->name('api.verif.return');


// Route::get('/verifikasi-barang', [VerifikasiBarangController::class, 'index'])->name('verif.index');
// Route::get('/get-items/{poId}', [VerifikasiBarangController::class, 'getItemsByPO'])->name('verif.items');


Route::resource('suppliers', SupplierController::class);
Route::resource('barangs', barangController::class);
Route::resource('kategori_barang', KategoriBarangController::class);
Route::resource('pelanggans', PelangganController::class);
Route::resource('kategori_pelanggan', KategoriPelangganController::class);
Route::resource('katalog_barang', KatalogController::class);

// Route untuk Halaman Login (Guest/Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
});

// Route setelah Login (Auth/Sudah Login)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route Dashboard berdasarkan Role (Hanya contoh)
    Route::get('/admin/dashboard', function () {
        return view('dashboard.admin');
    })->middleware('can:isAdmin'); // Perlu authorization gate

    Route::get('/staff/dashboard', function () {
        return view('dashboard.staff');
    })->middleware('can:isStaff'); // Perlu authorization gate
    
    Route::get('/head/dashboard', function () {
        return view('dashboard.head');
    })->middleware('can:isHead'); // Perlu authorization gate
});