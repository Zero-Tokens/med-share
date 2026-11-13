<?php

namespace App\Models;

use Database\Factories\DataSourceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $slug
 * @property string $name
 * @property string $base_url
 * @property string|null $resource_url
 * @property bool $is_enabled
 * @property array<string, mixed>|null $settings
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable(['slug', 'name', 'base_url', 'resource_url', 'is_enabled', 'settings'])]
class DataSource extends Model
{
    /** @use HasFactory<DataSourceFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'settings' => 'array',
        ];
    }

    /**
     * @return HasMany<DataSourceVersion, $this>
     */
    public function versions(): HasMany
    {
        return $this->hasMany(DataSourceVersion::class);
    }

    /**
     * @return HasMany<IntegrationRun, $this>
     */
    public function runs(): HasMany
    {
        return $this->hasMany(IntegrationRun::class);
    }
}
