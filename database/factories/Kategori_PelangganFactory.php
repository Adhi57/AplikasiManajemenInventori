<?php

namespace Database\Factories;

use App\Models\Kategori_Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

class Kategori_PelangganFactory extends Factory
{
    protected $model = Kategori_Pelanggan::class;

    public function definition(): array
    {
        return [
            'kategori_pelanggan' => $this->faker->randomElement(['Retail', 'Grosir', 'Biasa']),
            'jumlah_diskon' => $this->faker->randomFloat(2, 0, 50),
        ];
    }
}
