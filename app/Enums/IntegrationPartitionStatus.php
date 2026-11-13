<?php

namespace App\Enums;

/**
 * Situação de uma partição (recorte) de uma execução de importação.
 */
enum IntegrationPartitionStatus: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case Failed = 'failed';
}
