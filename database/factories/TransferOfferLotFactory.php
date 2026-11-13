<?php

namespace Database\Factories;

use App\Models\TransferOffer;
use App\Models\TransferOfferLot;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferOfferLot>
 */
class TransferOfferLotFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transfer_offer_id' => TransferOffer::factory(),
            'lot_number' => fake()->unique()->bothify('L##??##'),
            'expires_on' => now()->addYear()->toDateString(),
            'quantity_released' => '200.000',
            'quantity_reserved' => '0.000',
            'quantity_shipped' => '0.000',
            'is_blocked' => false,
        ];
    }
}
