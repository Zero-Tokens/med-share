<?php

namespace App\Models;

use Database\Factories\TransferItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $transfer_id
 * @property int $reservation_id
 * @property int $medication_id
 * @property int $transfer_offer_lot_id
 * @property string $quantity_planned
 * @property string $quantity_collected
 * @property string $quantity_received
 * @property string $quantity_accepted
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'transfer_id',
    'reservation_id',
    'medication_id',
    'transfer_offer_lot_id',
    'quantity_planned',
    'quantity_collected',
    'quantity_received',
    'quantity_accepted',
])]
class TransferItem extends Model
{
    /** @use HasFactory<TransferItemFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity_planned' => 'decimal:3',
            'quantity_collected' => 'decimal:3',
            'quantity_received' => 'decimal:3',
            'quantity_accepted' => 'decimal:3',
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
     * @return BelongsTo<Reservation, $this>
     */
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    /**
     * @return BelongsTo<Medication, $this>
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * @return BelongsTo<TransferOfferLot, $this>
     */
    public function lot(): BelongsTo
    {
        return $this->belongsTo(TransferOfferLot::class, 'transfer_offer_lot_id');
    }

    /**
     * @return HasMany<TransferDiscrepancy, $this>
     */
    public function discrepancies(): HasMany
    {
        return $this->hasMany(TransferDiscrepancy::class);
    }
}
