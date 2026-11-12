<?php

namespace Database\Factories;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use App\Models\Municipality;
use App\Models\MunicipalityMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MunicipalityMembership>
 */
class MunicipalityMembershipFactory extends Factory
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
            'municipality_id' => Municipality::factory(),
            'role' => MembershipRole::MunicipalManager,
            'status' => MembershipStatus::Active,
            'approved_by' => null,
            'approved_at' => now(),
            'starts_at' => null,
            'ends_at' => null,
        ];
    }

    /**
     * Indicate that the membership still awaits approval.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => MembershipStatus::Pending,
            'approved_at' => null,
        ]);
    }
}
