<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KategoriBarangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('kategori_barangs')->insert([
            ['kategori_barang_id' => 'KTG01', 'nama_kategori_barang' => 'Makanan'],
            ['kategori_barang_id' => 'KTG02', 'nama_kategori_barang' => 'Minuman'],
        ]);
    }
}
