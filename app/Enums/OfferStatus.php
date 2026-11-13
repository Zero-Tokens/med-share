<?php

namespace App\Enums;

/**
 * Situação de uma oferta de transferência. Só `Active` admite novas reservas.
 */
enum OfferStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Paused = 'paused';
    case Exhausted = 'exhausted';
    case Expired = 'expired';
    case Closed = 'closed';
}
