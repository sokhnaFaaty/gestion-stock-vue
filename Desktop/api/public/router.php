<?php

declare(strict_types=1);

/**
 * Routeur du serveur de développement intégré de PHP.
 *
 * Usage (depuis le dossier api/) :
 *   php -S 127.0.0.1:8080 -t public public/router.php
 *
 * Les fichiers existants (assets) sont servis normalement ; tout le reste
 * passe par index.php pour être traité par l'API.
 */

if (is_file(__DIR__ . '/' . ltrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/', '/'))) {
    return false;
}

require __DIR__ . '/index.php';
