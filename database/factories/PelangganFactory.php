<?php

namespace Database\Factories;

use App\Models\Pelanggan;
use App\Models\Kategori_Pelanggan;
use Illuminate\Database\Eloquent\Factories\Factory;

class PelangganFactory extends Factory
{
    protected $model = Pelanggan::class;

    public function definition(): array
    {
        return [
            'nama_pelanggan' => substr($this->faker->company(), 0, 40),
            'alamat' => $this->faker->address(),
            'NPWP' => $this->faker->numerify('################'), // 16 digits
            'PIC' => substr($this->faker->name(), 0, 40),
            'kategori_pelanggan_id' => Kategori_Pelanggan::factory(),
            'tipe_harga' => $this->faker->randomElement(['eceran', 'grosir', 'diskon']),
        ];
    }
}