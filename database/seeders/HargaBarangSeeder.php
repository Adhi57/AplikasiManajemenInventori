<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HargaBarangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('harga_barangs')->insert([
            [
                'harga_barang_id' => 1,
                'kode_barang' => 'BRG001',
                'tipe_harga_barang' => 'Eceran',
                'harga_jual' => 4500,
                'berlaku_mulai' => now(),
                'berlaku_sampai' => null,
            ],
            [
                'harga_barang_id' => 2,
                'kode_barang' => 'BRG002',
                'tipe_harga_barang' => 'Eceran',
                'harga_jual' => 4000,
                'berlaku_mulai' => now(),
                'berlaku_sampai' => null,
            ],
        ]);
    }
}
