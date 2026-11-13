<?php

namespace App\Enums;

/**
 * Gravidade de uma ocorrência de qualidade.
 */
enum IssueSeverity: string
{
    case Warning = 'warning';
    case Error = 'error';
}
