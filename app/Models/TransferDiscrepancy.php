<?php

namespace App\Models;

use App\Enums\DiscrepancyStatus;
use App\Enums\DiscrepancyType;
use Database\Factories\TransferDiscrepancyFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property int $transfer_id
 * @property int|null $transfer_item_id
 * @property DiscrepancyType $type
 * @property DiscrepancyStatus $status
 * @property string|null $quantity_expected
 * @property string|null $quantity_found
 * @property string $description
 * @property int|null $reported_by
 * @property int|null $reconciled_by
 * @property Carbon|null $reconciled_at
 * @property string|null $resolution
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
#[Fillable([
    'transfer_id',
    'transfer_item_id',
    'type',
    'status',
    'quantity_expected',
    'quantity_found',
    'description',
    'reported_by',
    'reconciled_by',
    'reconciled_at',
    'resolution',
])]
class TransferDiscrepancy extends Model
{
    /** @use HasFactory<TransferDiscrepancyFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => DiscrepancyType::class,
            'status' => DiscrepancyStatus::class,
            'quantity_expected' => 'decimal:3',
            'quantity_found' => 'decimal:3',
            'reconciled_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Transfer, $this>
     */
    public function transfer(): BelongsTo
    {
        return $this->belongsTo(Transfer::class);
    }

    /**
     * @return BelongsTo<TransferItem, $this>
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(TransferItem::class, 'transfer_item_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reconciler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reconciled_by');
    }
}
