<?php

namespace App\Enums;

/**
 * Situação de uma solicitação de medicamento.
 * `Fulfilled` só quando os itens atingem a quantidade recebida e aceita, nunca pela reserva.
 */
enum RequestStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case PartiallyFulfilled = 'partially_fulfilled';
    case Fulfilled = 'fulfilled';
    case Cancelled = 'cancelled';
    case ClosedUnfulfilled = 'closed_unfulfilled';
}
