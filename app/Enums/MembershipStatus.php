<?php

namespace App\Enums;

/**
 * Situação de um vínculo. Só `Active` concede acesso operacional.
 */
enum MembershipStatus: string
{
    case Pending = 'pending';
    case Active = 'active';
    case Suspended = 'suspended';
    case Revoked = 'revoked';
}
