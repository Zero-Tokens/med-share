<?php

namespace Database\Factories;

use App\Enums\RequestUrgency;
use App\Models\Medication;
use App\Models\MedicationRequest;
use App\Models\MedicationRequestItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicationRequestItem>
 */
class MedicationRequestItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'medication_request_id' => MedicationRequest::factory(),
            'medication_id' => Medication::factory(),
            'quantity_requested' => '100.000',
            'quantity_committed' => '0.000',
            'quantity_shipped' => '0.000',
            'quantity_received' => '0.000',
            'urgency' => RequestUrgency::Routine,
            'needed_by' => now()->addDays(7)->toDateString(),
        ];
    }
}
