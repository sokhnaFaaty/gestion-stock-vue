<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Filiere;

class FiliereController extends Controller {
    private $filiereModel;

    public function __construct() {
        $this->filiereModel = new Filiere();
    }

    public function index() {
        $filieres = $this->filiereModel->all();
        $this->view('filiere/index', ['filieres' => $filieres]);
    }
}