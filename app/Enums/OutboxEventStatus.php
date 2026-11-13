<?php

namespace App\Enums;

/**
 * Situação de publicação de um evento do outbox.
 */
enum OutboxEventStatus: string
{
    case Pending = 'pending';
    case Published = 'published';
    case Failed = 'failed';
}
