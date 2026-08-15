<?php

declare(strict_types=1);

namespace Core;

use App\Models\User;

/**
 * Représente la requête HTTP entrante (méthode, chemin, paramètres, corps, en-têtes).
 * En API : le corps est attendu en JSON, l'authentification via en-tête Authorization.
 */
class Request
{
    private ?User $user = null;

    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $body,
        private readonly array $headers,
    ) {}

    public static function fromGlobals(): self
    {
        $method  = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $path    = self::computePath();
        $query   = $_GET ?? [];
        $headers = self::readHeaders();

        $contentType = $headers['content-type'] ?? '';
        $body = [];
        if (str_contains($contentType, 'application/json')) {
            $decoded = json_decode((string) file_get_contents('php://input'), true);
            if (is_array($decoded)) {
                $body = $decoded;
            }
        } else {
            $body = $_POST ?? [];
        }

        return new self($method, $path, $query, $body, $headers);
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->body[$key] ?? $default;
    }

    /** @return array<string, mixed> */
    public function all(): array
    {
        return $this->body;
    }

    public function header(string $name): ?string
    {
        return $this->headers[strtolower($name)] ?? null;
    }

    public function bearerToken(): ?string
    {
        $auth = $this->header('authorization') ?? '';
        if (preg_match('/^Bearer\s+(.+)$/i', $auth, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    public function user(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): void
    {
        $this->user = $user;
    }

    /**
     * Calcule le chemin de la requête en retirant le préfixe du script
     * (fonctionne sous Apache avec/sans réécriture et sous le serveur PHP intégré).
     */
    private static function computePath(): string
    {
        $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

        $script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');

        if ($script !== '' && $script !== '/' && str_starts_with($path, $script)) {
            $path = substr($path, strlen($script));
        } else {
            $base = rtrim(str_replace('\\', '/', dirname($script)), '/');
            if ($base !== '' && str_starts_with($path, $base . '/')) {
                $path = substr($path, strlen($base));
            }
        }

        return $path === '' ? '/' : $path;
    }

    /** @return array<string, string> */
    private static function readHeaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $name = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = (string) $value;
            }
        }
        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['content-type'] = (string) $_SERVER['CONTENT_TYPE'];
        }
        return $headers;
    }
}
