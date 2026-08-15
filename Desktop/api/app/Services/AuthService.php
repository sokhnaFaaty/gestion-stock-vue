<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\ApiTokenRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Exceptions\AuthException;

/**
 * Gère la connexion par email/mot de passe et la délivrance d'un token API.
 *
 * Le token est stocké sous forme d'empreinte SHA-256 (jamais en clair),
 * comme dans l'atelier 19.
 */
class AuthService
{
    public const DUREE_HEURES = 24;

    public function __construct(
        private UserRepositoryInterface $users,
        private ApiTokenRepositoryInterface $tokens,
    ) {}

    /** @return array{token: string, type: string, expires_at: string, user: User} */
    public function attempt(string $email, string $password): array
    {
        $user = $this->users->findByEmail($email);

        if ($user === null || !password_verify($password, $user->password)) {
            throw new AuthException('Identifiants incorrects.');
        }

        $token     = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', time() + self::DUREE_HEURES * 3600);

        $this->tokens->create($user->id, hash('sha256', $token), $expiresAt);

        return [
            'token'      => $token,
            'type'       => 'Bearer',
            'expires_at' => $expiresAt,
            'user'       => $user,
        ];
    }

    public function revoke(string $token): void
    {
        if ($token !== '') {
            $this->tokens->deleteByHash(hash('sha256', $token));
        }
    }
}
