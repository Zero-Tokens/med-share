<?php

namespace App\Models;

use Database\Factories\IntegrationPayloadFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $integration_partition_id
 * @property int $page_offset
 * @property string $content_hash
 * @property string $storage_path
 * @property int $record_count
 * @property int|null $http_status
 * @property Carbon $fetched_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'integration_partition_id',
    'page_offset',
    'content_hash',
    'storage_path',
    'record_count',
    'http_status',
    'fetched_at',
])]
class IntegrationPayload extends Model
{
    /** @use HasFactory<IntegrationPayloadFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'page_offset' => 'integer',
            'record_count' => 'integer',
            'http_status' => 'integer',
            'fetched_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<IntegrationPartition, $this>
     */
    public function partition(): BelongsTo
    {
        return $this->belongsTo(IntegrationPartition::class, 'integration_partition_id');
    }

    /**
     * @return HasMany<ExternalStockPosition, $this>
     */
    public function positions(): HasMany
    {
        return $this->hasMany(ExternalStockPosition::class);
    }
}
