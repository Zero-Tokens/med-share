<?php

namespace App\Models;

use App\Enums\ReservationStatus;
use Database\Factories\ReservationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $transfer_id
 * @property int $medication_request_item_id
 * @property int $transfer_offer_lot_id
 * @property ReservationStatus $status
 * @property string $quantity
 * @property string $quantity_collected
 * @property Carbon|null $expires_at
 * @property int|null $reserved_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'transfer_id',
    'medication_request_item_id',
    'transfer_offer_lot_id',
    'status',
    'quantity',
    'quantity_collected',
    'expires_at',
    'reserved_by',
])]
class Reservation extends Model
{
    /** @use HasFactory<ReservationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ReservationStatus::class,
            'quantity' => 'decimal:3',
            'quantity_collected' => 'decimal:3',
            'expires_at' => 'datetime',
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
     * @return BelongsTo<MedicationRequestItem, $this>
     */
    public function requestItem(): BelongsTo
    {
        return $this->belongsTo(MedicationRequestItem::class, 'medication_request_item_id');
    }

    /**
     * @return BelongsTo<TransferOfferLot, $this>
     */
    public function lot(): BelongsTo
    {
        return $this->belongsTo(TransferOfferLot::class, 'transfer_offer_lot_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reserver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reserved_by');
    }

    /**
     * @return HasOne<TransferItem, $this>
     */
    public function transferItem(): HasOne
    {
        return $this->hasOne(TransferItem::class);
    }
}
