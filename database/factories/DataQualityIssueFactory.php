<?php

namespace Database\Factories;

use App\Enums\DataQualityIssueStatus;
use App\Enums\DataQualityIssueType;
use App\Enums\IssueSeverity;
use App\Models\DataQualityIssue;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataQualityIssue>
 */
class DataQualityIssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'data_source_id' => null,
            'integration_run_id' => null,
            'external_stock_position_id' => null,
            'facility_id' => null,
            'medication_id' => null,
            'type' => DataQualityIssueType::UnmappedMedication,
            'severity' => IssueSeverity::Warning,
            'status' => DataQualityIssueStatus::Open,
            'message' => 'Código CATMAT sem mapeamento confirmado.',
            'details' => null,
            'reviewed_by' => null,
            'reviewed_at' => null,
            'resolution' => null,
        ];
    }
}
