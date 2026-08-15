<?php

declare(strict_types=1);

namespace Exceptions;

/**
 * Règle métier violée (conflit) : stock épuisé, emprunt déjà en cours…
 */
class BusinessException extends ApiException
{
    public function __construct(string $message, int $status = 409)
    {
        parent::__construct($message, $status);
    }
}
