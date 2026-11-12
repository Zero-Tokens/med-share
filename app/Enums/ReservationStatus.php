<?php

namespace App\Enums;

/**
 * Situação de uma reserva. Uma reserva coletada não pode expirar nem ser liberada.
 */
enum ReservationStatus: string
{
    case Active = 'active';
    case PartiallyCollected = 'partially_collected';
    case Collected = 'collected';
    case Released = 'released';
    case Expired = 'expired';
    case Cancelled = 'cancelled';
}
