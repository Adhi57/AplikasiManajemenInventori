<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BarangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('barangs')->insert([
            [
                'kode_barang' => 'BRG001',
                'nama_barang' => 'Indomie Goreng',
                'harga_beli' => 4000,
                'kategori_barang_id' => 'KTG01',
                'id_supplier' => 1,
                'harga_barang_id' => 1,
            ],
            [
                'kode_barang' => 'BRG002',
                'nama_barang' => 'Aqua Botol 600ml',
                'harga_beli' => 3500,
                'kategori_barang_id' => 'KTG02',
                'id_supplier' => 2,
                'harga_barang_id' => 2,
            ],
        ]);
    }
}
