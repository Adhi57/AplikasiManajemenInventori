<?php

use App\Http\Controllers\Api\BarangApiController;
use App\Http\Controllers\Api\SuratJalanApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Aplikasi Manajemen Inventori
|--------------------------------------------------------------------------
| Semua route di sini dilindungi oleh API Key.
| Sertakan header: X-API-KEY: <your-key>
|
| Base URL: https://<ngrok-url>/api/v1/...
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->middleware('api.key')->group(function () {

    // --- BARANG ---
    Route::get('/barang',        [BarangApiController::class, 'index']);   // Daftar barang
    Route::get('/barang/{kode}', [BarangApiController::class, 'show']);    // Detail + batch stok

    // --- STOK ---
    Route::get('/stok',          [BarangApiController::class, 'stok']);    // Rekap stok per barang

    // --- KATEGORI ---
    Route::get('/kategori',      [BarangApiController::class, 'kategori']); // Daftar kategori

    // --- SURAT JALAN ---
    Route::get('/surat-jalan',         [SuratJalanApiController::class, 'index']);  // Daftar surat jalan
    Route::get('/surat-jalan/{sj_id}', [SuratJalanApiController::class, 'show']);   // Detail surat jalan
    Route::post('/surat-jalan',        [SuratJalanApiController::class, 'store']);  // Buat surat jalan baru

    // --- PENGIRIMAN ---
    Route::get('/pengiriman',          [\App\Http\Controllers\Api\PengirimanApiController::class, 'index']);
    Route::get('/pengiriman/{sj_id}',  [\App\Http\Controllers\Api\PengirimanApiController::class, 'show']);
    Route::post('/pengiriman/{sj_id}/terkirim', [\App\Http\Controllers\Api\PengirimanApiController::class, 'markTerkirim']);

    // --- PELANGGAN ---
    Route::get('/pelanggan',           [SuratJalanApiController::class, 'pelanggan']); // Daftar pelanggan

});
