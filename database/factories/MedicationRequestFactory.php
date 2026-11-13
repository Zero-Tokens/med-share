<?php

namespace Database\Factories;

use App\Enums\RequestStatus;
use App\Models\Facility;
use App\Models\MedicationRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedicationRequest>
 */
class MedicationRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'facility_id' => Facility::factory(),
            'requested_by' => User::factory(),
            'status' => RequestStatus::Open,
            'notes' => null,
            'opened_at' => now(),
            'closed_at' => null,
        ];
    }
}
