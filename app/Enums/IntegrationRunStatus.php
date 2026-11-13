<?php

namespace App\Enums;

/**
 * Situação de uma execução de importação. Uma execução incompleta
 * (`Failed`, `Cancelled`, `CompletedWithErrors`) nunca zera saldos.
 */
enum IntegrationRunStatus: string
{
    case Pending = 'pending';
    case Running = 'running';
    case Completed = 'completed';
    case CompletedWithErrors = 'completed_with_errors';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
}
