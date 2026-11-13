<?php

namespace Database\Factories;

use App\Models\IdempotencyKey;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<IdempotencyKey>
 */
class IdempotencyKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'actor_key' => 'user:1',
            'operation' => 'reservations.store',
            'idempotency_key' => fake()->unique()->uuid(),
            'content_hash' => hash('sha256', fake()->uuid()),
            'response_status' => null,
            'response_body' => null,
            'completed_at' => null,
        ];
    }
}
