<?php

declare(strict_types=1);

use Core\Router;

/**
 * Jeu de tests fonctionnels de l'API (mode in-process : aucun serveur requis).
 *
 * Usage :  php tests/run.php
 *
 * Les tests effectuent des appels réels sur la base de données (création,
 * modification, suppression) : relancez d'abord
 *   php database/seed.php  (dossier bibliotheque/) puis  php database/seed.php  (dossier api/)
 * pour repartir d'un état propre.
 */

require __DIR__ . '/../bootstrap/autoload.php';

/** @var Router $router */
$router = require __DIR__ . '/../bootstrap/app.php';

/** @return array{status: int, data: array, body: mixed} */
function api(Router $router, string $method, string $path, array $body = [], ?string $token = null, array $query = []): array
{
    $headers = $token !== null ? ['authorization' => 'Bearer ' . $token] : [];

    $response = $router->dispatch(new \Core\Request($method, $path, $query, $body, $headers));

    return [
        'status' => $response->status(),
        'data'   => $response->data(),
        'body'   => $response->data()['data'] ?? null,
    ];
}

$passed = 0;
$failed = 0;

function check(bool $ok, string $label, string $detail = ''): void
{
    global $passed, $failed;
    if ($ok) {
        $passed++;
        echo "  PASS  $label\n";
    } else {
        $failed++;
        echo "  FAIL  $label" . ($detail !== '' ? " — $detail" : '') . "\n";
    }
}

function expectStatus(array $result, int $expected, string $label): void
{
    check(
        $result['status'] === $expected,
        $label,
        "attendu $expected, obtenu {$result['status']} (" . json_encode($result['body'] ?? $result['data']) . ')'
    );
}

echo "== Authentification ==\n";

$loginAdmin = api($router, 'POST', '/api/auth/login', ['email' => 'admin@biblio.fr', 'password' => 'admin123']);
expectStatus($loginAdmin, 200, 'login admin → 200');
check(isset($loginAdmin['body']['token']), 'la réponse contient un token');

$loginReader = api($router, 'POST', '/api/auth/login', ['email' => 'alice.martin@mail.fr', 'password' => 'lecteur123']);
expectStatus($loginReader, 200, 'login lecteur → 200');
$readerToken = $loginReader['body']['token'] ?? null;

expectStatus(api($router, 'POST', '/api/auth/login', ['email' => 'admin@biblio.fr', 'password' => 'mauvais']), 401, 'login mauvais mot de passe → 401');
expectStatus(api($router, 'POST', '/api/auth/login', ['email' => 'x@y.z', 'password' => 'x']), 401, 'login email inconnu → 401');
expectStatus(api($router, 'POST', '/api/auth/login', []), 422, 'login champs manquants → 422');

expectStatus(api($router, 'GET', '/api/auth/me'), 401, 'me sans token → 401');
expectStatus(api($router, 'GET', '/api/auth/me', [], 'token-invalide'), 401, 'me token invalide → 401');
$me = api($router, 'GET', '/api/auth/me', [], $loginAdmin['body']['token'] ?? '');
expectStatus($me, 200, 'me avec token admin → 200');
check(($me['body']['email'] ?? '') === 'admin@biblio.fr', 'me renvoie le bon utilisateur');
check(!isset($me['body']['password']), 'me ne renvoie JAMAIS le mot de passe');

echo "== Lecture publique ==\n";

$info = api($router, 'GET', '/api');
expectStatus($info, 200, 'GET /api (page d\'accueil) → 200');
check(($info['body']['name'] ?? '') !== '', 'la page d\'accueil renvoie le nom de l\'API');

$books = api($router, 'GET', '/api/books');
expectStatus($books, 200, 'GET /api/books → 200');
check(is_array($books['body']['items'] ?? null) && ($books['body']['meta']['total'] ?? 0) >= 10, 'liste paginée avec meta');

