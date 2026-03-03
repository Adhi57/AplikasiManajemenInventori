<?php

namespace Database\Factories;

use App\Models\Barang;
use App\Models\kategoriBarang;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class BarangFactory extends Factory
{
    protected $model = Barang::class;

    public function definition(): array
    {
        return [
            'kode_barang' => $this->faker->unique()->numerify('BRG-#####'),
            'nama_barang' => substr($this->faker->words(3, true), 0, 255),
            'satuan_jual' => $this->faker->randomElement(['pcs', 'renteng', 'pack', 'karton']),
            'kategori_barang_id' => kategoriBarang::factory(),
            'id_supplier' => Supplier::factory(),
            'jml_barang_per_karton' => $this->faker->numberBetween(10, 100),
            'foto_produk' => null,
            'tipe_harga_barang' => $this->faker->randomElement(['Eceran', 'Grosir', 'Diskon']),
            'harga_jual' => $this->faker->randomFloat(2, 5000, 100000),
            'harga_beli' => $this->faker->randomFloat(2, 1000, 50000),
            'berlaku_mulai' => $this->faker->dateTimeThisYear(),
            'berlaku_sampai' => clone $this->faker->dateTimeThisYear()->modify('+1 year'),
        ];
    }
}
