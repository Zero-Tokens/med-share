<?php

namespace App\Models;

use Database\Factories\DataSourceVersionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $data_source_id
 * @property string $version_key
 * @property Carbon $observed_at
 * @property array<string, mixed>|null $metadata
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['data_source_id', 'version_key', 'observed_at', 'metadata'])]
class DataSourceVersion extends Model
{
    /** @use HasFactory<DataSourceVersionFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'observed_at' => 'datetime',
            'metadata' => 'array',
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
     * @return HasMany<IntegrationRun, $this>
     */
    public function runs(): HasMany
    {
        return $this->hasMany(IntegrationRun::class);
    }
}
