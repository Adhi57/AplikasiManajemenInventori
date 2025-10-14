<?php

use App\Http\Controllers\BarangController;
use App\Http\Controllers\KategoriPelangganController;
use App\Http\Controllers\PelangganController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VerifikasiBarangController; 

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


Route::get('/verifBarang', function () {
    return view('verifBarang.index');
});

// Halaman Verifikasi Barang
Route::get('/verifBarang', [VerifikasiBarangController::class, 'index'])->name('verif.barang.index');
    
// API untuk mengambil data
Route::get('/api/verifBarang/pos', [VerifikasiBarangController::class, 'getPurchaseOrders'])->name('api.verif.pos');
Route::get('/api/verifBarang/details/{po_id}', [VerifikasiBarangController::class, 'getPoDetails'])->name('api.verif.details');

// API untuk aksi
Route::post('/api/verifBarang/confirm', [VerifikasiBarangController::class, 'confirmGoods'])->name('api.verif.confirm');
Route::post('/api/verifBarang/return', [VerifikasiBarangController::class, 'processReturn'])->name('api.verif.return');


Route::get('/verifikasi-barang', [VerifikasiBarangController::class, 'index'])->name('verif.index');
Route::get('/get-items/{poId}', [VerifikasiBarangController::class, 'getItemsByPO'])->name('verif.items');


Route::resource('suppliers', SupplierController::class);
Route::resource('barangs', barangController::class);
Route::resource('kategoriBarangs', KategoriBarangController::class);
Route::resource('pelanggans', PelangganController::class);