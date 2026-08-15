<?php

declare(strict_types=1);

namespace App\Models;

class Loan
{
    public function __construct(
        public readonly int $id,
        public readonly int $userId,
        public readonly int $bookId,
        public readonly string $bookTitre,
        public readonly string $bookIsbn,
        public readonly string $dateEmprunt,
        public readonly string $dateRetourPrevue,
        public readonly ?string $dateRetour,
        public readonly string $statut,
        public readonly ?string $userNom = null,
        public readonly ?string $userPrenom = null,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            userId: (int) $row['user_id'],
            bookId: (int) $row['book_id'],
            bookTitre: $row['book_titre'],
            bookIsbn: $row['book_isbn'],
            dateEmprunt: $row['date_emprunt'],
            dateRetourPrevue: $row['date_retour_prevue'],
            dateRetour: $row['date_retour'] ?? null,
            statut: $row['statut'],
            userNom: $row['user_nom'] ?? null,
            userPrenom: $row['user_prenom'] ?? null,
        );
    }

    public function estEnCours(): bool
    {
        return $this->statut === 'en_cours';
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $data = [
            'id'                 => $this->id,
            'book'               => [
                'id'    => $this->bookId,
                'titre' => $this->bookTitre,
                'isbn'  => $this->bookIsbn,
            ],
            'date_emprunt'       => $this->dateEmprunt,
            'date_retour_prevue' => $this->dateRetourPrevue,
            'date_retour'        => $this->dateRetour,
            'statut'             => $this->statut,
        ];

        if ($this->userNom !== null) {
            $data['user'] = [
                'nom_complet' => trim(($this->userPrenom ?? '') . ' ' . $this->userNom),
            ];
        }

        return $data;
    }
}
