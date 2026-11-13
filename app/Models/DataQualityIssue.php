<?php

namespace App\Models;

use App\Enums\DataQualityIssueStatus;
use App\Enums\DataQualityIssueType;
use App\Enums\IssueSeverity;
use Database\Factories\DataQualityIssueFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int|null $data_source_id
 * @property int|null $integration_run_id
 * @property int|null $external_stock_position_id
 * @property int|null $facility_id
 * @property int|null $medication_id
 * @property DataQualityIssueType $type
 * @property IssueSeverity $severity
 * @property DataQualityIssueStatus $status
 * @property string $message
 * @property array<string, mixed>|null $details
 * @property int|null $reviewed_by
 * @property Carbon|null $reviewed_at
 * @property string|null $resolution
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'data_source_id',
    'integration_run_id',
    'external_stock_position_id',
    'facility_id',
    'medication_id',
    'type',
    'severity',
    'status',
    'message',
    'details',
    'reviewed_by',
    'reviewed_at',
    'resolution',
])]
class DataQualityIssue extends Model
{
    /** @use HasFactory<DataQualityIssueFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => DataQualityIssueType::class,
            'severity' => IssueSeverity::class,
            'status' => DataQualityIssueStatus::class,
            'details' => 'array',
            'reviewed_at' => 'datetime',
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
     * @return BelongsTo<IntegrationRun, $this>
     */
    public function run(): BelongsTo
    {
        return $this->belongsTo(IntegrationRun::class, 'integration_run_id');
    }

    /**
     * @return BelongsTo<ExternalStockPosition, $this>
     */
    public function position(): BelongsTo
    {
        return $this->belongsTo(ExternalStockPosition::class, 'external_stock_position_id');
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
     * @return BelongsTo<User, $this>
     */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
