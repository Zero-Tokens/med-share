<?php

namespace App\Models;

use App\Enums\RequestUrgency;
use Database\Factories\MedicationRequestItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $medication_request_id
 * @property int $medication_id
 * @property string $quantity_requested
 * @property string $quantity_committed
 * @property string $quantity_shipped
 * @property string $quantity_received
 * @property RequestUrgency $urgency
 * @property Carbon|null $needed_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'medication_request_id',
    'medication_id',
    'quantity_requested',
    'quantity_committed',
    'quantity_shipped',
    'quantity_received',
    'urgency',
    'needed_by',
])]
class MedicationRequestItem extends Model
{
    /** @use HasFactory<MedicationRequestItemFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity_requested' => 'decimal:3',
            'quantity_committed' => 'decimal:3',
            'quantity_shipped' => 'decimal:3',
            'quantity_received' => 'decimal:3',
            'urgency' => RequestUrgency::class,
            'needed_by' => 'date',
        ];
    }

    /**
     * @return BelongsTo<MedicationRequest, $this>
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(MedicationRequest::class, 'medication_request_id');
    }

    /**
     * @return BelongsTo<Medication, $this>
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class);
    }

    /**
     * @return HasMany<Reservation, $this>
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }
}