$search = api($router, 'GET', '/api/books', [], null, ['q' => 'petit']);
expectStatus($search, 200, 'GET /api/books?q=petit → 200');
check(($search['body']['meta']['total'] ?? 0) >= 1, 'la recherche "petit" trouve au moins 1 livre');

$categories = api($router, 'GET', '/api/categories');
expectStatus($categories, 200, 'GET /api/categories → 200');
check(count($categories['body'] ?? []) >= 5, '5 catégories présentes');
$firstCategoryId = (int) ($categories['body'][0]['id'] ?? 0);

expectStatus(api($router, 'GET', '/api/categories/' . $firstCategoryId . '/books'), 200, 'GET /api/categories/{id}/books → 200');
expectStatus(api($router, 'GET', '/api/categories/999999/books'), 404, 'catégorie inconnue → 404');

$firstBookId = (int) ($books['body']['items'][0]['id'] ?? 0);
expectStatus(api($router, 'GET', '/api/books/' . $firstBookId), 200, 'GET /api/books/{id} → 200');
expectStatus(api($router, 'GET', '/api/books/999999'), 404, 'livre inconnu → 404');

echo "== Création / modification / suppression (admin) ==\n";

expectStatus(api($router, 'POST', '/api/books', ['titre' => 'Test']), 401, 'POST /api/books sans token → 401');

$createBody = [
    'isbn'             => '978-0000000001',
    'titre'            => 'Livre de test API',
    'auteur'           => 'Testeur',
    'description'      => 'Créé par les tests.',
    'date_publication' => '2024-01-15',
    'quantite'         => 2,
    'categorie_id'     => $firstCategoryId,
];
expectStatus(api($router, 'POST', '/api/books', $createBody, $readerToken), 403, 'POST /api/books en lecteur → 403');

$created = api($router, 'POST', '/api/books', $createBody, $loginAdmin['body']['token'] ?? '');
expectStatus($created, 201, 'POST /api/books admin → 201');
$createdId = (int) ($created['body']['id'] ?? 0);

expectStatus(api($router, 'POST', '/api/books', ['titre' => 'Incomplet'], $loginAdmin['body']['token'] ?? ''), 422, 'POST livre invalide → 422');

expectStatus(api($router, 'PUT', '/api/books/' . $createdId, array_merge($createBody, ['titre' => 'Livre renommé', 'quantite' => 5]), $loginAdmin['body']['token'] ?? ''), 200, 'PUT /api/books/{id} admin → 200');
expectStatus(api($router, 'PUT', '/api/books/999999', $createBody, $loginAdmin['body']['token'] ?? ''), 404, 'PUT livre inconnu → 404');

expectStatus(api($router, 'DELETE', '/api/books/' . $createdId, [], $loginAdmin['body']['token'] ?? ''), 204, 'DELETE /api/books/{id} admin → 204');
expectStatus(api($router, 'GET', '/api/books/' . $createdId), 404, 'livre supprimé → 404');

echo "== Emprunts ==\n";

// Alice a déjà emprunté des livres dans les données de démo (1984, Le Petit
// Prince) : on identifie ses emprunts en cours pour choisir des livres
// réellement disponibles, et on lui libère une place (limite de 3 emprunts).
$myLoans0 = api($router, 'GET', '/api/loans/my', [], $readerToken);
$aliceActiveBookIds = [];
$seedLoanId = 0;
foreach (($myLoans0['body'] ?? []) as $loan) {
    $bookId = (int) ($loan['book']['id'] ?? 0);
    if (($loan['statut'] ?? '') === 'en_cours') {
        $aliceActiveBookIds[$bookId] = true;
        if ($seedLoanId === 0) {
            $seedLoanId = (int) $loan['id'];
        }
    }
}

