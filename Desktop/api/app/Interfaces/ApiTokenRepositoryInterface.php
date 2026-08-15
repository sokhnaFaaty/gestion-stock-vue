<?php

declare(strict_types=1);

namespace App\Interfaces;

interface ApiTokenRepositoryInterface
{
    public function create(int $userId, string $tokenHash, string $expiresAt): void;

    /** @return array{user_id: int}|null */
    public function findValidByHash(string $tokenHash): ?array;

    public function deleteByHash(string $tokenHash): void;

    public function deleteExpired(): void;
}
