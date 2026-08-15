<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\BookRepositoryInterface;
use App\Models\Book;
use PDO;

class BookRepository implements BookRepositoryInterface
{
    private const SELECT_WITH_CATEGORY = <<<'SQL'
        SELECT b.*, c.libelle AS categorie_libelle
        FROM books b
        LEFT JOIN categories c ON c.id = b.categorie_id
        SQL;

    public function __construct(private PDO $pdo) {}

    public function search(string $term, ?int $categoryId, int $limit, int $offset): array
    {
        [$where, $params] = $this->buildWhere($term, $categoryId);

        $stmt = $this->pdo->prepare(
            self::SELECT_WITH_CATEGORY . $where . ' ORDER BY b.titre LIMIT ? OFFSET ?'
        );
        $params[] = $limit;
        $params[] = $offset;
        $stmt->execute($params);

        return $this->hydrate($stmt->fetchAll());
    }

    public function searchCount(string $term, ?int $categoryId): int
    {
        [$where, $params] = $this->buildWhere($term, $categoryId);
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM books b' . $where);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?Book
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_CATEGORY . ' WHERE b.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Book::fromRow($row);
    }

    public function create(array $data): Book
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO books (isbn, titre, auteur, description, date_publication, quantite, categorie_id, couverture)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['isbn'],
            $data['titre'],
            $data['auteur'],
            $data['description'] ?? '',
            $data['date_publication'] ?? null,
            $data['quantite'],
            $data['categorie_id'] ?? null,
            $data['couverture'] ?? null,
        ]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->findById($id) ?? throw new \RuntimeException('Livre introuvable après création');
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->pdo->prepare(
            'UPDATE books SET isbn = ?, titre = ?, auteur = ?, description = ?, date_publication = ?, quantite = ?, categorie_id = ?, couverture = ? WHERE id = ?'
        );
        $stmt->execute([
            $data['isbn'],
            $data['titre'],
            $data['auteur'],
            $data['description'] ?? '',
            $data['date_publication'] ?? null,
            $data['quantite'],
            $data['categorie_id'] ?? null,
            $data['couverture'] ?? null,
            $id,
        ]);
    }

    public function delete(int $id): void
    {
        $this->pdo->prepare('DELETE FROM books WHERE id = ?')->execute([$id]);
    }

    public function decrementStock(int $id): void
    {
        $this->pdo->prepare('UPDATE books SET quantite = quantite - 1 WHERE id = ?')->execute([$id]);
    }

    public function incrementStock(int $id): void
    {
        $this->pdo->prepare('UPDATE books SET quantite = quantite + 1 WHERE id = ?')->execute([$id]);
    }

    /** @return array{string, list<mixed>} */
    private function buildWhere(string $term, ?int $categoryId): array
    {
        $where = ' WHERE 1 = 1';
        $params = [];

        if ($term !== '') {
            $where .= ' AND (b.titre LIKE ? OR b.auteur LIKE ? OR b.isbn LIKE ?)';
            $params = array_fill(0, 3, '%' . $term . '%');
        }

        if ($categoryId !== null) {
            $where .= ' AND b.categorie_id = ?';
            $params[] = $categoryId;
        }

        return [$where, $params];
    }

    /** @param array[] $rows */
    private function hydrate(array $rows): array
    {
        return array_map(Book::fromRow(...), $rows);
    }
}
