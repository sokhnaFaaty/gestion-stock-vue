<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Loan;

interface BorrowRepositoryInterface
{
    public function create(int $userId, int $bookId): Loan;

    public function findById(int $id): ?Loan;

    public function countActiveByUser(int $userId): int;

    public function hasActiveForUserAndBook(int $userId, int $bookId): bool;

    public function markReturned(int $id): void;

    /** @return Loan[] */
    public function byUser(int $userId): array;

    /** @return Loan[] */
    public function all(): array;
}
