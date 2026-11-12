<?php

namespace Database\Factories;

use App\Enums\ReservationStatus;
use App\Models\MedicationRequestItem;
use App\Models\Reservation;
use App\Models\Transfer;
use App\Models\TransferOfferLot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transfer_id' => Transfer::factory(),
            'medication_request_item_id' => MedicationRequestItem::factory(),
            'transfer_offer_lot_id' => TransferOfferLot::factory(),
            'status' => ReservationStatus::Active,
            'quantity' => '80.000',
            'quantity_collected' => '0.000',
            'expires_at' => now()->addDays(2),
            'reserved_by' => User::factory(),
        ];
    }
}
