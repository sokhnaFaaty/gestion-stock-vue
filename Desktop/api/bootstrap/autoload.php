<?php

declare(strict_types=1);

spl_autoload_register(function (string $classe): void {
    $prefixes = [
        'App\\'       => __DIR__ . '/../app/',
        'Core\\'      => __DIR__ . '/../Core/',
        'Exceptions\\' => __DIR__ . '/../Exceptions/',
    ];

    foreach ($prefixes as $prefixe => $base) {
        if (str_starts_with($classe, $prefixe)) {
            $chemin = $base . str_replace('\\', '/', substr($classe, strlen($prefixe))) . '.php';
            if (file_exists($chemin)) {
                require $chemin;
                return;
            }
        }
    }
});
