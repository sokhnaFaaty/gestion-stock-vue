<?php

declare(strict_types=1);

/**
 * Initialisation de l'API : crée la table api_tokens dans la base 'bibliotheque'.
 *
 * Prérequis : la base et les autres tables doivent déjà exister — lancez d'abord
 *   php database/seed.php
 * dans le dossier bibliotheque/ (il crée la base, les tables et les données démo).
 *
 * Usage :  php database/seed.php   (depuis le dossier api/)
 */

$config = require __DIR__ . '/../config/database.php';

$pdo = new PDO(
    sprintf('mysql:host=%s;charset=utf8mb4', $config['host']),
    $config['user'],
    $config['password'],
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$pdo->exec(sprintf('CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci', $config['dbname']));
$pdo->exec(sprintf('USE `%s`', $config['dbname']));

foreach (array_filter(array_map('trim', explode(';', (string) file_get_contents(__DIR__ . '/api_tokens.sql')))) as $statement) {
    $pdo->exec($statement);
}

$pdo->exec('DELETE FROM api_tokens WHERE expires_at <= NOW()');

echo "Table api_tokens prête.\n";
echo "Lancez les tests : php tests/run.php\n";
