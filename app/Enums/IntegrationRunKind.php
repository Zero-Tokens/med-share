<?php

namespace App\Enums;

/**
 * Tipo de execução de importação.
 */
enum IntegrationRunKind: string
{
    case Initial = 'initial';
    case Incremental = 'incremental';
    case Reprocess = 'reprocess';
}
