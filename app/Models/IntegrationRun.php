<?php

namespace App\Models;

use App\Enums\IntegrationRunKind;
use App\Enums\IntegrationRunStatus;
use Database\Factories\IntegrationRunFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $data_source_id
 * @property int|null $data_source_version_id
 * @property IntegrationRunKind $kind
 * @property IntegrationRunStatus $status
 * @property array<string, mixed>|null $scope
 * @property int|null $requested_by
 * @property Carbon|null $started_at
 * @property Carbon|null $finished_at
 * @property int $pages_processed
 * @property int $records_valid
 * @property int $records_rejected
 * @property string|null $error_message
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'data_source_id',
    'data_source_version_id',
    'kind',
    'status',
    'scope',
    'requested_by',
    'started_at',
    'finished_at',
    'pages_processed',
    'records_valid',
    'records_rejected',
    'error_message',
])]
class IntegrationRun extends Model
{
    /** @use HasFactory<IntegrationRunFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kind' => IntegrationRunKind::class,
            'status' => IntegrationRunStatus::class,
            'scope' => 'array',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'pages_processed' => 'integer',
            'records_valid' => 'integer',
            'records_rejected' => 'integer',
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
     * @return BelongsTo<DataSourceVersion, $this>
     */
    public function version(): BelongsTo
    {
        return $this->belongsTo(DataSourceVersion::class, 'data_source_version_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * @return HasMany<IntegrationPartition, $this>
     */
    public function partitions(): HasMany
    {
        return $this->hasMany(IntegrationPartition::class);
    }

    /**
     * @return HasMany<DataQualityIssue, $this>
     */
    public function issues(): HasMany
    {
        return $this->hasMany(DataQualityIssue::class);
    }
}
