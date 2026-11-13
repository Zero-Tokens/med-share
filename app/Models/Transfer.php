<?php

namespace App\Models;

use App\Enums\TransferStatus;
use Database\Factories\TransferFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $origin_facility_id
 * @property int $destination_facility_id
 * @property int|null $medication_request_id
 * @property TransferStatus $status
 * @property int|null $requested_by
 * @property Carbon|null $approved_at
 * @property Carbon|null $reserved_at
 * @property Carbon|null $collected_at
 * @property Carbon|null $received_at
 * @property Carbon|null $completed_at
 * @property Carbon|null $cancelled_at
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'origin_facility_id',
    'destination_facility_id',
    'medication_request_id',
    'status',
    'requested_by',
    'approved_at',
    'reserved_at',
    'collected_at',
    'received_at',
    'completed_at',
    'cancelled_at',
    'notes',
])]
class Transfer extends Model
{
    /** @use HasFactory<TransferFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => TransferStatus::class,
            'approved_at' => 'datetime',
            'reserved_at' => 'datetime',
            'collected_at' => 'datetime',
            'received_at' => 'datetime',
            'completed_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Facility, $this>
     */
    public function origin(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'origin_facility_id');
    }

    /**
     * @return BelongsTo<Facility, $this>
     */
    public function destination(): BelongsTo
    {
        return $this->belongsTo(Facility::class, 'destination_facility_id');
    }

    /**
     * @return BelongsTo<MedicationRequest, $this>
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(MedicationRequest::class, 'medication_request_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * @return HasMany<Reservation, $this>
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * @return HasMany<TransferItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(TransferItem::class);
    }

    /**
     * @return HasMany<TransferApproval, $this>
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(TransferApproval::class);
    }

    /**
     * @return HasMany<TransferEvent, $this>
     */
    public function events(): HasMany
    {
        return $this->hasMany(TransferEvent::class);
    }

    /**
     * @return HasMany<TransferDiscrepancy, $this>
     */
    public function discrepancies(): HasMany
    {
        return $this->hasMany(TransferDiscrepancy::class);
    }
}
