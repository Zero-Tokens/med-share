<?php

namespace App\Enums;

/**
 * Urgência de um item solicitado.
 */
enum RequestUrgency: string
{
    case Routine = 'routine';
    case Urgent = 'urgent';
    case Critical = 'critical';
}
