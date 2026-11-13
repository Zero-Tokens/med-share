<?php

namespace App\Enums;

/**
 * Situação da revisão de um mapeamento de código externo.
 * Só `Confirmed` pode ser usado para compatibilidade entre medicamentos.
 */
enum MedicationMappingStatus: string
{
    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Rejected = 'rejected';
}
