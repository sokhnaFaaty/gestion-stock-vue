<?php

declare(strict_types=1);

namespace Exceptions;

/**
 * Ressource introuvable (404).
 */
class NotFoundException extends ApiException
{
    public function __construct(string $message = 'Ressource introuvable.')
    {
        parent::__construct($message, 404);
    }
}
