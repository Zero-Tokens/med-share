<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\TransferItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferItem>
 */
class TransferItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * Transferência, medicamento, lote e quantidade vêm da reserva, para o item
     * nascer coerente com o compromisso que ele executa.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'reservation_id' => Reservation::factory(),
            'transfer_id' => fn (array $attributes) => $this->reservation($attributes)->transfer_id,
            'medication_id' => fn (array $attributes) => $this->reservation($attributes)->requestItem?->medication_id,
            'transfer_offer_lot_id' => fn (array $attributes) => $this->reservation($attributes)->transfer_offer_lot_id,
            'quantity_planned' => fn (array $attributes) => $this->reservation($attributes)->quantity,
            'quantity_collected' => '0.000',
            'quantity_received' => '0.000',
            'quantity_accepted' => '0.000',
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function reservation(array $attributes): Reservation
    {
        return Reservation::query()->findOrFail((int) $attributes['reservation_id']);
    }
}
