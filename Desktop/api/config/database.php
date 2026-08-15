<?php

declare(strict_types=1);

/**
 * Base de données : la même base 'bibliotheque' que le projet web.
 * Le tableau (users, categories, books, borrows, roles) est créé par
 * bibliotheque/database/schema.sql (ou seed.php).
 */
return [
    'host'     => 'localhost',
    'dbname'   => 'bibliotheque',
    'user'     => 'root',
    'password' => '',
];
