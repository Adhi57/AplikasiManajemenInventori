<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StokBarangSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('stok_barangs')->insert([
            [
                'stok_id' => 1,
                'kode_barang' => 'BRG001',
                'jumlah_stok' => 100,
                'jumlah_stok_rusak' => 2,
                'nama_satuan' => 'pcs',
                'updated_at' => Carbon::now(),
            ],
            [
                'stok_id' => 2,
                'kode_barang' => 'BRG002',
                'jumlah_stok' => 200,
                'jumlah_stok_rusak' => 5,
                'nama_satuan' => 'pcs',
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
