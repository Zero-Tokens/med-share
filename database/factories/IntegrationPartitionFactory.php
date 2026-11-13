<?php

namespace Database\Factories;

use App\Enums\IntegrationPartitionStatus;
use App\Models\IntegrationPartition;
use App\Models\IntegrationRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IntegrationPartition>
 */
class IntegrationPartitionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $municipality = fake()->unique()->numerify('25#####');

        return [
            'integration_run_id' => IntegrationRun::factory(),
            'partition_key' => "uf=25;municipio={$municipality}",
            'filters' => ['codigo_uf' => '25', 'codigo_municipio' => $municipality],
            'status' => IntegrationPartitionStatus::Pending,
            'next_offset' => 0,
            'attempts' => 0,
            'last_error' => null,
            'started_at' => null,
            'finished_at' => null,
        ];
    }
}
