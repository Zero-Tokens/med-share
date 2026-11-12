<?php

namespace App\Enums;

/**
 * Natureza de uma divergência apontada no recebimento.
 */
enum DiscrepancyType: string
{
    case Quantity = 'quantity';
    case Lot = 'lot';
    case Expiry = 'expiry';
    case Damage = 'damage';
    case Other = 'other';
}
