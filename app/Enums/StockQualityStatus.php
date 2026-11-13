<?php

namespace App\Enums;

/**
 * Situação de qualidade de uma posição de estoque na projeção atual.
 * "Sem informação" não é um status: é a ausência de linha, e nunca vira falta.
 */
enum StockQualityStatus: string
{
    case Current = 'current';
    case Stale = 'stale';
    case Inconsistent = 'inconsistent';
    case Quarantined = 'quarantined';
}
