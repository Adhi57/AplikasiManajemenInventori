<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\barangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PO_ApprovalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrdersController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\VerifikasiBarangController;
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

    // Approval PO
    Route::get('approval/approval_po', [PO_ApprovalController::class, 'index'])->name('approval.approval_po');
    Route::get('approval/show_po/{po_id}', [PO_ApprovalController::class, 'show'])
    ->name('approval.show_po')
    ->where('po_id', '.*');

    Route::put('approval/approve/{po_id}', [PO_ApprovalController::class, 'approve'])
    ->name('po.approve')
    ->where('po_id', '.*');

    Route::put('approval/reject/{po_id}', [PO_ApprovalController::class, 'reject'])
    ->name('po.reject')
    ->where('po_id', '.*');
    
    Route::get('/purchase_orders/buat_permintaan', [PurchaseOrdersController::class, 'create'])->name('purchase_orders.buat_permintaan');
    Route::get('/purchase-orders/fetch-barangs', [PurchaseOrdersController::class, 'fetchBarangs'])->name('purchase_orders.fetch_barangs'); // Untuk AJAX
    Route::post('/purchase_orders/buat_permintaan', [PurchaseOrdersController::class, 'store'])->name('purchase_orders.buat_permintaan');
    
    Route::get('verifBarang', [PurchaseOrdersController::class, 'index'])->name('verifBarang.index');
    Route::get('/verifBarang/get-items/{po_id}', [PurchaseOrdersController::class, 'getItems']);
    Route::post('/verifBarang/store', [BarangMasukController::class, 'store'])->name('verifBarang.store');    
    
    // Rute Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
