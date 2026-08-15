<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    /** @return Category[] */
    public function all(): array;

    public function findById(int $id): ?Category;
}
