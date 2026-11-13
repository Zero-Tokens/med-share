<?php

namespace Database\Factories;

use App\Enums\OutboxEventStatus;
use App\Models\OutboxEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OutboxEvent>
 */
class OutboxEventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'event_type' => 'transfer.approved',
            'aggregate_type' => 'transfer',
            'aggregate_id' => 1,
            'payload' => ['transfer_id' => 1],
            'status' => OutboxEventStatus::Pending,
            'attempts' => 0,
            'available_at' => now(),
            'published_at' => null,
            'last_error' => null,
        ];
    }
}
