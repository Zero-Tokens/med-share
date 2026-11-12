<?php

namespace App\Enums;

/**
 * Situação do tratamento de uma divergência.
 */
enum DiscrepancyStatus: string
{
    case Open = 'open';
    case Reconciled = 'reconciled';
    case Dismissed = 'dismissed';
}
