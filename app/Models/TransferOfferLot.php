<?php

namespace App\Models;

use Database\Factories\TransferOfferLotFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $transfer_offer_id
 * @property string $lot_number
 * @property Carbon $expires_on
 * @property numeric-string $quantity_released
 * @property numeric-string $quantity_reserved
 * @property numeric-string $quantity_shipped
 * @property bool $is_blocked
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'transfer_offer_id',
    'lot_number',
    'expires_on',
    'quantity_released',
    'quantity_reserved',
    'quantity_shipped',
    'is_blocked',
])]
class TransferOfferLot extends Model
{
    /** @use HasFactory<TransferOfferLotFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_on' => 'date',
            'quantity_released' => 'decimal:3',
            'quantity_reserved' => 'decimal:3',
            'quantity_shipped' => 'decimal:3',
            'is_blocked' => 'boolean',
        ];
    }

    /**
     * Saldo ofertável: liberada - reservada - expedida, em decimal exato.
     */
    public function availableQuantity(): string
    {
        return bcsub(bcsub($this->quantity_released, $this->quantity_reserved, 3), $this->quantity_shipped, 3);
    }

    /**
     * @return BelongsTo<TransferOffer, $this>
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(TransferOffer::class, 'transfer_offer_id');
    }

    /**
     * @return HasMany<Reservation, $this>
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
