<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SupplierController;

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/master_dataBarang', function () {
    return view('master_dataBarang');
});

Route::get('/suppliers', function () {
    return view('suppliers.index');
});

Route::get('/verifBarang', function () {
    return view('verifBarang.index');
});

Route::resource('suppliers', SupplierController::class);