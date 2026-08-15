<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Book;

interface BookRepositoryInterface
{
    /** @return Book[] */
    public function search(string $term, ?int $categoryId, int $limit, int $offset): array;

    public function searchCount(string $term, ?int $categoryId): int;

    public function findById(int $id): ?Book;

    /** @param array<string, mixed> $data */
    public function create(array $data): Book;

    /** @param array<string, mixed> $data */
    public function update(int $id, array $data): void;

    public function delete(int $id): void;

    public function decrementStock(int $id): void;

    public function incrementStock(int $id): void;
}
