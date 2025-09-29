<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VerifikasiBarangController extends Controller
{
    /**
     * Menampilkan halaman verifikasi barang (list PO saja).
     */
    public function index()
    {
        // --- SIMULASI DATA PO ---
        $po_letters = [
            (object)['id' => 1, 'number' => 'PO/2024/001', 'supplier' => 'PT Makmur Jaya'],
            (object)['id' => 2, 'number' => 'PO/2024/002', 'supplier' => 'CV Sejahtera Abadi'],
            (object)['id' => 3, 'number' => 'PO/2024/003', 'supplier' => 'PT Global Logistik'],
        ];
        // --- END SIMULASI ---

        return view('verifBarang.index', compact('po_letters'));
    }

    /**
     * Ambil daftar barang sesuai PO ID (API/AJAX).
     */
    public function getItemsByPO($poId)
    {
        // --- SIMULASI DATA (biasanya dari DB) ---
        $items = [
            1 => [
                (object)['id' => 632, 'name' => 'Kertas A4 80gsm', 'unit' => 'Dus', 'qty' => 15],
                (object)['id' => 633, 'name' => 'Tinta Printer Hitam', 'unit' => 'Pcs', 'qty' => 5],
            ],
            2 => [
                (object)['id' => 634, 'name' => 'Mouse Wireless Logitech', 'unit' => 'Pcs', 'qty' => 10],
                (object)['id' => 635, 'name' => 'Flashdisk 32GB Sandisk', 'unit' => 'Pcs', 'qty' => 20],
            ],
            3 => [
                (object)['id' => 636, 'name' => 'Kabel LAN Cat6', 'unit' => 'Roll', 'qty' => 3],
            ]
        ];
        // --- END SIMULASI ---

        // Jika ada data, kirim JSON, kalau tidak, array kosong
        return response()->json($items[$poId] ?? []);
    }
}
