<?php

namespace App\Models;

use App\Enums\StockQualityStatus;
use Database\Factories\ExternalStockCurrentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $external_stock_position_id
 * @property int $data_source_id
 * @property int $facility_id
 * @property int $medication_id
 * @property string|null $lot_number
 * @property Carbon|null $expires_on
 * @property numeric-string $quantity
 * @property Carbon $position_date
 * @property Carbon $imported_at
 * @property StockQualityStatus $quality_status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'external_stock_position_id',
    'data_source_id',
    'facility_id',
    'medication_id',
    'lot_number',
    'expires_on',
    'quantity',
    'position_date',
    'imported_at',
    'quality_status',
])]
class ExternalStockCurrent extends Model
{
    /** @use HasFactory<ExternalStockCurrentFactory> */
    use HasFactory;

    protected $table = 'external_stock_current';

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
            'quality_status' => StockQualityStatus::class,
        ];
    }

    /**
     * @return BelongsTo<ExternalStockPosition, $this>
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(ExternalStockPosition::class, 'external_stock_position_id');
    }

    /**
     * @return BelongsTo<DataSource, $this>
     */
    public function source(): BelongsTo
    {
        return $this->belongsTo(DataSource::class, 'data_source_id');
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
}
