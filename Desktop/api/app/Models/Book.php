<?php

declare(strict_types=1);

namespace App\Models;

class Book
{
    public function __construct(
        public readonly int $id,
        public readonly string $isbn,
        public readonly string $titre,
        public readonly string $auteur,
        public readonly string $description,
        public readonly ?string $datePublication,
        public readonly int $quantite,
        public readonly ?string $couverture,
        public readonly ?int $categorieId,
        public readonly ?string $categorieLibelle,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            isbn: $row['isbn'],
            titre: $row['titre'],
            auteur: $row['auteur'],
            description: $row['description'] ?? '',
            datePublication: $row['date_publication'] ?? null,
            quantite: (int) $row['quantite'],
            couverture: $row['couverture'] ?? null,
            categorieId: $row['categorie_id'] !== null ? (int) $row['categorie_id'] : null,
            categorieLibelle: $row['categorie_libelle'] ?? null,
        );
    }

    public function disponible(): bool
    {
        return $this->quantite > 0;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'id'               => $this->id,
            'isbn'             => $this->isbn,
            'titre'            => $this->titre,
            'auteur'           => $this->auteur,
            'description'      => $this->description,
            'date_publication' => $this->datePublication,
            'quantite'         => $this->quantite,
            'disponible'       => $this->disponible(),
            'couverture'       => $this->couverture,
            'categorie'        => [
                'id'     => $this->categorieId,
                'libelle' => $this->categorieLibelle,
            ],
        ];
    }
}
