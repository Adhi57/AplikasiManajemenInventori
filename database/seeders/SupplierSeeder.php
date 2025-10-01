<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('suppliers')->insert([
            [
                'id_supplier' => 1,
                'namaSupplier' => 'PT Indofood',
                'alamatSupplier' => 'Jl. Sudirman No. 1, Jakarta',
                'Kota' => 'Jakarta',
                'noTelepon' => '021123456',
                'waktuPengiriman' => 3, // dalam hari
            ],
            [
                'id_supplier' => 2,
                'namaSupplier' => 'PT Aqua Golden Mississippi',
                'alamatSupplier' => 'Jl. Industri No. 88, Bekasi',
                'Kota' => 'Bekasi',
                'noTelepon' => '021654321',
                'waktuPengiriman' => 2,
            ],
            [
                'id_supplier' => 3,
                'namaSupplier' => 'PT Sinar Sosro',
                'alamatSupplier' => 'Jl. Raya Cibitung No. 45, Cikarang',
                'Kota' => 'Bekasi',
                'noTelepon' => '021998877',
                'waktuPengiriman' => 4,
            ],
        ]);
    }
}
