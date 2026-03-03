<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'namaSupplier' => substr($this->faker->company(), 0, 40),
            'alamatSupplier' => substr($this->faker->address(), 0, 100),
            'Kota' => substr($this->faker->city(), 0, 30),
            'noTelepon' => $this->faker->numerify('08##########'), // 12 digits
            'waktuPengiriman' => $this->faker->numberBetween(1, 14),
        ];
    }
}
