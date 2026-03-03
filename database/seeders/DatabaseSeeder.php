<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // 1. Disable Foreign Key Checks to Truncate
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Truncate semua kecuali 'barangs' dan 'migrations'
        $tables = DB::getSchemaBuilder()->getTableListing();
        foreach ($tables as $table) {
            if (!in_array($table, ['barangs', 'migrations'])) {
                DB::table($table)->truncate();
            }
        }

        $this->command->info('Semua data tabel kecuali barangs telah dihapus/dikosongkan.');

        // 2. Kategori Barang: Makanan, Minuman, Bumbu (1, 2, 3)
        DB::table('kategori_barangs')->insert([
            ['kategori_barang_id' => 1, 'nama_kategori_barang' => 'Makanan', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_barang_id' => 2, 'nama_kategori_barang' => 'Minuman', 'created_at' => now(), 'updated_at' => now()],
            ['kategori_barang_id' => 3, 'nama_kategori_barang' => 'Bumbu', 'created_at' => now(), 'updated_at' => now()],
        ]);
        $this->command->info('Kategori Barang (Makanan, Minuman, Bumbu) berhasil disemai.');

        // 3. Kategori Pelanggan: Retail, Grosir, Biasa (Enum: Retail, Grosir, Biasa)
        DB::table('kategori_pelanggans')->insert([
            ['kategori_pelanggan_id' => 1, 'kategori_pelanggan' => 'Biasa', 'jumlah_diskon' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['kategori_pelanggan_id' => 2, 'kategori_pelanggan' => 'Grosir', 'jumlah_diskon' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['kategori_pelanggan_id' => 3, 'kategori_pelanggan' => 'Retail', 'jumlah_diskon' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);
        $this->command->info('Kategori Pelanggan (Biasa/Eceran, Grosir, Retail) berhasil disemai.');

        // 4. Seed Suppliers (Butuh 55 id_supplier sesuai data di tabel barangs)
        $suppliers = [];
        $companyPrefixes = ['PT', 'CV', 'UD', 'Toko', 'Grosir'];
        for ($i = 1; $i <= 55; $i++) {
            $suppliers[] = [
                'id_supplier' => $i,
                'namaSupplier' => substr($faker->randomElement($companyPrefixes) . ' ' . $faker->company, 0, 40),
                'alamatSupplier' => substr($faker->address, 0, 100),
                'Kota' => substr($faker->city, 0, 30),
                'noTelepon' => $faker->numerify('08##########'), // 12 karakter digit HP indo
                'waktuPengiriman' => $faker->numberBetween(1, 14),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('suppliers')->insert($suppliers);
        $this->command->info('55 Supplier (Indonesia) referensi referensi barangs berhasil disemai.');

        // 5. Seed Pelanggan 
        $pelanggans = [];
        for ($i = 1; $i <= 20; $i++) {
            $katId = $faker->numberBetween(1, 3);
            
            // Map kategori_id 1 (Biasa) -> eceran, 2 (Grosir) -> grosir, 3 (Retail) -> diskon
            $tipe = 'eceran';
            if ($katId == 2) $tipe = 'grosir';
            if ($katId == 3) $tipe = 'diskon';

            $pelanggans[] = [
                'nama_pelanggan' => substr($faker->company, 0, 40),
                'alamat' => substr($faker->address, 0, 255),
                'NPWP' => $faker->numerify('################'), // 16 digit
                'PIC' => substr($faker->name, 0, 40),
                'kategori_pelanggan_id' => $katId,
                'tipe_harga' => $tipe,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('pelanggans')->insert($pelanggans);
        $this->command->info('20 Data Pelanggan berhasil disemai.');

        // 6. Seed Default Admin User
        User::create([
            'nama_lengkap' => 'Administrator',
            'username' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('password'),
            'role' => 'Admin',
        ]);
        $this->command->info('User Admin berhasil dibuat.');

        // Re-enable Foreign Key Checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('SELESAI. Semua data master telah disesuaikan dengan data real indonesia.');
    }
}