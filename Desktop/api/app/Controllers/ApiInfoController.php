<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\Request;
use Core\Response;

/**
 * Page d'accueil de l'API : quelques informations sur le service et les
 * points d'entrée principaux (visible à http://localhost:8001/api).
 */
class ApiInfoController
{
    public function index(Request $request): Response
    {
        return Response::json([
            'name'    => 'API Bibliothèque du Savoir',
            'version' => '1.0',
            'format'  => 'JSON',
            'auth'    => 'Token Bearer via POST /api/auth/login (24 h, stocké en SHA-256)',
            'endpoints' => [
                '/api/books',
                '/api/categories',
                '/api/loans',
                '/api/users',
                '/api/auth/me',
            ],
        ]);
    }
}