$items = api($router, 'GET', '/api/books', [], null)['body']['items'] ?? [];
$availableId = 0;
$secondAvailableId = 0;
$unavailableId = 0;
foreach ($items as $item) {
    $id = (int) $item['id'];
    $disponible = (bool) ($item['disponible'] ?? false);
    if ($disponible && !isset($aliceActiveBookIds[$id])) {
        if ($availableId === 0) {
            $availableId = $id;
        } elseif ($secondAvailableId === 0) {
            $secondAvailableId = $id;
        }
    } elseif (!$disponible && $unavailableId === 0) {
        $unavailableId = $id;
    }
}

// Libère une place : Alice rend l'un de ses emprunts de démo.
expectStatus(api($router, 'POST', '/api/loans/' . $seedLoanId . '/return', [], $readerToken), 200, "retour d'un emprunt de démo → 200");
expectStatus(api($router, 'POST', '/api/loans/' . $seedLoanId . '/return', [], $readerToken), 404, "retour d'un emprunt déjà retourné → 404");

expectStatus(api($router, 'POST', '/api/books/' . $availableId . '/borrow', [], $readerToken), 201, 'emprunt lecteur → 201');
expectStatus(api($router, 'POST', '/api/books/' . $availableId . '/borrow', [], $readerToken), 409, 'double emprunt même livre → 409');
expectStatus(api($router, 'POST', '/api/books/' . $unavailableId . '/borrow', [], $readerToken), 409, 'emprunt livre indisponible → 409');
expectStatus(api($router, 'POST', '/api/books/' . $availableId . '/borrow'), 401, 'emprunt sans token → 401');

// Second emprunt, qui restera en cours pour tester le "retour d'autrui".
expectStatus(api($router, 'POST', '/api/books/' . $secondAvailableId . '/borrow', [], $readerToken), 201, 'second emprunt lecteur → 201');

$myLoans = api($router, 'GET', '/api/loans/my', [], $readerToken);
expectStatus($myLoans, 200, 'GET /api/loans/my → 200');
check(is_array($myLoans['body'] ?? null), 'mes emprunts sont une liste');

/** @return int identifiant de l'emprunt en cours pour le livre donné */
$activeLoanIdFor = static function (array $loans, int $bookId): int {
    foreach ($loans as $loan) {
        if ((int) ($loan['book']['id'] ?? 0) === $bookId && ($loan['statut'] ?? '') === 'en_cours') {
            return (int) $loan['id'];
        }
    }
    return 0;
};

// Un autre lecteur ne peut pas rendre l'emprunt de quelqu'un d'autre.
$loginOther = api($router, 'POST', '/api/auth/login', ['email' => 'lucas.bernard@mail.fr', 'password' => 'lecteur123']);
$otherToken = $loginOther['body']['token'] ?? '';
$secondLoanId = $activeLoanIdFor($myLoans['body'] ?? [], $secondAvailableId);
expectStatus(api($router, 'POST', '/api/loans/' . $secondLoanId . '/return', [], $otherToken), 403, "retour de l'emprunt d'autrui → 403");

echo "== Droits d'accès ==\n";

expectStatus(api($router, 'GET', '/api/users'), 401, 'GET /api/users sans token → 401');
expectStatus(api($router, 'GET', '/api/users', [], $readerToken), 403, 'GET /api/users en lecteur → 403');
expectStatus(api($router, 'GET', '/api/users', [], $loginAdmin['body']['token'] ?? ''), 200, 'GET /api/users admin → 200');
expectStatus(api($router, 'GET', '/api/loans', [], $readerToken), 403, 'GET /api/loans en lecteur → 403');

echo "== Déconnexion ==\n";

expectStatus(api($router, 'POST', '/api/auth/logout', [], $readerToken), 204, 'logout → 204');
expectStatus(api($router, 'GET', '/api/auth/me', [], $readerToken), 401, 'token révoqué → 401');

echo "\n";
echo "Résultat : $passed tests réussis, $failed échecs\n";
exit($failed === 0 ? 0 : 1);
