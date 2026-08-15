<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use PDO;

class UserRepository implements UserRepositoryInterface
{
    private const SELECT_WITH_ROLE = <<<'SQL'
        SELECT u.*, r.libelle AS role_libelle
        FROM users u
        JOIN roles r ON r.id = u.role_id
        SQL;

    public function __construct(private PDO $pdo) {}

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_ROLE . ' WHERE u.id = ?');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row === false ? null : User::fromRow($row);
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare(self::SELECT_WITH_ROLE . ' WHERE u.email = ?');
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row === false ? null : User::fromRow($row);
    }

    public function all(): array
    {
        $stmt = $this->pdo->query(self::SELECT_WITH_ROLE . ' ORDER BY u.nom, u.prenom');
        return array_map(User::fromRow(...), $stmt->fetchAll());
    }
}
