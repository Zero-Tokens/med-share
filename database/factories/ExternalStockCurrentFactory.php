<?php

namespace Database\Factories;

use App\Enums\StockQualityStatus;
use App\Models\ExternalStockCurrent;
use App\Models\ExternalStockPosition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ExternalStockCurrent>
 */
class ExternalStockCurrentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * Tudo vem da posição de origem, para a projeção nascer coerente com o histórico.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'external_stock_position_id' => ExternalStockPosition::factory()->resolved(),
            'data_source_id' => fn (array $attributes) => $this->position($attributes)->data_source_id,
            'facility_id' => fn (array $attributes) => $this->position($attributes)->facility_id,
            'medication_id' => fn (array $attributes) => $this->position($attributes)->medication_id,
            'lot_number' => fn (array $attributes) => $this->position($attributes)->lot_number,
            'expires_on' => fn (array $attributes) => $this->position($attributes)->expires_on,
            'quantity' => fn (array $attributes) => $this->position($attributes)->quantity,
            'position_date' => fn (array $attributes) => $this->position($attributes)->position_date,
            'imported_at' => fn (array $attributes) => $this->position($attributes)->imported_at,
            'quality_status' => StockQualityStatus::Current,
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function position(array $attributes): ExternalStockPosition
    {
        return ExternalStockPosition::query()->findOrFail((int) $attributes['external_stock_position_id']);
    }
}
