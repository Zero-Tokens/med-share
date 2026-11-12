<?php

namespace Database\Factories;

use App\Enums\ApprovalDecision;
use App\Enums\MembershipRole;
use App\Models\Transfer;
use App\Models\TransferApproval;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransferApproval>
 */
class TransferApprovalFactory extends Factory
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
            'approved_by' => User::factory(),
            'role' => MembershipRole::TransferApprover,
            'decision' => ApprovalDecision::Approved,
            'items_version' => hash('sha256', fake()->uuid()),
            'notes' => null,
            'decided_at' => now(),
        ];
    }
}
