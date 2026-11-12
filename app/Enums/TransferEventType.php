<?php

namespace App\Enums;

/**
 * Tipo de evento operacional de uma transferência. A sequência de eventos
 * permite reconstruir o histórico completo.
 */
enum TransferEventType: string
{
    case Created = 'created';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Reserved = 'reserved';
    case ReadyForPickup = 'ready_for_pickup';
    case Collected = 'collected';
    case InTransit = 'in_transit';
    case Received = 'received';
    case Disputed = 'disputed';
    case Reconciled = 'reconciled';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Note = 'note';
}
