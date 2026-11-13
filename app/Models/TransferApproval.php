<?php

namespace App\Models;

use App\Enums\ApprovalDecision;
use App\Enums\MembershipRole;
use Database\Factories\TransferApprovalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $transfer_id
 * @property int $approved_by
 * @property MembershipRole $role
 * @property ApprovalDecision $decision
 * @property string $items_version
 * @property string|null $notes
 * @property Carbon $decided_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['transfer_id', 'approved_by', 'role', 'decision', 'items_version', 'notes', 'decided_at'])]
class TransferApproval extends Model
{
    /** @use HasFactory<TransferApprovalFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'role' => MembershipRole::class,
            'decision' => ApprovalDecision::class,
            'decided_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Transfer, $this>
     */
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
