<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\ApiTokenRepositoryInterface;
use PDO;

class ApiTokenRepository implements ApiTokenRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function create(int $userId, string $tokenHash, string $expiresAt): void
    {
        $this->pdo->prepare(
            'INSERT INTO api_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)'
        )->execute([$userId, $tokenHash, $expiresAt]);
    }

    public function findValidByHash(string $tokenHash): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT user_id FROM api_tokens WHERE token_hash = ? AND expires_at > NOW()'
        );
        $stmt->execute([$tokenHash]);
        $row = $stmt->fetch();
        return $row === false ? null : ['user_id' => (int) $row['user_id']];
    }

    public function deleteByHash(string $tokenHash): void
    {
        $this->pdo->prepare('DELETE FROM api_tokens WHERE token_hash = ?')->execute([$tokenHash]);
    }

    public function deleteExpired(): void
    {
        $this->pdo->exec('DELETE FROM api_tokens WHERE expires_at <= NOW()');
    }
}
