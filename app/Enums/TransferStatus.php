<?php

namespace App\Enums;

/**
 * Situação logística de uma transferência, com transições explícitas nas ações.
 */
enum TransferStatus: string
{
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Reserved = 'reserved';
    case ReadyForPickup = 'ready_for_pickup';
    case PartiallyCollected = 'partially_collected';
    case Collected = 'collected';
    case InTransit = 'in_transit';
    case ReceivedPendingReview = 'received_pending_review';
    case Disputed = 'disputed';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
}
