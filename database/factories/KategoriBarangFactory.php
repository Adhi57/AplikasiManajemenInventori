<?php

namespace Database\Factories;

use App\Models\kategoriBarang;
use Illuminate\Database\Eloquent\Factories\Factory;

class KategoriBarangFactory extends Factory
{
    protected $model = kategoriBarang::class;

    public function definition(): array
    {
        return [
            'nama_kategori_barang' => $this->faker->unique()->words(2, true),
        ];
    }
}
