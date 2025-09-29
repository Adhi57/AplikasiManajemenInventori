<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VerifikasiBarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pastikan User dan Supplier ada (asumsi IDs 1 dan 10)
        // Jika belum ada, Anda harus membuatnya terlebih dahulu.
        
        // 1. Tambah Data Barang (Barang)
        $barang1_id = 'BRG001';
        $barang2_id = 'BRG002';

        DB::table('Barang')->insertOrIgnore([
            ['kode_barang' => $barang1_id, 'nama_barang' => 'Kertas A4 70gsm', 'unit' => 'ream'],
            ['kode_barang' => $barang2_id, 'nama_barang' => 'Tinta Printer Hitam', 'unit' => 'pcs'],
        ]);


        // 2. Tambah Data Purchase Order (Purchase_Order)
        $po_id_verifikasi = 'PO-VERIF-001';
        $po_id_lain = 'PO-LAIN-002'; // Untuk testing filter

        DB::table('Purchase_Order')->insert([
            [
                'po_id' => $po_id_verifikasi,
                'user_id' => 1, // Ganti dengan ID user yang valid
                'tanggal_po' => now(),
                'supplier_id' => 1, // Ganti dengan ID supplier yang valid
                'status' => 'Disetujui', // Status yang akan dimuat di halaman verifikasi
                'total' => 200000.00,
            ],
            [
                'po_id' => $po_id_lain,
                'user_id' => 1, 
                'tanggal_po' => now(),
                'supplier_id' => 2, 
                'status' => 'Pending', // Tidak muncul di verifikasi
                'total' => 100000.00,
            ]
        ]);

        // 3. Tambah Detail Purchase Order (Purchase_Order_Detail)
        DB::table('Purchase_Order_Detail')->insert([
            // Item 1 (Pending - siap diverifikasi)
            [
                'detail_po_id' => Str::uuid(),
                'po_id' => $po_id_verifikasi,
                'kode_barang' => $barang1_id,
                'harga_barang_id' => 101,
                'quantity' => 50,
                'harga_satuan' => 3000.00,
                'satuan' => 'pcs',
                'subtotal' => 150000.00,
                'status_verifikasi' => 'Pending', 
            ],
            // Item 2 (Pending - siap diverifikasi)
            [
                'detail_po_id' => $detail_id_test = Str::uuid(), // Simpan ID ini untuk pengujian retur
                'po_id' => $po_id_verifikasi,
                'kode_barang' => $barang2_id,
                'harga_barang_id' => 102,
                'quantity' => 10,
                'harga_satuan' => 5000.00,
                'satuan' => 'pcs',
                'subtotal' => 50000.00,
                'status_verifikasi' => 'Pending', 
            ],
        ]);
    }
}
