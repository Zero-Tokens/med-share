<?php

namespace Database\Factories;

use App\Enums\ExternalCodeSource;
use App\Enums\MedicationMappingStatus;
use App\Models\Medication;
use App\Models\MedicationMapping;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicationMapping>
 */
class MedicationMappingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medication_id' => Medication::factory(),
            'source' => ExternalCodeSource::Catmat,
            'external_code' => fake()->unique()->numerify('BR0#######'),
            'external_description' => fake()->sentence(4),
            'status' => MedicationMappingStatus::Pending,
            'reviewed_by' => null,
            'reviewed_at' => null,
        ];
    }

    /**
     * Indicate that the mapping was reviewed and confirmed.
     */
    public function confirmed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MedicationMappingStatus::Confirmed,
            'reviewed_by' => User::factory(),
            'reviewed_at' => now(),
        ]);
    }
}
