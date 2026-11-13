<?php

namespace App\Models;

use App\Enums\OutboxEventStatus;
use Database\Factories\OutboxEventFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $event_type
 * @property string $aggregate_type
 * @property int $aggregate_id
 * @property array<string, mixed> $payload
 * @property OutboxEventStatus $status
 * @property int $attempts
 * @property Carbon $available_at
 * @property Carbon|null $published_at
 * @property string|null $last_error
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'event_type',
    'aggregate_type',
    'aggregate_id',
    'payload',
    'status',
    'attempts',
    'available_at',
    'published_at',
    'last_error',
])]
class OutboxEvent extends Model
{
    /** @use HasFactory<OutboxEventFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'aggregate_id' => 'integer',
            'payload' => 'array',
            'status' => OutboxEventStatus::class,
            'attempts' => 'integer',
            'available_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }
}
