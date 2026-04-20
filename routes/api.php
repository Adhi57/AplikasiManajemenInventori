<?php

use App\Http\Controllers\Api\BarangApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — Aplikasi Manajemen Inventori
|--------------------------------------------------------------------------
| Semua route di sini dilindungi oleh API Key.
| Sertakan header: X-API-KEY: <your-key>
| Atau query string: ?api_key=<your-key>
|
| Base URL: http://127.0.0.1:8000/api/v1/...
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

});
