<?php

namespace Database\Factories;

use App\Enums\DiscrepancyStatus;
use App\Enums\DiscrepancyType;
use App\Models\Transfer;
use App\Models\TransferDiscrepancy;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferDiscrepancy>
 */
class TransferDiscrepancyFactory extends Factory
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
            'transfer_item_id' => null,
            'type' => DiscrepancyType::Quantity,
            'status' => DiscrepancyStatus::Open,
            'quantity_expected' => '80.000',
            'quantity_found' => '78.000',
            'description' => 'Caixa chegou com duas cartelas a menos.',
            'reported_by' => User::factory(),
            'reconciled_by' => null,
            'reconciled_at' => null,
            'resolution' => null,
        ];
    }
}
