<?php

namespace App\Models;

use Database\Factories\MedicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $name
 * @property string|null $active_ingredient
 * @property string|null $strength
 * @property string|null $pharmaceutical_form
 * @property string|null $presentation
 * @property string $supply_unit
 * @property int $quantity_precision
 * @property bool $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'name',
    'active_ingredient',
    'strength',
    'pharmaceutical_form',
    'presentation',
    'supply_unit',
    'quantity_precision',
    'is_active',
])]
class Medication extends Model
{
    /** @use HasFactory<MedicationFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'quantity_precision' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return HasMany<MedicationMapping, $this>
     */
    public function mappings(): HasMany
    {
        return $this->hasMany(MedicationMapping::class);
    }
}
