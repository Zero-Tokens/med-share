<?php

namespace App\Models;

use App\Enums\IntegrationPartitionStatus;
use Database\Factories\IntegrationPartitionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $integration_run_id
 * @property string $partition_key
 * @property array<string, mixed>|null $filters
 * @property IntegrationPartitionStatus $status
 * @property int $next_offset
 * @property int $pages_processed
 * @property int $records_valid
 * @property int $records_rejected
 * @property int $attempts
 * @property string|null $last_error
 * @property Carbon|null $started_at
 * @property Carbon|null $finished_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'integration_run_id',
    'partition_key',
    'filters',
    'status',
    'next_offset',
    'pages_processed',
    'records_valid',
    'records_rejected',
    'attempts',
    'last_error',
    'started_at',
    'finished_at',
])]
class IntegrationPartition extends Model
{
    /** @use HasFactory<IntegrationPartitionFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filters' => 'array',
            'status' => IntegrationPartitionStatus::class,
            'next_offset' => 'integer',
            'pages_processed' => 'integer',
            'records_valid' => 'integer',
            'records_rejected' => 'integer',
            'attempts' => 'integer',
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<IntegrationRun, $this>
     */
    public function run(): BelongsTo
    {
        return $this->belongsTo(IntegrationRun::class, 'integration_run_id');
    }

    /**
     * @return HasMany<IntegrationPayload, $this>
     */
    public function payloads(): HasMany
    {
        return $this->hasMany(IntegrationPayload::class);
    }
}
