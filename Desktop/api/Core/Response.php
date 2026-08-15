<?php

declare(strict_types=1);

namespace Core;

/**
 * Réponse JSON à renvoyer au client.
 * Succès :  {"data": …}          (200/201)
 * Vide   :  corps vide           (204)
 * Erreur :  {"error": "…", "code": <statut>, "errors": {champ: message}}  (4xx/5xx)
 */
class Response
{
    public function __construct(
        private readonly int $status,
        private readonly array $data = [],
        private readonly array $headers = [],
    ) {}

    public static function json(mixed $data, int $status = 200, array $headers = []): self
    {
        return new self($status, ['data' => $data], $headers);
    }

    public static function noContent(): self
    {
        return new self(204);
    }

    public static function error(string $message, int $status = 400, array $errors = []): self
    {
        $data = ['error' => $message, 'code' => $status];
        if ($errors !== []) {
            $data['errors'] = $errors;
        }
        return new self($status, $data);
    }

    public function status(): int
    {
        return $this->status;
    }

    /** @return array<string, mixed> */
    public function data(): array
    {
        return $this->data;
    }

    /** @return array<string, string> */
    public function headers(): array
    {
        return $this->headers;
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        if ($this->status === 204) {
            return;
        }

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
