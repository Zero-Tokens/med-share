<?php

namespace App\Enums;

/**
 * Catálogo externo de onde vem um código de medicamento.
 */
enum ExternalCodeSource: string
{
    case Catmat = 'catmat';
    case Obm = 'obm';
}
