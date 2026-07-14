<?php
namespace App\Models;

use App\Core\Model;

class Classe extends Model {
    protected $table = 'classes';

    public function create($nom, $filiere_id, $niveau_id) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (nom, filiere_id, niveau_id) VALUES (?, ?, ?)");
        return $stmt->execute([$nom, $filiere_id, $niveau_id]);
    }

    public function update($id, $nom, $filiere_id, $niveau_id) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET nom = ?, filiere_id = ?, niveau_id = ? WHERE id = ?");
        return $stmt->execute([$nom, $filiere_id, $niveau_id, $id]);
    }
}
