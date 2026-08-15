<?php

declare(strict_types=1);

namespace Exceptions;

use RuntimeException;

/**
 * Exception de base de l'API : porte un code de statut HTTP.
 */
class ApiException extends RuntimeException
{
    public function __construct(
        string $message,
        public readonly int $status = 400,
    ) {
        parent::__construct($message);
    }
}
