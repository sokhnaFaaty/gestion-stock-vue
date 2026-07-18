<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Niveau;

class NiveauController extends Controller {
    private $niveauModel;

    public function __construct() {
        $this->niveauModel = new Niveau();
    }

    public function index() {
        $niveaux = $this->niveauModel->all();
        $this->view('niveau/index', ['niveaux' => $niveaux]);
    }
}