<?php

namespace Database\Factories;

use App\Models\DataSource;
use App\Models\DataSourceVersion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataSourceVersion>
 */
class DataSourceVersionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'data_source_id' => DataSource::factory(),
            'version_key' => fake()->unique()->uuid(),
            'observed_at' => now(),
            'metadata' => null,
        ];
    }
}
