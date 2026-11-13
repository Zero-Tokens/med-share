<?php

namespace Database\Factories;

use App\Models\DataSource;
use App\Models\ExternalStockPosition;
use App\Models\Facility;
use App\Models\Medication;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExternalStockPosition>
 */
class ExternalStockPositionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * Por padrão a posição vem "crua": CNES e código externo sem unidade nem
     * medicamento resolvidos, como chega da fonte.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'data_source_id' => DataSource::factory(),
            'integration_payload_id' => null,
            'facility_id' => null,
            'medication_id' => null,
            'cnes' => str_pad((string) fake()->numberBetween(1, 9999999), 7, '0', STR_PAD_LEFT),
            'external_medication_code' => fake()->numerify('BR0#######'),
            'external_medication_description' => fake()->sentence(4),
            'lot_number' => fake()->bothify('L##??##'),
            'expires_on' => now()->addYear()->toDateString(),
            'quantity' => '1000.000',
            'program_code' => null,
            'origin_system' => 'HORUS',
            'product_type' => null,
            'position_date' => now()->subDay()->toDateString(),
            'imported_at' => now(),
            'record_hash' => hash('sha256', fake()->unique()->uuid()),
            'raw' => null,
        ];
    }

    /**
     * Indicate that the facility and the medication were resolved.
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'facility_id' => Facility::factory(),
            'medication_id' => Medication::factory(),
        ]);
    }
}
