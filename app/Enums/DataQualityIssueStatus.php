<?php

namespace App\Enums;

/**
 * Situação do tratamento de uma ocorrência de qualidade.
 */
enum DataQualityIssueStatus: string
{
    case Open = 'open';
    case Reviewed = 'reviewed';
    case Dismissed = 'dismissed';
    case Fixed = 'fixed';
}
