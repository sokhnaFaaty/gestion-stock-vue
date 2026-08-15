<?php

declare(strict_types=1);

namespace Exceptions;

/**
 * Authentification requise ou refusée (401 / 403).
 */
class AuthException extends ApiException
{
    public function __construct(string $message, int $status = 401)
    {
        parent::__construct($message, $status);
    }
}
