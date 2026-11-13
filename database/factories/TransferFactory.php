<?php

namespace Database\Factories;

use App\Enums\TransferStatus;
use App\Models\Facility;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transfer>
 */
class TransferFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'origin_facility_id' => Facility::factory(),
            'destination_facility_id' => Facility::factory(),
            'medication_request_id' => null,
            'status' => TransferStatus::PendingApproval,
            'requested_by' => User::factory(),
            'notes' => null,
        ];
    }
}
