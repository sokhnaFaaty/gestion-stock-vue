<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use PDO;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private PDO $pdo) {}

    public function all(): array
    {
        $stmt = $this->pdo->query(
            'SELECT c.*, (SELECT COUNT(*) FROM books b WHERE b.categorie_id = c.id) AS nb_livres
             FROM categories c
             ORDER BY c.libelle'
        );
        return array_map(Category::fromRow(...), $stmt->fetchAll());
    }

    public function findById(int $id): ?Category
    {
        $stmt = $this->pdo->prepare(
            'SELECT c.*, (SELECT COUNT(*) FROM books b WHERE b.categorie_id = c.id) AS nb_livres
             FROM categories c WHERE c.id = ?'
        );
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Category::fromRow($row);
    }
}
