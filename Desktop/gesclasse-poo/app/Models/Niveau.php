<?php
namespace App\Models;

use App\Core\Model;

class Niveau extends Model {
    protected $table = 'niveaux';

    public function create($nom) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (nom) VALUES (?)");
        return $stmt->execute([$nom]);
    }

    public function update($id, $nom) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET nom = ? WHERE id = ?");
        return $stmt->execute([$nom, $id]);
    }
}