<?php

namespace App\Models;

use Database\Factories\ExternalStockPositionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $data_source_id
 * @property int|null $integration_payload_id
 * @property int|null $facility_id
 * @property int|null $medication_id
 * @property string $cnes
 * @property string $external_medication_code
 * @property string|null $external_medication_description
 * @property string|null $lot_number
 * @property Carbon|null $expires_on
 * @property numeric-string $quantity
 * @property string|null $program_code
 * @property string|null $origin_system
 * @property string|null $product_type
 * @property Carbon $position_date
 * @property Carbon $imported_at
 * @property string $record_hash
 * @property array<string, mixed>|null $raw
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'data_source_id',
    'integration_payload_id',
    'facility_id',
    'medication_id',
    'cnes',
    'external_medication_code',
    'external_medication_description',
    'lot_number',
    'expires_on',
    'quantity',
    'program_code',
    'origin_system',
    'product_type',
    'position_date',
    'imported_at',
    'record_hash',
    'raw',
])]
class ExternalStockPosition extends Model
{
    /** @use HasFactory<ExternalStockPositionFactory> */
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
            'quantity' => 'decimal:3',
            'position_date' => 'date',
            'imported_at' => 'datetime',
            'raw' => 'array',
        ];
    }

    /**
     * @return BelongsTo<DataSource, $this>
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(DataSource::class, 'data_source_id');
    }

    /**
     * @return BelongsTo<IntegrationPayload, $this>
     */
    public function payload(): BelongsTo
    {
        return $this->belongsTo(IntegrationPayload::class, 'integration_payload_id');
    }

    /**
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
     * @return HasOne<ExternalStockCurrent, $this>
     */
    public function current(): HasOne
    {
        return $this->hasOne(ExternalStockCurrent::class);
    }
}
