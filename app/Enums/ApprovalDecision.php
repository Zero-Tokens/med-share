<?php

namespace App\Enums;

/**
 * Decisão registrada em uma aprovação de transferência.
 */
enum ApprovalDecision: string
{
    case Approved = 'approved';
    case Rejected = 'rejected';
}
