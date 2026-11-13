<?php

namespace Database\Factories;

use App\Enums\TransferEventType;
use App\Models\Transfer;
use App\Models\TransferEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferEvent>
 */
class TransferEventFactory extends Factory
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
            'type' => TransferEventType::Created,
            'actor_id' => User::factory(),
            'occurred_at' => now(),
            'payload' => null,
            'notes' => null,
        ];
    }
}
