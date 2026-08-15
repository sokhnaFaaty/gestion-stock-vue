<?php

declare(strict_types=1);

use Core\Request;
use Core\Response;

// CORS : autorise les appels depuis n'importe quelle origine (développement).
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Pré-requête CORS (OPTIONS) : réponse vide immédiate.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$router = require __DIR__ . '/../bootstrap/app.php';

$request  = Request::fromGlobals();
$response = $router->dispatch($request);

$response->send();
