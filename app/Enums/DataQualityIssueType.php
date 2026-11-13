<?php

namespace App\Enums;

/**
 * Natureza de uma ocorrência de qualidade dos dados importados.
 */
enum DataQualityIssueType: string
{
    case ConflictingValues = 'conflicting_values';
    case UnknownFacility = 'unknown_facility';
    case UnmappedMedication = 'unmapped_medication';
    case AmbiguousIdentity = 'ambiguous_identity';
    case InvalidRecord = 'invalid_record';
    case ContractChange = 'contract_change';
    case StalePosition = 'stale_position';
}
