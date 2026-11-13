<?php

namespace Database\Factories;

use App\Models\IntegrationPartition;
use App\Models\IntegrationPayload;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IntegrationPayload>
 */
class IntegrationPayloadFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $hash = hash('sha256', fake()->unique()->uuid());

        return [
            'integration_partition_id' => IntegrationPartition::factory(),
            'page_offset' => 0,
            'content_hash' => $hash,
            'storage_path' => "imports/{$hash}.json",
            'record_count' => 100,
            'http_status' => 200,
            'fetched_at' => now(),
        ];
    }
}
