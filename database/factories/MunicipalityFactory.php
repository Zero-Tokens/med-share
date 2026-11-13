<?php

namespace Database\Factories;

use App\Models\Municipality;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Municipality>
 */
class MunicipalityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ibge_code' => str_pad((string) fake()->unique()->numberBetween(1, 9999999), 7, '0', STR_PAD_LEFT),
            'uf_code' => '25',
            'uf' => 'PB',
            'name' => fake()->city(),
            'latitude' => fake()->latitude(-8.3, -6.0),
            'longitude' => fake()->longitude(-38.8, -34.8),
        ];
    }
}
