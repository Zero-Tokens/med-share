<?php

namespace Database\Factories;

use App\Enums\OfferStatus;
use App\Models\Facility;
use App\Models\Medication;
use App\Models\TransferOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferOffer>
 */
class TransferOfferFactory extends Factory
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
            'medication_id' => Medication::factory(),
            'status' => OfferStatus::Active,
            'confirmed_by' => User::factory(),
            'confirmed_at' => now(),
            'expires_at' => now()->addDays(30),
            'notes' => null,
        ];
    }

    /**
     * Indicate that the offer still awaits confirmation.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => OfferStatus::Draft,
            'confirmed_by' => null,
            'confirmed_at' => null,
            'expires_at' => null,
        ]);
    }
}
