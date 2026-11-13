<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\TransferOffer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuditLog>
 */
class AuditLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'action' => 'offer.confirmed',
            'auditable_type' => TransferOffer::class,
            'auditable_id' => 1,
            'municipality_id' => null,
            'facility_id' => null,
            'justification' => null,
            'changes' => null,
            'ip_address' => fake()->ipv4(),
            'occurred_at' => now(),
        ];
    }
}
