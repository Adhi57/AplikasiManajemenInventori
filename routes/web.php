<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\barangController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');

Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


Route::middleware('auth')->group(function () {
    Route::resource('barangs', barangController::class);
    Route::resource('pelanggans', PelangganController::class);
    Route::resource('kategori_pelanggan', KategoriBarangController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('kategori_barang', KategoriBarangController::class);
    Route::resource('katalog_barang', KatalogController::class);

    // Rute Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
