<?php

namespace App\Models;

use App\Enums\MembershipRole;
use App\Enums\MembershipStatus;
use Database\Factories\FacilityMembershipFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $user_id
 * @property int $facility_id
 * @property MembershipRole $role
 * @property MembershipStatus $status
 * @property int|null $approved_by
 * @property Carbon|null $approved_at
 * @property Carbon|null $starts_at
 * @property Carbon|null $ends_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'user_id',
    'facility_id',
    'role',
    'status',
    'approved_by',
    'approved_at',
    'starts_at',
    'ends_at',
])]
class FacilityMembership extends Model
{
    /** @use HasFactory<FacilityMembershipFactory> */
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
            'status' => MembershipStatus::class,
            'approved_at' => 'datetime',
            'starts_at' => 'date',
            'ends_at' => 'date',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Facility, $this>
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
