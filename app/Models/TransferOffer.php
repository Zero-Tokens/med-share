<?php

namespace App\Models;

use App\Enums\OfferStatus;
use Database\Factories\TransferOfferFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $facility_id
 * @property int $medication_id
 * @property OfferStatus $status
 * @property int|null $confirmed_by
 * @property Carbon|null $confirmed_at
 * @property Carbon|null $expires_at
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['facility_id', 'medication_id', 'status', 'confirmed_by', 'confirmed_at', 'expires_at', 'notes'])]
class TransferOffer extends Model
{
    /** @use HasFactory<TransferOfferFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OfferStatus::class,
            'confirmed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Unidade de origem, que libera a quantidade.
     *
     * @return BelongsTo<Facility, $this>
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * @return BelongsTo<Medication, $this>
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function confirmer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    /**
     * @return HasMany<TransferOfferLot, $this>
     */
    public function lots(): HasMany
    {
        return $this->hasMany(TransferOfferLot::class);
    }
}
