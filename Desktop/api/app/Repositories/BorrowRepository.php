<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\BorrowRepositoryInterface;
use App\Models\Loan;
use PDO;

class BorrowRepository implements BorrowRepositoryInterface
{
    private const SELECT = <<<'SQL'
        SELECT bo.*,
               b.titre AS book_titre,
               b.isbn  AS book_isbn,
               u.nom   AS user_nom,
               u.prenom AS user_prenom
        FROM borrows bo
        JOIN books b  ON b.id  = bo.book_id
        JOIN users u  ON u.id  = bo.user_id
        SQL;

    public function __construct(private PDO $pdo) {}

    public function create(int $userId, int $bookId): Loan
    {
        $dateEmprunt = date('Y-m-d');
        $dateRetour  = date('Y-m-d', strtotime('+21 days'));

        $stmt = $this->pdo->prepare(
            'INSERT INTO borrows (user_id, book_id, date_emprunt, date_retour_prevue, statut)
             VALUES (?, ?, ?, ?, "en_cours")'
        );
        $stmt->execute([$userId, $bookId, $dateEmprunt, $dateRetour]);

        $id = (int) $this->pdo->lastInsertId();
        return $this->findById($id) ?? throw new \RuntimeException('Emprunt introuvable après création');
    }

    public function findById(int $id): ?Loan
    {
        $stmt = $this->pdo->prepare(self::SELECT . ' WHERE bo.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : Loan::fromRow($row);
    }

    public function countActiveByUser(int $userId): int
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM borrows WHERE user_id = ? AND statut = "en_cours"'
        );
        $stmt->execute([$userId]);
        return (int) $stmt->fetchColumn();
    }

    public function hasActiveForUserAndBook(int $userId, int $bookId): bool
    {
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM borrows WHERE user_id = ? AND book_id = ? AND statut = "en_cours"'
        );
        $stmt->execute([$userId, $bookId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function markReturned(int $id): void
    {
        $this->pdo->prepare('UPDATE borrows SET date_retour = ?, statut = "retourne" WHERE id = ?')
            ->execute([date('Y-m-d'), $id]);
    }

    public function byUser(int $userId): array
    {
        $stmt = $this->pdo->prepare(self::SELECT . ' WHERE bo.user_id = ? ORDER BY bo.date_emprunt DESC');
        $stmt->execute([$userId]);
        return array_map(Loan::fromRow(...), $stmt->fetchAll());
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(self::SELECT . ' ORDER BY bo.date_emprunt DESC');
        return array_map(Loan::fromRow(...), $stmt->fetchAll());
    }
}
