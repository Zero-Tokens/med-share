<?php

namespace App\Enums;

/**
 * Perfil de um usuário no vínculo com um município ou uma unidade.
 *
 * Município: MunicipalManager, TransferApprover, Auditor.
 * Unidade: FacilityResponsible, TransferApprover, LogisticsOperator, Auditor.
 * A administração da plataforma não é vínculo: ver `users.is_platform_admin`.
 */
enum MembershipRole: string
{
    case MunicipalManager = 'municipal_manager';
    case FacilityResponsible = 'facility_responsible';
    case TransferApprover = 'transfer_approver';
    case LogisticsOperator = 'logistics_operator';
    case Auditor = 'auditor';
}
