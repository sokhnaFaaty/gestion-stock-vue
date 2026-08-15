<?php

declare(strict_types=1);

namespace Exceptions;

/**
 * Données invalides (422) : les erreurs par champ sont renvoyées dans le corps JSON.
 */
class ValidationException extends ApiException
{
    /** @param array<string, string> $errors Messages par champ */
    public function __construct(
        public readonly array $errors,
        string $message = 'Données invalides.',
    ) {
        parent::__construct($message, 422);
    }
}
