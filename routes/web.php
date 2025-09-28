<?php

use Illuminate\Support\Facades\Route;

Route::get('/dashboard', function () {
    return view('dashboard');
});

Route::get('/master_dataBarang', function () {
    return view('master_dataBarang');
});

Route::get('/master_dataSupplier', function () {
    return view('master_dataSupplier');
});