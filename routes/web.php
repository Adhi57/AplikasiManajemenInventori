<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\barangController;
use App\Http\Controllers\BarangMasukController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\KategoriBarangController;
use App\Http\Controllers\KategoriPelangganController;
use App\Http\Controllers\LapBarangKeluarController;
use App\Http\Controllers\LaporanBarangMasukController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\PengirimanController;
use App\Http\Controllers\PO_ApprovalController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseOrdersController;
use App\Http\Controllers\SJ_ApprovalController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ReturBarangController;
use App\Http\Controllers\SuratJalanController;
use App\Http\Controllers\VerifikasiBarangController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StokBarangController;
use App\Http\Controllers\StokOpnameController;
use App\Http\Controllers\ReorderPointController;
use App\Http\Controllers\TrackingKadaluarsaController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BarcodeScannerController;

Route::get('/', function () {
    return view('auth.login');
});


Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');


Route::middleware('auth')->group(function () {
    // Barcode Scanner Lookup
    Route::get('/barcode/lookup', [BarcodeScannerController::class, 'lookup'])->name('barcode.lookup');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('barangs', barangController::class);
    Route::resource('pelanggans', PelangganController::class);
    Route::resource('kategori_pelanggan', KategoriPelangganController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('kategori_barang', KategoriBarangController::class);
    Route::resource('katalog_barang', KatalogController::class);
    
    // Stok Barang
    Route::get('/stok-barang', [StokBarangController::class, 'index'])->name('stok.index');
    
    // Reorder Point
    Route::get('/reorder-point', [ReorderPointController::class, 'index'])->name('reorder_point.index');
    
    //stok opname
    // Stock opname index & update
    Route::get('/stock_opname', [StokOpnameController::class, 'index'])->name('stock.opname.index');
    Route::post('/stock_opname/update', [StokOpnameController::class, 'update'])->name('stock.opname.update');
    Route::get('/stock_opname/riwayat', [StokOpnameController::class, 'riwayat'])->name('stock.opname.riwayat');
    
    
    
    Route::get('/tracking-kadaluarsa', [TrackingKadaluarsaController::class, 'index'])
    ->name('tracking_kadaluarsa.index');

    Route::get('/tracking-kadaluarsa/riwayat', [TrackingKadaluarsaController::class, 'riwayat'])
    ->name('tracking_kadaluarsa.riwayat');
    
    Route::get('/tracking-kadaluarsa/{kode_barang}', [TrackingKadaluarsaController::class, 'detail'])
    ->name('tracking_kadaluarsa.detail');
    
    Route::delete('/tracking-kadaluarsa/delete/{id}', [TrackingKadaluarsaController::class, 'destroy'])
    ->name('tracking_kadaluarsa.destroy');
    
    // Approval PO
    Route::get('approval/approval_po', [PO_ApprovalController::class, 'index'])->name('approval.approval_po');
    
    Route::get('/approval/po/{po_id}', [PO_ApprovalController::class, 'show'])
    ->name('approval.show_po');
    
    Route::put('/approval/po/{po_id}/approve', [PO_ApprovalController::class, 'approve'])
    ->name('po.approve');
    
    
    Route::put('/approval/po/{po_id}/reject', [PO_ApprovalController::class, 'reject'])
    ->name('po.reject');
    Route::get('/approval/po/{po_id}/print', [PO_ApprovalController::class, 'print_po'])
    ->name('approval.print_po');
    
    // Approval Surat Jalan
    Route::get('approval/approval_surat_jalan', [SJ_ApprovalController::class, 'index'])->name('approval.approval_surat_jalan');
    Route::get('approval/show_surat_jalan/{sj_id}', [SJ_ApprovalController::class, 'show'])
    ->name('approval.show_surat_jalan')
    ->where('sj_id', '.*');
    
    Route::put('approval/sj/approve/{sj_id}', [SJ_ApprovalController::class, 'approve'])
    ->name('sj.approve');
    Route::put('approval/sj/reject/{sj_id}', [SJ_ApprovalController::class, 'reject'])
    ->name('sj.reject');
    Route::get('approval/sj/{sj_id}/print', [SJ_ApprovalController::class, 'print_sj'])
    ->name('approval.print_sj')
    ->where('sj_id', '.*');
    
    // buat permintaan
    Route::get('/purchase_orders/buat_permintaan', [PurchaseOrdersController::class, 'create'])->name('purchase_orders.buat_permintaan');
    Route::get('/purchase-orders/fetch-barangs', [PurchaseOrdersController::class, 'fetchBarangs'])->name('purchase_orders.fetch_barangs'); // Untuk AJAX
    Route::post('/purchase_orders/buat_permintaan', [PurchaseOrdersController::class, 'store'])->name('purchase_orders.buat_permintaan');
    
    
    // Transaksi Penjualan / Buat Surat Jalan
    Route::get('/surat_jalan', [SuratJalanController::class, 'index'])->name('surat_jalan.index');
    Route::get('/surat_jalan/create', [SuratJalanController::class, 'create'])->name('surat_jalan.create');
    Route::delete('/surat-jalan/{sj_id}', [SuratJalanController::class, 'destroy'])
    ->name('surat_jalan.destroy');
    Route::post('/surat_jalan', [SuratJalanController::class, 'store'])->name('surat_jalan.store');
    Route::get('/surat-jalan/fetch-barangs', [SuratJalanController::class, 'fetchBarangs'])->name('surat_jalan.fetch-barangs');
    Route::get('surat_jalan/show/{po_id}', [SuratJalanController::class, 'show'])
    ->name('surat_jalan.show')
    ->where('sj_id', '.*');
    
    
    
    // Verif Barang
    Route::get('verifBarang', [PurchaseOrdersController::class, 'index'])->name('verifBarang.index');
    Route::get('/verifBarang/get-items/{po_id}', [PurchaseOrdersController::class, 'getItems']);
    Route::post('/verifBarang/store', [BarangMasukController::class, 'store'])->name('verifBarang.store');    
    
    // Retur Barang
    Route::get('/returBarang', [ReturBarangController::class, 'index'])->name('retur.index');
    Route::get('/returBarang/{id}', [ReturBarangController::class, 'show'])->name('retur.show');
    Route::patch('/retur-barang/{id}/alasan', [ReturBarangController::class, 'updateAlasan'])
    ->name('retur.updateAlasan');
    Route::patch('/retur-barang/{id}/update-tanggal', [ReturBarangController::class, 'updateTanggal'])
    ->name('retur.updateTanggal');
    
    Route::patch('/retur-barang/{retur_id}/konfirmasi', [ReturBarangController::class, 'konfirmasiSesuai'])
    ->name('retur.konfirmasi');
    Route::patch('/retur-barang/{retur_id}/batal', [ReturBarangController::class, 'batalkanRetur'])
    ->name('retur.batal');
    
    
    // Pengiriman Barang
    Route::resource('pengiriman', PengirimanController::class);
    Route::post('/update-status/{id}', [PengirimanController::class, 'updateStatus'])->name('pengiriman.updateStatus');
    
    // Lap Barang Masuk
    Route::get('/laporan/barang-masuk', [LaporanBarangMasukController::class, 'index'])->name('laporan.barang-masuk.index');
    Route::get('/laporan/barang-masuk/cetak', [LaporanBarangMasukController::class, 'cetak'])->name('laporan.barang-masuk.cetak');
    
    // Lap Barang Keluar
    Route::get('/laporan/barang-keluar', [LapBarangKeluarController::class, 'index'])
        ->name('laporan.barang_keluar.index');
    Route::get('/laporan/barang-keluar/cetak', [LapBarangKeluarController::class, 'cetak'])
        ->name('laporan.barang_keluar.cetak');
    

    // Pengaturan (Settings)
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan/profil-perusahaan', [SettingController::class, 'updateCompanyProfile'])->name('pengaturan.profil');
    Route::put('/pengaturan/umum', [SettingController::class, 'updateGeneralSettings'])->name('pengaturan.umum');

    // Pastikan ada ->name('profile.index') di akhir
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    
    // Untuk proses update
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    
});

Route::middleware(['auth', 'role:SuperAdmin'])->group(function () {
    // Route resource standar
    Route::resource('users', UserController::class);

    Route::patch('users/{user}/update-role', [UserController::class, 'updateRole'])->name('users.updateRole');
});

require __DIR__.'/auth.php';
