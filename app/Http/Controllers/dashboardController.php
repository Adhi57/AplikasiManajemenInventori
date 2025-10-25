<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\View\View; 

class DashboardController extends Controller
{
    public function index(): View
    {
        $jumlahPelanggan = Pelanggan::count();

        return view('dashboard', compact('jumlahPelanggan'));
    }
}
