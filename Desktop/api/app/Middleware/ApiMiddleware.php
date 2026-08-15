<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Interfaces\ApiTokenRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use Core\Request;
use Exceptions\AuthException;

/**
 * Middlewares de l'API :
 *  - auth(Request)     : vérifie le jeton Bearer et charge l'utilisateur.
 *  - role(Request, …)  : restreint à certains rôles ('role:Admin,Bibliothecaire').
 */
class ApiMiddleware
{
    public function __construct(
        private ApiTokenRepositoryInterface $tokens,
        private UserRepositoryInterface $users,
    ) {}

    public function auth(Request $request): void
    {
        $token = $request->bearerToken();

        if ($token === null) {
            throw new AuthException('Authentification requise : en-tête Authorization: Bearer <token>.');
        }

        $entry = $this->tokens->findValidByHash(hash('sha256', $token));

        if ($entry === null) {
            throw new AuthException('Token invalide ou expiré.');
        }

        $user = $this->users->findById($entry['user_id']);

        if ($user === null) {
            throw new AuthException('Utilisateur introuvable.');
        }

        $request->setUser($user);
    }

    public function role(Request $request, string $roles): void
    {
        $user = $request->user();
        $allowed = array_map('trim', explode(',', $roles));

        if ($user === null || !in_array($user->role, $allowed, true)) {
            throw new AuthException('Accès refusé : droits insuffisants.', 403);
        }
    }
}
