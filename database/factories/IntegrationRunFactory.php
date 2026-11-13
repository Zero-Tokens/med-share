<?php

namespace Database\Factories;

use App\Enums\IntegrationRunKind;
use App\Enums\IntegrationRunStatus;
use App\Models\DataSource;
use App\Models\IntegrationRun;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IntegrationRun>
 */
class IntegrationRunFactory extends Factory
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
            'data_source_version_id' => null,
            'kind' => IntegrationRunKind::Incremental,
            'status' => IntegrationRunStatus::Pending,
            'scope' => ['codigo_uf' => '25'],
            'requested_by' => null,
            'started_at' => null,
            'finished_at' => null,
            'error_message' => null,
        ];
    }
}
