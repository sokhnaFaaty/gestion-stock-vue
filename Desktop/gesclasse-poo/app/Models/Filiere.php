<?php
namespace App\Models;

use App\Core\Model;

class Filiere extends Model {
    protected $table = 'filieres';

    public function create($libelle) {
        $stmt = $this->db->prepare("INSERT INTO {$this->table} (libelle) VALUES (?)");
        return $stmt->execute([$libelle]);
    }

    public function update($id, $libelle) {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET libelle = ? WHERE id = ?");
        return $stmt->execute([$libelle, $id]);
    }
}
