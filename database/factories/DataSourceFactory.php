<?php

namespace Database\Factories;

use App\Models\DataSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataSource>
 */
class DataSourceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'name' => 'Fonte '.fake()->company(),
            'base_url' => 'https://apidadosabertos.saude.gov.br',
            'resource_url' => null,
            'is_enabled' => true,
            'settings' => null,
        ];
    }
}
