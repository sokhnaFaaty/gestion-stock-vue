<?php

declare(strict_types=1);

namespace App\Models;

class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $nom,
        public readonly string $prenom,
        public readonly string $email,
        public readonly string $password,
        public readonly ?string $telephone,
        public readonly int $roleId,
        public readonly string $role,
    ) {}

    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            nom: $row['nom'],
            prenom: $row['prenom'],
            email: $row['email'],
            password: $row['password'],
            telephone: $row['telephone'] ?? null,
            roleId: (int) $row['role_id'],
            role: $row['role_libelle'],
        );
    }

    public function nomComplet(): string
    {
        return trim($this->prenom . ' ' . $this->nom);
    }

    /** Le mot de passe n'est JAMAIS renvoyé par l'API. */
    public function toArray(): array
    {
        return [
            'id'        => $this->id,
            'nom'       => $this->nom,
            'prenom'    => $this->prenom,
            'nom_complet' => $this->nomComplet(),
            'email'     => $this->email,
            'telephone' => $this->telephone,
            'role'      => $this->role,
        ];
    }
}
