<?php

declare(strict_types=1);

namespace App\Models;

class Category
{
    public function __construct(
        public readonly int $id,
        public readonly string $libelle,
        public readonly string $description,
        public readonly int $nbLivres = 0,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            libelle: $row['libelle'],
            description: $row['description'] ?? '',
            nbLivres: (int) ($row['nb_livres'] ?? 0),
        );
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'libelle'     => $this->libelle,
            'description' => $this->description,
            'nb_livres'   => $this->nbLivres,
        ];
    }
}
