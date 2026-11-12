<?php

namespace App\Models;

use App\Enums\ExternalCodeSource;
use App\Enums\MedicationMappingStatus;
use Database\Factories\MedicationMappingFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $medication_id
 * @property ExternalCodeSource $source
 * @property string $external_code
 * @property string|null $external_description
 * @property MedicationMappingStatus $status
 * @property int|null $reviewed_by
 * @property Carbon|null $reviewed_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'medication_id',
    'source',
    'external_code',
    'external_description',
    'status',
    'reviewed_by',
    'reviewed_at',
])]
class MedicationMapping extends Model
{
    /** @use HasFactory<MedicationMappingFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'source' => ExternalCodeSource::class,
            'status' => MedicationMappingStatus::class,
            'reviewed_at' => 'datetime',
        ];
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
